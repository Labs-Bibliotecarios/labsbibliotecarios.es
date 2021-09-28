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
 * Categories widget
 */
class Categories extends \WPH_Widget {

	public function __construct() {
		$args = array(
			'label'       => \__( 'Glossary Categories', 'glossary-by-codeat' ),
			'description' => \__( 'List of Glossary Categories', 'glossary-by-codeat' ),
			'slug'        => 'glossary-categories',
		);

		$args[ 'fields' ] = array(
			array(
				'name'     => \__( 'Title', 'glossary-by-codeat' ),
				'desc'     => \__( 'Enter the widget title.', 'glossary-by-codeat' ),
				'id'       => 'title',
				'type'     => 'text',
				'class'    => 'widefat',
				'std'      => \__( 'Glossary Categories', 'glossary-by-codeat' ),
				'validate' => 'alpha_dash',
				'filter'   => 'strip_tags|esc_attr',
			),
		);

		$args[ 'fields' ][] = array(
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
		);

		$this->create_widget( $args );
	}

	public function widget( $args, $instance ) {//phpcs:ignore
		$out   = $args[ 'before_widget' ];
		$out  .= $args[ 'before_title' ];
		$out  .= $instance[ 'title' ];
		$out  .= $args[ 'after_title' ];
		$theme = '';

		if ( isset( $instance[ 'theme' ] ) ) {
			$theme = ' theme-' . $instance[ 'theme' ];
		}

		$out .= '<div class="widget-glossary-category-list' . $theme . '">';
		$out .= \get_glossary_cats_list();
		$out .= '</div>';
		$out .= $args[ 'after_widget' ];
		echo $out; //phpcs:ignore
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
			\register_widget( 'Glossary\Integrations\Widgets\Categories' );
		}
		);
	}

}
