<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package CustomEmailTemplate
 */

/**
 * The admin-specific functionality of the plugin.
 */
class Custom_Email_Template_Admin {

    /**
     * Register the stylesheets and JavaScript for the admin area.
     *
     * @param string $hook The current admin page.
     */
    public function enqueue_scripts($hook) {
        // Only load on our settings page
        if ($hook != 'settings_page_custom-email-template') {
            return;
        }
        
        // Add WordPress color picker
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        // Add media uploader
        wp_enqueue_media();
        
        // Ensure asset directories exist
        $this->ensure_asset_directories();
        
        // Add plugin custom CSS
        wp_enqueue_style(
            'custom-email-template-admin', 
            CUSTOM_EMAIL_TEMPLATE_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            CUSTOM_EMAIL_TEMPLATE_VERSION
        );
        
        // Add plugin custom JS
        wp_enqueue_script(
            'custom-email-template-admin',
            CUSTOM_EMAIL_TEMPLATE_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            CUSTOM_EMAIL_TEMPLATE_VERSION,
            true
        );
        
        // Localize script with nonce
        wp_localize_script('custom-email-template-admin', 'customEmailTemplateSettings', array(
            'nonce' => wp_create_nonce('custom_email_template_nonce'),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'tab_nonce' => wp_create_nonce('custom_email_template_tab_nonce')
        ));
    }

    /**
     * Ensure asset directories and files exist.
     */
    private function ensure_asset_directories() {
        $css_dir = CUSTOM_EMAIL_TEMPLATE_PLUGIN_DIR . 'assets/css';
        $js_dir = CUSTOM_EMAIL_TEMPLATE_PLUGIN_DIR . 'assets/js';
        
        if (!file_exists($css_dir)) {
            wp_mkdir_p($css_dir);
            file_put_contents($css_dir . '/admin.css', $this->get_admin_css());
        }
        
        if (!file_exists($js_dir)) {
            wp_mkdir_p($js_dir);
            file_put_contents($js_dir . '/admin.js', $this->get_admin_js());
        }
    }

    /**
     * Add settings link to plugin page.
     *
     * @param array $links The existing plugin action links.
     * @return array The modified plugin action links.
     */
    public function add_action_links($links) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=custom-email-template') . '">' . __('Settings', 'custom-email-template') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Sanitize URL fields.
     *
     * @param string $url The URL to sanitize.
     * @return string The sanitized URL.
     */
    public function sanitize_url($url) {
        return esc_url_raw($url);
    }

    /**
     * Sanitize checkbox fields.
     *
     * @param string $value The checkbox value.
     * @return string The sanitized checkbox value.
     */
    public function sanitize_checkbox($value) {
        return ($value === 'yes') ? 'yes' : 'no';
    }

    /**
     * Sanitize color fields.
     *
     * @param string $color The color value.
     * @return string The sanitized color value.
     */
    public function sanitize_color($color) {
        // If empty, return empty
        if (empty($color)) {
            return '';
        }
        
        // If it's a standard hex color with #, sanitize and return
        if ('#' === substr($color, 0, 1)) {
            $color = sanitize_hex_color($color);
            return $color;
        }
        
        // If it's a rgb or rgba, convert to hex
        if (preg_match('/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*[\d\.]+)?\)/i', $color, $matches)) {
            $r = $matches[1];
            $g = $matches[2];
            $b = $matches[3];
            
            // Convert to hex and return
            $hex_color = '#' . dechex($r) . dechex($g) . dechex($b);
            return $hex_color;
        }
        
        // Otherwise, just return the sanitized value
        return sanitize_text_field($color);
    }

    /**
     * Special sanitization for password to preserve special characters but remove potentially harmful content.
     * 
     * @param string $password The password to sanitize.
     * @return string The sanitized password.
     */
    public function sanitize_password($password) {
        // Remove slashes
        $password = wp_unslash($password);
        
        // Remove any HTML/PHP tags
        $password = wp_strip_all_tags($password);
        
        // Remove control characters
        $password = preg_replace('/[\x00-\x1F\x7F]/', '', $password);
        
        return $password;
    }

