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

namespace Glossary\Frontend;

use Glossary\Engine;

/**
 * Enqueue stuff on the frontend
 */
class Enqueue extends Engine\Base {

	/**
	 * Initialize the class.
	 *
	 * @return bool
	 */
	public function initialize() {
		parent::initialize();

		// Add the url of the themes in the plugin
		\add_filter( 'glossary_themes_url', array( $this, 'add_glossary_url' ) );

		if ( !isset( $this->settings[ 'tooltip' ] ) ) {
			return false;
		}

		\add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 9999 );

		if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
			\add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts__premium_only' ), 9999 );
			\add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles__premium_only' ), 9999 );

			return false;
		}

		return true;
	}

	/**
	 * Add the path to the themes
	 *
	 * @param array $themes List of themes.
	 * @return array
	 */
	public function add_glossary_url( array $themes ) {
		$themes[ 'classic' ] = \plugins_url( 'assets/css/tooltip-classic.css', GT_PLUGIN_ABSOLUTE );
		$themes[ 'box' ]     = \plugins_url( 'assets/css/tooltip-box.css', GT_PLUGIN_ABSOLUTE );
		$themes[ 'line' ]    = \plugins_url( 'assets/css/tooltip-line.css', GT_PLUGIN_ABSOLUTE );
		$themes[ 'simple' ]  = \plugins_url( 'assets/css/tooltip-simple.css', GT_PLUGIN_ABSOLUTE );

		return $themes;
	}

	/**
	 * Check if shortcode is in page
	 *
	 * @param string $shortcode Shortcode name.
	 * @return bool
	 */
	public function is_shortcode_in_page( string $shortcode ) {
		global $post;

		return \is_a( $post, 'WP_Post' ) && \has_shortcode( $post->post_content, $shortcode );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		/**
		 * Array with all the url of themes
		 *
		 * @since 1.2.0
		 * @param array $urls The list.
		 * @return array $urls The list filtered.
		 */
		$url_themes = \apply_filters( 'glossary_themes_url', array() );
		\wp_enqueue_style( GT_SETTINGS . '-hint', $url_themes[ $this->settings[ 'tooltip_style' ] ], array(), GT_VERSION );

		if ( $this->is_shortcode_in_page( 'glossary-terms' ) ||
			\is_active_widget( false, false, 'glossary-categories', true ) ||
			\is_active_widget( false, false, 'glossary-latest-terms', true ) ) {
			\wp_enqueue_style( GT_SETTINGS . '-general', \plugins_url( 'assets/css/general.css', GT_PLUGIN_ABSOLUTE ), array(), GT_VERSION );
		}

		$this->enqueue_css_widget();
	}

	/**
	 * Register and enqueue public-facing style sheet for Premium.
	 *
	 * @return void
	 */
	public function enqueue_styles__premium_only() {
		$this->enqueue_css_customizer__premium_only();

		global $post;

		if ( \is_a( $post, 'WP_Post' )
			&& \has_shortcode( $post->post_content, 'glossary-list' ) ||
			\is_tax( 'glossary-cat' ) &&
			isset( $this->settings[ 'archive_alphabetical_bar' ] ) ) {
			\wp_enqueue_style(
				GT_SETTINGS . '-shortcode',
				\plugins_url( 'assets/css/css-pro/shortcode.css', GT_PLUGIN_ABSOLUTE ),
				array( GT_SETTINGS . '-hint' ),
				GT_VERSION
			);
		}

		if ( !\is_type_inject_set_as( 'footnote' ) ) {
			return;
		}

		\wp_enqueue_style(
			GT_SETTINGS . '-footnotes',
			\plugins_url( 'assets/css/css-pro/footnotes.css', GT_PLUGIN_ABSOLUTE ),
			array(),
			GT_VERSION
		);
	}

	/**
	 * Enqueue the css for widgets
	 *
	 * @return bool
	 */
	public function enqueue_css_widget() {
		if ( \is_active_widget( false, false, 'glossary-alphabetical-index', true ) ) {
			\wp_enqueue_style(
				GT_SETTINGS . '-a2z-widget',
				\plugins_url( 'assets/css/css-pro/A2Z-widget.css', GT_PLUGIN_ABSOLUTE ),
				array(),
				GT_VERSION
			);
		}

		if ( !\is_active_widget( false, false, 'glossary-search-terms', true ) ) {
			return false;
		}

		\wp_enqueue_style(
			GT_SETTINGS . '-search-widget',
			\plugins_url( 'assets/css/css-pro/search-widget.css', GT_PLUGIN_ABSOLUTE ),
			array(),
			GT_VERSION
		);

		return true;
	}

	/**
	 * Enqueue the CSS customizer based option
	 *
	 * @return bool
	 */
	public function enqueue_css_customizer__premium_only() {
		$custom_css = \get_option( GT_SETTINGS . '-customizer' );

		if ( !isset( $custom_css[ 'on_mobile' ] ) || 'responsive' !== $custom_css[ 'on_mobile' ] ) {
			return false;
		}

		\wp_enqueue_style(
			GT_SETTINGS . '-mobile-tooltip',
			\plugins_url( 'assets/css/css-pro/mobile-tooltip.css', GT_PLUGIN_ABSOLUTE ),
			array(),
			GT_VERSION
		);

		return true;
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @return void
	 */
	public function enqueue_scripts__premium_only() {
		global $post;
		$custom_css = \get_option( GT_SETTINGS . '-customizer' );
		\wp_enqueue_script(
			GT_SETTINGS . '-off-screen',
			\plugins_url( 'assets/js/off-screen.js', GT_PLUGIN_ABSOLUTE ),
			array( 'jquery' ),
			GT_VERSION,
			true
		);

		if ( $this->is_shortcode_in_page( 'glossary-list' ) ) {
			if ( \strpos( $post->post_content, 'search=' ) !== false ) {
				\wp_enqueue_script(
					GT_SETTINGS . '-mark-js',
					'https://cdnjs.cloudflare.com/ajax/libs/mark.js/8.11.1/mark.min.js',
					array(),
					GT_VERSION,
					true
				);
				\wp_enqueue_script(
					GT_SETTINGS . '-a2z-search',
					\plugins_url( 'assets/js/a2z-search.js', GT_PLUGIN_ABSOLUTE ),
					array( GT_SETTINGS . '-mark-js' ),
					GT_VERSION,
					true
				);
			}
		}

		if ( !isset( $custom_css[ 'on_mobile' ] ) || 'responsive' !== $custom_css[ 'on_mobile' ] ) {
			return;
		}

		\wp_enqueue_script(
			GT_SETTINGS . '-mobile-tooltip-js',
			\plugins_url( 'assets/js/mobile-tooltip.js', GT_PLUGIN_ABSOLUTE ),
			array( 'jquery' ),
			GT_VERSION,
			true
		);
	}

}
