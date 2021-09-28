<?php
/**
 * @version    $Id$
 * @package    WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

function wcf_register_text_field() {

	$options = array(
		'frontend_callback' => 'wcf_forms_text_frontend',
		'admin_options' => array(
			array(
				'type' => 'text',
				'name' => 'label',
				'value' => '',
				'label' => esc_html__( 'Label', 'wcf' ),
				'class' => '',
				'desc' => esc_html__( 'Display field label at the frontend', 'wcf' ),
			),
			array(
				'type' => 'textarea',
				'name' => 'text',
				'value' => '<p>This is html</p>',
				'label' => esc_html__( 'Text', 'wcf' ),
				'class' => '',
				'desc' => esc_html__('Enter Shortcode, HTML content, ....', 'wcf'),
			),

		),
	);

	wcf_register_field( 'text', esc_html__( 'Text', 'wcf' ), $options );
}

add_action( 'init', 'wcf_register_text_field' );


function wcf_forms_text_frontend($form_id = '', $field_id, $data = array() ) {
	if ($data['label'] != '') { ?>
		<label for="<?php echo esc_attr($field_id);?>" class="wcf-label"><?php echo esc_attr($data['label']);?> : </label>
	<?php }
	echo '<div class="wcf-field-body">';
	echo do_shortcode(html_entity_decode(strip_tags($data['text'])));
	echo '</div>';
}