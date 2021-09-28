<?php
namespace ACFFrontend\Classes;


use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class MultiStep{

	public function multi_step_settings( $widget ){
		
		$post_type_choices = acf_frontend_get_post_type_choices();    
		
		$widget->add_control(
			'steps_display',
			[
				'label' => __( 'Steps Display', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => [ 
					'tabs',
				],
				'multiple' => 'true',
				'options' => [
					'tabs' => __( 'Tabs', 'acf-frontend-form-element' ),
					'counter' => __( 'Counter', 'acf-frontend-form-element' ),
				],
								
			]
		);		
		$widget->add_control(
			'responsive_description',
			[
				'raw' => __( 'Responsive visibility will take effect only on preview or live page, and not while editing in Elementor.', 'elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);
		$widget->add_control(
			'steps_tabs_display',
			[
				'label' => __( 'Step Tabs Display', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => 'true',				
				'default' => [
					'desktop', 'tablet'
				],
				'multiple' => 'true',
				'options' => [
					'desktop' => __( 'Desktop', 'acf-frontend-form-element' ),
					'tablet' => __( 'Tablet', 'acf-frontend-form-element' ),
					'phone' => __( 'Mobile', 'acf-frontend-form-element' ),
				],
				'condition' => [
					'steps_display' => 'tabs'	
				],				
			]
		);		
		$widget->add_control(
			'tabs_align',
			[
				'label' => __( 'Tabs Position', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal' => __( 'Top', 'elementor' ),
					'vertical' => __( 'Side', 'elementor' ),
				],
				'prefix_class' => 'elementor-tabs-view-',
				'condition' => [
					'steps_display' => 'tabs'	
				],						
			]
		);
		
		$widget->add_control(
			'steps_counter_display',
			[
				'label' => __( 'Step Counter Display', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => 'true',
				'default' => [
					'desktop', 'tablet', 'phone',
				],
				'multiple' => 'true',
				'options' => [
					'desktop' => __( 'Desktop', 'acf-frontend-form-element' ),
					'tablet' => __( 'Tablet', 'acf-frontend-form-element' ),
					'phone' => __( 'Mobile', 'acf-frontend-form-element' ),
				],
				'condition' => [
					'steps_display' => 'counter'	
				],				
			]
		);		
		$widget->add_control(
			'counter_prefix',
			[
				'label' => __( 'Counter Prefix', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Step ', 'acf-frontend-form-element' ),
				'default' => __( 'Step ', 'acf-frontend-form-element' ),
				'dynamic' => [
					'active' => true,
				],	
				'condition' => [
					'steps_display' => 'counter'	
				],							
			]
		);			
		$widget->add_control(
			'counter_suffix',
			[
				'label' => __( 'Counter Suffix', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],	
				'condition' => [
					'steps_display' => 'counter'	
				],							
			]
		);	
		
		$widget->add_control(
			'step_number',
			[
				'label' => __( 'Step Number in Tabs', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'show', 'acf-frontend-form-element' ),
				'label_off' => __( 'hide','acf-frontend-form-element' ),
				'return_value' => 'true',
				'condition' => [
					'steps_display' => 'tabs'	
				],
			]
		);	
		
 		$widget->add_control(
			'tab_links',
			[
				'label' => __( 'Link to Step in Tabs', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'acf-frontend-form-element' ),
				'label_off' => __( 'No','acf-frontend-form-element' ),
				'return_value' => 'true',
				'condition' => [
					'steps_display' => 'tabs',	
				],
			]
		);	 
	

	}



	public function __construct() {
		add_action( 'acff/multi_step_settings', [ $this, 'multi_step_settings'] );
	}

}

new MultiStep();
