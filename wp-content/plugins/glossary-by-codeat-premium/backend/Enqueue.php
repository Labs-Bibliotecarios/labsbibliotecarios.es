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

namespace Glossary\Backend;

use Glossary\Engine;

/**
 * This class contain the Enqueue stuff for the backend
 */
class Enqueue extends Engine\Base {

	/**
	 * Initialize the class.
	 *
	 * @return bool
	 */
	public function initialize() {
		if ( !parent::initialize() ) {
			return false;
		}

		// Load admin style sheet and JavaScript.
		\add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		\add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
			\add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts__premium_only' ) );
		}

		return true;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since 2.0
	 */
	public function enqueue_admin_styles() {
		\wp_enqueue_style( GT_SETTINGS . '-admin-styles', \plugins_url( 'assets/css/admin.css', GT_PLUGIN_ABSOLUTE ), array( 'dashicons' ), GT_VERSION );
		\wp_enqueue_style( GT_SETTINGS . '-admin-single-style', \plugins_url( 'assets/css/glossary-admin.css', GT_PLUGIN_ABSOLUTE ), array(), GT_VERSION );
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since 2.0
	 */
	public function enqueue_admin_scripts() {
		\wp_enqueue_script( GT_SETTINGS . '-admin-script', \plugins_url( 'assets/js/admin.js', GT_PLUGIN_ABSOLUTE ), array( 'jquery', 'jquery-ui-tabs' ), GT_VERSION, false );
		$screen = $posttype = '';

		if ( \function_exists( 'get_current_screen' ) ) {
			$screen = \get_current_screen();

			if ( !\is_null( $screen ) ) {
				$posttype = $screen->post_type;
				$screen   = $screen->id;
			}
		}

		if ( 'glossary' !== $posttype ) {
			return;
		}

		\wp_enqueue_script( GT_SETTINGS . '-admin-pt-script', \plugins_url( 'assets/js/pt.js', GT_PLUGIN_ABSOLUTE ), array( 'jquery' ), GT_VERSION, false );
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since 2.0
	 * @return bool Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts__premium_only() {
		$screen = \get_current_screen();

		if ( !\is_null( $screen ) && 'glossary_page_glossary' !== $screen->base ) {
			return false;
		}

		\wp_enqueue_script( GT_SETTINGS . '-customizer-script', \plugins_url( 'assets/js/customizer.js', GT_PLUGIN_ABSOLUTE ), array( 'jquery', 'wp-color-picker' ), GT_VERSION, false );
		\wp_enqueue_script( GT_SETTINGS . '-sticky', \plugins_url( 'assets/js/sticky-jquery.js', GT_PLUGIN_ABSOLUTE ), array( 'jquery' ), GT_VERSION, false );

		return true;
	}

}
