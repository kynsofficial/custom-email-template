<?php
/**
 * Custom Email Template
 *
 * @package           CustomEmailTemplate
 * @author            Ssu-Technology Limited
 * @copyright         2025 Ssu-Technology Limited
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Custom Email Template
 * Plugin URI:        https://swiftspeed.org/
 * Description:       Customize how all emails from your WordPress site look and are delivered.
 * Version:           1.0.0
 * Author:            Ssu-Technology Limited
 * Author URI:        https://swiftspeed.org
 * Text Domain:       custom-email-template
 * Domain Path:       /languages
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires PHP:      7.4
 * Requires at least: 5.6
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Define plugin constants
 */
define('CUSTOM_EMAIL_TEMPLATE_VERSION', '1.0.0');
define('CUSTOM_EMAIL_TEMPLATE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CUSTOM_EMAIL_TEMPLATE_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_custom_email_template() {
    require_once CUSTOM_EMAIL_TEMPLATE_PLUGIN_DIR . 'includes/class-custom-email-template-activator.php';
    Custom_Email_Template_Activator::activate();
}
register_activation_hook(__FILE__, 'activate_custom_email_template');

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_custom_email_template() {
    require_once CUSTOM_EMAIL_TEMPLATE_PLUGIN_DIR . 'includes/class-custom-email-template-deactivator.php';
    Custom_Email_Template_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_custom_email_template');

/**
 * The core plugin class
 */
require_once CUSTOM_EMAIL_TEMPLATE_PLUGIN_DIR . 'includes/class-custom-email-template.php';


/**
 * Begins execution of the plugin.
 */
function run_custom_email_template() {
    $plugin = new Custom_Email_Template();
    $plugin->run();
}
run_custom_email_template();


