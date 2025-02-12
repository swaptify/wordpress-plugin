<?php
/**
 * Plugin Name: Elementor Swaptify Tab
 * Description: Create Swaps with Elementor to personalize your website's user experience
 * Plugin URI:  https://elementor.com/
 * Version:     1.0.0
 * Author:      Swaptify
 * Author URI:  https://swaptify.com
 * Text Domain: elementor-swaptify-tab
 * 
 * Elementor tested up to: 3.16.0
 * Elementor Pro tested up to: 3.16.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * function to instantiate the Swaptify addon
 * 
 * NOTE: this is from a code template
 *
 * @return void
 */
function elementor_swaptify_tab() {

	// Load plugin file
	require_once( __DIR__ . '/includes/plugin.php' );

	// Run the plugin
	\ElementorSwaptifyTab\Plugin::instance();

}

add_action( 'plugins_loaded', 'elementor_swaptify_tab' );