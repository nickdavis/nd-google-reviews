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

final class View implements Registerable {

	public function register(): void {
		add_action( 'nd_google_reviews', [ $this, 'render' ] );
		add_shortcode( 'nd_google_reviews', [ $this, 'render_shortcode' ] );
	}

	public function render(): void {
		$args = $this->get_args();

		if ( empty( $args ) ) {
			return;
		}

		$theme_file = get_template_part( 'partials/nd-google-reviews', null, $args );

		// TODO: Put in a default view?
//		if ( false === $theme_file ) {
//			include ND_GOOGLE_REVIEWS_DIR . 'views/nd-google-reviews.php';
//		}
	}

	public function render_shortcode(): string {
		$args = $this->get_args();

		if ( empty( $args ) ) {
			return '';
		}

		ob_start();
		get_template_part( 'partials/nd-google-reviews', null, $args );
		$theme_file_output = ob_get_clean();

		if ( ! empty( $theme_file_output ) ) {
			return $theme_file_output;
		}

		// TODO: Put in a default view?
//		ob_start();
//		include ND_GOOGLE_REVIEWS_DIR . 'views/nd-google-reviews.php';
//
//		return ob_get_clean();
	}

	public function get_args(): array {

		$post = $this->get_reviews_post();

		if ( null === $post ) {
			return [];
		}

		$args['rating'] = $this->get_rating( $post->ID );

		if ( ! $args['rating']->has() ) {
			return [];
		}

		if ( $args['rating']->rounded() < 4 ) {
			return [];
		}

		$args['review_count'] = $this->get_review_count( $post->ID );
		$args['reviews']      = $this->get_reviews( $post->ID );

		return $args;
	}

	private function get_rating( int $post_id ): Rating {
		return new Rating( (float) get_post_meta( $post_id, Fields::RATING, true ) ?: 0.0 );
	}

	private function get_review_count( int $post_id ): int {
		return (int) get_post_meta( $post_id, Fields::REVIEWS_TOTAL, true );
	}

	private function get_reviews( int $post_id ): array {
		$reviews = get_field( Fields::REVIEWS, $post_id ) ?: [];

		if ( empty( $reviews ) ) {
			return [];
		}

		$reviews = array_map( function ( $review ) {
			return new Review( $review );
		}, $reviews );

		return array_filter( $reviews, function ( Review $review ) {
			return $review->get_rating() >= 4;
		} );
	}

	private function get_reviews_post(): ?\WP_Post {
		$post = get_post();

		if ( null === $post ) {
			return null;
		}

		return $post;
	}

}
