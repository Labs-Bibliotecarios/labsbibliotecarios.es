<?php

/**
 * Plugin_name
 *
 * @package   Plugin_name
 * @author    Codeat <support@codeat.co>
 * @copyright 2020
 * @license   GPL 2.0+
 * @link      https://codeat.co
 */

namespace Glossary\Internals;

use Glossary\Engine;
use Glossary\Frontend\Core;

/**
 * Shortcodes of this plugin
 */
class Shortcode extends Engine\Base {

	/**
	 * Initialize the class.
	 *
	 * @return bool
	 */
	public function initialize() {
		parent::initialize();

		\add_shortcode( 'glossary-terms', array( $this, 'terms' ) );
		\add_shortcode( 'glossary-cats', array( $this, 'cats' ) );
		\add_shortcode( 'glossary-categories', array( $this, 'cats' ) );

		if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
			\add_shortcode( 'glossary-list', array( $this, 'list' ) );
			\add_shortcode( 'glossary-ignore', array( $this, 'ignore' ) );
			\add_shortcode( 'glossary', array( $this, 'parser' ) );

			return false;
		}

		return true;
	}

	/**
	 * Remap old shortcode proprierty on new
	 *
	 * @param array  $atts    An array with all the parameters.
	 * @param array  $attributes An array with all the new parameters.
	 * @param string $old_key The old parameter.
	 * @param string $new_key The new parameter.
	 * @param bool   $revert  Revert the bool parameter.
	 * @return array
	 */
	public function remap_old_proprierty(
		array $atts,
		array $attributes,
		string $old_key,
		string $new_key,
		bool $revert = false
	) {
		if ( isset( $atts[ $old_key ] ) ) {
			$attributes[ $new_key ] = $atts[ $old_key ];

			if ( $revert ) {
				$attributes[ $new_key ] = 'true';

				if ( $atts[ $old_key ] === 'true' ) {
					$attributes[ $new_key ] = 'false';
				}
			}
		}

		return $attributes;
	}

	/**
	 * Shortcode for generate list of glossary terms
	 *
	 * @param array|string $atts An array with all the parameters.
	 * @since 1.1.0
	 * @return array
	 */
	public function terms( $atts ) {
		$attributes = array(
			'order'    => 'asc',
			'num'      => '100',
			'taxonomy' => '',
			'theme'    => '',
		);

		if ( \is_array( $atts ) ) {
			$attributes = \shortcode_atts( $attributes, $atts );
			$attributes = $this->remap_old_proprierty( $atts, $attributes, 'tax', 'taxonomy' );
		}

		$key  = 'glossary_terms_list-' . \get_locale() . '-' . \md5( (string) \wp_json_encode( $attributes ) );
		$html = \get_transient( $key );

		if ( false === $html || empty( $html ) ) {
			$html = \get_glossary_terms_list( $attributes[ 'order' ], $attributes[ 'num' ], $attributes[ 'taxonomy' ], $attributes[ 'theme' ] );
			\set_transient( $key, $html, DAY_IN_SECONDS );
		}

		return $html;
	}

	/**
	 * Shortcode for generate list of glossary cat
	 *
	 * @param array|string $atts An array with all the parameters.
	 * @since 1.1.0
	 * @return string
	 */
	public function cats( $atts ) {
		$attributes = array(
			'order' => 'ASC',
			'num'   => '100',
			'theme' => '',
			);

		if ( \is_array( $atts ) ) {
			$attributes = \shortcode_atts( $attributes, $atts );
		}

		return \get_glossary_cats_list( $attributes[ 'order' ], $attributes[ 'num' ], $attributes[ 'theme' ] );
	}

	/**
	 * Generate a navigable list of terms
	 *
	 * @param array|string $atts An array with all the parameters.
	 * @global object $wpdb WPDB object.
	 * @return string
	 */
	public function list( $atts ) {
		$attributes = array(
			'letter-anchor' => 'true',
			'empty-letters' => 'true',
			'excerpt'       => 'false',
			'content'       => 'false',
			'term-anchor'   => 'true',
			'search'        => 'disabled',
			'custom-url'    => 'false',
			'theme'         => '',
			'show-letter'   => '',
			'taxonomy'      => '',
		);

		if ( \is_array( $atts ) ) {
			$attributes = \shortcode_atts( $attributes, $atts );
			$attributes = $this->remap_old_proprierty( $atts, $attributes, 'anchor', 'letter-anchor' );
			$attributes = $this->remap_old_proprierty( $atts, $attributes, 'customurl', 'custom-url' );
			$attributes = $this->remap_old_proprierty( $atts, $attributes, 'empty', 'empty-letters' );
			$attributes = $this->remap_old_proprierty( $atts, $attributes, 'show-letter', 'letters' );
			$attributes = $this->remap_old_proprierty( $atts, $attributes, 'noanchorterms', 'term-anchor', true );
		}

		// Let's see if we have a cached version
		$key  = 'glossary_list_page-' . \get_locale() . '-' . \md5( (string) \wp_json_encode( $attributes ) );
		$html = \get_transient( $key );

		if ( false === $html || empty( $html ) ) {
			$alphabets_bar = new Core\Alphabetical_Index_Bar;
			$alphabets_bar->initialize();
			$alphabets_bar->generate_index( $attributes );

			$html = $alphabets_bar->generate_html_index() . $alphabets_bar->generate_html_content();

			\set_transient( $key, $html, DAY_IN_SECONDS );
		}

		$html = \trim( \str_replace( array( "\r\n", "\r", "\n" ), ' ', $html ) );

		return $html;
	}

	/**
	 * Wrap the content to be ignored
	 *
	 * @param array|string $atts The attributes, not used.
	 * @param string       $content The text to ignore.
	 * @return string
	 */
	public function ignore( $atts, string $content = '' ) { //phpcs:ignore
		return '<glwrap>' . \do_shortcode( $content ) . '</glwrap>';
	}

	/**
	 * Parse the content with Glossary
	 *
	 * @param array|string $atts The attributes, not used.
	 * @param string       $text The text to parse.
	 * @return string
	 */
	public function parser( $atts, string $text ) { //phpcs:ignore
		$search_engine = new Core\Search_Engine;

		return $search_engine->auto_link( $text );
	}

}
