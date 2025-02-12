<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/swaptify
 * @since      1.0.0
 *
 * @package    Swaptify
 * @subpackage Swaptify/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Swaptify
 * @subpackage Swaptify/includes
 * @author     Swaptify <support@swaptify.com>
 */
class Swaptify_Activator 
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        static::makeSegmentSwapTable();
        static::makeSwapEventTable();
        static::makeSwapVisitorTypeTable();
        static::makeDefaultContentTable();
        static::makeCookiesTable();
    }
    
    /**
     * contains the updates for each version 
     *
     * @return void
     */
    public static function migrate() 
    {        
        // just put all the migrations in here. using the dbDelta method should make it so the db is checked correctly
        
        /**
         * set the version to the current version
         */
        update_option('swaptify_version', swaptify_version);
    }

    /**
     * add the segment table for pages
     * 
     * @since 1.0.0
     * @return void
     */
    public static function makeSegmentSwapTable()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "post_swap_segments"; 
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            wp_post_id BIGINT(20) UNSIGNED NOT NULL,
            swap_segment_key VARCHAR(64), 
            active TINYINT(1) DEFAULT 1, 
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * add the segment table for pages
     * 
     * @since 1.0.0
     * @return void
     */
    public static function makeSwapEventTable()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "post_swap_events"; 
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            wp_post_id BIGINT(20) UNSIGNED NOT NULL,
            swaptify_event_key VARCHAR(64), 
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * add the segment table for pages
     * 
     * @since 1.0.0
     * @return void
     */
    public static function makeSwapVisitorTypeTable()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "post_swap_visitor_types"; 
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            wp_post_id BIGINT(20) UNSIGNED NOT NULL,
            swaptify_visitor_type_key VARCHAR(64), 
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * add default content table
     * 
     * @since 1.0.0
     * @return void
     */
    public static function makeDefaultContentTable()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "swap_default_contents"; 
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            swap_segment_key VARCHAR(64), 
            swap_key VARCHAR(64),
            name VARCHAR(255), 
            swap_name VARCHAR(255),
            type VARCHAR(20), 
            content MEDIUMTEXT,
            sub_content MEDIUMTEXT,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * add cookies table table
     * 
     * @since 1.0.0
     * @return void
     */
    public static function makeCookiesTable()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "swap_cookies"; 
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255), 
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
