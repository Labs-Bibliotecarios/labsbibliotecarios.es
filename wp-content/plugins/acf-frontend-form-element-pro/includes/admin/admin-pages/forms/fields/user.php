<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

return array(		
    array(
        'key' => 'save_to_user',
        'field_label_hide' => 0,
        'type' => 'select',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'choices' => array(
            'edit_user' => __( 'Edit User', 'acf-frontend-form-element' ),
            'new_user' => __( 'New User', 'acf-frontend-form-element' ),
        ),
        'default_value' => false,
        'allow_null' => 0,
        'multiple' => 0,
        'ui' => 0,
        'return_format' => 'value',
        'ajax' => 0,
        'placeholder' => '',
    ),	
    array(
        'key' => 'user_to_edit',
        'label' => __( 'User to Edit', 'acf-frontend-form-element' ),
        'type' => 'select',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'save_to_user',
                    'operator' => '==',
                    'value' => 'edit_user',
                ),
            ),
        ),
        'choices' => array(
            'current_user' => __( 'Current User', 'acf-frontend-form-element' ),
            'current_author' => __( 'Current Author', 'acf-frontend-form-element' ),
            'post_author' => __( 'Form Post Author', 'acf-frontend-form-element' ),
            'url_query' => __( 'URL Query', 'acf-frontend-form-element' ),
            'select_user' => __( 'Select User', 'acf-frontend-form-element' ),
        ),
        'default_value' => false,
        'allow_null' => 0,
        'multiple' => 0,
        'ui' => 0,
        'return_format' => 'value',
        'ajax' => 0,
        'placeholder' => '',
    ),
    array(
        'key' => 'url_query_user',
        'label' => __( 'URL Query Key', 'acf-frontend-form-element' ),
        'type' => 'text',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'save_to_user',
                    'operator' => '==',
                    'value' => 'edit_user',
                ),
                array(
                    'field' => 'user_to_edit',
                    'operator' => '==',
                    'value' => 'url_query',
                ),
            ),
        ),
        'placeholder' => '',
    ),
    array(
        'key' => 'select_user',
        'label' => __( 'Select User', 'acf-frontend-form-element' ),
        'name' => 'select_user',
        'prefix' => 'form',
        'type' => 'user',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'save_to_user',
                    'operator' => '==',
                    'value' => 'edit_user',
                ),
                array(
                    'field' => 'user_to_edit',
                    'operator' => '==',
                    'value' => 'select_user',
                ),
            ),
        ),
        'role' => '',
        'allow_null' => 0,
        'multiple' => 0,
        'return_format' => 'object',
        'ui' => 1,
    ),

);