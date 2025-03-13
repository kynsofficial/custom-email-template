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
            'ajaxurl' => admin_url('admin-ajax.php')
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
     * Register plugin settings.
     */
    public function register_settings() {
        // General settings
        register_setting('custom-email-template-group', 'custom_email_logo_url');
        register_setting('custom-email-template-group', 'custom_email_logo_alignment');
        register_setting('custom-email-template-group', 'custom_email_footer_text');
        
        // Email sender settings
        register_setting('custom-email-template-group', 'custom_email_from_email');
        register_setting('custom-email-template-group', 'custom_email_from_name');
        register_setting('custom-email-template-group', 'custom_email_reply_to');
        register_setting('custom-email-template-group', 'custom_email_avatar_url');
        
        // Color settings
        register_setting('custom-email-template-group', 'custom_email_bg_color');
        register_setting('custom-email-template-group', 'custom_email_container_bg_color');
        register_setting('custom-email-template-group', 'custom_email_header_border_color');
        register_setting('custom-email-template-group', 'custom_email_primary_text_color');
        register_setting('custom-email-template-group', 'custom_email_secondary_text_color');
        register_setting('custom-email-template-group', 'custom_email_footer_text_color');
        register_setting('custom-email-template-group', 'custom_email_footer_border_color');
        register_setting('custom-email-template-group', 'custom_email_button_bg_color');
        register_setting('custom-email-template-group', 'custom_email_button_text_color');
        
        // Template exclusion settings
        register_setting('custom-email-template-group', 'custom_email_exclude_tutor_lms', array('default' => 'yes'));
        register_setting('custom-email-template-group', 'custom_email_exclude_woocommerce', array('default' => 'no'));
        
        // SMTP settings
        register_setting('custom-email-template-group', 'custom_email_use_smtp');
        register_setting('custom-email-template-group', 'custom_email_smtp_host');
        register_setting('custom-email-template-group', 'custom_email_smtp_auth');
        register_setting('custom-email-template-group', 'custom_email_smtp_port');
        register_setting('custom-email-template-group', 'custom_email_smtp_username');
        register_setting('custom-email-template-group', 'custom_email_smtp_password');
        register_setting('custom-email-template-group', 'custom_email_smtp_encryption');
        register_setting('custom-email-template-group', 'custom_email_smtp_debug');
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
     * Ajax handler for sending test email.
     */
    public function send_test_callback() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'custom_email_template_nonce')) {
            wp_send_json_error(__('Security check failed', 'custom-email-template'));
        }
        
        // Get test email address
        $test_email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        
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
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'custom_email_template_nonce')) {
            wp_send_json_error(__('Security check failed', 'custom-email-template'));
        }
        
        // Get SMTP settings from form data
        $host = isset($_POST['host']) ? sanitize_text_field($_POST['host']) : '';
        $port = isset($_POST['port']) ? intval($_POST['port']) : 587;
        $encryption = isset($_POST['encryption']) ? sanitize_text_field($_POST['encryption']) : 'tls';
        $auth = isset($_POST['auth']) && $_POST['auth'] === 'yes';
        $username = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : ''; // Don't sanitize password to preserve special chars
        
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
            // In the test_smtp_callback method, add this before $mail->smtpConnect():
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