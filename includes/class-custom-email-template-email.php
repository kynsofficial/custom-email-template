<?php
/**
 * The email functionality of the plugin.
 *
 * @package CustomEmailTemplate
 */

/**
 * The email functionality of the plugin.
 */
class Custom_Email_Template_Email {

    /**
     * Set the content type of emails to HTML.
     *
     * @return string The content type.
     */
    public function set_content_type() {
        return 'text/html';
    }

    /**
     * Modify the from email address for WordPress emails.
     *
     * @param string $original_email The original from email address.
     * @return string The modified from email address.
     */
    public function from_email($original_email) {
        $custom_email = get_option('custom_email_from_email', '');
        if (!empty($custom_email)) {
            return $custom_email;
        }
        return $original_email;
    }

    /**
     * Modify the from name for WordPress emails.
     *
     * @param string $original_name The original from name.
     * @return string The modified from name.
     */
    public function from_name($original_name) {
        $custom_name = get_option('custom_email_from_name', '');
        if (!empty($custom_name)) {
            return $custom_name;
        }
        // Use site name as default if original is "WordPress"
        if ($original_name === 'WordPress') {
            return get_bloginfo('name');
        }
        return $original_name;
    }

    /**
     * Add headers to emails.
     *
     * @param mixed $headers The original headers.
     * @return mixed The modified headers.
     */
    public function headers($headers) {
        $avatar_url = get_option('custom_email_avatar_url', '');
        
        if (!empty($avatar_url)) {
            // Add the avatar header
            if (is_array($headers)) {
                $headers[] = 'X-Sender-Avatar: ' . $avatar_url;
            } else {
                $headers .= "\r\n" . 'X-Sender-Avatar: ' . $avatar_url;
            }
        }
        
        return $headers;
    }

