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

use Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece;

final class ReviewSchemaPiece extends Abstract_Schema_Piece {

	private ReviewsPost $reviews_post;
	public $context;

	public function __construct( ReviewsPost $reviews_post, $context ) {
		$this->reviews_post = $reviews_post;
		$this->context      = $context;
	}

	public function is_needed(): bool {
		if ( ! in_array( $this->reviews_post->get_post_type(), Settings::get_post_types() ) ) {
			return false;
		}

		if ( ! $this->reviews_post->get_rating()->has() ) {
			return false;
		}

		// TODO: Remove hard coded minimum rating.
		if ( ! $this->reviews_post->get_rating()->is_at_least( 4.0 ) ) {
			return false;
		}

		return true;
	}

	public function generate(): array {
		// TODO: Add a filter here so other post types can be set as LocalBusiness.
		// TODO: Although other defaults than Product or use Yoast context?
		$type = 'wpseo_locations' === $this->reviews_post->get_post_type() ? 'LocalBusiness' : 'Product';

		$data = [
			'@type'           => $type,
			'name'            => $this->reviews_post->get_title(),
			'aggregateRating' => [
				'@type'       => 'AggregateRating',
				'ratingValue' => $this->reviews_post->get_rating()->decimal(),
				'reviewCount' => $this->reviews_post->get_review_count(),
			]
		];

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

		$data['review'] = $review_schema_pieces;

		return $data;
	}

}
