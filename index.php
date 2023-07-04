<?php declare( strict_types=1 );
/**
 * Plugin Name: ND Google Reviews
 * Plugin URI: https://github.com/nickdavis/nd-google-reviews/
 * Description: Add Google Reviews to any WordPress post type.
 * Version: 2.0.0
 * Author: Nick Davis
 * Author URI: https://nickdavis.io
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace NickDavis\GoogleReviews;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Cheatin&#8217; uh?' );
}

/**
 * Sets up the plugin's constants.
 *
 * @since 1.0.0
 *
 * @return void
 */
function constants(): void {
	$plugin_url = plugin_dir_url( __FILE__ );

	if ( is_ssl() ) {
		$plugin_url = str_replace( 'http://', 'https://', $plugin_url );
	}

	define( 'ND_GOOGLE_REVIEWS_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
	define( 'ND_GOOGLE_REVIEWS_URL', $plugin_url );
	define( 'ND_GOOGLE_REVIEWS_FILE', __FILE__ );
}

/**
 * Autoload files.
 *
 * @since 1.0.0
 *
 * @return void
 * @throws \Exception
 */
function autoload(): void {
	$autoloader = ND_GOOGLE_REVIEWS_DIR . 'vendor/autoload.php';

	if ( is_readable( $autoloader ) ) {
		require_once $autoloader;
	} else {
		/**
		 * Composer autoloader apparently was not found, so fall back to our bundled
		 * autoloader.
		 */
		require_once __DIR__ . '/src/Autoloader.php';

		( new Autoloader() )
			->add_namespace( __NAMESPACE__, __DIR__ . '/src' )
			->register();
	}
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\launch' );
/**
 * Launches the plugin.
 *
 * @since 1.0.0
 *
 * @return void
 * @throws \Exception
 */
function launch() {
	constants();
	autoload();
	( new Plugin )->run();
}
