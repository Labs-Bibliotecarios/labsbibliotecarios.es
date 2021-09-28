<?php
namespace ACFFrontend\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class SaveFields {
	public function iterate_data( $post_id, $data_container ) {
		if ( isset( $data_container['elType'] ) ) {
			if ( ! empty( $data_container['elements'] ) ) {
				$data_container['elements'] = $this->iterate_data( $post_id, $data_container['elements'] );
			}
            if ( empty( $data_container['widgetType'] ) ) {
                return $data_container;
			}
            return $this->save_widget_fields( $data_container, $post_id, true );
		}

        if( is_array( $data_container ) ){
            foreach ( $data_container as $element_key => $element_value ) {
                $element_data = $this->iterate_data( $post_id, $data_container[ $element_key ] );

                if ( null === $element_data ) {
                    continue;
                }

                $data_container[ $element_key ] = $element_data;
            }
        }

		return $data_container;
	}
	
	public function save_widget_fields( $widget, $post_id = '', $update = false ) {
		$widget_names = acff()->elementor->form_widgets;
		if( is_object( $widget ) ){
			if( in_array( $widget->get_name(), $widget_names ) ){
				$settings = $widget->get_settings();
				$wg_id = $widget->get_id();
				$wg_type = $widget->get_name();
			}else{
				return $widget;
			}
		}elseif( is_array( $widget ) ){
			if( in_array( $widget['widgetType'], $widget_names ) ){
				$settings = $widget['settings'];
				$wg_id = $widget['id'];
				$wg_type = $widget['widgetType'];
			}else{
				return $widget;
			}
		}else{
			return $widget;
		}

		if( ! $post_id ){
			$post_id = get_queried_object_id();
		} 
		$module = acff()->elementor;
				
		if( ! empty( $settings['fields_selection'] ) ){
			$exclude = ['ACF_field_groups', 'ACF_fields', 'default_fields', 'step'];

			foreach( $settings['fields_selection'] as $form_field ){
				if( empty( $form_field['field_type'] ) || in_array( $form_field['field_type'], $exclude ) ){
					continue;
				}	

				$form_field = acf_frontend_parse_args( $form_field, array(
					'field_taxonomy' => 'category',
					'field_default_value' => '',
					'number_default_value' => '',
					'field_required' => '',
					'field_instruction' => '',
					'field_placeholder' => '',
					'number_placeholder' => '',
					'prepend' => '',
					'append' => '',
					'minimum' => '',
					'maximum' => '',
					'field_readonly' => 0,						
					'field_hidden' => 0,						
					'field_disabled' => 0,						
				) );
				$local_field = [];

				if( $form_field['field_type'] == 'taxonomy' ){
					$taxonomy = $form_field['field_taxonomy'];
					$field_key = 'acfef_' . $wg_id .  '_' . $taxonomy;
				}else{
					$field_key = 'acfef_' . $wg_id .  '_' . $form_field['field_type'];
					if( $form_field['field_type'] == 'recaptcha' ){
						$field_key .= '_' . $form_field['_id'];
					}
				}

				$local_field = acf_get_field( $field_key );

				if( ! empty( $local_field ) && ! $update ){
					continue;
				}
				if( empty( $local_field ) ){
					$local_field = [
						'name' => $field_key,
						'key' => $field_key,
					]; 
				}
				if( isset( $form_field['__dynamic__'] ) ) $form_field = $this->parse_tags( $form_field );
				$default_value = $form_field['field_default_value'];
				
				$local_field = array_merge( $local_field, [
					'required' => $form_field['field_required'] ? 1 : 0,
					'instructions' => $form_field['field_instruction'],
					'wrapper' => ['class' => 'elementor-repeater-item-' . $form_field['_id'] ],
					'placeholder' => $form_field['field_placeholder'],
					'default_value' => $default_value,
					'disabled' => $form_field['field_disabled'],
					'readonly' => $form_field['field_readonly'],
					'min' => $form_field['minimum'],
					'max' => $form_field['maximum'],
					'prepend' => $form_field['prepend'],
					'append' => $form_field['append'],
				] );
				if( isset( $data_default ) ){
					$local_field['wrapper']['data-default'] = $data_default;
					$local_field['wrapper']['data-dynamic_value'] = $default_value;
				}
				if( $form_field['field_hidden'] ){
					$local_field['wrapper']['class'] = ' acf-hidden';
				}
				$field_label = ucwords( str_replace( '_', ' ', $form_field['field_type'] ) );
				$local_field['label'] = isset( $form_field['field_label'] ) ? $form_field['field_label'] : $field_label;

				if( isset( $form_field['button_text'] ) && $form_field['button_text'] ){
					$local_field['button_text'] = $form_field['button_text'];
				}

				$sub_fields = false;
				if( $form_field['field_type'] == 'attributes' ){
					$sub_fields = $settings['attribute_fields'];     
				} 
				if( $form_field['field_type'] == 'variations' ){
					$sub_fields = $settings['variable_fields'];     
				}     

				foreach ( acf_frontend_get_field_type_groups() as $name => $group ) {					
					if ( in_array( $form_field['field_type'], array_keys( $group['options'] ) ) ) {
						$action_name = explode( '_', $name )[0];
						if( isset( acff()->local_actions[$action_name] ) ){
							$action = acff()->local_actions[$action_name];
							$local_field = $action->get_fields_display(
								$form_field,
								$local_field,
								$wg_id,
								$sub_fields,
								true
							);
						}
					}
				}

				if( isset( $local_field['type'] ) ){ 	
					if ( $local_field['type'] == 'number' ) {
						$local_field['placeholder'] = $form_field['number_placeholder'];
						$local_field['default_value'] = $form_field['number_default_value'];
					}
				}else{
					$local_field['type'] = $form_field['field_type'];
				}			
				
				if( isset( $form_field['field_label_on'] ) ){
					$local_field['field_label_hide'] = 1;
					$local_field['label'] = '';
				}else{
					$local_field['field_label_hide'] = 0;
				}

				if( ! empty( $form_field['default_terms'] ) ){
					$local_field['default_terms'] = $form_field['default_terms'];
				}
				if( !empty( $form_field['save_prepend'] ) ){
					$local_field['save_prepend'] = 1;
				}else{
					$local_field['save_prepend'] = 0;
				}
				if( !empty( $form_field['save_append'] ) ){
					$local_field['save_append'] = 1;
				}else{
					$local_field['save_append'] = 0;
				}
							
				acf_update_field( $local_field );
				unset( $data_default );
				unset( $default_value );
	
			}

		}
		return $widget;
    }

	public function parse_tags( $settings ){
		$dynamic_tags = $settings['__dynamic__'];
		foreach( $dynamic_tags as $control_name => $tag ){
			$settings[ $control_name ] = $tag;
		}
		return $settings;
	}

	public function __construct(){
		add_action( 'elementor/editor/after_save', [ $this, 'iterate_data'], 10, 2 );
	}
	
}

new SaveFields();