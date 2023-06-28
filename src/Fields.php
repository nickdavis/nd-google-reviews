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

final class Fields implements Registerable {

	private const GOOGLE_PLACE_ID = 'nd_google_place_id';

	public const KEY = 'nd_google_reviews';
	public const RATING = self::KEY . '_rating';
	public const REVIEWS = self::KEY . '_reviews';
	public const REVIEWS_TOTAL = self::KEY . '_user_ratings_total';

	public function register(): void {
		add_action( 'acf/init', [ $this, 'register_google_place_id_field' ] );
		add_action( 'acf/init', [ $this, 'register_google_reviews_fields' ] );
	}

	private function get_field_location(): array {
		$location   = [];
		$post_types = Settings::get_post_types();

		foreach ( $post_types as $post_type ) {
			$location[] =
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => $post_type,
					],
				];
		}

		return $location;
	}

	public static function get_google_place_id( int $post_id = 0 ): string {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! $post_id ) {
			return '';
		}

		return (string) get_post_meta( $post_id, self::get_google_place_id_key(), true );
	}

	public static function get_google_place_id_key(): string {
		$key = self::GOOGLE_PLACE_ID;

		// Add filter to override Google Place ID key & disable field.
		$key = apply_filters( 'nd_google_place_id_key', $key );

		return (string) $key;
	}

	public function register_google_place_id_field(): void {
		if ( self::GOOGLE_PLACE_ID !== self::get_google_place_id_key() ) {
			return;
		}

		acf_add_local_field_group( array(
			'key'                   => 'group_60993fb3dde97',
			'title'                 => 'Google Place ID',
			'fields'                => array(
				array(
					'key'          => 'field_' . self::GOOGLE_PLACE_ID,
					'label'        => 'Google Place ID',
					'name'         => self::GOOGLE_PLACE_ID,
					'type'         => 'text',
					'instructions' => 'Enter the Google Place ID.',
					'required'     => 0,
					'wrapper'      => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
				),
			),
			'location'              => $this->get_field_location(),
			'menu_order'            => 0,
			'position'              => 'side',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		) );
	}

	public function register_google_reviews_fields(): void {
		acf_add_local_field_group( array(
			'key'                   => 'group_nd_google_reviews',
			'title'                 => 'Google Reviews',
			'fields'                => array(
				array(
					'key'      => 'field_' . self::RATING,
					'label'    => 'Rating',
					'name'     => self::RATING,
					'type'     => 'text',
					'readonly' => 1,
				),
				array(
					'key'      => 'field_' . self::REVIEWS_TOTAL,
					'label'    => 'Reviews (Total)',
					'name'     => self::REVIEWS_TOTAL,
					'type'     => 'text',
					'readonly' => 1,
				),
				array(
					'key'               => 'field_' . self::REVIEWS,
					'label'             => 'Reviews',
					'name'              => self::REVIEWS,
					'type'              => 'repeater',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'collapsed'         => '',
					'min'               => 0,
					'max'               => 0,
					'layout'            => 'block',
					'button_label'      => 'Add Review',
					'sub_fields'        => array(
						array(
							'key'               => 'field_62856a917de6c',
							'label'             => 'Author Name',
							'name'              => 'author_name',
							'type'              => 'text',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => 25,
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => '',
							'readonly'          => 1,
						),
						array(
							'key'               => 'field_62856b687de6e',
							'label'             => 'Rating',
							'name'              => 'rating',
							'type'              => 'text',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => 25,
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'readonly'          => 1,
						),
						array(
							'key'               => 'field_relative_time_description',
							'label'             => 'Relative Time',
							'name'              => 'relative_time_description',
							'type'              => 'text',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => 25,
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => '',
							'readonly'          => 1,
						),
						array(
							'key'               => 'field_62856b7d7de70',
							'label'             => 'Time',
							'name'              => 'time',
							'type'              => 'text',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => 25,
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => '',
							'readonly'          => 1,
						),
						array(
							'key'               => 'field_62856b777de6f',
							'label'             => 'Text',
							'name'              => 'text',
							'type'              => 'textarea',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => '',
							'rows'              => 4,
							'readonly'          => 1,
						),
						array(
							'key'               => 'field_62856ae67de6d',
							'label'             => 'Author URL',
							'name'              => 'author_url',
							'type'              => 'url',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
							'readonly'          => 1,
						),
					),
				),
			),
			'location'              => $this->get_field_location(),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		) );
	}

}
