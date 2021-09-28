<?php
namespace ACFFrontend\Widgets;

use ACFFrontend\Widgets\ACF_Elementor_Form_Base;


	
/**
 * Elementor ACF Frontend Form Widget.
 *
 * Elementor widget that inserts an ACF frontend form into the page.
 *
 * @since 1.0.0
 */
class Edit_Product_Widget extends ACF_Frontend_Form_Widget {
	
	/**
	 * Get widget name.
	 *
	 * Retrieve acf ele form widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'edit_product';
	}

	/**
	* Get widget action.
	*
	* Retrieve acf ele form widget action.
	*
	* @since 1.0.0
	* @access public
	*
	* @return string Widget action.
	*/
	public function get_form_defaults() {
		$defaults = array(
			'custom_fields_save' => 'product',
			'save_to_product' => 'edit_product',
			'form_title' => __( 'Edit Product', 'acf-frontend-form-element' ),
			'submit' => __( 'Update', 'acf-frontend-form-element' ),
			'success_message' => __( 'Your product has been updated successfully.', 'acf-frontend-form-element' ),
			'field_type' => 'title',
		);
		$i = 0;
		foreach ( acf_frontend_get_field_type_groups( 'product' ) as $name => $group ) {
			foreach( $group['options'] as $type => $label ){
				$defaults['fields'][] = array(
					'field_type' => $type,
					'field_label' => $label,
				); 
				$i++;
				if( $i == 13 ) break 2;
			}		
		}
		return $defaults; 
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return ['product editing', 'edit product'];
	}


	/**
	 * Get widget title.
	 *
	 * Retrieve acf ele form widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Product Edit Form', 'acf-frontend-form-element' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve acf ele form widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fas fa-edit frontend-icon';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the acf ele form widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array('acff-products');
	}

}
