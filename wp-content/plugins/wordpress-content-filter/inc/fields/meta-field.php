<?php
/**
 * @version    $Id$
 * @package    WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

function wcf_register_field_meta_field() {

	$meta_keys = array();

	if (is_admin()) {

		$metas = wcf_get_all_meta_keys();
		if (is_array($metas) && !empty($metas)) {
			$meta_keys[''] = esc_html__('Select a meta key', 'wcf');
			foreach ( $metas as $key_value ) {
				$meta_keys[$key_value] = $key_value;
			}
		}

	}

	$options = array(
		'frontend_callback' => 'wcf_forms_field_meta_frontend',
		'admin_options' => array(
			array(
				'type' => 'select',
				'name' => 'meta_key',
				'options' => $meta_keys,
				'value' => '',
				'label' => esc_html__( 'Meta Key', 'wcf' ),
				'class' => '',
				'desc' => esc_html__( 'Select a meta key you prefer, the options below will be generated values base on this meta key', 'wcf' ),
			),
			array(
				'type' => 'text',
				'name' => 'label',
				'value' => '',
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
				'type' => 'radio',
				'label' => esc_html__( 'Hide Select All', 'wcf' ),
				'name' => 'hide_all',
				'value' => 'no',
				'options' => array(
					'yes' => esc_html__('Yes', 'wcf'),
					'no' => esc_html__('No', 'wcf'),
				),
				'class' => '',
				'desc' => esc_html__('Hide select all option', 'wcf'),
			),
			array(
				'type' => 'radio',
				'label' => esc_html__( 'Filter Select All', 'wcf' ),
				'name' => 'filter_select_all',
				'value' => 'no',
				'options' => array(
					'yes' => esc_html__('Yes', 'wcf'),
					'no' => esc_html__('No', 'wcf'),
				),
				'class' => '',
				'desc' => esc_html__('Filter posts for Select All by default', 'wcf'),
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
				'name' => 'compare',
				'value' => '=',
				'options' => array(
					'=' => esc_html__('=' , 'wcf'),
					'!=' => esc_html__('!=' , 'wcf'),
					'>' => esc_html__('>', 'wcf'),
					'>=' => esc_html__('>=', 'wcf'),
					'<' => esc_html__('<', 'wcf'),
					'<=' => esc_html__('<=', 'wcf'),
					'like' => esc_html__('LIKE', 'wcf'),
					'not_like' => esc_html__('NOT LIKE', 'wcf'),
					'in' => esc_html__('IN', 'wcf'),
					'not_in' => esc_html__('NOT IN', 'wcf'),
					'between' => esc_html__('BETWEEN', 'wcf'),
					'not_between' => esc_html__('NOT BETWEEN', 'wcf'),
					'exist' => esc_html__('EXIST', 'wcf'),
					'not_exist' => esc_html__('NOT EXIST', 'wcf'),
				),
				'label' => esc_html__( 'Compare', 'wcf' ),
				'class' => '',
				'desc' => esc_html__('The \'range options\' (see the options below) only use for BETWEEN, NOT BETWEEN and the Display Type is not CheckBox, Multiselect .<br>
if you select IN, NOT IN then make sure the Display Type must be CheckBox, Multiselect <br>
if you select =, !=, >, >=, <, <=, LIKE, NOT LIKE then make sure the field type must be Select, Radio', 'wcf'),
			),
			array(
				'type' => 'select',
				'label' => esc_html__( 'Display Type', 'wcf' ),
				'name' => 'display_type',
				'value' => 'dropdown',
				'options' => array(
					'select' => esc_html__('Select', 'wcf'),
					'radio' => esc_html__('Radio', 'wcf'),
					'checkbox' => esc_html__('CheckBox', 'wcf'),
					'multiselect' => esc_html__('Multiselect', 'wcf'),
				),
				'class' => '',
				'desc' => '',
			),
			array(
				'type' => 'radio',
				'label' => esc_html__( 'Radio/Checkbox Layout', 'wcf' ),
				'name' => 'radio_checkbox_layout',
				'value' => 'vertical',
				'options' => array(
					'vertical' => esc_html__('Vertical', 'wcf'),
					'horizontal' => esc_html__('Horizontal', 'wcf'),
				),
				'class' => '',
				'desc' => '',
			),
			array(
				'type' => 'textarea',
				'name' => 'options',
				'value' => '',
				'label' => esc_html__( 'Options', 'wcf' ),
				'class' => '',
				'desc' => esc_html__('Enter each option on a new line. You define both a value and label like this: <br> value1::Label1 <br> value2::Label2.
<br> ==== <br> for normal options <br>50::50<br>100::100 <br> for range option <br>50-100::50 - 100<br>100-200::100 - 200', 'wcf'),
			),
			array(
				'type' => 'textarea',
				'name' => 'default_value',
				'value' => '',
				'label' => esc_html__( 'Default Value', 'wcf' ),
				'class' => '',
				'desc' => esc_html__('Enter a default value for dropdown or radio type. Otherwise <br>
Enter each default value on a new line for CheckBox or Multiple Select type. <br> Enter \'all\' for all values (when Hide Select All is No)', 'wcf'),
			),
		),
	);

	wcf_register_field( 'meta_field', esc_html__('Meta Field', 'wcf' ), $options );
}

add_action( 'init', 'wcf_register_field_meta_field' );

function wcf_forms_field_meta_frontend($form_id = '', $field_id, $data = array() ) {


	$first_name = 'wcf_meta_field';
	$pre_name = $first_name . "[".$field_id."]";
	$intro = isset($data['intro']) ? 'data-tooltip="'.$data['intro'].'"' : '';

	$value_all = 'all';
	if ($data['filter_select_all'] == 'no') {
		$value_all = '';
	}
	if ($data['meta_key'] == '') { echo esc_html__('Please, select a meta key', 'wcf'); return;}

	$opts = explode("\n", $data['options']);
	$options = array();
	if (!empty($opts)) {

		if ($data['hide_all'] == 'no') {
			$options[$value_all] = ($data['change_all_label'] != '') ? $data['change_all_label'] : esc_html__('All', 'wcf');
		}

		foreach ( $opts as $opt ) {
			$opt_tem                = explode( '::', $opt );
			$options[ $opt_tem[0] ] = $opt_tem[1];
		}
	}
	$label_class = 'wcf-label';
	$radio_checkbox_layout = 'wcf-'.$data['radio_checkbox_layout'];

	$field_value = apply_filters('wcf_selected_value', $data['default_value'], $first_name, $field_id);

	$data_reset = str_replace(array("\r"), "", $data['default_value']) ;
	$data_reset = str_replace(array("\n"), "|", $data_reset) ;

	switch ($data['display_type']) {
		case 'select':
			$field_args = array(
				'type' => 'select',
				'name' => $pre_name,
				'id' => $pre_name . '_select',
				'value' => $field_value,
				'options' => $options,
				'label' => $data['label'],
				'class' => '',
				'label_class' => $label_class,
				'label_attr' => $intro,
				'desc' => '',
				'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-display="'.$data['display_type'].'" data-reset="'.$data_reset.'"',
				'before_html' => '<div class="wcf-field-body">',
				'after_html' => '</div>',
			);
			wcf_forms_field($field_args);
			break;
		case 'multiselect':


			if (!is_array($field_value)) {
				$field_value = explode("\n", $field_value);
			}

			$field_args = array(
				'type' => 'multiple',
				'name' => $pre_name . '[]',
				'id' => $pre_name . '_select',
				'value' => $field_value,
				'options' => $options,
				'label' => $data['label'],
				'class' => '',
				'label_class' => $label_class,
				'label_attr' => $intro,
				'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-display="'.$data['display_type'].'" data-reset="'.$data_reset.'"',
				'before_html' => '<div class="wcf-field-body">',
				'after_html' => '</div>',
			);

			wcf_forms_field($field_args);
			break;
		case 'radio':

			$field_args = array(
				'type' => 'radio',
				'name' => $pre_name ,
				'id' => $pre_name . '_radio',
				'value' => $field_value,
				'options' => $options,
				'label' => $data['label'],
				'label_class' => $label_class,
				'label_attr' => $intro,
				'wrapper_class' => $radio_checkbox_layout,
				'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-display="'.$data['display_type'].'" data-reset="'.$data_reset.'"',
				'before_html' => '<div class="wcf-field-body">',
				'after_html' => '</div>',
			);

			wcf_forms_field($field_args);
			break;
		case 'checkbox':

			if (!is_array($field_value)) {
				$field_value = explode("\n", $field_value);
			}

			$pre_html = '';
			if ($data['hide_all'] == 'no') {

				$all_checked = in_array('all', $field_value) ?'checked="checked"' : '';
				$pre_html = '<div class="wcf-checkbox-wrapper"><input type="checkbox" value="'.$value_all.'" name="'.$pre_name . '[]'.'" class="wcf-checkbox-all" '.$all_checked.'><label class="wcf-checkbox-label"> '.$options[$value_all].'</label></div>';
				unset($options[$value_all]);
				if ($all_checked != '') {
					$field_value = array();
					foreach ( $options as $key => $default ) {
						$field_value[] = $key;
					}
				}
			}

			$field_args = array(
				'type' => 'checkbox',
				'name' => $pre_name . '[]' ,
				'id' => $pre_name . '_checkbox',
				'value' => $field_value,
				'options' => $options,
				'label' => $data['label'],
				'label_class' => $label_class,
				'label_attr' => $intro,
				'class' => 'wcf-checkbox-item',
				'wrapper_class' => $radio_checkbox_layout,
				'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-display="'.$data['display_type'].'" data-reset="'.$data_reset.'"',
				'before_html' => '<div class="wcf-field-body">' . $pre_html,
				'after_html' => '</div>',
			);

			wcf_forms_field($field_args);
			break;
	}


}