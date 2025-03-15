<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package CustomEmailTemplate
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Plugin uninstall hook - cleanup options
 */
function custom_email_template_uninstall() {
    // Check if data should be preserved
    $preserve_data = get_option('custom_email_preserve_data', 'no');
    
    // If user wants to preserve data, exit early
    if ($preserve_data === 'yes') {
        return;
    }
    
    // Otherwise, delete all plugin options
    $options = array(
        'custom_email_logo_url',
        'custom_email_logo_alignment',
        'custom_email_footer_text',
        'custom_email_from_email',
        'custom_email_from_name',
        'custom_email_reply_to',
        'custom_email_avatar_url',
        'custom_email_bg_color',
        'custom_email_container_bg_color',
        'custom_email_header_border_color',
        'custom_email_primary_text_color',
        'custom_email_secondary_text_color',
        'custom_email_footer_text_color',
        'custom_email_footer_border_color',
        'custom_email_button_bg_color',
        'custom_email_button_text_color',
        'custom_email_use_smtp',
        'custom_email_smtp_host',
        'custom_email_smtp_auth',
        'custom_email_smtp_port',
        'custom_email_smtp_username',
        'custom_email_smtp_password',
        'custom_email_smtp_encryption',
        'custom_email_smtp_debug',
        'custom_email_exclude_tutor_lms',
        'custom_email_exclude_woocommerce',
        'custom_email_preserve_data', // Also delete the preservation setting itself
    );
    
    foreach ($options as $option) {
        delete_option($option);
    }
}

custom_email_template_uninstall();