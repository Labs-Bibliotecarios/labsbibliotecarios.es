<?php

/**
 * Glossary
 *
 * @package   Glossary
 * @author  Codeat <support@codeat.co>
 * @copyright 2020
 * @license   GPL 2.0+
 * @link      https://codeat.co
 */

namespace Glossary\Integrations;

use Glossary\Engine;

/**
 * All the CMB related code.
 */
class CMB_Metabox extends Engine\Base {

	/**
	 * CMB metabox
	 *
	 * @var object
	 */
	private $cmb_post;

	/**
	 * Initialize class.
	 *
	 * @since 2.0
	 * @return bool
	 */
	public function initialize() {
		parent::initialize();

		if ( empty( $this->settings[ 'posttypes' ] ) ) {
			$this->settings[ 'posttypes' ] = array( 'post' );
		}

		\add_action( 'cmb2_init', array( $this, 'post_override' ) );
		\add_action( 'cmb2_init', array( $this, 'glossary_post_type' ) );

		if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
			\add_action( 'cmb2_init', array( $this, 'glossary_custom_fields__premium_only' ) );
			\add_action( 'cmb2_init', array( $this, 'post_override__premium_only' ) );

			return false;
		}

		return true;
	}

	/**
	 * Metabox for post types
	 *
	 * @return void
	 */
	public function post_override() {
		$this->cmb_post = \new_cmb2_box(
			array(
				'id'           => 'glossary_post_metabox',
				'title'        => \__( 'Glossary Post Override', 'glossary-by-codeat' ),
				'object_types' => $this->settings[ 'posttypes' ],
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);

		$this->cmb_post->add_field(
			array(
				'name' => \__( 'Disable Glossary for this post', 'glossary-by-codeat' ),
				'id'   => GT_SETTINGS . '_disable',
				'type' => 'checkbox',
			)
		);
	}

	/**
	 * Metabox for post types for Premium
	 *
	 * @return void
	 */
	public function post_override__premium_only() {
		$taxs = \get_terms( array( 'taxonomy' => 'glossary-cat', 'hide_empty' => false, 'orderby' => 'name', 'parent' => 0 ) );

		if ( \is_wp_error( $taxs ) || empty( $taxs ) || !\is_array( $taxs ) ) {
			return;
		}

		$cmb_tax = array();

		foreach ( $taxs as $item ) {
			if ( !\is_object( $item ) ) {
				continue;
			}

			$cmb_tax[ $item->term_id ] = $item->name;
			$terms_of_taxonomy         = \get_terms( array( 'taxonomy' => 'glossary-cat', 'hide_empty' => false, 'orderby' => 'name', 'parent' => $item->term_id ) );

			if ( !\is_iterable( $terms_of_taxonomy ) || empty( $terms_of_taxonomy ) ) { // phpcs:ignore
				continue;
			}

			foreach ( $terms_of_taxonomy as $subitem ) {
				if ( !\is_object( $subitem ) ) {
					continue;
				}

				$cmb_tax[ $subitem->term_id ] = '- ' . $subitem->name;
			}
		}

		$this->cmb_post->add_field(
			array(
				'name'    => \__( 'Select specific glossary categories', 'glossary-by-codeat' ),
				'desc'    => \__(
					'By selecting one or more categories, only terms belonging to these will be linked',
					'glossary-by-codeat'
				),
				'id'      => GT_SETTINGS . '_filter_tax',
				'type'    => 'multicheck',
				'options' => $cmb_tax,
			)
		);
	}

	/**
	 * Metabox for glossary post type
	 *
	 * @return void
	 */
	public function glossary_post_type() {
		$cmb = \new_cmb2_box(
			array(
				'id'           => 'glossary_metabox',
				'title'        => \__( 'Glossary Auto-Link settings', 'glossary-by-codeat' ),
				'object_types' => 'glossary',
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);
		$cmb->add_field(
			array(
				'name' => \__( 'Additional key terms for this definition', 'glossary-by-codeat' ),
				'desc' => \__(
					'Case-insensitive. To add more than one, separate them with commas',
					'glossary-by-codeat'
				),
				'id'   => GT_SETTINGS . '_tag',
				'type' => 'text',
			)
		);
		$cmb->add_field(
			array(
				'name'    => \__( 'What type of link?', 'glossary-by-codeat' ),
				'id'      => GT_SETTINGS . '_link_type',
				'type'    => 'radio',
				'default' => 'external',
				'options' => array(
					'external' => 'External URL',
					'internal' => 'Internal URL',
				),
			)
		);
		$cmb->add_field(
			array(
				'name'      => \__( 'Link to external URL', 'glossary-by-codeat' ),
				'desc'      => \__(
					'If this is left blank, the previous options defaults back and key term is linked to internal definition page',
					'glossary-by-codeat'
				),
				'id'        => GT_SETTINGS . '_url',
				'type'      => 'text_url',
				'protocols' => array( 'http', 'https' ),
			)
		);
		$cmb->add_field(
			array(
				'name'        => \__( 'Internal', 'glossary-by-codeat' ),
				'desc'        => \__( 'Select a post type of your site', 'glossary-by-codeat' ),
				'id'          => GT_SETTINGS . '_cpt',
				'type'        => 'post_search_text',
				'select_type' => 'radio',
				'onlyone'     => true,
			)
		);

		if ( empty( $this->settings[ 'open_new_window' ] ) ) {
			$cmb->add_field(
				array(
					'name' => \__( 'Open external link in a new window', 'glossary-by-codeat' ),
					'id'   => GT_SETTINGS . '_target',
					'type' => 'checkbox',
				)
			);
		}

		$cmb->add_field(
			array(
				'name' => \__( 'Mark this link as "No Follow"', 'glossary-by-codeat' ),
				'desc' => \__(
					'To learn more about No-Follow links, check <a href="https://support.google.com/webmasters/answer/96569?hl=en">this article</a>',
					'glossary-by-codeat'
				),
				'id'   => GT_SETTINGS . '_nofollow',
				'type' => 'checkbox',
			)
		);
	}

	/**
	 * Metabox for custom fields
	 *
	 * @return bool
	 */
	public function glossary_custom_fields__premium_only() {
		$custom_fields = \get_option( GT_SETTINGS . '-extra' );

		if ( !empty( $custom_fields ) && !\is_array( $custom_fields ) || !isset( $custom_fields[ 'custom_fields' ] ) ) {
			return false;
		}

		$custom_fields = $custom_fields[ 'custom_fields' ];
		$cmb_custom    = \new_cmb2_box(
		array(
			'id'           => 'glossary_custom_metabox',
			'title'        => \__( 'Glossary Custom Fields', 'glossary-by-codeat' ),
			'object_types' => array( 'glossary' ),
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
			)
		);

		if ( empty( $custom_fields ) ) {
			return false;
		}

		foreach ( $custom_fields as $field_name ) {
			$field_name = \trim( $field_name );
			$field_id   = \str_replace( ' ', '', \strtolower( $field_name ) );
			$cmb_custom->add_field(
				array(
					'name' => $field_name,
					'id'   => 'glossary-by-codeat_' . $field_id,
					'type' => 'text',
				)
			);
		}

		return true;
	}

}
