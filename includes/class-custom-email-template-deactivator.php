<?php
/**
 * Fired during plugin deactivation.
 *
 * @package CustomEmailTemplate
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 */
class Custom_Email_Template_Deactivator {

    /**
     * Plugin deactivation tasks.
     */
    public static function deactivate() {
        // Nothing to do here for now
        // We don't want to remove options on deactivation, only on uninstall
    }
}