    /**
     * Register plugin settings.
     */
    public function register_settings() {
        // General settings
        register_setting('custom-email-template-group', 'custom_email_logo_url', 'esc_url_raw');
        register_setting('custom-email-template-group', 'custom_email_logo_alignment', 'sanitize_text_field');
        register_setting('custom-email-template-group', 'custom_email_footer_text', 'sanitize_textarea_field');
        register_setting('custom-email-template-group', 'custom_email_preserve_data', array($this, 'sanitize_checkbox'));
        
        // Email sender settings
        register_setting('custom-email-template-group', 'custom_email_from_email', 'sanitize_email');
        register_setting('custom-email-template-group', 'custom_email_from_name', 'sanitize_text_field');
        register_setting('custom-email-template-group', 'custom_email_reply_to', 'sanitize_email');
        register_setting('custom-email-template-group', 'custom_email_avatar_url', 'esc_url_raw');
        
        // Color settings
        register_setting('custom-email-template-group', 'custom_email_bg_color', array($this, 'sanitize_color'));
        register_setting('custom-email-template-group', 'custom_email_container_bg_color', array($this, 'sanitize_color'));
        register_setting('custom-email-template-group', 'custom_email_header_border_color', array($this, 'sanitize_color'));
        register_setting('custom-email-template-group', 'custom_email_primary_text_color', array($this, 'sanitize_color'));
        register_setting('custom-email-template-group', 'custom_email_secondary_text_color', array($this, 'sanitize_color'));
        register_setting('custom-email-template-group', 'custom_email_footer_text_color', array($this, 'sanitize_color'));
        register_setting('custom-email-template-group', 'custom_email_footer_border_color', array($this, 'sanitize_color'));
        register_setting('custom-email-template-group', 'custom_email_button_bg_color', array($this, 'sanitize_color'));
        register_setting('custom-email-template-group', 'custom_email_button_text_color', array($this, 'sanitize_color'));
        
        // Template exclusion settings
        register_setting('custom-email-template-group', 'custom_email_exclude_tutor_lms', array($this, 'sanitize_checkbox'));
        register_setting('custom-email-template-group', 'custom_email_exclude_woocommerce', array($this, 'sanitize_checkbox'));
        
        // SMTP settings
        register_setting('custom-email-template-group', 'custom_email_use_smtp', array($this, 'sanitize_checkbox'));
        register_setting('custom-email-template-group', 'custom_email_smtp_host', 'sanitize_text_field');
        register_setting('custom-email-template-group', 'custom_email_smtp_auth', array($this, 'sanitize_checkbox'));
        register_setting('custom-email-template-group', 'custom_email_smtp_port', 'intval');
        register_setting('custom-email-template-group', 'custom_email_smtp_username', 'sanitize_text_field');
        register_setting('custom-email-template-group', 'custom_email_smtp_password', array($this, 'sanitize_password'));
        register_setting('custom-email-template-group', 'custom_email_smtp_encryption', 'sanitize_text_field');
        register_setting('custom-email-template-group', 'custom_email_smtp_debug', array($this, 'sanitize_checkbox'));
        
        // Set defaults after registration
        $this->set_default_options();
    }
    
