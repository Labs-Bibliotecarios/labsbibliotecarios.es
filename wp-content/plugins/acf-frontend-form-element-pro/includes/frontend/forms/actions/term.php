<?php
namespace ACFFrontend\Actions;

use ACFFrontend\Plugin;
use ACFFrontend;
use ACFFrontend\Classes\ActionBase;
use ACFFrontend\Widgets;
use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if( ! class_exists( 'ActionTerm' ) ) :

class ActionTerm extends ActionBase {
	
	public function get_name() {
		return 'term';
	}

	public function get_label() {
		return __( 'Term', 'acf-frontend-form-element' );
	}

	public function get_fields_display( $form_field, $local_field ){
		switch( $form_field['field_type'] ){
			case 'term_name':
				$local_field['type'] = 'term_name';
				$local_field['change_slug'] = isset( $form_field['change_slug'] ) ? $form_field['change_slug'] : 0;
			break;
			case 'term_slug':
				$local_field['type'] = 'term_slug';
			break;
			case 'term_description':
				$local_field['type'] = 'term_description';
			break;
		}
		return $local_field;
	}
	

	public function register_settings_section( $widget ) {
						
		$widget->start_controls_section(
			'section_edit_term',
			[
				'label' => $this->get_label(),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->action_controls( $widget );
		$widget->end_controls_section();
	}

	public function action_controls( $widget, $step = false, $type = '' ){
		if( ! empty( $widget->form_defaults['save_to_term'] ) ){
			$type = $widget->form_defaults['save_to_term'];
		}

		if( $step ){
			$condition = [
				'field_type' => 'step',
				'overwrite_settings' => 'true',
			];
		}
		$args = [
            'label' => __( 'Term', 'acf-frontend-form-element' ),
            'type'      => Controls_Manager::SELECT,
            'options'   => [
				'edit_term' => __( 'Edit Term', 'acf-frontend-form-element' ),
				'new_term' => __( 'New Term', 'acf-frontend-form-element' ),
			],
            'default'   => 'edit_term',
        ];
		if( $step ){
			$condition = [
				'field_type' => 'step',
				'overwrite_settings' => 'true',
			];
			$args['condition'] = $condition;
		}else{
			$condition = array();
		}

		if( $type ){
			$args = [
				'type' => Controls_Manager::HIDDEN,
				'default' => $type,
			];
		}

		$widget->add_control( 'save_to_term', $args );
		
		$condition['save_to_term'] = [ 'edit_term', 'delete_term' ];

		$widget->add_control(
			'term_to_edit',
			[
				'label' => __( 'Term To Edit', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'current_term',
				'options' => [
					'current_term'  => __( 'Current Term', 'acf-frontend-form-element' ),
					'url_query' => __( 'Url Query', 'acf-frontend-form-element' ),
					'select_term' => __( 'Select Term', 'acf-frontend-form-element' ),
				],
				'condition' => $condition,
			]
		);
		$condition['term_to_edit'] = 'url_query';
		$widget->add_control(
			'url_query_term',
			[
				'label' => __( 'URL Query', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'term_id', 'acf-frontend-form-element' ),
				'default' => __( 'term_id', 'acf-frontend-form-element' ),
				'required' => true,
				'description' => __( 'Enter the URL query parameter containing the id of the term you want to edit', 'acf-frontend-form-element' ),
				'condition' => $condition,
			]
		);	
		$condition['term_to_edit'] = 'select_term';
			$widget->add_control(
				'term_select',
				[
					'label' => __( 'Term', 'acf-frontend-form-element' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => __( '18', 'acf-frontend-form-element' ),
					'description' => __( 'Enter term id', 'acf-frontend-form-element' ),
					'condition' => $condition,
				]
			);		

		$condition['save_to_term'] = 'new_term';
		unset( $condition['term_to_edit'] );
		$widget->add_control(
			'new_term_taxonomy',
			[
				'label' => __( 'Taxonomy', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => 'category',
				'options' => acf_get_taxonomy_labels(),
				'condition' => $condition,
			]
		);
	}
	
	public function save_form( $form ){	
		if( empty( $_POST['_acf_term'] ) ) return $form;

		if( empty( $_POST['acff']['term'] ) && empty( $_POST['_acf_step'] ) ) return $form;

		$term_id = wp_kses( $_POST['_acf_term'], 'strip' );

		// allow for custom save
		$term_id = apply_filters('acf/pre_save_term', $term_id, $form);

		$wg_id = isset( $_POST['_acf_element_id'] ) ? '_' . $_POST['_acf_element_id'] : '';

		$core_fields = array(
			'term_name' => 'name',
			'term_slug' => 'slug',
			'term_description' => 'description',				
		);

		$term_name = '(no-name)';
		$term_args = array();

		if( ! empty( $_POST['acff']['term'] ) ){
			foreach( $_POST['acff']['term'] as $key => $value ){
				$field = acf_get_field( $key );

				if( ! $field ) continue;

				$field_type = $field['type'];
				
				if( ! in_array( $field_type, array_keys( $core_fields ) ) ){
					$metas[] = array( 'value' => $value, 'field' => $field ); 
					continue;
				} 
				$field_value = acf_extract_var( $_POST['acff']['term'], $field['key'] );

				if( strpos( $field['default_value'], '[' ) !== false ){
					$dynamic_value = acff()->dynamic_values->get_dynamic_values( $field['default_value'] ); 
					if( $dynamic_value ) $field_value = $dynamic_value;
				} 

				if( $field_type == 'term_name' ){
					$term_name = $field_value;
				}else{
					$term_args[ $core_fields[$field_type] ] = $field_value;
				}
			}
		}

		$taxonomy = ! empty( $_POST['_acf_taxonomy_type'] ) ? $_POST['_acf_taxonomy_type'] : 'category';

		if( $form['save_to_term'] == 'new_term' ){
			$term_data = wp_insert_term( $term_name, $taxonomy, $term_args );
			if( is_wp_error( $term_data ) ){
				return $form;
			}else{
				$term_id = $term_data['term_id'];
			}
		}elseif( is_numeric( $term_id ) ){
			wp_update_term( $term_id, $taxonomy, $term_args );
		}else{
			return $form;
		}

		if( ! empty( $metas ) ){
			foreach( $metas as $meta ){
				acf_update_value( $meta['value'], 'term_'.$term_id, $meta['field'] );
			}
		}

		$form['record']['term'] = $term_id;

		do_action( 'acf_frontend/save_term', $form, $term_id );

		return $form;
	}

	public function __construct(){
		add_filter( 'acf_frontend/save_form', array( $this, 'save_form' ), 4 );
	}
}
acff()->local_actions['term'] = new ActionTerm();

endif;	