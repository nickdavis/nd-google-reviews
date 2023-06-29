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

final class Importer implements Registerable {

	/**
	 * Register the service.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'acf/save_post', [ $this, 'update_from_admin_trigger' ] );
		add_action( 'nd_google_reviews_import', [ $this, 'update_from_cron_trigger' ] );
	}

	public function update_from_admin_trigger(): void {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$screen = get_current_screen();

		/**
		 * Only run import on correct settings page.
		 */
		if ( ! str_contains( $screen->id, Settings::MENU_SLUG ) ) {
			return;
		}

		$this->trigger_update();
	}

	public function update_from_cron_trigger(): void {
		$this->trigger_update();
	}

	private function trigger_update(): void {
		update_field( Settings::STATUS, 'Import failed for unknown reason', 'options' );

		if ( ! Settings::get_api_key() ) {
			update_field( Settings::STATUS, 'No Google API key set', 'options' );

			return;
		}

		$this->generate_google_ratings_for_all_posts();
	}

	private function generate_google_ratings_for_all_posts(): void {
		$post_types = Settings::get_post_types();

		if ( empty( $post_types ) ) {
			update_field( Settings::STATUS, 'No post types set', 'options' );

			return;
		}

		$update_type = 'new';

		// First get all posts that have never been updated.
		$post_ids = new \WP_Query( [
			'post_type'              => $post_types,
			'posts_per_page'         => 250,
			'meta_query'             => [
				'relation' => 'AND',
				[
					'key'     => Fields::get_google_place_id_key(),
					'compare' => 'EXISTS',
				],
				[
					'relation' => 'OR',
					[
						'key'     => 'nd_google_reviews_last_updated',
						'compare' => 'NOT EXISTS', // Check for not set
					],
					[
						'key'     => 'nd_google_reviews_last_updated',
						'value'   => '',
						'compare' => '=', // Check for empty
					],
				],
			],
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_term_cache' => false
		] );

		if ( empty( $post_ids->posts ) ) {
			$update_type = 'existing';

			/**
			 * WP_Query of all post types (IDs only) with non-empty Google Place ID field.
			 * Gets the less recently updated posts first.
			 */
			$post_ids = new \WP_Query( [
				'post_type'              => $post_types,
				'posts_per_page'         => 250,
				'meta_key'               => 'nd_google_reviews_last_updated',
				'orderby'                => 'meta_value_num',
				'meta_query'             => [
					[
						'key'     => Fields::get_google_place_id_key(),
						'compare' => 'EXISTS',
					],
				],
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_term_cache' => false
			] );
		}

		if ( empty( $post_ids->posts ) ) {
			update_field( Settings::STATUS, 'No posts with Google Place IDs found', 'options' );

			return;
		}

		update_field( Settings::STATUS, 'Import of reviews for ' . $post_ids->post_count . ' ' . $update_type . ' posts incomplete. Likely because of server timeout, due to too many posts', 'options' );

		foreach ( $post_ids->posts as $post_id ) {
			$error_message = $this->update_google_rating_field( $post_id );

			if ( ! empty( $error_message ) ) {
				update_field( Settings::STATUS, $error_message, 'options' );

				return;
			}
		}

		$existing_posts_message = 'new' === $update_type ? ' Run again to update existing posts.' : '';

		update_field( Settings::STATUS, 'Import of reviews for ' . $post_ids->post_count . ' ' . $update_type . ' posts complete. Last updated post ID was ' . $post_id . '.' . $existing_posts_message, 'options' );
	}

	private function update_google_rating_field( int $post_id ): string {
		$error_message   = '';
		$response_fields = $this->get_response_from_google( Fields::get_google_place_id( $post_id ) );

		if ( isset( $response_fields['error_message'] ) ) {
			return $response_fields['error_message'];
		}

		update_post_meta( $post_id, Fields::LAST_UPDATED, time() );

		if ( empty( $response_fields ) ) {
			return $error_message;
		}

		foreach ( $response_fields as $key => $value ) {
			if ( 'reviews' === $key ) {
				$this->update_reviews_fields( $value, $post_id );
				continue;
			}

			update_post_meta( $post_id, Fields::KEY . '_' . $key, $value );
		}

		return $error_message;
	}

	private function update_reviews_fields( array $reviews, int $post_id ): void {
		if ( empty( $reviews ) ) {
			return;
		}

		$updated_values = [];

		foreach ( $reviews as $i => $review ) {
			$updated_values[ $i ] = array(
				"author_name"               => $review['author_name'],
				"author_url"                => $review['author_url'],
				"rating"                    => $review['rating'],
				"relative_time_description" => $review['relative_time_description'],
				"text"                      => $review['text'],
				"time"                      => $review['time'],
			);
		}

		usort( $updated_values, function ( $a, $b ) {
			return $b['time'] <=> $a['time'];
		} );

		update_field( Fields::REVIEWS, $updated_values, $post_id );
	}

	private function get_response_from_google( string $place_id ): array {
		// Google Map geocode api url.
		$url = 'https://maps.googleapis.com/maps/api/place/details/json?place_id=' . $place_id . '&fields=rating%2Creviews%2Cuser_ratings_total&reviews_sort=newest&key=' . Settings::get_api_key();

		// Get the json response.
		$resp_json = file_get_contents( $url );

		// Decode the json.
		$resp = json_decode( $resp_json, true );

		// Response status will be 'OK', if able to geocode given address.
		if ( 'OK' !== $resp['status'] ) {
			// TODO: Save this error message to the post meta.
			if ( str_contains( $resp['error_message' ], 'The provided Place ID is no longer valid' ) ) {
				return [];
			}

			return [ 'error_message' => 'Import failed. Message from Google: ' . $resp['error_message'] ];
		}

		if ( empty( $resp['result'] ) ) {
			return [];
		}

		if ( empty( $resp['result']['rating'] ) ) {
			return [];
		}

		return $resp['result'];
	}

}
