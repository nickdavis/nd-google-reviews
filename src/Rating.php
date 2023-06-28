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

final class Rating implements ValueObject {

	private float $rating;

	public function __construct( float $rating ) {
		$this->rating = $rating;
	}

	public function decimal(): string {
		return number_format( $this->rating, 1 );
	}

	public function has(): bool {
		return 0.0 !== $this->rating;
	}

	public function rounded(): int {
		return (int) round( $this->rating );
	}

}
