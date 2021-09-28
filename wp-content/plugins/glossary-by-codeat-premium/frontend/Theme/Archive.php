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

namespace Glossary\Frontend\Theme;

use Glossary\Engine;
use Glossary\Frontend\Core;

/**
 * Integrations for Archive
 */
class Archive extends Engine\Base {

	/**
	 * Initialize the class with all the hooks
	 *
	 * @since 1.0.0
	 */
	public function initialize() {
		parent::initialize();

		if ( isset( $this->settings[ 'tax_archive' ] ) ) {
			\add_action( 'pre_get_posts', array( $this, 'hide_taxonomy_frontend' ) );
		}

		if ( isset( $this->settings[ 'remove_archive_label' ] ) ) {
			\add_filter( 'get_the_archive_title', array( $this, 'remove_archive_label' ) );
			\add_filter( 'pre_get_document_title', array( $this, 'remove_archive_label' ), 99999 );
		}

		if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
			$this->methods_for__premium_only();

			return;
		}
	}

	/**
	 * Methods for premium
	 */
	public function methods_for__premium_only() {
		if ( isset( $this->settings[ 'number_archive_title' ] ) ) {
			\add_filter( 'get_the_archive_title', array( $this, 'archive_count_terms__premium_only' ) );
		}

		if ( isset( $this->settings[ 'archive_alphabetical_bar' ] ) ) {
			\add_filter( 'get_the_archive_description', array( $this, 'archive_bar' ) );

			return;
		}
	}

	/**
	 * Hide the taxonomy on the frontend
	 *
	 * @param object $query The query.
	 * @return void
	 */
	public function hide_taxonomy_frontend( $query ) {
		if ( \is_admin() ) {
			return;
		}

		if ( !\is_tax( 'glossary-cat' ) ) {
			return;
		}

		$query->set_404();
	}

	/**
	 * Cleanup the Archive/Tax page from terms
	 *
	 * @param string $title The archive title.
	 * @return string
	 */
	public function remove_archive_label( string $title ) {
		$object = \get_queried_object();

		if ( isset( $object->taxonomy ) ) {
			$tax = \get_queried_object()->taxonomy;

			if ( 'glossary-cat' === $tax ) {
				$title = \single_term_title( '', false );
			}
		}

		if ( isset( $object->name ) ) {
			if ( 'glossary' === $object->name ) {
				$title = \str_replace( \__( 'Archives', 'glossary-by-codeat' ) . ':', '', $title );
				$title = \str_replace( \__( 'Archives', 'glossary-by-codeat' ), '', $title );
			}
		}

		if ( empty( $title ) ) {
			$title = \post_type_archive_title( '', false );
		}

		return \trim( $title );
	}

	/**
	 * Append to the title the number of strings
	 *
	 * @param string $title The archive title.
	 * @return string
	 */
	public function archive_count_terms__premium_only( string $title ) {
		$object = \get_queried_object();
		$count  = '';

		if ( isset( $object->taxonomy ) && 'glossary-cat' === $object->taxonomy ) {
				$count = new \WP_Query(
					array(
						'post_type'      => 'glossary',
						'posts_per_page' => -1,
						'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Required to count by related terms.
							array(
								'taxonomy' => \get_queried_object()->taxonomy,
								'field'    => 'ID',
								'terms'    => $object->term_taxonomy_id,
							),
						),
					)
				);
		} elseif ( 'glossary' === $object->name ) {
			$count = new \WP_Query( array('post_type' => 'glossary', 'posts_per_page' => -1) );
		}

		if ( empty( $count ) ) {
			return $title;
		}

		return $title . ': ' . $count->post_count . ' ' . \__( 'terms', 'glossary-by-codeat' );
	}

	/**
	 * Replace description with the list of letters
	 *
	 * @param string $desc The archive description.
	 * @return string
	 */
	public function archive_bar( string $desc ) {
		$object = \get_queried_object();

		if ( isset( $object->taxonomy ) && 'glossary-cat' === $object->taxonomy ) {
			$alphabets_bar = new Core\Alphabetical_Index_Bar;
			$alphabets_bar->initialize();
			$alphabets_bar->generate_index(
				array( 'theme' => 'list', 'search' => false, 'anchor' => 'false', 'empty' => false )
			);

			return $alphabets_bar->generate_html_index();
		}

		return $desc;
	}

}
