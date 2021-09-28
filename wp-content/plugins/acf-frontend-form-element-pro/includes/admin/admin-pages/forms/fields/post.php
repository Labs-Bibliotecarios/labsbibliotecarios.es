<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

return array(		
    array(
        'key' => 'save_to_post',
        'field_label_hide' => 0,
        'type' => 'select',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'choices' => array(
            'edit_post' => __( 'Edit Post', 'acf-frontend-form-element' ),
            'new_post' => __( 'New Post', 'acf-frontend-form-element' ),
        ),
        'value' => $form['main_action'],
        'allow_null' => 0,
        'multiple' => 0,
        'ui' => 0,
        'return_format' => 'value',
        'ajax' => 0,
        'placeholder' => '',
    ),	
    array(
        'key' => 'new_post_type',
        'label' => __( 'Post Type', 'acf-frontend-form-element' ),
        'type' => 'select',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'save_to_post',
                    'operator' => '==',
                    'value' => 'new_post',
                ),
            ),
        ),
        'choices' => acf_get_pretty_post_types(),
        'default_value' => false,
        'allow_null' => 0,
        'multiple' => 0,
        'ui' => 0,
        'return_format' => 'value',
        'ajax' => 0,
        'placeholder' => '',
    ),
    array(
        'key' => 'post_to_edit',
        'label' => __( 'Post to Edit', 'acf-frontend-form-element' ),
        'type' => 'select',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'save_to_post',
                    'operator' => '==',
                    'value' => 'edit_post',
                ),
            ),
        ),
        'choices' => array(
            'current_post' => __( 'Current Post', 'acf-frontend-form-element' ),
            'url_query' => __( 'URL Query', 'acf-frontend-form-element' ),
            'select_post' => __( 'Select Post', 'acf-frontend-form-element' ),
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
        'key' => 'url_query_post',
        'label' => __( 'URL Query Key', 'acf-frontend-form-element' ),
        'type' => 'text',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'save_to_post',
                    'operator' => '==',
                    'value' => 'edit_post',
                ),
                array(
                    'field' => 'post_to_edit',
                    'operator' => '==',
                    'value' => 'url_query',
                ),
            ),
        ),
        'placeholder' => '',
    ),
    array(
        'key' => 'select_post',
        'label' => __( 'Select Post', 'acf-frontend-form-element' ),
        'name' => 'select_post',
        'prefix' => 'form',
        'type' => 'post_object',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'save_to_post',
                    'operator' => '==',
                    'value' => 'edit_post',
                ),
                array(
                    'field' => 'post_to_edit',
                    'operator' => '==',
                    'value' => 'select_post',
                ),
            ),
        ),
        'post_type' => '',
        'taxonomy' => '',
        'allow_null' => 0,
        'multiple' => 0,
        'return_format' => 'object',
        'ui' => 1,
    ),

);