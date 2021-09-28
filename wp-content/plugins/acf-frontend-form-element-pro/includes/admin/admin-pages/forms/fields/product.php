<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

return array(		
    array(
        'key' => 'save_to_product',
        'field_label_hide' => 0,
        'type' => 'select',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'choices' => array(
            'edit_product' => __( 'Edit Product', 'acf-frontend-form-element' ),
            'new_product' => __( 'New Product', 'acf-frontend-form-element' ),
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
        'key' => 'product_to_edit',
        'label' => __( 'Product to Edit', 'acf-frontend-form-element' ),
        'type' => 'select',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'save_to_product',
                    'operator' => '==',
                    'value' => 'edit_product',
                ),
            ),
        ),
        'choices' => array(
            'current_product' => __( 'Current Product', 'acf-frontend-form-element' ),
            'url_query' => __( 'URL Query', 'acf-frontend-form-element' ),
            'select_product' => __( 'Select Product', 'acf-frontend-form-element' ),
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
        'key' => 'url_query_product',
        'label' => __( 'URL Query Key', 'acf-frontend-form-element' ),
        'type' => 'text',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'save_to_product',
                    'operator' => '==',
                    'value' => 'edit_product',
                ),
                array(
                    'field' => 'product_to_edit',
                    'operator' => '==',
                    'value' => 'url_query',
                ),
            ),
        ),
        'placeholder' => '',
    ),
    array(
        'key' => 'select_product',
        'label' => __( 'Select Product', 'acf-frontend-form-element' ),
        'name' => 'select_product',
        'prefix' => 'form',
        'type' => 'post_object',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'save_to_product',
                    'operator' => '==',
                    'value' => 'edit_product',
                ),
                array(
                    'field' => 'product_to_edit',
                    'operator' => '==',
                    'value' => 'select_product',
                ),
            ),
        ),
        'post_type' => 'product',
        'taxonomy' => '',
        'allow_null' => 0,
        'multiple' => 0,
        'return_format' => 'object',
        'ui' => 1,
    ),

);