<?php

/**
 * Plugin name
 *
 * @package   Plugin_name
 * @author    Codeat <support@codeat.co>
 * @copyright 2020
 * @license   GPL 2.0+
 * @link      https://codeat.co
 */

namespace Glossary\Internals;

use Glossary\Engine;

/**
 * Transient used by the plugin
 */
class Transient extends Engine\Base {

	/**
	 * Initialize the class.
	 *
	 * @return bool
	 */
	public function initialize() {
		parent::initialize();

		if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
			\add_action( 'save_post_glossary', array( $this, 'reset_transient__premium_only' ) );
		}

		return true;
	}

	/**
	 * Clean the transient for the list shortcode
	 *
	 * @return void
	 */
	public function reset_transient__premium_only() {
		\set_transient( 'glossary_list_page', '' );
	}

}
