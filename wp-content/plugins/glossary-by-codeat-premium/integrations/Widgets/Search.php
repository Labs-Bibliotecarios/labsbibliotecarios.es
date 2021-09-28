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

namespace Glossary\Integrations\Widgets;

/**
 * Search terms wdiget
 */
class Search extends \WPH_Widget {

	/**
	 * Initialize the widget
	 *
	 * @return void
	 */
	public function __construct() {
		$args = array(
			'label'       => \__( 'Glossary Search Terms', 'glossary-by-codeat' ),
			'description' => \__( 'Search in Glossary Terms', 'glossary-by-codeat' ),
			'slug'        => 'glossary-search-terms',
		);

		$args[ 'fields' ] = array(
			array(
				'name'     => \__( 'Title', 'glossary-by-codeat' ),
				'desc'     => \__( 'Enter the widget title.', 'glossary-by-codeat' ),
				'id'       => 'title',
				'type'     => 'text',
				'class'    => 'widefat',
				'validate' => 'alpha_dash',
				'filter'   => 'strip_tags|esc_attr',
			),
			array(
				'name'  => \__( 'Add a dropdown for category based filtering', 'glossary-by-codeat' ),
				'id'    => 'taxonomy',
				'type'  => 'checkbox',
				'class' => 'widefat',
			),
		);

		$this->create_widget( $args );
	}

	/**
	 * Print the widget
	 *
	 * @param array $args     Parameters.
	 * @param array $instance Values.
	 * @return void
	 */
	public function widget( $args, $instance ) { //phpcs:ignore
		$search = $taxonomy = $cat = '';
		$out    = $args[ 'before_widget' ];
		$out   .= '<div class="glossary-search-terms">';
		$out   .= $args[ 'before_title' ];
		$out   .= $instance[ 'title' ];
		$out   .= $args[ 'after_title' ];

		if ( isset( $_GET[ 's' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$search = \esc_html( $_GET[ 's' ] ); // phpcs:ignore WordPress.Security
			$cat    = \esc_html( $_GET[ 'glossary-cat' ] ); // phpcs:ignore WordPress.Security
		}

		if ( !empty( $instance[ 'taxonomy' ] ) ) {
			$taxonomy = \wp_dropdown_categories(
					array(
						'taxonomy'          => 'glossary-cat',
						'value_field'       => 'slug',
						'name'              => 'glossary-cat',
						'show_option_none'  => \__( 'Select Glossary Category', 'glossary-by-codeat' ),
						'option_none_value' => '0',
						'echo'              => 0,
						'hierarchical'      => 1,
						'order'             => 'ASC',
						'selected'          => $cat,
					)
			);
			$taxonomy = '<label for="glossary-cat" class="screen-reader-text">' . \__( 'Select Glossary Category', 'glossary-by-codeat' ) . '</label>' . $taxonomy;
		}

		$out .= '<form role="search" class="search-form" method="get" id="searchform" action="' . \home_url( '/' ) . '">'
			. '<input type="hidden" name="post_type" value="glossary" /><input type="text" aria-label="' . \__( 'Search', 'glossary-by-codeat' ) . '" value="' . $search . '" name="s" />'
			. '<input type="submit" value="' . \__( 'Search', 'glossary-by-codeat' ) . '" />'
			. $taxonomy . '</form>';
		$out .= '</div>' . $args[ 'after_widget' ];
		echo $out; // phpcs:ignore
	}

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public function initialize() {
		\add_action(
		'widgets_init',
		static function() {
			\register_widget( 'Glossary\Integrations\Widgets\Search' );
		}
		);
	}

}
