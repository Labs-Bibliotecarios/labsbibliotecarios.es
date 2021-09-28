<?php
/**
 * @version    $Id$
 * @package    WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

function wcf_register_field_submit_button_field() {

	$options = array(
		'frontend_callback' => 'wcf_forms_field_submit_button_frontend',
		'admin_options' => array(

			array(
				'type' => 'radio',
				'label' => esc_html__( 'Show Filter', 'wcf' ),
				'name' => 'show_filter',
				'value' => 'yes',
				'options' => array(
					'yes' => esc_html__('Yes', 'wcf'),
					'no' => esc_html__('No', 'wcf'),
				),
				'class' => '',
				'desc' => '',
			),

			array(
				'type' => 'radio',
				'label' => esc_html__( 'Show Reset', 'wcf' ),
				'name' => 'show_reset',
				'value' => 'yes',
				'options' => array(
					'yes' => esc_html__('Yes', 'wcf'),
					'no' => esc_html__('No', 'wcf'),
				),
				'class' => '',
				'desc' => '',
			),

			array(
				'type' => 'text',
				'name' => 'search_button_text',
				'value' => esc_html__('Filter', 'wcf'),
				'label' => esc_html__( 'Filter Button Text', 'wcf' ),
				'class' => '',
				'desc' => '',
			),

			array(
				'type' => 'text',
				'name' => 'reset_button_text',
				'value' => esc_html__('Reset', 'wcf'),
				'label' => esc_html__( 'Reset Button Text', 'wcf' ),
				'class' => '',
				'desc' => '',
			),
		),
	);

	wcf_register_field( 'submit_button', esc_html__( 'Submit Button', 'wcf' ), $options );
}

add_action( 'init', 'wcf_register_field_submit_button_field' );


function wcf_forms_field_submit_button_frontend($form_id = '', $field_id, $data = array() ) {

	$show_filter = isset($data['show_filter']) ? $data['show_filter'] : 'yes';
	$show_reset = isset($data['show_reset']) ? $data['show_reset'] : 'yes';
	$filter_text = isset($data['search_button_text']) ? $data['search_button_text'] : esc_html__('Filter', 'wcf');
	$reset_text = isset($data['reset_button_text']) ? $data['reset_button_text'] : esc_html__('Reset', 'wcf');

	?>
	<div class="wcf-clear"></div>
	<?php if ($show_filter == 'yes') { ?>
		<button type="submit" class="wcf-submit-button"><?php echo esc_attr($filter_text);?></button>
	<?php } ?>
	<?php if ($show_reset == 'yes') { ?>
		<button type="button" class="wcf-reset-button"><?php echo esc_attr($reset_text);?></button>
	<?php } ?>
<?php
}