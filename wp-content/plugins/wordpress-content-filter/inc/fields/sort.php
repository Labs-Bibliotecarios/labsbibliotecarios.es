<?php
/**
 * @version    $Id$
 * @package    WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

function wcf_register_field_sort_field() {

	$order_by_options = apply_filters('wcf_order_by_options', array(
		'date' => esc_html__('Date', 'wcf'),
		'title' => esc_html__('Title', 'wcf'),
		'name' => esc_html__('Name', 'wcf'),
		'type' => esc_html__('Post Type', 'wcf'),
		'author' => esc_html__('Author', 'wcf'),
		'modified' => esc_html__('Modified Date', 'wcf'),
		'parent' => esc_html__('Post Parent ID', 'wcf'),
		'rand' => esc_html__('Random order', 'wcf'),
	));

	$options = array(
		'frontend_callback' => 'wcf_forms_field_sort_frontend',
		'admin_options' => array(
			array(
				'type' => 'text',
				'name' => 'label_order_by',
				'value' => 'Order By',
				'label' => esc_html__( 'Label Order By', 'wcf' ),
				'class' => '',
				'desc' => esc_html__( 'Display field label at the frontend, leave blank to hide', 'wcf' ),
			),
			array(
				'type' => 'textarea',
				'name' => 'intro_order_by',
				'value' => '',
				'label' => esc_html__( 'Intro Field for Order By', 'wcf' ),
				'class' => '',
				'desc' => esc_html__('An introduction field on tooltip when hovering on label, leave blank to hide', 'wcf'),
			),

			array(
				'type' => 'text',
				'name' => 'label_order',
				'value' => 'Sorting Order',
				'label' => esc_html__( 'Label Order', 'wcf' ),
				'class' => '',
				'desc' => esc_html__( 'Display field label at the frontend, leave blank to hide', 'wcf' ),
			),

			array(
				'type' => 'textarea',
				'name' => 'intro_order',
				'value' => '',
				'label' => esc_html__( 'Intro Field for Order', 'wcf' ),
				'class' => '',
				'desc' => esc_html__('An introduction field on tooltip when hovering on label, leave blank to hide', 'wcf'),
			),

			array(
				'type' => 'select',
				'label' => esc_html__( 'Order By', 'wcf' ),
				'name' => 'order_by',
				'value' => 'date',
				'options' => $order_by_options,
				'class' => '',
				'desc' => '',
			),
			array(
				'type' => 'select',
				'label' => esc_html__( 'Sorting Order', 'wcf' ),
				'name' => 'sorting_order',
				'value' => 'DESC',
				'options' => array(
					'DESC' => esc_html__('Descending', 'wcf'),
					'ASC' => esc_html__('Ascending', 'wcf')
				),
				'class' => '',
				'desc' => '',
			),

		),
		'order_by_options' => $order_by_options
	);

	wcf_register_field( 'sort', esc_html__( 'Sort', 'wcf' ), $options );
}

add_action( 'init', 'wcf_register_field_sort_field' );


function wcf_forms_field_sort_frontend($form_id = '', $field_id, $data = array() ) {

	global $wcf_register_fields;
	$order_by_options = $wcf_register_fields['sort']['options']['order_by_options'];
	$label_class = 'wcf-label';
	$first_name = 'wcf_sort';
	$pre_name = $first_name . "[".$field_id."]";

	$label_order_by = isset($data['label_order_by']) ? $data['label_order_by']  : '';
	$intro_order_by = isset($data['intro_order_by']) ? 'data-tooltip="'.$data['intro_order_by'].'"' : '';
	$label_order = isset($data['label_order']) ? $data['label_order']  : '';
	$intro_order = isset($data['intro_order']) ? 'data-tooltip="'.$data['intro_order'].'"' : '';

	$field_value = apply_filters('wcf_selected_value', array('order_by' => $data['order_by'], 'order' => $data['sorting_order']), $first_name, $field_id);

	$order_by = array(
		'type' => 'select',
		'name' => $pre_name . '[order_by]',
		'value' => $field_value['order_by'],
		'options' => $order_by_options,
		'label' => $label_order_by,
		'label_class' => $label_class,
		'label_attr' => $intro_order_by,
		'class' => '',
		'desc' => '',
		'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-reset="'.$data['order_by'].'"',
		'before_html' => '<div class="wcf-field-body">',
		'after_html' => '</div>',
	);

	wcf_forms_field($order_by);

	$order = array(
		'type' => 'select',
		'name' => $pre_name . '[order]',
		'value' => $field_value['order'],
		'options' => array(
			'DESC' => esc_html__('Descending', 'wcf'),
			'ASC' => esc_html__('Ascending', 'wcf')
		),
		'label' => $label_order,
		'label_class' => $label_class,
		'label_attr' => $intro_order,
		'class' => '',
		'desc' => '',
		'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-reset="'.$data['sorting_order'].'"',
		'before_html' => '<div class="wcf-field-body">',
		'after_html' => '</div>',
	);

	wcf_forms_field($order);

}