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
 * Process the content to inject tooltips
 */
class Term_Injector extends Engine\Base {

	/**
	 * Terms to parse
	 *
	 * @var array
	 */
	private $terms = array();

	/**
	 * Terms found to insert
	 *
	 * @var array
	 */
	private $terms_to_inject = array();

	/**
	 * Terms already added
	 *
	 * @var array
	 */
	private $already_found = array();

	/**
	 * List of ignore area
	 *
	 * @var array
	 */
	private $ignore_area = array();

	/**
	 * Text to inject
	 *
	 * @var string
	 */
	private $text = '';

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function initialize() {
		$this->ignore_area = array();

		return true;
	}

	/**
	 * Return the array list of terms found
	 *
	 * @return array
	 */
	public function get_terms_injected() {
		return $this->terms_to_inject;
	}

	/**
	 * Wrap the string with a tooltip/link.
	 *
	 * @param string $text  The string to find.
	 * @param array  $terms The list of links.
	 * @return string
	 */
	public function do_wrap( string $text, array $terms ) {
		if ( !empty( $text ) && !empty( $terms ) ) {
			$this->terms_to_inject = $this->already_found = array();
			$this->terms           = $terms;
			$this->text            = \trim( $text );

			if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
				$this->split_replace_ignore_area__premium_only();
			}

			$this->regex_match();
			$this->replace_with_utf_8();

			if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
				$this->split_reinsert_ignore_area__premium_only();

				if ( \is_type_inject_set_as( 'footnote' ) ) {
					$type = new Type\Footnote;
					$type->initialize();
					$this->text .= $type->append_content( $this->terms_to_inject );
				}
			}

			if ( !empty( $this->terms_to_inject ) ) {
				// This eventually remove broken UTF-8
				return (string) \iconv( 'UTF-8', 'UTF-8//IGNORE', $this->text );
			}
		}

		return $text;
	}

	/**
	 * Find terms with the regex
	 *
	 * @return array The list of terms finded in the text.
	 */
	public function regex_match() {
		foreach ( $this->terms as $term ) {
			if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
				if ( \gl_get_bool_settings( 'first_all_occurrence' ) &&
					isset( $this->already_found[ $term[ 'hash' ] ] ) ) {
					continue;
				}
			}

			try {
				$this->create_html_pair( $term );
			} catch ( \Throwable $error ) {
				\error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log, Squiz.PHP.DiscouragedFunctions.Discouraged -- In few cases was helpful on debugging.
					$error->getMessage() . ' at ' . $error->getFile() . ':' . $error->getLine() . ', regex:' . $term[ 'regex' ]
				);
			}
		}

		return $this->terms_to_inject;
	}

	/**
	 * Inject based on the settings
	 *
	 * @param array $term List of terms.
	 */
	public function create_html_pair( array $term ) {
		$matches = array();

		if ( !\preg_match_all( $term[ 'regex' ], $this->text, $matches, PREG_OFFSET_CAPTURE ) ) {
			return;
		}

		$generate = new HTML_Type_Injector;
		$generate->initialize();
		$html_generated = '';

		foreach ( $matches[ 0 ] as $match ) {
			list( $term[ 'replace' ], $text_found ) = $match;

			if ( $this->is_already_found( $text_found, $term ) ) {
				continue;
			}

			if ( empty( $html_generated ) ) {
				$html_generated = $generate->html( $term );
			}

			$this->terms_to_inject[ $text_found ] = array(
				$term[ 'long' ],
				$html_generated,
				$term[ 'replace' ],
				$term[ 'term_ID' ],
			);
			$this->already_found[ $text_found ]   = $text_found + $term[ 'long' ];

			if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
				if ( \gl_get_bool_settings( 'first_all_occurrence' ) ) {
					$this->already_found[ $term[ 'hash' ] ] = true;
				}
			}

			if ( \gl_get_bool_settings( 'first_occurrence' ) ) {
				break;
			}
		}
	}

	/**
	 * Is already find
	 *
	 * @param int   $text_found Found.
	 * @param array $term       Term data.
	 * @return bool
	 */
	public function is_already_found( int $text_found, array $term ) {
		// Avoid annidate detection
		foreach ( $this->already_found as $previous_init => $previous_end ) {
			if ( !\is_numeric( $previous_init ) ) {
				continue;
			}

			if ( ( $previous_init <= $text_found && ( $text_found + $term[ 'long' ] ) <= $previous_end ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Replace the terms with the link or tooltip with UTF-8 support
	 *
	 * @return string The new text.
	 */
	public function replace_with_utf_8() {
		if ( empty( $this->terms_to_inject ) ) {
			return '';
		}

		\uksort( $this->terms_to_inject, 'strnatcmp' );
		$new_pos = \key( $this->terms_to_inject );
		// Copy of text is required for replace
		$new_term_length = $old_pos = 0;
		$new_text        = $this->text;
		$old_term_length = '';

		foreach ( $this->terms_to_inject as $pos => $term ) {
			list( $length, $term_value, $value ) = $term;

			// Calculate the cursor position after the first loop
			if ( 0 !== $old_pos ) {
				$old_pos_temp = $pos - ( $old_pos + $old_term_length );
				$new_pos     += $new_term_length + $old_pos_temp;
			}

			$new_term_length = \gl_get_len( $term_value );
			$old_term_length = $length;

			$real_length = $this->get_real_length( $value, $length );
			// 0 is the term long, 1 is the new html
			$new_text = \substr_replace( $new_text, $term_value, $new_pos, $real_length );
			$old_pos  = $pos;
		}

		$this->text = $new_text;

		return $this->text;
	}

	/**
	 * Check encoding to calculate the real length
	 *
	 * @param string $value  Text.
	 * @param int    $length Original length.
	 * @return int
	 */
	public function get_real_length( string $value, int $length ) {
		$encode = \mb_detect_encoding( $value );

		// With utf-8 character with multiple bits this is the workaround for the right value
		if ( 'ASCII' === $encode ) {
			return $length;
		}

		if ( !\gl_text_is_rtl( $this->text ) ) {
			return $length;
		}

		$multiply = 0;
		// Seems that when there are symbols I need to add 2 for every of them
		$multiply += \mb_substr_count( $this->text, '-' ) + \mb_substr_count(
			$this->text,
			'.'
		) + \mb_substr_count( $this->text, ':' );

		if ( $multiply > 0 ) {
			$length += $multiply * 2;
		}

		$length += $length;

		return $length;
	}

	/**
	 * Split the text by ignore area
	 *
	 * @return string
	 */
	public function split_replace_ignore_area__premium_only() {
		if ( \strpos( $this->text, '<glwrap' ) ) {
			$match = array();
			\preg_match_all( '@<glwrap>(.*?)</glwrap>@su', $this->text, $match );
			$this->ignore_area = array();

			// 0 original text, 1 cleaned text
			foreach ( $match[ 0 ] as $index => $values ) {
				$this->text                  = \str_replace( $values, '|GL-' . $index . '|', $this->text );
				$this->ignore_area[ $index ] = $match[ 1 ][ $index ];
			}
		}

		return $this->text;
	}

	/**
	 * Rebuild the text with ignore area
	 *
	 * @return string
	 */
	public function split_reinsert_ignore_area__premium_only() {
		if ( \strpos( $this->text, '|GL-' ) ) {
			foreach ( $this->ignore_area as $index => $values ) {
				$this->text = \str_replace( '|GL-' . $index . '|', $values, $this->text );
			}
		}

		return $this->text;
	}

}
