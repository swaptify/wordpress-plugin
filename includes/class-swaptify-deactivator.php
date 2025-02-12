<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/swaptify
 * @since      1.0.0
 *
 * @package    Swaptify
 * @subpackage Swaptify/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Swaptify
 * @subpackage Swaptify/includes
 * @author     Swaptify <support@swaptify.com>
 */
class Swaptify_Deactivator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // drop swap table, no don't. do it on uninstall
        // perhaps run through all the posts and exchange all content for default
    }

}
