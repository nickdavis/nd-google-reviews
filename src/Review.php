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

use DateTime;

final class Review implements ValueObject {

	private array $review;

	public function __construct( array $review ) {
		$this->review = $review;
	}

	/**
	 * 'May 18, 2022'.
	 *
	 * @return string
	 */
	public function get_date_human(): string {
		return ( new DateTime( '@' . $this->review['time'] ) )->format( 'M j, Y' );
	}

	/**
	 * '1 week ago'.
	 *
	 * @return string
	 */
	public function get_date_relative(): string {
		return $this->review['relative_time_description'];
	}

	/**
	 * '2022-05-18'.
	 *
	 * @return string
	 */
	public function get_date_schema(): string {
		return ( new DateTime( '@' . $this->review['time'] ) )->format( 'Y-m-d' );
	}

	public function get_name(): string {
		return $this->review['author_name'];
	}

	public function get_rating(): Rating {
		return new Rating( (float) $this->review['rating'] ?: 0.0 );
	}

	public function get_text(): string {
		return $this->review['text'];
	}

	public function get_time(): string {
		return $this->review['time'];
	}

	public function get_url(): string {
		return $this->review['author_url'];
	}

}
