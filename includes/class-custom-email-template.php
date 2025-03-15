<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * email and SMTP related functionality.
 *
 * @package CustomEmailTemplate
 */

/**
 * The core plugin class.
 */
class Custom_Email_Template {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @access   protected
     * @var      Custom_Email_Template_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * Define the core functionality of the plugin.
     */
    public function __construct() {
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_email_hooks();
        $this->define_smtp_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @access   private
     */
    private function load_dependencies() {
        // Admin class
        require_once CUSTOM_EMAIL_TEMPLATE_PLUGIN_DIR . 'includes/class-custom-email-template-admin.php';
        
        // Email class
        require_once CUSTOM_EMAIL_TEMPLATE_PLUGIN_DIR . 'includes/class-custom-email-template-email.php';
        
        // SMTP class
        require_once CUSTOM_EMAIL_TEMPLATE_PLUGIN_DIR . 'includes/class-custom-email-template-smtp.php';
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * @access   private
     */
    private function set_locale() {
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new Custom_Email_Template_Admin();
        
        // Add settings link to plugin page
        add_filter('plugin_action_links_' . plugin_basename(CUSTOM_EMAIL_TEMPLATE_PLUGIN_DIR . 'custom-email-template.php'), 
            array($plugin_admin, 'add_action_links')
        );
        
        // Register admin scripts and styles
        add_action('admin_enqueue_scripts', array($plugin_admin, 'enqueue_scripts'));
        
        // Register settings
        add_action('admin_init', array($plugin_admin, 'register_settings'));
        
        // Register admin page
        add_action('admin_menu', array($plugin_admin, 'register_settings_page'));
        
        // Ajax handlers
        add_action('wp_ajax_custom_email_template_send_test', array($plugin_admin, 'send_test_callback'));
        add_action('wp_ajax_custom_email_template_test_smtp', array($plugin_admin, 'test_smtp_callback'));
        
        // Add redirect filter to preserve tab ID
        add_filter('wp_redirect', array($plugin_admin, 'settings_redirect'));
    }

    /**
     * Register all of the hooks related to the email functionality
     * of the plugin.
     *
     * @access   private
     */
    private function define_email_hooks() {
        $plugin_email = new Custom_Email_Template_Email();
        
        // Set email to HTML
        add_filter('wp_mail_content_type', array($plugin_email, 'set_content_type'));
        
        // Modify the from email and name
        add_filter('wp_mail_from', array($plugin_email, 'from_email'), 20);
        add_filter('wp_mail_from_name', array($plugin_email, 'from_name'), 20);
        
        // Add email headers
        add_filter('wp_mail_headers', array($plugin_email, 'headers'));
        
        // Apply the custom template
        add_filter('wp_mail', array($plugin_email, 'apply_template'));
        
        // Configure PHPMailer
        add_action('phpmailer_init', array($plugin_email, 'phpmailer_init'));
    }

    /**
     * Register all of the hooks related to the SMTP functionality
     * of the plugin.
     *
     * @access   private
     */
    private function define_smtp_hooks() {
        $plugin_smtp = new Custom_Email_Template_SMTP();
        
        // Configure SMTP for PHPMailer
        add_action('phpmailer_init', array($plugin_smtp, 'configure_smtp'));
    }

    /**
     * Load the plugin text domain for translation.
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'custom-email-template',
            false,
            dirname(plugin_basename(CUSTOM_EMAIL_TEMPLATE_PLUGIN_DIR . 'custom-email-template.php')) . '/languages/'
        );
    }

    /**
     * Run the plugin.
     */
    public function run() {
        // All hooks are registered in the constructor
    }
}