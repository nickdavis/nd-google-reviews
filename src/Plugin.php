<?php declare( strict_types=1 );

/**
 * ND Google Reviews.
 *
 * @package   NickDavis\GoogleReviews
 * @author    Nick Davis
 * @license   MIT
 * @link      https://github.com/nickdavis/nd-google-reviews/
 * @copyright 2023 Nick Davis
 */

namespace NickDavis\GoogleReviews;

final class Plugin {
	public function run(): void {
		foreach ( $this->services as $class ) {
			/** @var Registerable $service */
			$service = new $class;
			$service->register();
		}
	}

	protected array $services = [
		Fields::class,
		Importer::class,
		Settings::class,
		Schema::class,
		View::class,
	];
}
