<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$data_types = array(
    'post' => __( 'Post', 'acf-frontend-form-element' ),
    'user' => __( 'User', 'acf-frontend-form-element' ),
    'term' => __( 'Term', 'acf-frontend-form-element' ),
    'options' => __( 'Site Options', 'acf-frontend-form-element' ),
);
if ( class_exists( 'woocommerce' ) ){
    $data_types['product'] = __( 'Product', 'acf-frontend-form-element' );
}
return array(		
    array(
        'key' => 'custom_fields_save',
        'label' => __( 'Save Custom Fields to...', 'acf-frontend-form-element' ),
        'field_label_hide' => 0,
        'type' => 'select',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'choices' => $data_types,
        'allow_null' => 0,
        'multiple' => 0,
        'ui' => 0,
        'return_format' => 'value',
        'ajax' => 0,
        'placeholder' => '',
        'wrapper' => array(
            'width' => '50',
            'class' =>'',
            'id' => ''
        )
    ),
    array(
        'key' => 'no_kses',
        'label' => __( 'Allow Unfiltered HTML', 'acf-frontend-form-element' ),
        'field_label_hide' => 0,
        'type' => 'true_false',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'ui' => 1,
        'wrapper' => array(
            'width' => '50',
            'class' =>'',
            'id' => ''
        )
    ),
);    