<?php
namespace ACFFrontend\Actions;

use ACFFrontend\Plugin;
use ACFFrontend\Classes\ActionBase;
use ACFFrontend\Widgets;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if( ! class_exists( 'ActionOptions' ) ) :

class ActionOptions extends ActionBase {
	
	public function get_name() {
		return 'options';
	}

	public function show_in_tab(){
		return false;
	}

	public function get_label() {
		return __( 'Options', 'acf-frontend-form-element' );
	}
	
	public function get_fields_display( $form_field, $local_field ){
		switch( $form_field['field_type'] ){
			case 'site_title':
				$local_field['type'] = 'site_title';
			break;
			case 'site_tagline':
				$local_field['type'] = 'site_tagline';
			break;
			case 'site_logo':
				$local_field['type'] = 'site_logo';
			break;
		}
		return $local_field;
	}
	

	public function register_settings_section( $widget ) {
		return;
	}

	public function save_form( $form ){	
		if( ! empty( $_POST['_acf_options'] ) ){
			foreach( $_POST['acf'] as $key => $value ){
				update_option( $key, $value );
			}
		}
		if( ! empty( $_POST['acff']['options'] ) ){
			$this->save_form_data( 'options', $_POST['acff']['options'] );
			do_action( 'acf_frontend/save_options', $form );
		}

		return $form;
	}

	public function __construct(){
		add_filter( 'acf_frontend/save_form', array( $this, 'save_form' ), 4 );
	}
	
}

acff()->local_actions['options'] = new ActionOptions();

endif;	