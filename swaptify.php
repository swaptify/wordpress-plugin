<?php

/**
 * The plugin bootstrap file
 * 
 * Initial file structure from Wordpress Plugin Boilerplate
 * Generated via https://wppb.me/
 * Source located at https://github.com/devinvinson/WordPress-Plugin-Boilerplate/
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/swaptify
 * @since             1.0.0
 * @package           Swaptify
 *
 * @wordpress-plugin
 * Plugin Name:       Swaptify
 * Plugin URI:        https://swaptify.com
 * Description:       Swaptify personalizes content for each visitor with the aim of increasing conversions
 * Version:           1.0.0
 * Author:            Swaptify
 * Author URI:        https://github.com/swaptify
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       swaptify
 * Domain Path:       /languages
 */


if (!defined('WPINC')) 
{
    die;
}

/**
 * Currently plugin version number
 * use SemVer - https://semver.org for naming
 */
define('swaptify_version', '1.0.0');

/**
 * Load and run the functions via the activator
 * This will add the necessary tables to the database
 * and run migrations for inserting any data to those tables as well as update wp options
 * 
 * @since 1.0.0
 * 
 * @return void
 */
function activate_swaptify() 
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-swaptify-activator.php';
    Swaptify_Activator::activate();
    Swaptify_Activator::migrate();
}

/**
 * Load and run the functions via the deactivator
 * This will remove swaptify tables as well as purge associated wp options
 * 
 * @since 1.0.0
 * 
 * @return void
 */
function deactivate_swaptify() 
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-swaptify-deactivator.php';
    Swaptify_Deactivator::deactivate();
}

/**
 * register hooks for activation and deactivation
 */
register_activation_hook(__FILE__, 'activate_swaptify');
register_deactivation_hook(__FILE__, 'deactivate_swaptify');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-swaptify.php';

/**
 * Begins execution of Swaptify plugin
 *
 * Creates a new Swaptify object and executes the run() command to execute all plugin hooks
 *
 * @since   1.0.0
 * 
 * @return void
 */
function run_swaptify() 
{
    $plugin = new Swaptify();
    $plugin->run();
}

/**
 * Start the Swaptify plugin
 */
run_swaptify();