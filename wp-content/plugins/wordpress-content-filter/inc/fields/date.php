<?php
/**
 * @version    $Id$
 * @package    WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

function wcf_register_field_date_field() {

	$options = array(
		'frontend_callback' => 'wcf_forms_field_date_frontend',
		'admin_options' => array(
			array(
				'type' => 'text',
				'name' => 'label',
				'value' => 'Date',
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
				'type' => 'select',
				'name' => 'format_date',
				'options' => array(
					'mm/dd/yy' => 'mm/dd/yy',
					'dd/mm/yy' => 'dd/mm/yy',
					'yy-mm-dd' => 'yy-mm-dd',
					'd M, y' => 'd M, y',
					'd MM, y' => 'd MM, y',
				),
				'value' => 'mm/dd/yy',
				'label' => esc_html__( 'Format Date', 'wcf' ),
				'class' => '',
				'desc' => '',
			),

		),
	);

	wcf_register_field( 'date', esc_html__( 'Date', 'wcf' ), $options );
}

add_action( 'init', 'wcf_register_field_date_field' );


function wcf_forms_field_date_frontend($form_id = '', $field_id, $data = array() ) {

	$format_date = $data['format_date'] != '' ? $data['format_date'] : 'mm/dd/yy';
	$intro = isset($data['intro']) ? ' data-tooltip="'.$data['intro'].'"' : '';
	?>
	<?php if ($data['label'] != '') { ?>
	<label for="<?php echo esc_attr($field_id);?>" class="wcf-label"<?php echo $intro;?>><?php echo esc_attr($data['label']);?> : </label>
	<?php } ?>
	<div class="range_date_wrapper wcf-field-body" data-format-date="<?php echo esc_attr($format_date)?>">

	<?php

	$first_name = 'wcf_date';
	$pre_name = $first_name . "[".$field_id."]";

	$field_value = apply_filters('wcf_selected_value', array('date_from' => '', 'date_to' => ''), $first_name, $field_id);

	$date_from = array(
		'type' => 'text',
		'name' => $pre_name . '[date_from]',
		'value' => $field_value['date_from'],
		'class' => 'date_from',
		'label_class' => '',
		'desc' => '',
		'extra_attr' => 'placeholder="'.esc_html__('From').'"',
		'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-reset=""',
	);

	wcf_forms_field($date_from);

	$date_to = array(
		'type' => 'text',
		'name' => $pre_name . '[date_to]',
		'value' => $field_value['date_to'],
		'class' => 'date_to',
		'label_class' => '',
		'desc' => '',
		'extra_attr' => 'placeholder="'.esc_html__('To').'"',
		'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-reset=""',
	);

	wcf_forms_field($date_to);
	?>

	</div>
<?php
}