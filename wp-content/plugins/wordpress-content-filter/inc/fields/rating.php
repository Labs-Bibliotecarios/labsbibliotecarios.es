<?php
/**
 * @version    $Id$
 * @package    WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

function wcf_register_rating_field() {

	$options = array(
		'frontend_callback' => 'wcf_forms_field_rating_frontend',
		'before_admin_options_desc' => esc_html__('Note: currently, the plugin only supports filter rating for Products (WooCommerce)', 'wcf'),
		'admin_options' => array(
			array(
				'type' => 'text',
				'name' => 'label',
				'value' => 'Rating',
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
				'options' => apply_filters('wcf_rating_shop_type_options', array(
					'product' => esc_html__('Products', 'wcf'),
//					'download' => esc_html__('Downloads', 'wcf'),
				)),
				'class' => '',
				'desc' => esc_html__( 'Note: you need check the shop that you want to search at Settings -> Post Type (Settings metabox right). In case, Shop no listed in Settings -> Post Type. That means you have\'t installed yet or It was not supported by the this plugin', 'wcf' ),
			),
			array(
				'type' => 'radio',
				'label' => esc_html__( 'Filter by rating', 'wcf' ),
				'name' => 'filter',
				'value' => 'no',
				'options' => array(
					'no' => esc_html__('No', 'wcf'),
					'yes' => esc_html__('Yes', 'wcf'),
				),
				'class' => '',
				'desc' => esc_html__( 'Check default filter by rating at the frontend', 'wcf' ),
			),
		),
	);

	wcf_register_field( 'rating', esc_html__( 'Rating', 'wcf' ), $options );
}

add_action( 'init', 'wcf_register_rating_field' );


function wcf_forms_field_rating_frontend($form_id = '', $field_id, $data = array() ) {


	$first_name = 'wcf_rating';
	$pre_name = $first_name . "[".$field_id."]";
	$intro = isset($data['intro']) ? 'data-tooltip="'.$data['intro'].'"' : '';
	$min = 1;
	$max = 5;

	$rating_default = array('star' => 5, 'filter' => $data['filter']);
	$field_value = apply_filters('wcf_selected_value', $rating_default, $first_name, $field_id);
	if (!isset($field_value['filter'])) {
		$field_value['filter'] = 'no';
	}

	$field_value = wp_parse_args( $field_value, $rating_default );

	$stars = array(5,4,3,2,1);
	?>

	<div class="wcf-rating">
		<?php if ($data['label'] != '') { ?>
		<label for="<?php echo esc_attr($field_id);?>" class="wcf-label" <?php echo $intro;?>><?php echo $data['label'];?> : <span class="range-slider-label"><span class="range_from"><?php echo $min;?></span> - <span class="range_to"><?php echo $max;?></span> <?php echo esc_html__('Stars', 'wcf');?></span></label>
		<?php } ?>
		<div class="wcf-field-body">
			<div class="wcf-stars" data-type="<?php echo $data['field_type'];?>" data-reset="5">
				<?php
				foreach ( $stars as $star ) { ?>
					<input class="wcf-star wcf-star-<?php echo $star;?>" id="<?php echo $pre_name . '_';?>star-<?php echo $star;?>" type="radio" name="<?php echo $pre_name . '[star]';?>" value="<?php echo $star;?>" <?php checked($field_value['star'], $star);?>/>
					<label class="wcf-star wcf-star-<?php echo $star;?>" for="<?php echo $pre_name . '_';?>star-<?php echo $star;?>"></label>
				<?php } ?>
		    </div>
			<?php
			$field_args = array(
				'type' => 'checkbox',
				'name' => $pre_name . '[filter]' ,
				'id' => $pre_name . '_checkbox',
				'class' => 'wcf-checkbox-item',
				'value' => $field_value['filter'],
				'options' => array(
					'yes' => esc_html__('Filter by rating')
				),
				'wrapper_attr' => 'data-type="'.$data['field_type'].'" data-reset="'.$data['filter'].'"',
			);

			wcf_forms_field($field_args);
			?>

		</div>
	</div>
<?php
}