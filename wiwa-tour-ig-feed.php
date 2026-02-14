<?php
/**
 * Plugin Name: Wiwa Tour IG Feed
 * Plugin URI:  https://wiwatour.com/
 * Description: Professional Instagram Feed integration with video thumbnail support and performance optimizations.
 * Version:     1.0.0
 * Author:      Wiwa Tour Dev Team
 * Author URI:  https://wiwatour.com/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wiwa-tour-ig-feed
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define Constants
define( 'WIWA_IG_VERSION', '1.0.0' );
define( 'WIWA_IG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WIWA_IG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Autoloader
spl_autoload_register( function ( $class ) {
	$prefix = 'WiwaTour\\IGFeed\\';
	$base_dir = WIWA_IG_PLUGIN_DIR . 'includes/';

	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class, $len );
	$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

	if ( file_exists( $file ) ) {
		require $file;
	}
} );

// Activation/Deactivation Hooks
function activate_wiwa_tour_ig_feed() {
	WiwaTour\IGFeed\Core\Activator::activate();
}

function deactivate_wiwa_tour_ig_feed() {
	WiwaTour\IGFeed\Core\Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wiwa_tour_ig_feed' );
register_deactivation_hook( __FILE__, 'deactivate_wiwa_tour_ig_feed' );

// Initialize the plugin
function run_wiwa_tour_ig_feed() {
	$plugin = new WiwaTour\IGFeed\Core\Plugin();
	$plugin->run();
}
run_wiwa_tour_ig_feed();
