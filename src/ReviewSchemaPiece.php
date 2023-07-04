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

	private Review $review;
	public $context;

	public function __construct( Review $review, $context ) {
		$this->review  = $review;
		$this->context = $context;
	}

	public function is_needed(): bool {
		return true;
	}

	public function generate(): array {
		return [
			'@type'         => 'Review',
			'datePublished' => $this->review->get_date_schema(),
			'reviewRating'  => [
				'@type'       => 'Rating',
				'ratingValue' => $this->review->get_rating(),
			],
			'author'        => $this->review->get_name(),
			'reviewBody'    => $this->review->get_text(),
			'itemReviewed'  => get_the_title(),
		];
	}

}
