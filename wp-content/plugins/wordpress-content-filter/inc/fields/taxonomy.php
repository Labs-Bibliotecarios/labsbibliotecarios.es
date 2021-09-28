<?php
/**
 * @version    $Id$
 * @package    WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

function wcf_register_field_taxonomy_field() {

	$taxonomy_options = array();

	if (is_admin()) {

		$taxonomies = get_taxonomies( array(), 'objects' );

		if (is_array($taxonomies) && !empty($taxonomies)) {
			$taxonomy_options[''] = esc_html__('Select a taxonomy', 'wcf');
			foreach ( $taxonomies as $key_value ) {
				$taxonomy_options[$key_value->name] = $key_value->label;
			}
		}

	}


	$options = array(
		'frontend_callback' => 'wcf_forms_field_taxonomy_frontend',
		'admin_options' => array(
			array(
				'type' => 'select',
				'name' => 'taxonomy',
				'options' => $taxonomy_options,
				'value' => '',
				'label' => esc_html__( 'Taxonomy', 'wcf' ),
				'class' => '',
				'desc' => esc_html__( 'Select a taxonomy you prefer', 'wcf' ),
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
				'label' => esc_html__( 'Display Type', 'wcf' ),
				'name' => 'display_type',
				'value' => 'dropdown',
				'options' => array(
					'select' => esc_html__('Select', 'wcf'),
					'radio' => esc_html__('Radio', 'wcf'),
					'checkbox' => esc_html__('CheckBox', 'wcf'),
					'multiselect' => esc_html__('Multiselect', 'wcf'),
					'color' => esc_html__('Color', 'wcf'),
				),
				'class' => '',
				'desc' => '',
			),
			array(
				'type' => 'terms_color',
				'label' => esc_html__( 'Terms Color', 'wcf' ),
				'name' => 'terms_color',
				'value' => array(),
				'class' => '',
				'desc' => esc_html__( 'There colors are generated base on Taxonomy when you select, Using this option for "Display Type" is Color', 'wcf' ),
				'after_html_field' => '<a href="#" class="button wcf-generate-terms-color-button">'.esc_html__('Generate terms color', 'wcf').'</a>',
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
				'type' => 'radio',
				'label' => esc_html__( 'Operator', 'wcf' ),
				'name' => 'taxonomy_operator',
				'value' => 'in',
				'options' => array(
					'in' => esc_html__('IN', 'wcf'),
					'not_in' => esc_html__('NOT IN', 'wcf'),
					'and' => esc_html__('AND', 'wcf'),
				),
				'class' => '',
				'desc' => esc_html__('Operator to test relationship between the taxonomy values, Only use for CheckBox, Multiselect, Color. Default is IN', 'wcf'),
			),

			array(
				'type' => 'textarea',
				'name' => 'default_value',
				'value' => '',
				'label' => esc_html__( 'Default Value', 'wcf' ),
				'class' => '',
				'desc' => esc_html__('Enter a default value for dropdown or radio type. Otherwise <br>
Enter each default value on a new line for CheckBox or Multiple or Color Select type. <br> Enter \'all\' for all values (when Hide Select All is No)', 'wcf'),
				'after_html_field' => '<a href="#TB_inline?width=400&height=400&inlineId=wcf-thickbox" class="thickbox button wcf-select-terms-button" data-type="default">'.esc_html__('Select Terms', 'wcf').'</a>',
			),

			array(
				'type' => 'radio',
				'label' => esc_html__( 'Hide Empty Terms?', 'wcf' ),
				'name' => 'hide_empty_terms',
				'value' => 'no',
				'options' => array(
					'yes' => esc_html__('Yes', 'wcf'),
					'no' => esc_html__('No', 'wcf'),
				),
				'class' => '',
				'desc' => esc_html__('Hide the terms that no posts assigned to', 'wcf'),
			),
			array(
				'type' => 'text',
				'label' => esc_html__( 'Exclude Terms', 'wcf' ),
				'name' => 'exclude_terms',
				'value' => '',
				'class' => '',
				'desc' => esc_html__('Exclude term IDs of Taxonomy, separated by commas', 'wcf'),
				'after_html_field' => '<a href="#TB_inline?width=400&height=400&inlineId=wcf-thickbox" class="thickbox button wcf-select-terms-button" data-type="exclude">'.esc_html__('Select Terms', 'wcf').'</a>',
			),

			array(
				'type' => 'select',
				'label' => esc_html__( 'Order By', 'wcf' ),
				'name' => 'order_by',
				'value' => 'name',
				'options' => array(
					'name' => esc_html__('Name', 'wcf'),
					'slug' => esc_html__('Slug', 'wcf'),
					'term_id' => esc_html__('Term ID', 'wcf'),
				),
				'class' => '',
				'desc' => esc_html__('Order terms by', 'wcf'),
			),

			array(
				'type' => 'select',
				'label' => esc_html__( 'Order', 'wcf' ),
				'name' => 'order',
				'value' => 'ASC',
				'options' => array(
					'ASC' => esc_html__('Ascending', 'wcf'),
					'DESC' => esc_html__('Descending', 'wcf'),
				),
				'class' => '',
				'desc' => esc_html__('Whether to order terms in ascending or descending order', 'wcf'),
			),

			array(
				'type' => 'radio',
				'label' => esc_html__( 'Show Count', 'wcf' ),
				'name' => 'show_count',
				'value' => 'yes',
				'options' => array(
					'yes' => esc_html__('Yes', 'wcf'),
					'no' => esc_html__('No', 'wcf'),
				),
				'class' => '',
				'desc' => esc_html__('Show count posts are in the taxonomy', 'wcf'),
			),
			array(
				'type' => 'radio',
				'label' => esc_html__( 'Hierarchical', 'wcf' ),
				'name' => 'display_hierarchical',
				'value' => 'yes',
				'options' => array(
					'yes' => esc_html__('Yes', 'wcf'),
					'no' => esc_html__('No', 'wcf'),
				),
				'class' => '',
				'desc' => esc_html__('Display as a hierarchical taxonomy', 'wcf'),
			),
		),
	);

	wcf_register_field( 'taxonomy', esc_html__('Taxonomy', 'wcf' ), $options );
}

add_action( 'init', 'wcf_register_field_taxonomy_field', 100 );

function wcf_forms_field_taxonomy_frontend($form_id = '', $field_id, $data = array() ) {


	if ($data['taxonomy'] == '') { echo esc_html__('Please, select a taxonomy', 'wcf'); return;}

	$first_name = 'wcf_taxonomy';
	$pre_name = $first_name . "[".$field_id."]";
	$intro = isset($data['intro']) ? 'data-tooltip="'.$data['intro'].'"' : '';
	$value_all = 'all';
	if ($data['filter_select_all'] == 'no') {
		$value_all = '';
	}

	$options = array();

	$taxonomy_args = array(
//		'all_item' => $options['all'],
		'taxonomy' => $data['taxonomy'],
		'orderby' => $data['order_by'],
		'order' => $data['order'],
		'show_count' => $data['show_count'] == 'yes' ? true : false,
		'exclude' => $data['exclude_terms'],
		'hide_empty' => $data['hide_empty_terms'] == 'yes' ? true : false,
	);

	if ($data['hide_all'] == 'no') {
		$options[$value_all] = ($data['change_all_label'] != '') ? $data['change_all_label'] : esc_html__('All', 'wcf');
		$taxonomy_args['set_all'] = $value_all;
		$taxonomy_args[$value_all] = ($data['change_all_label'] != '') ? $data['change_all_label'] : esc_html__('All', 'wcf');
	}

	if (!taxonomy_exists($data['taxonomy'])) { echo esc_html__('The taxonomy doesn\'t exist', 'wcf'); return;}

	if ($data['display_hierarchical'] != 'yes' || $data['display_type'] == 'color') {

		$terms = get_terms($data['taxonomy'], $taxonomy_args);

		if (!empty($terms)) {
			foreach ( $terms as $term ) {
				$term_name = $data['show_count'] == 'yes' ? $term->name . ' <span class="wcf-count-terms">('.$term->count.')</span>' : $term->name;
				$options[$term->slug] = $term_name;
			}
		}
	}

	$label_class = 'wcf-label';
	$radio_checkbox_layout = 'wcf-'.$data['radio_checkbox_layout'];

	$field_value = apply_filters('wcf_selected_value', $data['default_value'], $first_name, $field_id);

	$data_reset = str_replace(array("\r"), "", $data['default_value']) ;
	$data_reset = str_replace(array("\n"), "|", $data_reset) ;

	switch ($data['display_type']) {
		case 'select':

			$type = $data['display_hierarchical'] == 'yes' ? 'taxonomy_select' : 'select';

			$field_args = array(
				'type' => $type,
				'name' => $pre_name,
				'id' => $pre_name . '_select',
				'value' => $field_value,
				'options' => $options,
				'taxonomy_args' => $taxonomy_args,
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

			$type = $data['display_hierarchical'] == 'yes' ? 'taxonomy_multiple' : 'multiple';

			$field_args = array(
				'type' => $type,
				'name' => $pre_name . '[]',
				'id' => $pre_name . '_select',
				'value' => $field_value,
				'options' => $options,
				'taxonomy_args' => $taxonomy_args,
				'label' => $data['label'],
				'label_class' => $label_class,
				'label_attr' => $intro,
				'class' => '',
				'desc' => '',
				'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-display="'.$data['display_type'].'" data-reset="'.$data_reset.'"',
				'before_html' => '<div class="wcf-field-body">',
				'after_html' => '</div>',
			);

			wcf_forms_field($field_args);
			break;

		case 'radio':

			$type = $data['display_hierarchical'] == 'yes' ? 'taxonomy_radio' : 'radio';
			$data_reset = str_replace("\n", " ", $data_reset);

			$field_args = array(
				'type' => $type,
				'name' => $pre_name,
				'id' => $pre_name . '_radio',
				'value' => $field_value,
				'options' => $options,
				'taxonomy_args' => $taxonomy_args,
				'label' => $data['label'],
				'label_class' => $label_class,
				'label_attr' => $intro,
				'class' => '',
				'desc' => '',
				'wrapper_class' => $radio_checkbox_layout,
				'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-display="'.$data['display_type'].'" data-reset="'.$data_reset.'"',
				'before_html' => '<div class="wcf-field-body">',
				'after_html' => '</div>',
			);

			wcf_forms_field($field_args);
			break;
		case 'color':

			if (!is_array($field_value)) {
				$field_value = explode("\n", $field_value);
			}
			$color_options = array();

			if ($data['terms_color'] != '') {
				$color_options = maybe_unserialize($data['terms_color']);
			}

			$field_args = array(
				'type' => 'checkbox_color',
				'name' => $pre_name . '[]',
				'id' => $pre_name . '_checkbox',
				'value' => $field_value,
				'options' => $color_options,
				'label' => $data['label'],
				'label_class' => $label_class,
				'label_attr' => $intro,
				'class' => 'wcf-checkbox-item',
				'desc' => '',
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
				$pre_html = '<div class="wcf-checkbox-wrapper"><input type="checkbox" value="'.$value_all.'" name="'.$pre_name . '[]'.'" class="wcf-checkbox-all" '.$all_checked.'> <label class="wcf-checkbox-label">'.$options[$value_all].'</label></div>';
				unset($options[$value_all]);
				unset($taxonomy_args['set_all']);
				unset($taxonomy_args[$value_all]);
				if ($all_checked != '') {
					$field_value = array();
					foreach ( $options as $key => $default ) {
						$field_value[] = $key;
					}
				}
			}

			$type = $data['display_hierarchical'] == 'yes' ? 'taxonomy_checkbox' : 'checkbox';

			$field_args = array(
				'type' => $type,
				'name' => $pre_name . '[]',
				'id' => $pre_name . '_checkbox',
				'value' => $field_value,
				'options' => $options,
				'taxonomy_args' => $taxonomy_args,
				'label' => $data['label'],
				'label_class' => $label_class,
				'label_attr' => $intro,
				'class' => 'wcf-checkbox-item',
				'desc' => '',
				'wrapper_class' => $radio_checkbox_layout,
				'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-display="'.$data['display_type'].'" data-reset="'.$data_reset.'"',
				'before_html' => '<div class="wcf-field-body">' . $pre_html,
				'after_html' => '</div>',
			);

			wcf_forms_field($field_args);
			break;
	}
}