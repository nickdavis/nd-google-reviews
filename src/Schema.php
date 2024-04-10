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
		add_filter( 'wpseo_schema_graph', [ $this, 'add_review_schema' ], 100, 2 );
	}

	public function add_review_schema( $data, $context ) {
		$disable_schema = apply_filters( 'nd_google_reviews_disable_schema', false );

		if ( $disable_schema ) {
			return $data;
		}

		$main_entity_key = null;

		// Find the main entity key.
		foreach ( $data as $key => $data_item ) {
			if ( ! isset( $data_item['mainEntityOfPage'] ) ) {
				continue;
			}

			$main_entity_key = $key;
		}

		if ( null === $main_entity_key ) {
			return $data;
		}

		$this->get_reviews_post();

		if ( ! in_array( $this->reviews_post->get_post_type(), Settings::get_post_types() ) ) {
			return $data;
		}

		if ( null === $this->reviews_post ) {
			return $data;
		}

		if ( ! $this->reviews_post->get_rating()->has() ) {
			return $data;
		}

		// TODO: Remove hard coded minimum rating.
		if ( ! $this->reviews_post->get_rating()->is_at_least( 4.0 ) ) {
			return $data;
		}

		// RATING!

		$data[ $main_entity_key ]['aggregateRating'] = [
			'@type'       => 'AggregateRating',
			'ratingValue' => $this->reviews_post->get_rating()->decimal(),
			'reviewCount' => $this->reviews_post->get_review_count(),
		];

		// REVIEWS!

		$disable_reviews_schema = apply_filters( 'nd_google_reviews_disable_reviews_schema', false );

		if ( $disable_reviews_schema ) {
			return $data;
		}

		if ( empty( $this->reviews_post->get_reviews() ) ) {
			return $data;
		}

		$review_schema_pieces = [];

		foreach ( $this->reviews_post->get_reviews() as $review ) {
			$review_schema_pieces[] = [
				'@type'         => 'Review',
				'datePublished' => $review->get_date_schema(),
				'reviewRating'  => [
					'@type'       => 'Rating',
					'ratingValue' => $review->get_rating()->rounded(),
				],
				'author'        => [
					'@type' => 'Person',
					'name'  => $review->get_name(),
				],
				'reviewBody'    => $review->get_text(),
				'itemReviewed'  => $this->reviews_post->get_title()
			];
		}

		$data[ $main_entity_key ]['review'] = $review_schema_pieces;

		return $data;
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
