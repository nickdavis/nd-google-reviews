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

final class Schema implements Registerable {
	private ?ReviewsPost $reviews_post = null;

	public function register(): void {
		add_filter( 'wpseo_schema_graph_pieces', [ $this, 'add_review_schema' ], 10, 2 );
	}

	public function add_review_schema( $pieces, $context ) {
		$reviews_post = $this->get_reviews_post();

		if ( null === $reviews_post ) {
			return $pieces;
		}

		$reviews = $reviews_post->get_reviews();

		if ( empty( $reviews ) ) {
			return $pieces;
		}

		foreach ( $reviews as $review ) {
			$pieces[] = new ReviewSchemaPiece( $review, $context );
		}

		return $pieces;
	}

	// TODO: Reduce duplication with View class.
	private function get_reviews_post(): ?ReviewsPost {
		if ( $this->reviews_post !== null ) {
			return $this->reviews_post;
		}

		$post = get_post();

		if ( null === $post ) {
			return null;
		}

		$this->reviews_post = new ReviewsPost( $post );

		return $this->reviews_post;
	}
}
