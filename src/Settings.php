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

final class Settings implements Registerable {

	const MENU_SLUG = 'acf-options-google-reviews';

	const API_KEY = 'nd_google_api_key';
	const API_KEY_MESSAGE = 'nd_google_api_key_message';
	const HOW_TO = 'nd_google_reviews_message';
	const STATUS = 'nd_google_reviews_last_import_status';

	/**
	 * Register the service.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'acf/init', [ $this, 'register_site_options_page' ], 9 );
		add_action( 'acf/init', [ $this, 'register_options_page' ] );
		add_action( 'acf/init', [ $this, 'register_field' ] );
	}

	public static function get_api_key(): string {
		if ( defined( 'GOOGLE_API_KEY' ) ) {
			return (string) GOOGLE_API_KEY;
		}

		// For backwards compatibility.
		if ( defined( 'GOOGLE_MAPS_API_KEY' ) ) {
			return (string) GOOGLE_MAPS_API_KEY;
		}

		return (string) get_field( self::API_KEY, 'options' );
	}

	public static function get_post_types(): array {
		return (array) get_field( 'nd_google_reviews_post_types', 'options' );
	}

	/**
	 * Registers the Site Options page with Advanced Custom Fields.
	 *
	 * @since 1.0.0
	 */
	function register_site_options_page(): void {
		$options_pages_already_registered = acf_get_options_pages();

		foreach ( $options_pages_already_registered as $options_page ) {
			if ( $options_page['page_title'] === 'Site Options' ) {
				return;
			}
		}

		// Redirect = false if you want to use the parent page as a page + sub pages.
		acf_add_options_page( [ 'page_title' => 'Site Options', 'redirect' => true ] );
	}


	public function register_options_page(): void {
		$args = [
			'page_title'  => __( 'Google Reviews' ),
			'menu_title'  => __( 'Google Reviews' ),
			'parent_slug' => 'acf-options-site-options',
		];

		acf_add_options_sub_page( $args );
	}

	public function register_field(): void {

		acf_add_local_field_group( array(
			'key'                   => 'group_60bdfc65d9f6e',
			'title'                 => 'How To Use',
			'fields'                => array(
				array(
					'key'               => 'field_' . self::HOW_TO,
					'label'             => '',
					'name'              => '',
					'type'              => 'message',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'message'           => '<ol><li>Enter a Google API key with the appropriate permissions.</li><li>Choose at least one post type.</li><li>Add Google Place IDs to each post you want to import Google Reviews for.</li><li>Revisit this page and click Update to run an import.</li><li>The status of the last import will be shown below.</li></ol>',
					'new_lines'         => 'wpautop',
					'esc_html'          => 0,
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => self::MENU_SLUG,
					),
				),
			),
			'menu_order'            => 1,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		) );

		acf_add_local_field_group( array(
			'key'        => 'group_nd_google_reviews_last_import',
			'title'      => 'Last Import',
			'fields'     => array(
				array(
					'key'      => 'key_' . self::STATUS,
					'label'    => 'Status',
					'name'     => self::STATUS,
					'type'     => 'text',
					'readonly' => 1,
				),
			),
			'location'   => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => self::MENU_SLUG,
					),
				),
			),
			'menu_order' => 1
		) );

		if ( ! defined( 'GOOGLE_API_KEY' ) && ! defined( 'GOOGLE_MAPS_API_KEY' ) ) {
			$api_key_fields = array(
				array(
					'key'               => 'field_' . self::API_KEY,
					'label'             => 'Google API Key',
					'name'              => self::API_KEY,
					'type'              => 'text',
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
				)
			);
		} else {
			$api_key_constant = defined( 'GOOGLE_API_KEY' ) ? 'GOOGLE_API_KEY' : 'GOOGLE_MAPS_API_KEY';

			$api_key_fields = array(
				array(
					'key'               => 'field_' . self::API_KEY_MESSAGE,
					'label'             => '',
					'name'              => '',
					'type'              => 'message',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'message'           => 'Google API key has already been set by the ' . $api_key_constant . ' constant.',
					'new_lines'         => 'wpautop',
					'esc_html'          => 0,
				),
			);
		}

		acf_add_local_field_group( array(
			'key'                   => 'group_' . self::API_KEY,
			'title'                 => 'Google API Key',
			'fields'                => $api_key_fields,
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => self::MENU_SLUG,
					),
				),
			),
			'menu_order'            => 2,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		) );

		$post_types         = get_post_types( [ 'public' => true ], 'names', 'and' );
		$post_types_choices = [];

		foreach ( $post_types as $post_type ) {
			if ( $post_type !== 'attachment' ) {
				$post_type_object                 = get_post_type_object( $post_type );
				$post_types_choices[ $post_type ] = $post_type_object->labels->name;
			}
		}

		acf_add_local_field_group( [
			'key'                   => 'group_nd_google_reviews_post_types',
			'title'                 => 'Post Types',
			'fields'                => [
				[
					'key'           => 'field_nd_google_reviews_post_types',
					'label'         => 'Select post types',
					'name'          => 'nd_google_reviews_post_types',
					'type'          => 'select',
					'choices'       => $post_types_choices,
					'multiple'      => true,
					'ui'            => true,
					'return_format' => 'value',
				],
			],
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => self::MENU_SLUG,
					),
				),
			),
			'menu_order'            => 3,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		] );

	}

}
