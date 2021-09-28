<?php
/**
 * @version    $Id$
 * @package    WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

function wcf_register_price_field() {

	$options = array(
		'frontend_callback' => 'wcf_forms_field_price_frontend',
		'before_admin_options_desc' => esc_html__('This field only use for shop that filter products by price', 'wcf'),
		'admin_options' => array(
			array(
				'type' => 'text',
				'name' => 'label',
				'value' => 'Price',
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
				'label' => esc_html__( 'Select Shop', 'wcf' ),
				'name' => 'shop',
				'value' => 'product',
				'options' => apply_filters('wcf_price_shop_type_options', array(
					'product' => esc_html__('Products', 'wcf'),
					'download' => esc_html__('Downloads', 'wcf'),
				)),
				'class' => '',
				'desc' => esc_html__( 'Note: you need check the shop that you want to search at Settings -> Post Type (Settings metabox right). In case, Shop no listed in Settings -> Post Type. That means you have\'t installed yet or It was not supported by the this plugin', 'wcf' ),
			),

			array(
				'type' => 'text',
				'name' => 'range_value',
				'value' => '1-100',
				'label' => esc_html__( 'Price Min/Max', 'wcf' ),
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
			array(
				'type' => 'text',
				'name' => 'price_format',
				'value' => '${price}',
				'label' => esc_html__( 'Price Format', 'wcf' ),
				'class' => '',
				'desc' => esc_html__( 'You use {price} to format price like this: ${price}, {price}$, ...', 'wcf' ),
			),
		),
	);

	wcf_register_field( 'price', esc_html__( 'Price - Shop', 'wcf' ), $options );
}

add_action( 'init', 'wcf_register_price_field' );


function wcf_forms_field_price_frontend($form_id = '', $field_id, $data = array() ) {

	$settings = wcf_get_form_search_settings( $form_id );
	$post_types = isset($settings['post_type']) ? $settings['post_type'] : array();
	if (!in_array($data['shop'], $post_types)) {
		echo esc_html__('This shop has not checked yet in the Settings -> Post Type of form search', 'wcf');
		return;
	}

	$first_name = 'wcf_range_price';
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

	$min_symbol = str_replace('{price}', '<span class="range_from">'.$min.'</span>', $data['price_format']);
	$max_symbol = str_replace('{price}', '<span class="range_to">'.$max.'</span>', $data['price_format']);
	?>
	<div class="slider-range-wrapper" data-type="<?php echo $data['field_type'];?>" data-reset="<?php echo str_replace('-', '|', $data['range_value']);?>">
		<?php if ($data['label'] != '') { ?>
		<label for="<?php echo esc_attr($field_id);?>" class="wcf-label" <?php echo $intro;?>><?php echo $data['label'];?> : <span class="range-slider-label"><?php echo $min_symbol;?> - <?php echo $max_symbol;?></span></label>
		<?php } ?>
		<div class="wcf-field-body">
			<div class="slider-range" data-step="1"></div>
			<input type="hidden" name="<?php echo $pre_name . '[min]';?>" class="range_min" data-min="<?php echo $price_value['min'];?>" value="<?php echo $min; ?>"/>
			<input type="hidden" name="<?php echo $pre_name . '[max]';?>" class="range_max" data-max="<?php echo $price_value['max'];?>" value="<?php echo $max; ?>"/>
		</div>
	</div>
<?php
}