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
class Search_Engine extends Engine\Base {

	/**
	 * Is_Methods class
	 *
	 * @var \Glossary\Engine\Is_Methods
	 */
	private $content;

	/**
	 * Terms_list class
	 *
	 * @var \Glossary\Frontend\Core\Terms_List
	 */
	private $terms_list;

	/**
	 * Injector class
	 *
	 * @var \Glossary\Frontend\Core\Term_Injector
	 */
	private $injector;

	public function __construct() {
		parent::initialize();

		$this->injector = new Term_Injector;
		$this->injector->initialize();
		$this->content    = new Engine\Is_Methods;
		$this->terms_list = new Terms_List;
		$this->terms_list->initialize();
	}

	/**
	 * Initialize the class with all the hooks
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function initialize() {
		$priority = 999;

		if ( \defined( 'FTOC_VERSION' ) ) {
			$priority = 9;
		}

		if ( \defined( 'ELEMENTOR_VERSION' ) ) {
			\add_filter( 'widget_text', array( $this, 'check_auto_link' ), $priority );
		}

		\add_filter( 'the_content', array( $this, 'check_auto_link' ), $priority );
		\add_filter( 'the_excerpt', array( $this, 'check_auto_link' ), $priority );

		// BuddyPress support on activities
		if ( \apply_filters( $this->default_parameters[ 'filter_prefix' ] . '_buddypress_support', false ) ) {
			\add_filter( 'bp_get_activity_content_body', array( $this, 'auto_link' ), $priority );
		}

		if ( !\gt_fs()->is_plan__premium_only( 'professional' ) ) {
			\add_filter( 'the_excerpt_rss', array( $this, 'check_auto_link' ), $priority );

			return false;
		}

		return true;
	}

	/**
	 * Validate to show the auto link
	 *
	 * @param string $text The content.
	 * @return string
	 */
	public function check_auto_link( string $text ) {
		if (
			! $this->content->is_already_parsed( $text ) &&
			\apply_filters(
				$this->default_parameters[ 'filter_prefix' ] . '_is_page_to_parse',
				$this->content->is_page_type_to_check()
			)
		) {
			return $this->auto_link( $text );
		}

		return $text;
	}

	/**
	 * If there are terms to inject, let's do it.
	 *
	 * @param string $text String that wrap with a tooltip/link.
	 * @return string
	 */
	public function auto_link( string $text ) {
		$terms = $this->terms_list->get();

		if ( empty( $terms ) ) {
			return $text;
		}

		return $this->injector->do_wrap( $text, $terms );
	}

}
