<?php
namespace ACFFrontend\Classes;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class ActionBase {

	abstract public function get_name();

	public function show_in_tab(){
		return true;
	}

	abstract public function get_label();

	public function run( $settings, $step = false ){
		return $settings;
	}

	public function add_field_options( $widget, $field, $label, $options ){
		return;
	}

	public function action_controls( $widget, $step = false ){
		return;
	}

	public function save_form_data( $data_id, $values ){
		acf_set_form_data( 'post_id', $data_id );
		if( !acf_allow_unfiltered_html() ) {
			$values = wp_kses_post_deep( $values );
		}
		acf_update_values( $values, $data_id );
		
	} 

	abstract public function register_settings_section( $widget );

}