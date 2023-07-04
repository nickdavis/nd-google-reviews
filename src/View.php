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

		return '';

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

		$args['rating'] = $post->get_rating();

		if ( ! $args['rating']->has() ) {
			return [];
		}

		if ( $args['rating']->rounded() < 4 ) {
			return [];
		}

		$args['review_count'] = $post->get_review_count();
		$args['reviews']      = $post->get_reviews();

		return $args;
	}

	private function get_reviews_post(): ?ReviewsPost {
		$post = get_post();

		if ( null === $post ) {
			return null;
		}

		return new ReviewsPost( $post );
	}

}
