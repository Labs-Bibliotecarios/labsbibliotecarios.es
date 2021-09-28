<?php

if( class_exists('acf_field_select') ) :

class acf_field_product_tax_class extends acf_field_select {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function initialize() {
		
		// vars
		$this->name = 'product_tax_class';
		$this->label = __('Tax Class', 'acf-frontend-form-element');
		$this->public = false;
		$this->defaults = array(
			'multiple' 		=> 0,
			'allow_null' 	=> 0,
			'choices'		=> array(),
			'default_value'	=> '',
			'ui'			=> 0,
			'ajax'			=> 0,
			'placeholder'	=> '',
			'return_format'	=> 'value'
		);

		add_filter( 'acf/load_field/type=select',  [ $this, 'tax_class_field'] );
		add_filter( 'acf/update_value/type=' . $this->name,  [ $this, 'pre_update_value'], 9, 3 );   
	}

	function tax_class_field( $field ){
        if( ! empty( $field['custom_tax_class'] ) ){
            $field['type'] = 'product_tax_class';
        }
        return $field;
    }


    function prepare_field( $field ) {
		$field['choices'] = wc_get_product_tax_class_options();

		$field_key = explode( '_', $field['key'] );
		$form_id = $field_key[0] == 'acfef' ? $field_key[1] : $field_key[0];
		$field['conditional_logic'] = array(
            array(
                array(
                    'field' => 'acfef_' . $form_id . '_product_type',
                    'operator' => '!=',
                    'value' => 'grouped',
                ),
            ),
        );

		$product = acf_frontend_get_product_object();

		if( $product ){
			$field['value'] = $product->get_tax_class( 'edit' );
		}

		return $field;
	}

	
	function pre_update_value( $value, $post_id, $field ) {
        if( empty( $post_id ) || ! is_numeric( $post_id ) ) return null;  

		$product = wc_get_product( $post_id );

		if( $product ){
			$product->set_tax_class( $value );
			$product->save(); 
		}
		return null;

	}
	function update_value( $value, $post_id, $field ) {
		return null;
	}

}

// initialize
acf_register_field_type( 'acf_field_product_tax_class' );

endif; // class_exists check

?>