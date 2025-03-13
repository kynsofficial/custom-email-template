<?php
/**
 * The SMTP functionality of the plugin.
 *
 * @package CustomEmailTemplate
 */

/**
 * The SMTP functionality of the plugin.
 */
class Custom_Email_Template_SMTP {

    /**
     * Configure SMTP if enabled.
     *
     * @param PHPMailer $phpmailer The PHPMailer instance.
     */
    /**
 * Configure SMTP if enabled.
 *
 * @param PHPMailer $phpmailer The PHPMailer instance.
 */
public function configure_smtp($phpmailer) {
    // Check if SMTP is enabled
    if (get_option('custom_email_use_smtp', 'no') === 'yes') {
        // Configure SMTP
        $phpmailer->isSMTP();
        $phpmailer->Host = get_option('custom_email_smtp_host', '');
        $phpmailer->SMTPAuth = (get_option('custom_email_smtp_auth', 'yes') === 'yes');
        $phpmailer->Port = get_option('custom_email_smtp_port', 587);
        $phpmailer->Username = get_option('custom_email_smtp_username', '');
        $phpmailer->Password = get_option('custom_email_smtp_password', '');
        
        // Set encryption type
        $encryption = get_option('custom_email_smtp_encryption', 'tls');
        if ($encryption === 'none') {
            $phpmailer->SMTPSecure = '';
            $phpmailer->SMTPAutoTLS = false;
        } else {
            $phpmailer->SMTPSecure = $encryption;
        }
        
        // Add important setting for Gmail and other strict servers
        $phpmailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Debug mode (1 = errors and messages, 2 = messages only)
        if (get_option('custom_email_smtp_debug', 'no') === 'yes') {
            $phpmailer->SMTPDebug = 1;
            $phpmailer->Debugoutput = 'error_log';
        }
    }
 }

}