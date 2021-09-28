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
 * Last glossary terms widget
 */
class Last_Glossaries extends \WPH_Widget {

	/**
	 * Initialize the widget
	 *
	 * @return void
	 */
	public function __construct() {
		$args = array(
			'label'       => \__( 'Glossary Latest Terms', 'glossary-by-codeat' ),
			'description' => \__( 'List of latest Glossary Terms', 'glossary-by-codeat' ),
			'slug'        => 'glossary-latest-terms',
		);

		$args[ 'fields' ] = array(
			array(
				'name'     => \__( 'Title', 'glossary-by-codeat' ),
				'desc'     => \__( 'Enter the widget title.', 'glossary-by-codeat' ),
				'id'       => 'title',
				'type'     => 'text',
				'class'    => 'widefat',
				'std'      => \__( 'Latest Glossary Terms', 'glossary-by-codeat' ),
				'validate' => 'alpha_dash',
				'filter'   => 'strip_tags|esc_attr',
			),
			array(
				'name'     => \__( 'Number', 'glossary-by-codeat' ),
				'desc'     => \__( 'The number of terms to be shown.', 'glossary-by-codeat' ),
				'id'       => 'number',
				'type'     => 'text',
				'validate' => 'numeric',
				'std'      => 5,
				'filter'   => 'strip_tags|esc_attr',
			),
			array(
				'name'     => \__( 'Category', 'glossary-by-codeat' ),
				'desc'     => \__( 'Filter from Glossary category.', 'glossary-by-codeat' ),
				'id'       => 'tax',
				'type'     => 'taxonomyterm',
				'taxonomy' => 'glossary-cat',
			),
			array(
				'name'   => \__( 'Choose theme', 'glossary-by-codeat' ),
				'id'     => 'theme',
				'type'   => 'select',
				'fields' => array(
					array(
						'name'  => \__( 'Hyphen', 'glossary-by-codeat' ),
						'value' => 'hyphen',
					),
					array(
						'name'  => \__( 'Arrow', 'glossary-by-codeat' ),
						'value' => 'arrow',
					),
					array(
						'name'  => \__( 'Dot', 'glossary-by-codeat' ),
						'value' => 'dot',
					),
					array(
						'name'  => \__( 'Tilde', 'glossary-by-codeat' ),
						'value' => 'tilde',
					),
				),
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
		$out  = $args[ 'before_widget' ];
		$out .= $args[ 'before_title' ];

		if ( !isset( $instance[ 'tax' ] ) ) {
			$instance[ 'tax' ] = array();
		}

		if ( \is_null( $instance[ 'number' ] ) ) {
			$instance[ 'number' ] = 5;
		}

		$theme = '';

		if ( isset( $instance[ 'theme' ] ) ) {
			$theme = ' theme-' . $instance[ 'theme' ];
		}

		$out .= $instance[ 'title' ];
		$out .= $args[ 'after_title' ];
		$out .= '<div class="widget-glossary-terms-list' . $theme . '">';
		$out .= \get_glossary_terms_list( 'last', $instance[ 'number' ], $instance[ 'tax' ] );
		$out .= '</div>';
		$out .= $args[ 'after_widget' ];
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
			\register_widget( 'Glossary\Integrations\Widgets\Last_Glossaries' );
		}
		);
	}

}
