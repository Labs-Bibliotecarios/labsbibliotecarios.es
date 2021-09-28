<?php
/**
 * @version    $Id$
 * @package    WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

function wcf_register_acf_field() {


	$fields = array();

	if (is_admin()) {
		$args = array(
			'posts_per_page' => -1,
			'post_type'      => 'acf-field-group',
		);
		$field_types = apply_filters('wcf_acf_field_type', array('text','textarea','select','checkbox','radio','true_false' ));

		$groups = get_posts( $args );
		if ($groups) {
			foreach ( $groups as $group ) {
                $field_group_fields = acf_get_fields($group->ID);
				if (is_array($field_group_fields)) {
					$fields[''] = esc_html__('Select a Field', 'wcf');
					foreach ( $field_group_fields as $field ) {
						if (in_array($field['type'], $field_types)) {
							$fields[$group->post_title][$field['key']] = $field['label'] .' - ' .$field['type'];
						}
					}
				}
			}
		}

	}

	$options = array(
		'frontend_callback' => 'wcf_forms_acf_frontend',
		'before_admin_options_desc' => esc_html__('This field only support some field types from Advanced Custom Fields if field type is Text, Text Area, Select/Multiselect, Checkbox, Radio Button, True/False : ', 'wcf'),
		'admin_options' => array(

			array(
				'type' => 'select',
				'name' => 'field',
				'options' => $fields,
				'value' => '',
				'label' => esc_html__( 'Field', 'wcf' ),
				'class' => '',
				'desc' => esc_html__( 'Select a field of Advanced Custom Fields', 'wcf' ),
				'extra_attr' => '',
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
				'desc' => esc_html__('The \'range choices\' only use for BETWEEN, NOT BETWEEN and the field type is not Checkbox, Multiselect .<br>
if you select IN, NOT IN then make sure the field type must be Checkbox, Multiselect <br>
if you select =, !=, >, >=, <, <=, LIKE, NOT LIKE then make sure the field type must be Text, Text Area, Select, Radio Button,True/False
<br> ==== <br> for normal choices <br>50 : 50<br>100 : 100 <br> for range choices <br>50-100 : 50 - 100<br>100-200 : 100 - 200', 'wcf'),
			),
		),
	);

	wcf_register_field( 'acf', esc_html__( 'Advanced Custom Fields', 'wcf' ), $options );
}

add_action( 'init', 'wcf_register_acf_field' );

function wcf_forms_acf_frontend($form_id = '', $field_id, $data = array() ) {


	if ($data['field'] == '') { echo esc_html__('Please, select a field', 'wcf'); return;}

	$field = acf_get_field($data['field'] );
	$value_all = 'all';
	if ($data['filter_select_all'] == 'no') {
		$value_all = '';
	}

	$options = array();
	if (isset($field['choices'])) {
		if ($data['hide_all'] == 'no') {
			$options[$value_all] = ($data['change_all_label'] != '') ? $data['change_all_label'] : esc_html__('All', 'wcf');

		}
		$options = array_merge($options, $field['choices']);
	}

	$first_name = 'wcf_acf';
	$pre_name = $first_name . "[".$field_id."]";
	$intro = isset($data['intro']) ? 'data-tooltip="'.$data['intro'].'"' : '';

	$label_class = 'wcf-label';
	$radio_checkbox_layout = 'wcf-horizontal';

	$field_value = apply_filters('wcf_selected_value', $field['default_value'], $first_name, $field_id);
    if (is_array($field['default_value'])) {
        $data_reset = implode("|", $field['default_value']);
    } else {
        $data_reset = str_replace(array("\r"), "", $field['default_value']) ;
        $data_reset = str_replace(array("\n"), "|", $data_reset) ;
    }


	switch ($field['type']) {
		case 'select':
			$field_type = isset($field['multiple']) && $field['multiple'] ? 'multiple' : 'select';
			$display_type = $field_type == 'multiple' ? 'multiselect' : 'select';

			if (!is_array($field_value)) {
				$field_value = explode("\n", $field_value);
			}

			$field_args = array(
				'type' => $field_type,
				'name' => $pre_name,
				'id' => $pre_name . '_select',
				'value' => $field_value,
				'options' => $options,
				'label' => $data['label'],
				'label_attr' => $intro,
				'class' => '',
				'label_class' => $label_class,
				'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-display="'.$display_type.'" data-reset="'.$data_reset.'"',
				'before_html' => '<div class="wcf-field-body">',
				'after_html' => '</div>',
				'desc' => '',
			);
			wcf_forms_field($field_args);
			break;

		case 'radio':

			if ($field['layout'] == 'vertical') {
				$radio_checkbox_layout = 'wcf-vertical';
			}

			$field_args = array(
				'type' => 'radio',
				'name' => $pre_name ,
				'id' => $pre_name . '_radio',
				'value' => $field_value,
				'options' => $options,
				'label' => $data['label'],
				'label_attr' => $intro,
				'label_class' => $label_class,
				'wrapper_class' => $radio_checkbox_layout,
				'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-display="'.$data['type'].'" data-reset="'.$data_reset.'"',
				'before_html' => '<div class="wcf-field-body">',
				'after_html' => '</div>',
			);

			wcf_forms_field($field_args);
			break;
		case 'checkbox':

			if (!is_array($field_value)) {
				$field_value = explode("\n", $field_value);
			}

			if ($field['layout'] == 'vertical') {
				$radio_checkbox_layout = 'wcf-vertical';
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
				'label_attr' => $intro,
				'label_class' => $label_class,
				'class' => 'wcf-checkbox-item',
				'wrapper_class' => $radio_checkbox_layout,
				'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-display="'.$data['type'].'" data-reset="'.$data_reset.'"',
				'before_html' => '<div class="wcf-field-body">' . $pre_html,
				'after_html' => '</div>',
			);

			wcf_forms_field($field_args);
			break;
		case 'text':
			$field_args = array(
				'type' => 'text',
				'name' => $pre_name ,
				'id' => $pre_name . '_text',
				'value' => $field_value,
				'label' => $data['label'],
				'label_attr' => $intro,
				'label_class' => $label_class,
				'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-display="'.$data['type'].'" data-reset="'.$data_reset.'"',
				'before_html' => '<div class="wcf-field-body">',
				'after_html' => '</div>',
			);

			wcf_forms_field($field_args);
			break;
		case 'textarea':

			$field_args = array(
				'type' => 'textarea',
				'name' => $pre_name ,
				'id' => $pre_name . '_text',
				'value' => $field_value,
				'label' => $data['label'],
				'label_class' => $label_class,
				'label_attr' => $intro,
				'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-display="'.$data['type'].'" data-reset="'.$field['default_value'].'"',
				'before_html' => '<div class="wcf-field-body">',
				'after_html' => '</div>',
			);

			wcf_forms_field($field_args);
			break;
	}

}