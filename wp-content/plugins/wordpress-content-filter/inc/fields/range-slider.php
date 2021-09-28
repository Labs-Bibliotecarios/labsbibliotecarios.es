<?php
/**
 * @version    $Id$
 * @package    WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

function wcf_register_range_slider_field() {

	$options = array(
		'frontend_callback' => 'wcf_forms_field_range_slider_frontend',
		'before_admin_options_desc' => esc_html__('Note: This field only for developer.', 'wcf'),
		'admin_options' => array(
			array(
				'type' => 'text',
				'name' => 'label',
				'value' => 'Room',
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
				'name' => 'range_slug',
				'value' => 'room',
				'label' => esc_html__( 'Slug', 'wcf' ),
				'class' => '',
				'desc' => esc_html__( 'Enter your slug. For example: room, Using this value to build query post', 'wcf' ),
			),

			array(
				'type' => 'text',
				'name' => 'range_value',
				'value' => '1-100',
				'label' => esc_html__( 'Range Min/Max', 'wcf' ),
				'class' => '',
				'desc' => esc_html__( 'Enter a range min/max. For example: 1-300', 'wcf' ),
			),
			array(
				'type' => 'text',
				'name' => 'range_step',
				'value' => '1',
				'label' => esc_html__( 'Step Size', 'wcf' ),
				'class' => '',
				'desc' => esc_html__( 'Enter step the slider takes between the min and max', 'wcf' ),
			),
		),
	);

	wcf_register_field( 'range_slider', esc_html__( 'Range Slider', 'wcf' ), $options );
}

add_action( 'init', 'wcf_register_range_slider_field' );


function wcf_forms_field_range_slider_frontend($form_id = '', $field_id, $data = array() ) {

	$first_name = isset($data['range_slug']) ? $data['range_slug'] : 'wcf_range_slider';
	$pre_name = $first_name . "[".$field_id."]";
	$intro = isset($data['intro']) ? 'data-tooltip="'.$data['intro'].'"' : '';

	$range_value = isset($data['range_value']) && !empty($data['range_value']) ? explode('-', $data['range_value']) : array();

	if (empty($range_value)) {
		echo esc_html__('Invalid range value', 'wcf');
		return;
	}

	$price_value = array(
		'min' => $range_value[0],
		'max' => $range_value[1],
	);

	$field_value = apply_filters('wcf_selected_value', $price_value, $first_name, $field_id);

	$min = $field_value['min'];
	$max = $field_value['max'];
	?>

	<div class="slider-range-wrapper" data-type="<?php echo $data['field_type'];?>" data-reset="<?php echo str_replace('-', '|', $data['range_value']);?>">
		<?php if ($data['label'] != '') { ?>
		<label for="<?php echo esc_attr($field_id);?>" class="wcf-label" <?php echo $intro;?>><?php echo $data['label'];?> : <span class="range-slider-label"><span class="range_from"><?php echo $min;?></span> - <span class="range_to"><?php echo $max;?></span></span></label>
		<?php } ?>
		<div class="wcf-field-body">
			<div class="slider-range" data-step="<?php echo $data['range_step'];?>"></div>
			<input type="hidden" name="<?php echo $pre_name . '[min]';?>" class="range_min" data-min="<?php echo $price_value['min'];?>" value="<?php echo $min; ?>"/>
			<input type="hidden" name="<?php echo $pre_name . '[max]';?>" class="range_max" data-max="<?php echo $price_value['max'];?>" value="<?php echo $max; ?>"/>
		</div>
	</div>
<?php
}