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
 * Combine the Core to gather, search and inject
 */
class Alphabetical_Index_Bar extends Engine\Base {

	/**
	 * Settings
	 *
	 * @var array
	 */
	private $atts = array();

	/**
	 * Term index
	 *
	 * @var array
	 */
	private $alpha_terms = array();

	/**
	 * Term index alphabetic
	 *
	 * @var array
	 */
	private $alpha_index = array();

	/**
	 * Generate a list A B C D etc.
	 *
	 * @param array $atts The various parameters.
	 * @return array
	 */
	public function generate_index( array $atts ) {
		$this->atts        = $atts;
		$this->alpha_index = $this->alpha_terms = array();

		$args = array(
			'post_type'      => 'glossary',
			'posts_per_page' => -1,
			'order'          => 'ASC',
			'orderby'        => 'title',
		);

		if ( !empty( $this->atts[ 'taxonomy' ] ) ) {
			$args[ 'tax_query' ] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy' => 'glossary-cat',
						'field'    => 'slug',
						'terms'    => $atts[ 'taxonomy' ],
					),
			);
		}

		if ( !empty( $this->atts[ 'show-letter' ] ) ) {
			$args[ 'post__in' ] = \gl_get_a2z_ids( $this->atts );
		}

		$this->loop_terms( $args );
		$this->parse_terms();
		$this->order_letters();

		return array( $this->alpha_index, $this->alpha_terms );
	}

	/**
	 * Generate index
	 */
	public function parse_terms() {
		if ( 'true' !== $this->atts[ 'empty-letters' ] ) {
			return;
		}

		$letters = \range( 'A', 'Z' );

		foreach ( $letters as $letter ) {
			$letter = \strtolower( (string) $letter );

			if ( isset( $this->alpha_index[ $letter ] ) ) {
				continue;
			}

			$this->alpha_index[ $letter ] = '<span class="glossary-no-link-initial-item">' . $letter . '</span>';
		}
	}

	/**
	 * Loop terms
	 *
	 * @param array $args WP Query arguments.
	 */
	public function loop_terms( array $args ) {
		$terms = new \WP_Query( $args );

		foreach ( $terms->posts as $post ) {
			$post_id = $post;
			$title   = \get_the_title( $post_id );

			if ( !\is_int( $post ) ) {
				$post_id = $post->ID;
				$title   = $post->post_title;
			}

			$initial_index = $title;

			if ( \gl_is_latin( $initial_index ) ) {
				$initial_index = \mb_strtolower( $initial_index );
			}

			$initial_index = \mb_substr( $initial_index, 0, 1 );
			$link          = '#' . $initial_index;

			if ( 'false' === $this->atts[ 'letter-anchor' ] ) {
				$link = \add_query_arg( 'az', $initial_index, \gl_get_base_url() );
			}

			$this->alpha_index[ $initial_index ]             = '<span class="glossary-link-initial-item"><a href="' . $link . '">' . $initial_index . '</a></span>';
			$this->alpha_terms[ $initial_index ][ $post_id ] = $title;
		}
	}

	/**
	 * Generate content based on the list.
	 *
	 * @return string
	 */
	public function generate_html_content() {
		$html = '<div class="glossary-term-list ' . $this->atts[ 'theme' ] . '">';

		foreach ( $this->alpha_terms as $letter => $terms ) {
			$html .= '<div class="glossary-block glossary-block-' . $letter . '">';
			$html .= '<span class="glossary-letter" id="' . $letter . '">' . $letter . '</span>';
			$html .= '<ul>';

			foreach ( $terms as $id => $title ) {
				$html .= '<li>';
				$html .= '<span class="glossary-link-item">' . $this->get_anchor( $id, $title );
				$html .= $this->get_text( $id );
				$html .= '</span>';
				$html .= '</li>';
			}

			$html .= '</ul></div>';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Get anchor from a term.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $title Term title.
	 * @return string
	 */
	public function get_anchor( int $post_id, string $title ) {
		$anchor = $title;

		if ( 'true' === $this->atts[ 'term-anchor' ] ) {
			$anchor    = '<a href="' . \get_permalink( (int) $post_id ) . '">' . $title . '</a>';
			$customurl = \get_glossary_term_url( $post_id );

			if ( 'true' === $this->atts[ 'custom-url' ] && !empty( $customurl ) ) {
				$anchor = '<a href="' . $customurl . '">' . $title . '</a>';
			}
		}

		return $anchor;
	}

	/**
	 * Generate HTML index
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	public function get_text( int $post_id ) {
		$separator = \apply_filters( 'glossary_list_excerpt_separator', ' - ' );
		$type      = 'excerpt';
		$text      = \get_the_excerpt( (int) $post_id );

		if ( 'true' === $this->atts[ 'content' ] ) {
			$text = \get_post_field( 'post_content', (int) $post_id );
			$type = 'content';
		}

		if ( 'true' === $this->atts[ 'excerpt' ] || 'true' === $this->atts[ 'content' ] ) {
			return $separator . '<span class="glossary-list-term-' . $type . '">' . $text . '</span>';
		}

		return '';
	}

	/**
	 * Generate HTML index
	 *
	 * @return string
	 */
	public function generate_html_index() {
		$this->remap_search_attribute();
		$bar  = '<div class="glossary-term-bar">';
		$bar .= \implode( '', $this->alpha_index );
		$bar .= '</div>';

		if ( 'disabled' !== $this->atts[ 'search' ] ) {
			$bar .= '<div class="gt-search-bar" data-scroll="' . $this->atts[ 'search' ] . '">
				<input type="text" class="gt-search" name="gt-search" aria-label="' . \__( 'Search', 'glossary-by-codeat' ) . '" placeholder="' . \__( 'Search', 'glossary-by-codeat' ) . '" value="">'; // phpcs:ignore

			if ( 'scroll' === $this->atts[ 'search' ] || 'scroll-bottom' === $this->atts[ 'search' ] ) {
				$bar .= '<button class="gt-next">&darr;</button><button class="gt-prev">&uarr;</button>';
			}

			$bar .= '</div>';
		}

		return $bar;
	}

	/**
	 * Remap old parameters from Glossary 1.0 to 2.0
	 *
	 * @return array
	 */
	public function remap_search_attribute() {
		if ( 'true' === $this->atts[ 'search' ] ) {
			$this->atts[ 'search' ] = 'scroll';
		}

		if ( 'false' === $this->atts[ 'search' ] ) {
			$this->atts[ 'search' ] = 'disabled';
		}

		if ( 'no-fixed' === $this->atts[ 'search' ] ) {
			$this->atts[ 'search' ] = 'no-scroll';
		}

		return $this->atts;
	}

	/**
	 * Order the two arrays based on php modules avalaible
	 */
	private function order_letters() {
		if ( \extension_loaded( 'intl' ) ) {
			$keys   = \array_keys( $this->alpha_index );
			$values = \array_values( $this->alpha_index );
			\collator_asort( \collator_create( 'root' ), $keys );

			$this->alpha_index = array();

			foreach ( $keys as $index => $key ) {
				$this->alpha_index[$key] = $values[$index];
			}

			return;
		}

		\ksort( $this->alpha_index );
		\ksort( $this->alpha_terms );
	}

}
