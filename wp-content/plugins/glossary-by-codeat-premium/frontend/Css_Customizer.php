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
 * Generate the css in the frontend
 */
class Css_Customizer extends Engine\Base {

	/**
	 * CSS code to print
	 *
	 * @var string
	 */
	private $print = '';

	/**
	 * Customizer settings
	 *
	 * @var array
	 */
	private $custom_css = array();

	/**
	 * Initialize the class
	 *
	 * @return bool
	 */
	public function initialize() {
		parent::initialize();

		$this->set_default();
		// Add the professional themes
		\add_filter( 'glossary_themes_dropdown', array( $this, 'add_customizer_themes' ) );
		\add_filter( 'glossary_themes_url', array( $this, 'add_customizer_themes_url' ) );

		if ( !isset( $this->settings[ 'tooltip' ] ) ) {
			return false;
		}

		\add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 99999 );

		return true;
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_styles() {
		$version = \get_option( GT_SETTINGS . '_css_last_edit' );

		if ( empty( $version ) ) {
			return;
		}

		\wp_add_inline_style( GT_SETTINGS . '-hint', $this->print_css() );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 * @return string The CSS code.
	 */
	public function print_css() {
		// Tooltip text
		$this->generate_css_selector(
			'.glossary-tooltip-content, .glossary-tooltip-text',
			array(
				'color'      => $this->custom_css[ 'text_color' ],
				'background' => $this->custom_css[ 'text_background' ],
				'font-size'  => $this->custom_css[ 'text_size' ],
			)
		);

		// Lemma css
		$this->generate_css_selector(
			'.glossary-link a, .glossary-underline',
			array(
				'color'      => $this->custom_css[ 'keyterm_color' ],
				'background' => $this->custom_css[ 'keyterm_background' ],
				'font-size'  => $this->custom_css[ 'keyterm_size' ],
			)
		);

		$this->generate_css_selector(
			'.glossary-tooltip-text a',
			array( 'color' => $this->custom_css[ 'link_keyterm_color' ] )
		);

		if ( 'fancy' !== $this->settings[ 'tooltip_style' ] ) {
			// Arrow code
			$this->generate_css_selector(
				'.glossary-tooltip-content::after',
				array( 'border-top-color' => $this->custom_css[ 'text_background' ] )
			);
		}

		if (
			\in_array( $this->settings['tooltip_style'], array( 'book', 'light', 'material' ), true )
			&& isset( $this->custom_css[ 'keyterm_color' ] )
		) {
			$this->generate_css_selector(
				'.glossary-tooltip a::before',
				array( 'background' => $this->custom_css[ 'keyterm_color' ] )
			);
		}

		if ( 'fancy' === $this->settings[ 'tooltip_style' ] && isset( $this->custom_css[ 'keyterm_color' ] ) ) {
			$this->generate_css_selector(
				'.glossary-tooltip a::before, .glossary-tooltip a::after',
				array( 'border-color' => $this->custom_css[ 'keyterm_color' ] )
			);
			$this->generate_css_selector(
				'.glossary-tooltip-content::before',
				array( 'border-top-color' => $this->custom_css[ 'text_background' ] )
			);
		}

		if ( isset( $this->custom_css[ 'no_padding_text' ] ) ) {
			$this->print .= '.glossary-link a {padding:0px;}' . "\n";
		}

		if ( isset( $this->custom_css[ 'on_mobile' ] ) && 'disable' === $this->custom_css[ 'on_mobile' ] ) {
			$this->print .= '@media (max-device-width: 768px) {.glossary-tooltip-content {display:none;}}' . "\n";
		}

		return \substr( $this->print, 0, -1 );
	}

	/**
	 * Set the default values for the css customizer
	 *
	 * @return array
	 */
	public function set_default() {
		$css        = \get_option( GT_SETTINGS . '-customizer' );
		$custom_css = array(
			'text_color'            => '',
			'text_background'       => '',
			'text_size'             => '',
			'keyterm_background'    => '',
			'keyterm_color'         => '',
			'keyterm_size'          => '',
			'link_keyterm_color'    => '',
			'media_icon_background' => '',
			'media_background'      => '',
		);

		if ( \is_array( $css ) ) {
			$this->custom_css = \array_merge( $custom_css, $css );
		}

		return $this->custom_css;
	}

	/**
	 * Add new themes
	 *
	 * @param array $themes Array of themes.
	 * @return array
	 */
	public function add_customizer_themes( array $themes ) {
		$themes[ 'book' ]     = 'Book (PRO)';
		$themes[ 'fancy' ]    = 'Fancy (PRO)';
		$themes[ 'light' ]    = 'Light (PRO)';
		$themes[ 'material' ] = 'Material (PRO)';
		$themes[ 'black' ]    = 'Black (PRO)';

		return $themes;
	}

	/**
	 * Add URL of the new themes
	 *
	 * @param array $themes Array of themes.
	 * @return array
	 */
	public function add_customizer_themes_url( array $themes ) {
		$path                 = \str_replace( '/frontend', '', __FILE__ );
		$themes[ 'light' ]    = \plugins_url( 'assets/css/css-pro/tooltip-light.css', $path );
		$themes[ 'material' ] = \plugins_url( 'assets/css/css-pro/tooltip-material.css', $path );
		$themes[ 'fancy' ]    = \plugins_url( 'assets/css/css-pro/tooltip-fancy.css', $path );
		$themes[ 'book' ]     = \plugins_url( 'assets/css/css-pro/tooltip-book.css', $path );
		$themes[ 'black' ]    = \plugins_url( 'assets/css/css-pro/tooltip-black.css', $path );

		return $themes;
	}

	/**
	 * Generate CSS selector
	 *
	 * @param string $selector     The CSS selector.
	 * @param array  $proprierties The CSS proprierty.
	 * @return string The complete CSS rule.
	 */
	public function generate_css_selector( string $selector, array $proprierties ) {
		$css = '';

		foreach ( $proprierties as $proprierty => $value ) {
			if ( !\is_string( $value ) ) {
				continue;
			}

			$css .= $this->generate_css_proprierty( $proprierty, $value );
		}

		if ( !empty( $css ) ) {
			$this->print .= $selector . ' {' . $css . '}' . "\n";

			return $this->print;
		}

		return '';
	}

	/**
	 * Generate CSS code
	 *
	 * @param string $proprierty The CSS proprierty.
	 * @param string $value      The CSS value.
	 * @return string The complete CSS proprierty.
	 */
	public function generate_css_proprierty( string $proprierty, string $value ) {
		if ( ' !important' === $value ) {
			return '';
		}

		if ( !empty( $value ) ) {
			if ( \is_numeric( $value ) ) {
				$value = \intval( $value ) . 'px';
			}

			return $proprierty . ':' . $value . ';';
		}

		return '';
	}

}
