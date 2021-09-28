<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

return array(		
    array(
        'key' => 'save_to_term',
        'field_label_hide' => 0,
        'type' => 'select',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'choices' => array(
            'edit_term' => __( 'Edit Term', 'acf-frontend-form-element' ),
            'new_term' => __( 'New Term', 'acf-frontend-form-element' ),
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
        'key' => 'new_term_taxonomy',
        'label' => __( 'Taxonomy', 'acf-frontend-form-element' ),
        'type' => 'select',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'save_to_term',
                    'operator' => '==',
                    'value' => 'new_term',
                ),
            ),
        ),
        'choices' => acf_get_taxonomy_labels(),
        'default_value' => 'category',
        'allow_null' => 0,
        'multiple' => 0,
        'ui' => 0,
        'return_format' => 'value',
        'ajax' => 0,
        'placeholder' => '',
    ),
    array(
        'key' => 'term_to_edit',
        'label' => __( 'Term to Edit', 'acf-frontend-form-element' ),
        'type' => 'select',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'save_to_term',
                    'operator' => '==',
                    'value' => 'edit_term',
                ),
            ),
        ),
        'choices' => array(
            'current_term' => __( 'Current Term', 'acf-frontend-form-element' ),
            'url_query' => __( 'URL Query', 'acf-frontend-form-element' ),
            'select_term' => __( 'Select Term', 'acf-frontend-form-element' ),
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
        'key' => 'url_query_term',
        'label' => __( 'URL Query Key', 'acf-frontend-form-element' ),
        'type' => 'text',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'save_to_term',
                    'operator' => '==',
                    'value' => 'edit_term',
                ),
                array(
                    'field' => 'term_to_edit',
                    'operator' => '==',
                    'value' => 'url_query',
                ),
            ),
        ),
        'placeholder' => '',
    ),
    array(
        'key' => 'select_term',
        'label' => __( 'Select Term', 'acf-frontend-form-element' ),
        'name' => 'select_term',
        'type' => 'select',
        'prefix' => 'form',
        'instructions' => '',
        'required' => 0,
        'choices' => acf_get_taxonomy_terms(),
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'save_to_term',
                    'operator' => '==',
                    'value' => 'edit_term',
                ),
                array(
                    'field' => 'term_to_edit',
                    'operator' => '==',
                    'value' => 'select_term',
                ),
            ),
        ),
        'allow_null' => 0,
        'multiple' => 0,
        'ui' => 1,
    ),

);