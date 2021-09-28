<?php
/**
 * @version    $Id$
 * @package    WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

function wcf_register_field_input_query_field() {

	$options = array(
		'frontend_callback' => 'wcf_forms_field_input_query_frontend',
		'admin_options' => array(
			array(
				'type' => 'text',
				'name' => 'label',
				'value' => 'Keyword',
				'label' => esc_html__( 'Label', 'wcf' ),
				'class' => '',
				'desc' => esc_html__( 'Display field label at the frontend, leave blank to hide', 'wcf' ),
			),
			array(
				'type' => 'textarea',
				'name' => 'intro',
				'value' => 'Enter keyword to filter',
				'label' => esc_html__( 'Intro Field', 'wcf' ),
				'class' => '',
				'desc' => esc_html__('An introduction field on tooltip when hovering on label, leave blank to hide', 'wcf'),
			),
			array(
				'type' => 'text',
				'name' => 'input_query_placeholder',
				'value' => esc_html__('Enter keyword'),
				'label' => esc_html__( 'Placeholder Text', 'wcf' ),
				'class' => '',
				'desc' => '',
			),

		),
	);

	wcf_register_field( 'input_query', esc_html__( 'Input Query', 'wcf' ), $options );
}

add_action( 'init', 'wcf_register_field_input_query_field' );


function wcf_forms_field_input_query_frontend($form_id = '', $field_id, $data = array() ) {
	$intro = isset($data['intro']) ? ' data-tooltip="'.$data['intro'].'"' : '';
	?>
	<?php if ($data['label'] != '') { ?>
		<label for="<?php echo esc_attr($field_id);?>" class="wcf-label" <?php echo $intro;?>><?php echo esc_attr($data['label']);?> : </label>
	<?php } ?>

	<div class="wcf-field-body">
		<input type="text" name="s" id="wcf-input-query-<?php echo esc_attr($field_id);?>" class="wcf-input-query" placeholder="<?php echo esc_attr($data['input_query_placeholder'])?>" value="<?php echo get_search_query();?>" autocomplete="off" data-type="<?php echo esc_attr($data['field_type']);?>" data-reset="">
	</div>
<?php
}