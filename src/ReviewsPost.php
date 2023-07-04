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

use WP_Post;

final class ReviewsPost {

	private WP_Post $post;

	public function __construct( WP_Post $post ) {
		$this->post = $post;
	}

	public function get_post_type(): string {
		return $this->post->post_type;
	}

	public function get_rating(): Rating {
		return new Rating( (float) get_post_meta( $this->post->ID, Fields::RATING, true ) ?: 0.0 );
	}

	public function get_review_count(): int {
		return (int) get_post_meta( $this->post->ID, Fields::REVIEWS_TOTAL, true );
	}

	public function get_reviews(): array {
		$reviews = get_field( Fields::REVIEWS, $this->post->ID ) ?: [];

		if ( ! is_array( $reviews ) || empty( $reviews ) ) {
			return [];
		}

		$reviews = array_map( function ( $review ) {
			return new Review( $review );
		}, $reviews );

		return array_filter( $reviews, function ( Review $review ) {
			// TODO: Remove hard coded minimum rating.
			return $review->get_rating()->is_at_least( 4.0 );
		} );
	}

	public function get_title(): string {
		return $this->post->post_title;
	}

}
