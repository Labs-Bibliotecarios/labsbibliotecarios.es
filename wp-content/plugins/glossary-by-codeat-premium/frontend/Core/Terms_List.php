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

namespace Glossary\Frontend\Core;

use Glossary\Engine;

/**
 * Generate the Terms list
 */
class Terms_List extends Engine\Base {

	/**
	 * The list of terms parsed
	 *
	 * @var array
	 */
	private $terms_queue = array();

	/**
	 * WP_Query settings
	 *
	 * @var array
	 */
	private $query_args = array();

	/**
	 * What taxonomies want to use to filter?
	 *
	 * @var array
	 */
	private $term_taxs = array();

	/**
	 * Hash associated to that taxonomy selection
	 *
	 * @var string
	 */
	private $what_queue = '';

	/**
	 * Term ID
	 *
	 * @var int
	 */
	private $id_term = 0;

	/**
	 * Term parameters
	 *
	 * @var array
	 */
	private $parameters = array();

	/**
	 * Return the regular expression
	 *
	 * @param string $term Terms.
	 * @return string
	 */
	public function search_string( string $term ) {
		$term            = \preg_quote( $term, '/' );
		$caseinsensitive = '(?i)' . $term . '(?-i)';

		if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
			// Regex is different for support and case sensitive
			if ( \gl_get_bool_settings( 'case_sensitive' ) ) {
				$caseinsensitive = $term;
			}
		}

		$span_open = $span_close = '';

		// Exclude span from parsing to avoid conflicts
		if ( \gl_get_bool_settings( 'ignore_span' ) ) {
			$span_open  = '|<span class="glossary';
			$span_close = '|<\/span';
		}

		$symbols = '(?=[ \.\,\:\;\*\"\)\!\?\/\%\$\€\£\|\^\<\>\“\”])';
		$unicode = 'u';

		if ( \preg_match( '/[\p{Han}]/simu', $term ) ) {
			$symbols = '';
			$unicode = '';
		}

		// <\/tags use from the end of the string so avoid HTML attributes

