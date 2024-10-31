<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ovosolution.com
 * @since             1.0.1
 * @package           Ovoform
 *
 * @wordpress-plugin
 * Plugin Name:       Ovoform
 * Plugin URI:        https://ovosolution.com/plugins/ovoform
 * Description:       Ovoform is a WordPress plugin for creating Contact Form and use as short code to show on pages.
 * Version:           1.0.0
 * Requires at least: 4.7
 * Tested up to:      6.4
 * Author:            ovosolution
 * Author URI:        https://ovosolution.com
 * Text Domain:       ovoform
 * Domain Path:       /languages
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use Ovoform\Hook\Hook;
use Ovoform\Includes\Activator;

require_once __DIR__.'/vendor/autoload.php';

define('OVOFORM_PLUGIN_VERSION', ovoform_system_details()['version']);
define('OVOFORM_ROOT', plugin_dir_path(__FILE__));

include_once(ABSPATH . 'wp-includes/pluggable.php');


$activator = new Activator();
register_activation_hook( __FILE__, [$activator, 'activate']);
register_deactivation_hook( __FILE__, [$activator, 'deactivate']);

$system = ovoform_system_instance();
$system->bootMiddleware();
$system->handleRequestThroughRouter();



$hook = new Hook;
$hook->init();