    /**
     * Set default options for all settings.
     */
    public function set_default_options() {
        $defaults = array(
            'custom_email_logo_url' => 'https://img.freepik.com/premium-vector/simple-letter-n-company-logo_197415-6.jpg?w=1380',
            'custom_email_logo_alignment' => 'center',
            'custom_email_footer_text' => 'This is an automatically generated email. Please do not reply.',
            'custom_email_preserve_data' => 'no',
            'custom_email_from_email' => '',
            'custom_email_from_name' => '',
            'custom_email_reply_to' => '',
            'custom_email_avatar_url' => '',
            'custom_email_bg_color' => '#f7f7f7',
            'custom_email_container_bg_color' => '#ffffff',
            'custom_email_header_border_color' => '#e0e2ea',
            'custom_email_primary_text_color' => '#212327',
            'custom_email_secondary_text_color' => '#5b616f',
            'custom_email_footer_text_color' => '#9da3af',
            'custom_email_footer_border_color' => '#e0e2ea',
            'custom_email_button_bg_color' => '#696cff',
            'custom_email_button_text_color' => '#ffffff',
            'custom_email_exclude_tutor_lms' => 'yes',
            'custom_email_exclude_woocommerce' => 'no',
            'custom_email_use_smtp' => 'no',
            'custom_email_smtp_host' => '',
            'custom_email_smtp_auth' => 'yes',
            'custom_email_smtp_port' => 587,
            'custom_email_smtp_username' => '',
            'custom_email_smtp_password' => '',
            'custom_email_smtp_encryption' => 'tls',
            'custom_email_smtp_debug' => 'no',
        );
        
        foreach ($defaults as $option => $default_value) {
            if (false === get_option($option)) {
                add_option($option, $default_value);
            }
        }
    }

    /**
     * Register the settings page.
     */
    public function register_settings_page() {
        add_options_page(
            __('Email Template Settings', 'custom-email-template'), 
            __('Email Template', 'custom-email-template'), 
            'manage_options', 
            'custom-email-template', 
            array($this, 'display_settings_page')
        );
    }

    /**
     * Display the settings page.
     */
    public function display_settings_page() {
        require_once CUSTOM_EMAIL_TEMPLATE_PLUGIN_DIR . 'templates/admin-page.php';
    }
    
    /**
     * Redirect to the correct tab after settings save.
     *
     * @param string $location The URI the user will be redirected to.
     * @return string The modified redirect URI.
     */
    public function settings_redirect($location) {
        // Only modify our plugin's settings page redirects
        if (strpos($location, 'options-general.php?page=custom-email-template') !== false) {
            // Check if we have a tab_id in the submitted form data and verify nonce
            $nonce_verified = false;
            
            // Check nonce from the settings form
            if (isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'custom-email-template-group-options')) {
                $nonce_verified = true;
            }
            
            if ($nonce_verified && isset($_POST['tab_id'])) {
                $tab_id = sanitize_text_field(wp_unslash($_POST['tab_id']));
                
                // Add or update the tab_id parameter in the redirect URL
                if (strpos($location, 'tab_id=') !== false) {
                    // Replace existing tab_id
                    $location = preg_replace('/tab_id=[^&]+/', 'tab_id=' . $tab_id, $location);
                } else {
                    // Add tab_id
                    $location .= (strpos($location, '?') !== false) ? '&' : '?';
                    $location .= 'tab_id=' . $tab_id;
                }
                
                // Add nonce to the redirect URL for security
                $tab_nonce = wp_create_nonce('custom_email_template_tab_nonce');
                $location .= '&_wpnonce=' . $tab_nonce;
            }
        }
        
