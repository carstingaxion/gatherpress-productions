<?php
/**
 * Plugin Name:       GatherPress Productions
 * Plugin URI:        https://github.com/carstingaxion/gatherpress-productions
 * Description:
 * Version:           0.3.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires plugins:  gatherpress
 * Author:            carstenbach
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gatherpress-productions
 * Domain Path:       /languages
 *
 * @package GatherPress_Productions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit; // @codeCoverageIgnore

// Constants.
define( 'GATHERPRESS_PRODUCTIONS_VERSION', current( get_file_data( __FILE__, array( 'Version' ), 'plugin' ) ) );
define( 'GATHERPRESS_PRODUCTIONS_CORE_PATH', __DIR__ );


/**
 * Adds the GatherPress_Productions namespace to the autoloader.
 *
 * This function hooks into the 'gatherpress_autoloader' filter and adds the
 * GatherPress_Productions namespace to the list of namespaces with its core path.
 *
 * @param array<string, string> $namespaces An associative array of namespaces and their paths.
 * @return array<string, string> Modified array of namespaces and their paths.
 */
function gatherpress_productions_autoloader( array $namespaces ): array {
	$namespaces['GatherPress_Productions'] = GATHERPRESS_PRODUCTIONS_CORE_PATH;

	return $namespaces;
}
add_filter( 'gatherpress_autoloader', 'gatherpress_productions_autoloader' );

/**
 * Initialize the plugin.
 *
 * Bootstrap function that starts the plugin by initializing the main class.
 *
 * This function hooks into the 'plugins_loaded' action to ensure that
 * the instances are created once all plugins are loaded,
 * only if the GatherPress plugin is active.
 *
 * @since 0.1.0
 * @return void
 */
function gatherpress_productions_setup(): void {
	if ( defined( 'GATHERPRESS_VERSION' ) ) {
		\GatherPress_Productions\Setup::get_instance();
		\GatherPress_Productions\Taxonomies::get_instance();
		\GatherPress_Productions\Block::get_instance();
	}
}
add_action( 'plugins_loaded', 'gatherpress_productions_setup' );
