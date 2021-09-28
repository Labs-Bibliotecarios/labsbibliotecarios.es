<?php

if( ! class_exists('acf_field_download_limit') ) :

class acf_field_download_limit extends acf_field_number {
	
	
	/*
	*  initialize
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
		$this->name = 'download_limit';
		$this->label = __("Download Limit",'acf');
        $this->category = 'Downloadable Product';
		$this->defaults = array(
			'default_value'	=> '',
			'min'			=> '0',
			'max'			=> '',
			'step'			=> '',
			'placeholder'	=> __('Unlimited','woocommerce'),
		);
        add_filter( 'acf/update_value/type=' . $this->name,  [ $this, 'pre_update_value'], 9, 3 );      
	}


    function prepare_field( $field ){
        $field_key = explode( '_', $field['key'] );
		$form_id = $field_key[0] == 'acfef' ? $field_key[1] : $field_key[0];
        $field['conditional_logic'] = array(
            array(
                array(
                    'field' => 'acfef_' . $form_id . '_product_type',
                    'operator' => '==',
                    'value' => 'simple',
                ),
                array(
                    'field' => 'acfef_' . $form_id . '_is_downloadable',
                    'operator' => '==',
                    'value' => '1',
                ),
            ),
        );
        $field['placeholder'] = __('Unlimited','woocommerce');
        if( $field['min'] < 0 || $field['min'] == '' ) $field['min'] = 0;
        $field['type'] = 'number';

        return $field;
    }
    function load_value( $value, $post_id = false, $field = false ){
        $value = get_post_meta( $post_id, '_download_limit', true );
        if( $value == '-1' ) $value = '';
        return $value;
    }
    

    function pre_update_value( $value, $post_id = false, $field = false ){
        if( $value == '' ) $value = '-1';
        update_metadata( 'post', $post_id, '_download_limit', $value );
        return null;
    }

    function update_value( $value, $post_id = false, $field = false ){
        return null;
    }
	
    
}

// initialize
acf_register_field_type( 'acf_field_download_limit' );

endif; // class_exists check

?>