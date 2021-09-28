<?php

if( ! class_exists('acf_field_user_bio') ) :

class acf_field_user_bio extends acf_field_textarea {
	
	
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
		$this->name = 'user_bio';
		$this->label = __("Bio",'acf');
        $this->category = 'User';
		$this->defaults = array(
			'default_value'	=> '',
			'new_lines'		=> '',
			'maxlength'		=> '',
			'placeholder'	=> '',
			'rows'			=> ''
		);
        add_filter( 'acf/load_field/type=textarea',  [ $this, 'load_user_bio_field'] );
        add_filter( 'acf/update_value/type=' . $this->name,  [ $this, 'pre_update_value'], 9, 3 );      
		
	}
    
    function load_user_bio_field( $field ){
        if( ! empty( $field['custom_user_bio'] ) ){
            $field['type'] = 'user_bio';
        }
        return $field;
    }

    function prepare_field( $field ){
        $field['type'] = 'textarea';
        return $field;
    }

    public function load_value( $value, $post_id = false, $field = false ){
        $user = explode( '_', $post_id );
        if( $user[0] == 'user' && ! empty( $user[1] ) ){				
            if( isset( $field['custom_user_bio'] ) && $field['custom_user_bio'] == 1 ){
                $value = get_user_meta( $user[1], 'description', true );
            }
        }
        return $value;
    }

    public function pre_update_value( $value, $post_id = false, $field = false ){
        if( $field['name'] == 'display_name' ) return $value;

        $user = explode( 'user_', $post_id ); 

        if( ! empty( $user[1] ) ){
            $user_id = $user[1]; 
            remove_action( 'acf/save_post', '_acf_do_save_post' );
            wp_update_user( array( 'ID' => $user_id, 'description' => $value ) );
            add_action( 'acf/save_post', '_acf_do_save_post' );
        }
        return null;
    }

    public function update_value( $value, $post_id = false, $field = false ){
        return null;
    }

}

// initialize
acf_register_field_type( 'acf_field_user_bio' );

endif;
	
?>