        return $location;
    }

    /**
     * Ajax handler for sending test email.
     */
    public function send_test_callback() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST['nonce'])), 'custom_email_template_nonce')) {
            wp_send_json_error(__('Security check failed', 'custom-email-template'));
        }
        
        // Get test email address
        $test_email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        
        // If no email provided, use admin email
        if (empty($test_email)) {
            $test_email = get_option('admin_email');
        }
        
        // Validate email
        if (!is_email($test_email)) {
            wp_send_json_error(__('Please enter a valid email address.', 'custom-email-template'));
        }
        
        // Send test email
        $subject = __('Test Email from Custom Email Template', 'custom-email-template');
        $message = '
            <h1>This is a Test Email</h1>
            <p>Hello,</p>
            <p>This is a test email to verify your email template settings are working correctly. If you received this email and it displays properly with your customized template, your settings are configured properly!</p>
            <p>Here\'s a sample button to test button styling:</p>
            <p style="text-align: center; margin: 25px 0;">
                <a href="#" style="background-color: ' . esc_attr(get_option('custom_email_button_bg_color', '#696cff')) . '; color: ' . esc_attr(get_option('custom_email_button_text_color', '#ffffff')) . '; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: 500;">Sample Button</a>
            </p>
            <p>If you notice any issues with the template, please check your settings.</p>
            <p>Best regards,<br>Custom Email Template</p>
        ';
        
        $result = wp_mail($test_email, $subject, $message);
        
        if ($result) {
            wp_send_json_success(__('Test email sent successfully to ', 'custom-email-template') . $test_email);
        } else {
            wp_send_json_error(__('Failed to send test email. Please check your server\'s email configuration.', 'custom-email-template'));
        }
    }

    /**
     * Ajax handler for testing SMTP connection.
     */
    public function test_smtp_callback() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST['nonce'])), 'custom_email_template_nonce')) {
            wp_send_json_error(__('Security check failed', 'custom-email-template'));
        }
        
        // Get SMTP settings from form data with proper sanitization and unslashing
        $host = isset($_POST['host']) ? sanitize_text_field(wp_unslash($_POST['host'])) : '';
        $port = isset($_POST['port']) ? intval($_POST['port']) : 587;
        $encryption = isset($_POST['encryption']) ? sanitize_text_field(wp_unslash($_POST['encryption'])) : 'tls';
        $auth = isset($_POST['auth']) && $_POST['auth'] === 'yes';
        $username = isset($_POST['username']) ? sanitize_text_field(wp_unslash($_POST['username'])) : '';
        
		
		// Special handling for password - unslash first then sanitize
        $raw_password = isset($_POST['password']) ? sanitize_text_field(wp_unslash($_POST['password'])) : '';
        // Process sanitized password for SMTP use (restore certain special characters if needed)
        $password = $this->process_smtp_password($raw_password);
        
        // Validate required fields
        if (empty($host)) {
            wp_send_json_error(__('SMTP Host is required.', 'custom-email-template'));
        }
        
        if ($auth && (empty($username) || empty($password))) {
            wp_send_json_error(__('Username and Password are required for authentication.', 'custom-email-template'));
        }
        
        // Test SMTP connection
        require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
        require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
        require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // Server settings
            $mail->SMTPDebug = 0; // Disable debug output
            $mail->isSMTP();
            $mail->Host = $host;
            $mail->Port = $port;
            $mail->SMTPAuth = $auth;
            
            if ($auth) {
                $mail->Username = $username;
                $mail->Password = $password;
            }
            
            // Set encryption
            if ($encryption === 'tls') {
                $mail->SMTPSecure = 'tls';
            } elseif ($encryption === 'ssl') {
                $mail->SMTPSecure = 'ssl';
            } else {
                $mail->SMTPSecure = '';
                $mail->SMTPAutoTLS = false;
            }
            
            // Add SSL options for compatibility with self-signed certificates
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // Try to connect to SMTP server
            $mail->smtpConnect();
            
            wp_send_json_success(__('SMTP connection successful! Your settings are correct.', 'custom-email-template'));
        } catch (Exception $e) {
            wp_send_json_error(__('SMTP connection failed: ', 'custom-email-template') . $mail->ErrorInfo);
        }
    }
    
    /**
     * Process a sanitized password for SMTP use.
     * This method takes a sanitized password and restores certain special characters 
     * that may be needed for SMTP authentication but were affected by sanitization.
     *
     * @param string $sanitized_password A sanitized password.
     * @return string The processed password ready for SMTP use.
     */
    public function process_smtp_password($sanitized_password) {
        // For now, just return the sanitized password
        // This method can be expanded later if special character handling is needed
        return $sanitized_password;
    }
	
	
    /**
     * Get admin CSS content.
     *
     * @return string The CSS content.
     */
    private function get_admin_css() {
        ob_start();
        include CUSTOM_EMAIL_TEMPLATE_PLUGIN_DIR . 'assets/css/admin.css';
        return ob_get_clean();
    }

    /**
     * Get admin JS content.
     *
     * @return string The JS content.
     */
    private function get_admin_js() {
        ob_start();
        include CUSTOM_EMAIL_TEMPLATE_PLUGIN_DIR . 'assets/js/admin.js';
        return ob_get_clean();
    }
}