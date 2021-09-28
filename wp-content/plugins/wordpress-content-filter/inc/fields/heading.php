<?php
/**
 * @version    $Id$
 * @package    WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

function wcf_register_field_heading_field() {

	$options = array(
		'frontend_callback' => 'wcf_forms_field_heading_frontend',
		'admin_options' => array(
			array(
				'type' => 'text',
				'name' => 'heading',
				'value' => '',
				'label' => esc_html__( 'Enter Heading', 'wcf' ),
				'class' => '',
				'desc' => '',
			),

		),
	);

	wcf_register_field( 'heading', esc_html__( 'Heading', 'wcf' ), $options );
}

add_action( 'init', 'wcf_register_field_heading_field' );


function wcf_forms_field_heading_frontend($form_id = '', $field_id, $data = array() ) {
	?>
	<label for="<?php echo esc_attr($field_id);?>" id="<?php echo esc_attr($field_id);?>_heading" class="wcf-field-heading"><?php echo esc_html__($data['heading']);?></label>
<?php
}