    /**
     * Apply custom template to WordPress emails, excluding Tutor LMS and WooCommerce emails if configured.
     *
     * @param array $args The wp_mail arguments.
     * @return array The modified wp_mail arguments.
     */
/**
 * Apply custom template to WordPress emails, excluding certain plugin emails.
 *
 * @param array $args The wp_mail arguments.
 * @return array The modified wp_mail arguments.
 */
/**
 * Apply custom template to WordPress emails, excluding certain plugin emails or handling them specially.
 *
 * @param array $args The wp_mail arguments.
 * @return array The modified wp_mail arguments.
 */
public function apply_template($args) {
    // Make sure we have the message
    if (!isset($args['message'])) {
        return $args;
    }
    
    $message = $args['message'];
    $subject = isset($args['subject']) ? $args['subject'] : '';
    
    // Check if this is a Tutor LMS email by looking for specific patterns
    $exclude_tutor_lms = get_option('custom_email_exclude_tutor_lms', 'yes') === 'yes';
    if ($exclude_tutor_lms) {
        $tutor_patterns = array(
            'tutor-email-body',
            'tutor-email-wrapper',
            'tutor-email-header',
            'tutor-email-content',
            'tutor-email-footer',
            'class="tutor-',
            'id="tutor-',
            '--primary-button-color',
            'tutor-email-button'
        );
        
        foreach ($tutor_patterns as $pattern) {
            if (stripos($message, $pattern) !== false) {
                // This is a Tutor LMS email, return args as-is
                return $args;
            }
        }
    }
    
    // Check for WooCommerce emails
    $exclude_woocommerce = get_option('custom_email_exclude_woocommerce', 'no') === 'yes';
    $is_woo_email = false;
    
    $woo_patterns = array(
        'woocommerce',
        '[ORDER #',
        'ORDER #',
        'Payment Approved',
        'class="td font-family',
        'template_header',
        'template_footer',
        'woocommerce-table',
        'shop_table',
        'order_details'
    );
    
    foreach ($woo_patterns as $pattern) {
        if (stripos($message, $pattern) !== false) {
            $is_woo_email = true;
            break;
        }
    }
    
    // If this is a WooCommerce email and we should exclude it
    if ($is_woo_email && $exclude_woocommerce) {
        return $args;
    }
    
    // If this is a WooCommerce email and we shouldn't exclude it,
    // extract the content and create a clean email
    if ($is_woo_email && !$exclude_woocommerce) {
        // Get the payment/order status heading
        $payment_heading = '';
        if (preg_match('/<h1[^>]*?>(Payment Approved|Order.*?|Your order.*?)<\/h1>/is', $message, $heading_matches)) {
            $payment_heading = $heading_matches[1];
        } elseif (preg_match('/<td[^>]*?id="header_wrapper"[^>]*?><h1[^>]*?>(.*?)<\/h1>/is', $message, $heading_matches)) {
            $payment_heading = $heading_matches[1];
        }
        
        // Extract all important content sections
        $extracted_content = '';
        
        // First try to get the main content
        if (preg_match('/<div id="body_content_inner"[^>]*?>(.*?)<\/div>\s*<\/td>/is', $message, $matches)) {
            $extracted_content .= $matches[1];
        }
        
        // Extract billing and shipping addresses if they exist
        if (preg_match('/<h2[^>]*?>Billing address<\/h2>.*?<address[^>]*?>(.*?)<\/address>/is', $message, $billing_matches)) {
            if (strpos($extracted_content, 'Billing address') === false) {
                $extracted_content .= '<h2>Billing address</h2><address>' . $billing_matches[1] . '</address>';
            }
        }
        
        if (preg_match('/<h2[^>]*?>Shipping address<\/h2>.*?<address[^>]*?>(.*?)<\/address>/is', $message, $shipping_matches)) {
            if (strpos($extracted_content, 'Shipping address') === false) {
                $extracted_content .= '<h2>Shipping address</h2><address>' . $shipping_matches[1] . '</address>';
            }
        }
        
        // Extract any additional content/notes at the end
        if (preg_match('/<\/table>.*?<p[^>]*?>(We have approved your payment.*?)<\/p>/is', $message, $additional_matches)) {
            if (strpos($extracted_content, $additional_matches[1]) === false) {
                $extracted_content .= '<p>' . $additional_matches[1] . '</p>';
            }
        }
        
        // If content is empty, use the original message but strip headers/footers
        if (empty($extracted_content)) {
            // Remove the outer template structure but keep content
            $extracted_content = preg_replace('/<html.*?>.*?<body.*?>(.*?)<\/body>.*?<\/html>/is', '$1', $message);
            // Remove header and footer
            $extracted_content = preg_replace('/<table[^>]*?id="template_header".*?>.*?<\/table>/is', '', $extracted_content);
            $extracted_content = preg_replace('/<table[^>]*?id="template_footer".*?>.*?<\/table>/is', '', $extracted_content);
        }
        
        // Create a complete content with payment heading
        $complete_content = '';
        
        // Add payment heading as a styled element if it exists
        if (!empty($payment_heading)) {
            $button_bg_color = get_option('custom_email_button_bg_color', '#696cff');
            $button_text_color = get_option('custom_email_button_text_color', '#ffffff');
            
            $complete_content .= '<div style="background-color: ' . esc_attr($button_bg_color) . '; color: ' . esc_attr($button_text_color) . '; padding: 30px 20px; text-align: center; font-size: 24px; margin-bottom: 20px; border-radius: 4px;">';
            $complete_content .= esc_html($payment_heading);
            $complete_content .= '</div>';
        }
        
        // Add the extracted content
        $complete_content .= $extracted_content;
        
        // Apply our template to the cleaned content
        $args['message'] = $this->get_template($complete_content, $subject);
        
        // Add Reply-To header if set
        $this->add_reply_to_header($args);
        
        return $args;
    }
    
    // For non-WooCommerce, non-excluded emails, apply our template normally
    $original_message = $args['message'];
    $args['message'] = $this->get_template($original_message, $subject);
    
    // Add Reply-To header if set
    $this->add_reply_to_header($args);
    
    return $args;
}

/**
 * Add Reply-To header if set.
 *
 * @param array $args The email arguments.
 */
private function add_reply_to_header(&$args) {
    $reply_to = get_option('custom_email_reply_to', '');
    if (!empty($reply_to)) {
        if (isset($args['headers']) && is_array($args['headers'])) {
            $has_reply_to = false;
            foreach ($args['headers'] as $header) {
                if (strpos(strtolower($header), 'reply-to:') !== false) {
                    $has_reply_to = true;
                    break;
                }
            }
            if (!$has_reply_to) {
                $args['headers'][] = 'Reply-To: ' . $reply_to;
            }
        } else if (isset($args['headers']) && is_string($args['headers'])) {
            if (strpos(strtolower($args['headers']), 'reply-to:') === false) {
                $args['headers'] .= "\r\n" . 'Reply-To: ' . $reply_to;
            }
        } else {
            $args['headers'] = ['Reply-To: ' . $reply_to];
        }
    }
}
    
