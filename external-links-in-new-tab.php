<?php
/**
 * The External Links in New Tab bootstrap file.
 *
 * @since       1.0.0
 * @version     1.0.0
 * @author      WordPress.com Special Projects
 * @license     GPL-3.0-or-later
 *
 * @noinspection    ALL
 *
 * @wordpress-plugin
 * Plugin Name:             External Links in New Tab
 * Plugin URI:              https://wpspecialprojects.wordpress.com
 * Description:             Adds a link parser to make all external links open in a new window
 * Version:                 1.0.0
 * Requires at least:       6.2
 * Tested up to:            6.2
 * Requires PHP:            8.0
 * Author:                  WordPress.com Special Projects
 * Author URI:              https://wpspecialprojects.wordpress.com
 * License:                 GPL v3 or later
 * License URI:             https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:             wpcomsp-external-links-in-new-tab
 * Domain Path:             /languages
 * WC requires at least:    7.4
 * WC tested up to:         7.4
 **/

defined( 'ABSPATH' ) || exit;

// Define plugin constants.
function_exists( 'get_plugin_data' ) || require_once ABSPATH . 'wp-admin/includes/plugin.php';
define( 'WPCOMSP_ELINT_METADATA', get_plugin_data( __FILE__, false, false ) );

define( 'WPCOMSP_ELINT_BASENAME', plugin_basename( __FILE__ ) );
define( 'WPCOMSP_ELINT_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPCOMSP_ELINT_URL', plugin_dir_url( __FILE__ ) );

// Load plugin translations so they are available even for the error admin notices.
add_action(
	'init',
	static function() {
		load_plugin_textdomain(
			WPCOMSP_ELINT_METADATA['TextDomain'],
			false,
			dirname( WPCOMSP_ELINT_BASENAME ) . WPCOMSP_ELINT_METADATA['DomainPath']
		);
	}
);

// Load the autoloader.
if ( ! is_file( WPCOMSP_ELINT_PATH . '/vendor/autoload.php' ) ) {
	add_action(
		'admin_notices',
		static function() {
			$message      = __( 'It seems like <strong>External Links in New Tab</strong> is corrupted. Please reinstall!', 'wpcomsp-external-links-in-new-tab' );
			$html_message = wp_sprintf( '<div class="error notice wpcomsp-external-links-in-new-tab-error">%s</div>', wpautop( $message ) );
			echo wp_kses_post( $html_message );
		}
	);
	return;
}
require_once WPCOMSP_ELINT_PATH . '/vendor/autoload.php';

// Initialize the plugin if system requirements check out.
$wpcomsp_elint_requirements = validate_plugin_requirements( WPCOMSP_ELINT_BASENAME );
define( 'WPCOMSP_ELINT_REQUIREMENTS', $wpcomsp_elint_requirements );

if ( $wpcomsp_elint_requirements instanceof WP_Error ) {
	add_action(
		'admin_notices',
		static function() use ( $wpcomsp_elint_requirements ) {
			$html_message = wp_sprintf( '<div class="error notice wpcomsp-external-links-in-new-tab-error">%s</div>', $wpcomsp_elint_requirements->get_error_message() );
			echo wp_kses_post( $html_message );
		}
	);
} else {
	require_once WPCOMSP_ELINT_PATH . 'functions.php';
	add_action( 'plugins_loaded', array( wpcomsp_elint_get_plugin_instance(), 'initialize' ) );
}
