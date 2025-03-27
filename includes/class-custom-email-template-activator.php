<?php
/**
 * Fired during plugin activation.
 *
 * @package CustomEmailTemplate
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 */
class Custom_Email_Template_Activator {

    /**
     * Create default options and directories during plugin activation.
     */
    public static function activate() {
        // Create default options if they don't exist
        $options = array(
            'custom_email_logo_url' => plugins_url('assets/images/default-logo.png', dirname(__FILE__)),
            'custom_email_logo_alignment' => 'center',
            'custom_email_footer_text' => 'This is an automatically generated email. Please do not reply.',
            'custom_email_bg_color' => '#f7f7f7',
            'custom_email_container_bg_color' => '#ffffff',
            'custom_email_header_border_color' => '#e0e2ea',
            'custom_email_primary_text_color' => '#212327',
            'custom_email_secondary_text_color' => '#5b616f',
            'custom_email_footer_text_color' => '#9da3af',
            'custom_email_footer_border_color' => '#e0e2ea',
            'custom_email_button_bg_color' => '#696cff',
            'custom_email_button_text_color' => '#ffffff',
            'custom_email_use_smtp' => 'no',
            'custom_email_smtp_port' => '587',
            'custom_email_smtp_encryption' => 'tls',
            'custom_email_smtp_auth' => 'yes',
        );
        
        foreach ($options as $option => $value) {
            if (get_option($option) === false) {
                add_option($option, $value);
            }
        }
        
        // Create plugin asset directories and ensure files exist
        $css_dir = CUSTOM_EMAIL_TEMPLATE_PLUGIN_DIR . 'assets/css';
        $js_dir = CUSTOM_EMAIL_TEMPLATE_PLUGIN_DIR . 'assets/js';
        
        if (!file_exists($css_dir)) {
            wp_mkdir_p($css_dir);
        }
        
        if (!file_exists($js_dir)) {
            wp_mkdir_p($js_dir);
        }
    }
}