    /**
     * Configure PHPMailer directly.
     *
     * @param PHPMailer $phpmailer The PHPMailer instance.
     */
    public function phpmailer_init($phpmailer) {
        $custom_from_name = get_option('custom_email_from_name', '');
        if (!empty($custom_from_name)) {
            $phpmailer->FromName = $custom_from_name;
        } else if ($phpmailer->FromName === 'WordPress') {
            $phpmailer->FromName = get_bloginfo('name');
        }
        
        $custom_from_email = get_option('custom_email_from_email', '');
        if (!empty($custom_from_email)) {
            $phpmailer->From = $custom_from_email;
        }
        
        $reply_to = get_option('custom_email_reply_to', '');
        if (!empty($reply_to)) {
            // Only add reply-to if it doesn't already exist
            if (!$phpmailer->getReplyToAddresses()) {
                $phpmailer->addReplyTo($reply_to);
            }
        }
        
        // Add avatar through custom header
        $avatar_url = get_option('custom_email_avatar_url', '');
        if (!empty($avatar_url)) {
            $phpmailer->addCustomHeader('X-Sender-Avatar', $avatar_url);
        }
    }
    
 
//**
 /**
 * Get the custom email template.
 *
 * @param string $content The original email content.
 * @param string $subject The email subject.
 * @return string The formatted email with template applied.
 */
public function get_template($content, $subject) {
    // Get site info
    $site_name = get_bloginfo('name');
    
    // Get logo URL from settings or use default
    $logo_url = get_option('custom_email_logo_url', plugins_url('assets/images/default-logo.png', dirname(__FILE__)));
    
    // Get logo alignment
    $logo_alignment = get_option('custom_email_logo_alignment', 'center');
    
    // Check if content is already HTML
    $is_html = preg_match('/<html|<body|<div|<p|<table|<h[1-6]|<img/i', $content);
    
    // If it's not HTML, wrap it in paragraph tags and convert line breaks
    if (!$is_html) {
        $content = wpautop($content);
    }
    
    // Get footer text from settings or use default
    $footer_text = get_option('custom_email_footer_text', 'This is an automatically generated email. Please do not reply.');
    
    // Get color settings with defaults
    $bg_color = get_option('custom_email_bg_color', '#f7f7f7');
    $container_bg_color = get_option('custom_email_container_bg_color', '#ffffff');
    $header_border_color = get_option('custom_email_header_border_color', '#e0e2ea');
    $primary_text_color = get_option('custom_email_primary_text_color', '#212327');
    $secondary_text_color = get_option('custom_email_secondary_text_color', '#5b616f');
    $footer_text_color = get_option('custom_email_footer_text_color', '#9da3af');
    $footer_border_color = get_option('custom_email_footer_border_color', '#e0e2ea');
    $button_bg_color = get_option('custom_email_button_bg_color', '#696cff');
    $button_text_color = get_option('custom_email_button_text_color', '#ffffff');
    
    // Pre-apply button styling to content
    $content = str_replace(
        '<a href="#"',
        '<a href="#" style="background-color: ' . esc_attr($button_bg_color) . '; color: ' . esc_attr($button_text_color) . '; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: 500; display: inline-block;"',
        $content
    );
    
    // Include the email template file
    ob_start();
    include CUSTOM_EMAIL_TEMPLATE_PLUGIN_DIR . 'templates/email-template.php';
    $html_message = ob_get_clean();
    
    return $html_message;
}

}