		/**
		 * The regex to do the first step of scanning
		 *
		 * @param string $regex The regex.
		 * @param string $term  The term of the regex.
		 * @since 1.1.0
		 * @return array $regex We need the regex.
		 */
		return \apply_filters(
			$this->default_parameters[ 'filter_prefix' ] . '_regex',
			'/(?<![\w\—\-\.\/]|=")(' . $caseinsensitive . ')' . $symbols . '(?![^<]*(\/>' . $span_open . '|<h|<\/button|<\/h|<\/a|<\/pre|<\/figcaption' . $span_close . '|\"))/' . $unicode,
			$term
		);
	}

	/**
	 * Generate the list of terms with the various attributes.
	 *
	 * @return array
	 */
	public function get() {
		$this->get_term_tax();
		/**
		 * Use a different set of terms and avoid the WP_Query
		 *
		 * @param array $term_queue The terms.
		 * @since 1.5.0
		 * @return array $term_queue We need the terms.
		 */
		$this->terms_queue = \apply_filters(
			$this->default_parameters[ 'filter_prefix' ] . '_custom_terms',
			$this->terms_queue
		);

		if ( empty( $this->terms_queue[ $this->what_queue ] ) ) {
			$this->get_query_args();

			$gl_query = new \WP_Query( $this->query_args );

			while ( $gl_query->have_posts() ) {
				$gl_query->the_post();
				$this->id_term = (int) \get_the_ID();
				$title         = \get_the_title();

				if ( empty( $title ) ) {
					continue;
				}

				$term_value = $this->get_lower( $title );
				$this->default_term_parameters( $term_value );

				if ( !isset( $this->terms_queue[ $this->what_queue ][ $term_value ] ) ) {
					// Add tooltip based on the title of the term
					$this->enqueue_term( $title );
				}

				$this->enqueue_related_post();
			}

			\wp_reset_postdata();

			if ( !\is_null( $this->terms_queue[ $this->what_queue ] ) ) {
				/**
				* All the terms parsed in array
				*
				* @param array $term_queue The terms.
				* @since 1.4.4
				* @return array $term_queue We need the terms.
				*/
				$this->terms_queue[ $this->what_queue ] = \apply_filters(
					$this->default_parameters[ 'filter_prefix' ] . '_terms_results',
					$this->terms_queue[ $this->what_queue ]
				);
				// We need to sort by long to inject the long version of terms and not the most short
				\usort( $this->terms_queue[ $this->what_queue ], array( $this, 'sort_by_long' ) );
			}
		}

		return $this->terms_queue[ $this->what_queue ];
	}

	/**
	 * Set default parameters
	 *
	 * @param string $term_value The value.
	 */
	public function default_term_parameters( string $term_value ) {
		$this->parameters                 = array();
		$this->parameters[ 'url' ]        = \get_post_meta( $this->id_term, GT_SETTINGS . '_url', true );
		$this->parameters[ 'type' ]       = \get_post_meta( $this->id_term, GT_SETTINGS . '_link_type', true );
		$this->parameters[ 'link' ]       = \get_glossary_term_url( $this->id_term );
		$this->parameters[ 'target' ]     = \get_post_meta( $this->id_term, GT_SETTINGS . '_target', true );
		$this->parameters[ 'nofollow' ]   = \get_post_meta( $this->id_term, GT_SETTINGS . '_nofollow', true );
		$this->parameters[ 'noreadmore' ] = false;

		if (
			empty( $this->parameters[ 'url' ] )
			&& empty( $this->parameters[ 'type' ] )
			|| 'internal' === $this->parameters[ 'type' ]
		) {
			$this->parameters[ 'noreadmore' ] = true;
		}

		$this->parameters[ 'hash' ] = \md5( $term_value );
	}

	/**
	 * Enqueue the related post
	 */
	public function enqueue_related_post() {
		// Add tooltip based on the related post term of the term
		$related = \gl_related_post_meta( $this->id_term );

		if ( empty( $related ) ) {
			return;
		}

		foreach ( $related as $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			$term_value = $this->get_lower( $value );

			if ( isset( $this->terms_queue[ $this->what_queue ][ $term_value ] ) ) {
				continue;
			}

			$this->enqueue_term( $value );
		}
	}

	/**
	 * Enqueue the term
	 *
	 * @param string $value   The text.
	 */
	public function enqueue_term( string $value ) {
		$this->terms_queue[ $this->what_queue ][ $value ] = array(
			'value'      => $value,
			'regex'      => $this->search_string( $value ),
			'link'       => $this->parameters[ 'link' ],
			'term_ID'    => $this->id_term,
			'target'     => $this->parameters[ 'target' ],
			'nofollow'   => $this->parameters[ 'nofollow' ],
			'noreadmore' => $this->parameters[ 'noreadmore' ],
			'long'       => \gl_get_len( $value ),
			'hash'       => $this->parameters[ 'hash' ],
		);
	}

	/**
	 * Return a lower string using the settings
	 *
	 * @param string $term The term.
	 * @return string
	 */
	public function get_lower( string $term ) {
		if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
			if ( \gl_get_bool_settings( 'case_sensitive' ) ) {
				return \strtolower( $term );
			}
		}

		return $term;
	}

	/**
	 * Return the queue for that post
	 *
	 * @return array
	 */
	public function get_term_tax() {
		$this->what_queue = 'general';
		$term_taxs        = array();

		if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
			$term_taxs = \get_post_meta(
				(int) \get_the_ID(),
				$this->default_parameters[ 'filter_prefix' ] . '_filter_tax',
				true
			);

			/**
			 * To filter the terms for a post by Taxonomies terms
			 *
			 * @param array|string $terms An array with IDs of terms to use.
			 * @param int $ID ID of the post.
			 * @since 1.8.0
			 * @return array $terms The list of IDs.
			 */
			$this->term_taxs = \apply_filters(
				$this->default_parameters[ 'filter_prefix' ] . '_taxonomy_terms',
				$term_taxs,
				\get_the_ID()
			);

			if ( !empty( $this->term_taxs ) && \is_array( $this->term_taxs ) ) {
				$this->what_queue = \md5( (string) \wp_json_encode( $this->term_taxs ) );
			}
		}

		return array( $this->what_queue, $this->term_taxs );
	}

	/**
	 * Set query args
	 *
	 * @return array
	 */
	public function get_query_args() {
		$this->query_args = array(
			'post_type'              => $this->default_parameters[ 'post_type' ],
			'order'                  => 'ASC',
			'orderby'                => 'title',
			'posts_per_page'         => -1,
			'post_status'            => 'publish',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			$this->default_parameters[ 'filter_prefix' ] . '_auto_link' => true,
		);

		if ( \gl_get_bool_settings( 'match_same_page' ) ) {
			$this->query_args[ 'post__not_in' ] = array( \get_the_ID() );
		}

		if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
			$this->query_args = $this->get_query_args__premium_only();
		}

		return $this->query_args;
	}

	/**
	 * Set query args in Premium
	 *
	 * @return array
	 */
	public function get_query_args__premium_only() {
		/**
		 * In a multisite environment is possible to use a specific site as database for terms
		 *
		 * @param array|string $site_ids An array with IDs of sites to use.
		 * @since 1.6.0
		 * @return array $site_ids The list of IDs.
		 * */
		$site_ids = \apply_filters( $this->default_parameters[ 'filter_prefix' ] . 'multisite_parent', '' );

		if ( !empty( $site_ids ) ) {
			$this->query_args[ 'network' ]            = 1;
			$this->query_args[ 'sites' ]['sites__in'] = $site_ids;
		}

		if ( !empty( $this->term_taxs ) && \is_array( $this->term_taxs ) ) {
			foreach ( $this->term_taxs as $item ) {
				$this->query_args[ 'tax_query' ][] = array(
					'taxonomy' => $this->default_parameters[ 'taxonomy' ],
					'terms'    => $item,
					'field'    => 'term_id',
				);
			}
		}

		return $this->query_args;
	}

	/**
	 * Function for usort to order all the terms on DESC
	 *
	 * @param array $first Previous index.
	 * @param array $second Next index.
	 * @return float|int
	 */
	private static function sort_by_long( array $first, array $second ) { //phpcs:ignore
		return $second[ 'long' ] - $first[ 'long' ];
	}

}
