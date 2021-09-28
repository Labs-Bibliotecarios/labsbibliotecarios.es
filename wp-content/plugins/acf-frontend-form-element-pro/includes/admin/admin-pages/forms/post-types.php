<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( function_exists('register_post_type') ):
    $labels = array(
        'name'                  => _x( 'Forms', 'Post Type General Name', 'acf-frontend-form-element' ),
        'singular_name'         => _x( 'Form', 'Post Type Singular Name', 'acf-frontend-form-element' ),
        'menu_name'             => __( 'Forms', 'acf-frontend-form-element' ),
        'name_admin_bar'        => __( 'Form', 'acf-frontend-form-element' ),
        'archives'              => __( 'Form Archives', 'acf-frontend-form-element' ),
        'all_items'             => __( 'Forms', 'acf-frontend-form-element' ),
        'add_new_item'          => __( 'Add New Form', 'acf-frontend-form-element' ),
        'add_new'               => __( 'Add New', 'acf-frontend-form-element' ),
        'new_item'              => __( 'New Form', 'acf-frontend-form-element' ),
        'edit_item'             => __( 'Edit Form', 'acf-frontend-form-element' ),
        'update_item'           => __( 'Update Form', 'acf-frontend-form-element' ),
        'view_item'             => __( 'View Form', 'acf-frontend-form-element' ),
        'search_items'          => __( 'Search Form', 'acf-frontend-form-element' ),
        'not_found'             => __( 'Not found', 'acf-frontend-form-element' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'acf-frontend-form-element' ),
        'items_list'            => __( 'Forms list', 'acf-frontend-form-element' ),
        'items_list_navigation' => __( 'Forms list navigation', 'acf-frontend-form-element' ),
        'filter_items_list'     => __( 'Filter forms list', 'acf-frontend-form-element' ),
    );
    $args = array(
        'label'                 => __( 'Form', 'acf-frontend-form-element' ),
        'description'           => __( 'Form', 'acf-frontend-form-element' ),
        'labels'                => $labels,
        'supports'              => array( 'title' ),
        'show_in_rest'          => true,
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => 'acff-settings',
        'menu_position'         => 80,
        'show_in_admin_bar'     => false,
        'can_export'            => true,
        'rewrite'               => false,
        'capability_type'       => 'page',
        'query_var'				=> false,
    );
    register_post_type( 'acf_frontend_form', $args );

    $labels = array(
        'name'                  => _x( 'Submissions', 'Post Type General Name', 'acf-frontend-form-element' ),
        'singular_name'         => _x( 'Submission', 'Post Type Singular Name', 'acf-frontend-form-element' ),
        'menu_name'             => __( 'Submissions', 'acf-frontend-form-element' ),
        'name_admin_bar'        => __( 'Submission', 'acf-frontend-form-element' ),
        'archives'              => __( 'Submission Archives', 'acf-frontend-form-element' ),
        'all_items'             => __( 'Submissions', 'acf-frontend-form-element' ),
        'add_new_item'          => __( 'Add New Submission', 'acf-frontend-form-element' ),
        'add_new'               => __( 'Add New', 'acf-frontend-form-element' ),
        'new_item'              => __( 'New Submission', 'acf-frontend-form-element' ),
        'edit_item'             => __( 'Edit Submission', 'acf-frontend-form-element' ),
        'update_item'           => __( 'Update Submission', 'acf-frontend-form-element' ),
        'view_item'             => __( 'View Submission', 'acf-frontend-form-element' ),
        'search_items'          => __( 'Search Submission', 'acf-frontend-form-element' ),
        'not_found'             => __( 'Not found', 'acf-frontend-form-element' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'acf-frontend-form-element' ),
        'items_list'            => __( 'Submissions list', 'acf-frontend-form-element' ),
        'items_list_navigation' => __( 'Submissions list navigation', 'acf-frontend-form-element' ),
        'filter_items_list'     => __( 'Filter forms list', 'acf-frontend-form-element' ),
    );
    $args = array(
        'label'                 => __( 'Submissions', 'acf-frontend-form-element' ),
        'labels'                => $labels,
        'supports'              => array(),
        'show_in_rest'          => false,
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => false,//'acff-settings',
        'menu_position'         => 81,
        'show_in_admin_bar'     => false,
        'can_export'            => false,
        'rewrite'               => false,
        'capability_type'       => 'page',
        'query_var'				=> false,
    );
    register_post_type( 'acf_form_record', $args );

    do_action( 'acf_frontend_post_types' );

endif;