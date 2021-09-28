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
class Term_Content extends Engine\Base {

	/**
	 * Initialize the class
	 */
	public function __construct() {
		// 9998 To avoid Ninja Forms
		\add_filter( 'the_content', array( $this, 'custom_fields' ), 9998 );
	}

	/**
	 * Append the list of custom fields in the term content
	 *
	 * @param string $content The post content.
	 * @since 1.2.0
	 * @return string HTML
	 */
	public function custom_fields( string $content ) {
		if ( 'glossary' === \get_post_type() && !\is_post_type_archive() && !\is_search() ) {
			$custom_fields = \get_option( GT_SETTINGS . '-extra' );

			if ( empty( $custom_fields[ 'custom_fields' ] ) ) {
				return $content;
			}

			$content .= $this->table_output( $custom_fields[ 'custom_fields' ] );
		}

		return $content;
	}

	/**
	 * Table output of custom fields
	 *
	 * @param array $custom_fields List of fields.
	 * @return string
	 */
	public function table_output( array $custom_fields ) {
		$print  = '';
		$fields = array();

		if ( !empty( $custom_fields ) ) {
			foreach ( $custom_fields as $field_name ) {
				$field_name          = \trim( $field_name );
				$field_id            = \str_replace( ' ', '', \strtolower( $field_name ) );
				$fields[ $field_id ] = $field_name;
			}

			/**
			 * Array with all the fields and their values before to print it
			 *
			 * @param array $fields The array.
			 * @since 1.2.0
			 * @return array $fields The array filtered.
			*/
			$fields = \apply_filters( 'glossary_customizer_fields_list', $fields );
			$print  = $this->generate_table( $fields );
		}

		/**
		 * String with all the HTML before to print
		 *
		 * @param string $print HTML.
		 * @param array $fields The array.
		 * @since 1.2.0
		 * @return string $print HTML filtered.
		 */
		return \apply_filters( 'glossary_customizer_fields_output', $print, $fields );
	}

	/**
	 * Generate table by fields
	 *
	 * @param array $fields List of fields.
	 * @return string
	 */
	public function generate_table( array $fields ) {
		$print = $table_content = '';

		foreach ( $fields as $field_id => $field_name ) {
			$value = \get_post_meta( (int) \get_the_ID(), 'glossary-by-codeat_' . $field_id, true );

			if ( empty( $value ) ) {
				continue;
			}

			$table_content .= '<tr>';
			$table_content .= '<td>' . $field_name . '</td>';
			$table_content .= '<td>' . $value . '</td>';
			$table_content .= '</tr>';
		}

		if ( !empty( $table_content ) ) {
			$print = '<table class="glossary-custom-fields">' . $table_content . '</table>';
		}

		return $print;
	}

}
