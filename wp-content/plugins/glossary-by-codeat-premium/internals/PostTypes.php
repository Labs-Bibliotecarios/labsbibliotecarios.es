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

namespace Glossary\Internals;

use Glossary\Engine;

/**
 * Post Types and Taxonomies
 */
class PostTypes extends Engine\Base {

	/**
	 * Tax and Post Types labels.
	 *
	 * @var array
	 */
	private $labels = array();

	/**
	 * Initialize the class.
	 *
	 * @return bool
	 */
	public function initialize() {
		parent::initialize();

		$this->generate_labels();
		\add_action( 'init', array( $this, 'load_cpts' ) );
		\add_action( 'init', array( $this, 'load_taxs' ) );

		\add_filter( 'pre_get_posts', array( $this, 'filter_search' ) );
		\add_filter( 'posts_orderby', array( $this, 'orderby_whitespace' ), 9999, 2 );

		return true;
	}

	/**
	 * Change the orderby for the glossary auto link system to add priority based on number of the spaces
	 *
	 * @param string $orderby How to oder the query.
	 * @param object $object  The object.
	 * @global object $wpdb
	 * @return string
	 */
	public function orderby_whitespace( string $orderby, $object ) {
		if ( isset( $object->query[ 'glossary_auto_link' ] ) ) {
			global $wpdb;
			$orderby = '(LENGTH(' . $wpdb->prefix . 'posts.post_title) - LENGTH(REPLACE(' . $wpdb->prefix . "posts.post_title, ' ', ''))+1) DESC";
		}

		return $orderby;
	}

	/**
	 * Add support for custom CPT on the search box
	 *
	 * @param object $query Wp_Query.
	 * @return object
	 */
	public function filter_search( $query ) {
		if ( $query->is_search && !\is_admin() ) {
			$post_types = $query->get( 'post_type' );

			if ( 'post' === $post_types ) {
				$post_types = array( $post_types );
				$query->set( 'post_type', \array_push( $post_types, array( 'glossary' ) ) );
			}
		}

		return $query;
	}

	public function generate_labels() {
		$single = \__( 'Glossary Term', 'glossary-by-codeat' );
		$multi  = \__( 'Glossary', 'glossary-by-codeat' );

		if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
			if ( isset( $this->settings[ 'label_single' ] ) ) {
				$single = $this->settings[ 'label_single' ];
			}

			if ( isset( $this->settings[ 'label_multi' ] ) ) {
				$multi = $this->settings[ 'label_multi' ];
			}
		}

		$this->labels = array(
			'singular' => $single,
			'plural'   => $multi,
		);
	}

	/**
	 * Load CPT on WordPress
	 */
	public function load_cpts() {
		$glossary_cpt = array(
			'slug'               => 'glossary',
			'show_in_rest'       => true,
			'menu_icon'          => 'dashicons-book-alt',
			'dashboard_activity' => true,
			'capability_type'    => array( 'glossary', 'glossaries' ),
			'supports'           => array( 'thumbnail', 'author', 'editor', 'title', 'genesis-seo', 'genesis-layouts', 'genesis-cpt-archive-settings', 'revisions' ),
			'admin_cols'         => array(
				'title',
				'glossary-cat' => array(
					'taxonomy' => 'glossary-cat',
				),
				'date'         => array(
					'title'   => \__( 'Date', 'glossary-by-codeat' ),
					'default' => 'ASC',
				),
			),
			'admin_filters'      => array(
				'glossary-cat' => array(
					'taxonomy' => 'glossary-cat',
				),
			),
		);

		$glossary_cpt_atts = $this->labels;

		if ( !empty( $this->settings[ 'slug' ] ) ) {
			$glossary_cpt_atts[ 'slug' ] = $this->settings[ 'slug' ];
		}

		if ( isset( $this->settings[ 'archive' ] ) ) {
			$glossary_cpt[ 'has_archive' ] = false;
		}

		if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
			$glossary_cpt[ 'supports' ][] = 'excerpt';
		}

		$posttype = \register_extended_post_type( 'glossary', $glossary_cpt, $glossary_cpt_atts );

		$posttype->add_taxonomy( 'glossary-cat', array('hierarchical' => false, 'show_ui' => false) );
	}

	/**
	 * Load Taxonomies on WordPress
	 */
	public function load_taxs() {
		$glossary_tax = $this->labels;

		if ( !empty( $this->settings[ 'slug_cat' ] ) ) {
			$glossary_tax[ 'slug' ] = $this->settings[ 'slug_cat' ];
		}

		\register_extended_taxonomy(
			'glossary-cat',
			'glossary',
			array(
				'public'           => true,
				'dashboard_glance' => true,
				'slug'             => 'glossary-cat',
				'show_in_rest'     => true,
				'capabilities'     => array(
					'manage_terms' => 'manage_glossaries',
					'edit_terms'   => 'manage_glossaries',
					'delete_terms' => 'manage_glossaries',
					'assign_terms' => 'read_glossary',
				),
			),
			$glossary_tax
		);
	}

}
