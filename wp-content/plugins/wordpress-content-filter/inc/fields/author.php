<?php
/**
 * @version    $Id$
 * @package    WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

function wcf_register_field_author_field() {
	global $wp_roles;

	$roles = $wp_roles->roles;

	if ( !empty($roles)) {
		foreach ( $roles as $key => $role ) {
			$roles_options[ $key ] = $role['name'];
		}
	}

	$query_args = array();
	$query_args['fields'] = array( 'ID', 'display_name' );
	$query_args['number'] = '-1';
	$users_options = array();
	$users = get_users( $query_args);

	$users_options = array('' => esc_html__('Select an author')) + $users_options;
	foreach ( $users  as $user ) {
		$ob = get_userdata( $user->ID );
		$role_text = implode(',', $ob->roles);
		$users_options[$role_text][$user->ID] = $user->display_name;
	}

	$options = array(
		'frontend_callback' => 'wcf_forms_field_author_frontend',
		'admin_options' => array(
			array(
				'type' => 'text',
				'name' => 'label',
				'value' => 'Author',
				'label' => esc_html__( 'Label', 'wcf' ),
				'class' => '',
				'desc' => esc_html__( 'Display field label at the frontend, leave blank to hide', 'wcf' ),
			),
			array(
				'type' => 'textarea',
				'name' => 'intro',
				'value' => '',
				'label' => esc_html__( 'Intro Field', 'wcf' ),
				'class' => '',
				'desc' => esc_html__('An introduction field on tooltip when hovering on label, leave blank to hide', 'wcf'),
			),
			array(
				'type' => 'text',
				'name' => 'change_all_label',
				'value' => '',
				'label' => esc_html__( 'Change All Label', 'wcf' ),
				'class' => '',
				'desc' => esc_html__( 'Change select all items label', 'wcf' ),
			),
			array(
				'type' => 'select',
				'name' => 'role',
				'value' => 'author',
				'options' => $roles_options,
				'label' => esc_html__( 'Show author by role', 'wcf' ),
				'class' => '',
				'desc' => '',
			),
			array(
				'type' => 'select',
				'name' => 'default_value',
				'value' => '',
				'options' => $users_options,
				'label' => esc_html__( 'Select default author', 'wcf' ),
				'class' => '',
				'desc' => '',
			),

		),
	);

	wcf_register_field( 'author', esc_html__( 'Author', 'wcf' ), $options );
}

add_action( 'init', 'wcf_register_field_author_field' );


function wcf_forms_field_author_frontend($form_id = '', $field_id, $data = array() ) {

	global $wcf_register_fields;

	$role = isset($data['role']) ? $data['role'] : 'author';
	$default_value = isset($data['default_value']) ? $data['default_value'] : '';
	$query_args = array();
	$query_args['fields'] = array( 'ID', 'display_name' );
	$query_args['role'] = apply_filters('wcf_author_field_role', $role);
	$users_options = array();
	$users = get_users( $query_args);

	foreach ( $users  as $user ) {
		$users_options[$user->ID] = $user->display_name;
	}

	$users = $users_options;

	if (count($users)) {

		$all_label = $data['change_all_label'] != '' ? $data['change_all_label'] : esc_html__('All', 'wcf');

		$users = array('all' => $all_label) + $users;

		$first_name = 'wcf_author';
		$pre_name = $first_name . "[".$field_id."]";
		$intro = isset($data['intro']) ? 'data-tooltip="'.$data['intro'].'"' : '';

		$field_value = apply_filters('wcf_selected_value', $default_value, $first_name, $field_id);

		$author_options = array(
			'type' => 'select',
			'name' => $pre_name,
			'value' => $field_value,
			'options' => $users,
			'label' => $data['label'],
			'class' => '',
			'label_class' => 'wcf-label',
			'label_attr' => $intro,
			'desc' => '',
			'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-reset="all"',
			'before_html' => '<div class="wcf-field-body">',
			'after_html' => '</div>',
		);
		wcf_forms_field($author_options);
	} else {
		echo esc_html__('Not Found any users with the role is Author', 'wcf' );
	}

}