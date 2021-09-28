<?php
/**
 * Glossary
 *
 * @package   Glossary
 * @author    Codeat <support@codeat.co>
 * @copyright 2020
 * @license   GPL 2.0+
 * @link      https://codeat.co
 */

namespace Glossary\Rest;

use Glossary\Engine;

/**
 * Example class for REST
 */
class Support extends Engine\Base {

	/**
	 * Initialize the class.
	 *
	 * @return bool
	 */
	public function initialize() {
		parent::initialize();

		\add_action( 'init', array( $this, 'enable_post_type_support' ), 25 );
		\add_action( 'rest_api_init', array( $this, 'add_fields_to_request' ) );

		return true;
	}

	/**
	 * Add rest support to post type
	 *
	 * @global array $wp_post_types
	 * @global array $wp_taxonomies
	 * @return void
	 */
	public function enable_post_type_support() {
		global $wp_post_types, $wp_taxonomies;
		// Add rest support to post type
		$post_type_name = 'glossary';

		if ( isset( $wp_post_types[ $post_type_name ] ) ) {
			$wp_post_types[ $post_type_name ]->show_in_rest          = true;
			$wp_post_types[ $post_type_name ]->rest_base             = $post_type_name;
			$wp_post_types[ $post_type_name ]->rest_controller_class = 'WP_REST_Posts_Controller';
		}

		$taxonomy_name = 'glossary-cat';

		// Add rest support to taxonomy
		if ( !isset( $wp_taxonomies[ $taxonomy_name ] ) ) {
			return;
		}

		$wp_taxonomies[ $taxonomy_name ]->show_in_rest          = true;
		$wp_taxonomies[ $taxonomy_name ]->rest_base             = $taxonomy_name;
		$wp_taxonomies[ $taxonomy_name ]->rest_controller_class = 'WP_REST_Terms_Controller';
	}

	/**
	 * Add fields
	 *
	 * @return void
	 */
	public function add_fields_to_request() {
		if ( !\function_exists( 'register_rest_field' ) ) {
			return;
		}

		\register_rest_field(
			'glossary',
			'related_terms',
			array(
				'get_callback' => array( $this, 'get_field' ),
				'schema'       => array(
					'description' => 'Related terms',
					'type'        => 'string',
					'context'     => array( 'view' ),
				),
			)
		);
		\register_rest_field(
			'glossary',
			'external_url',
			array(
				'get_callback' => array( $this, 'get_field' ),
				'schema'       => array(
					'description' => 'External URL',
					'type'        => 'string',
					'context'     => array( 'view' ),
				),
			)
		);
		\register_rest_field(
			'glossary',
			'internal_reference_id',
			array(
				'get_callback' => array( $this, 'get_field' ),
				'schema'       => array(
					'description' => 'Internal Post Type ID',
					'type'        => 'string',
					'context'     => array( 'view' ),
				),
			)
		);
		$custom_fields = \gl_get_settings();

		if ( !isset( $custom_fields[ 'custom_fields' ] ) ) {
			return;
		}

		$custom_fields = $custom_fields[ 'custom_fields' ];

		if ( !\is_array( $custom_fields ) ) {
			return;
		}

		foreach ( $custom_fields as $field_name ) {
			$field_name = \trim( $field_name );
			$field_id   = \str_replace( ' ', '', \strtolower( $field_name ) );
			\register_rest_field(
				'glossary',
				$field_id,
				array(
					'get_callback' => array( $this, 'get_custom_field' ),
					'schema'       => array(
						'description' => $field_name,
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
				)
			);
		}
	}

	/**
	 * Return the field
	 *
	 * @param object $post     Post object.
	 * @param string $field_id ID of the field.
	 * @param object $request  The request.
	 * @return string
	 */
	public function get_field( $post, string $field_id, $request ) { //phpcs:ignore
		if ( 'related_terms' === $field_id ) {
			$field_id = 'tag';
		} elseif ( 'external_url' === $field_id ) {
			$field_id = 'url';
		} elseif ( 'internal_reference_id' === $field_id ) {
			$field_id = 'cpt';
		}

		$value = \get_post_meta( $post[ 'id' ], GT_SETTINGS . '_' . $field_id, true );

		if ( !empty( $value ) ) {
			return $value;
		}

		return '';
	}

	/**
	 * Return the custom field
	 *
	 * @param object $post     Post object.
	 * @param string $field_id ID of the field.
	 * @param object $request  The request.
	 * @return string
	 */
	public function get_custom_field( $post, string $field_id, $request ) { //phpcs:ignore
		$value = \get_post_meta( $post[ 'id' ], GT_SETTINGS . '_' . $field_id, true );

		if ( !empty( $value ) ) {
			return $value;
		}

		return '';
	}

}
