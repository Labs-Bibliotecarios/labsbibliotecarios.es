<?php
/**
 * @version    $Id$
 * @package    WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

function wcf_register_field_separator_field() {

	$options = array(
		'frontend_callback' => 'wcf_forms_field_separator_frontend',
		'admin_options' => array(
		),
	);

	wcf_register_field( 'separator', esc_html__( 'Separator', 'wcf' ), $options );
}

add_action( 'init', 'wcf_register_field_separator_field' );


function wcf_forms_field_separator_frontend($form_id = '', $field_id, $data = array() ) {

	?>
	<hr class="wcf-form-separator-field"/>
<?php
}