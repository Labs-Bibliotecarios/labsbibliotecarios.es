<?php
namespace ACFFrontend\Classes;

use Elementor\Controls_Manager;
use ElementorPro\Modules\QueryControl\Module as Query_Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class LimitSubmit{

	public function submit_limit_setting( $widget ){
				
		$widget->add_control(
			'limit_reached',
			[
				'label' => __( 'Limit Reached Message', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'default' => 'show_message',
				'options' => [
					'show_message'  => __( 'Limit Message', 'acf-frontend-form-element' ),
					'custom_content' => __( 'Custom Content', 'acf-frontend-form-element' ),
					'show_nothing' => __( 'Nothing', 'acf-frontend-form-element' ),
				],
			]
		);		
		$widget->add_control(
			'limit_submit_message',
			[
				'label' => __( 'Reached Limit Message', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'rows' => 4,
				'default' => __( 'You have already submitted this form the maximum amount of times that you are allowed', 'acf-frontend-form-element' ),
				'placeholder' => __( 'you have already submitted this form the maximum amount of times that you are allowed', 'acf-frontend-form-element' ),
				'condition' => [
					'limit_reached' => 'show_message',
				]
			]
		);
		$widget->add_control(
			 'limit_submit_content',
			[
				'label' => __( 'Reached Limit Content', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::WYSIWYG,
				'placeholder' => 'You have already submitted this form the maximum amount of times that you are allowed',
				'label_block' => true,
				'render_type' => 'none',
				'condition' => [
					'limit_reached' => 'custom_content',
				]
			]
		);

		
		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'rule_name', [
				'label' => __( 'Rule Name', 'acf-frontend-form-element' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Rule Name' , 'acf-frontend-form-element' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'allowed_submits',
			[
				'label' => __( 'Allowed Submissions', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '',
			]
		);

		$repeater->add_control(
			'limit_to_everyone',
			[
				'label' => __( 'Limit For Everyone', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'acf-frontend-form-element' ),
				'label_off' => __( 'No','acf-frontend-form-element' ),
				'return_value' => 'true',
			]
		);

		$user_roles = acf_frontend_get_user_roles();

		$repeater->add_control(
			'limit_by_role',
			[
				'label' => __( 'Limit By Role', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'default' => 'subscriber',
				'options' => $user_roles,
				'condition' => [
					'limit_to_everyone' => ''
				]
			]
		);
		if( ! class_exists( 'ElementorPro\Modules\QueryControl\Module' ) ){		
			$repeater->add_control(
				'limit_by_user',
				[
					'label' => __( 'Limit By User', 'acf-frontend-form-element' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => __( '18', 'acf-frontend-form-element' ),
					'description' => __( 'Enter a commma seperated list of user ids', 'acf-frontend-form-element' ),
					'condition' => [
						'limit_to_everyone' => ''
					]
				]
			);		
		}else{			
			$repeater->add_control(
				'limit_by_user',
				[
					'label' => __( 'Limit By User', 'acf-frontend-form-element' ),
					'type' => Query_Module::QUERY_CONTROL_ID,
					'label_block' => true,
					'autocomplete' => [
						'object' => Query_Module::QUERY_OBJECT_USER,
						'display' => 'detailed',
					],				
					'multiple' => true,
					'condition' => [
						'limit_to_everyone' => ''
					]
				]
			);
		}

		$widget->add_control(
			'limiting_rules',
			[
				'label' => __( 'Add Limiting Rules', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'prevent_empty' => false,
				'default' => [
					[
						'rule_name' => __( 'Subscribers', 'acf-frontend-form-element' ),
					],
				],
				'title_field' => '{{{ rule_name }}}',
			]
		);

	}

	public function limit_form_render( $settings ){

		if( ! $settings['display'] ) return $settings;


		if( empty( $settings['element_id'] ) ) return $settings;

		$element_id = $settings['element_id'];

		$active_user = wp_get_current_user();
		$submitted = get_user_meta( $active_user->ID, $element_id . '_submitted', true );

		if( empty( $submitted ) ){
			return $settings;
		}

		$message = '';

		if( empty( $settings['limiting_rules'] ) ) return $settings;

		$limit_rules = $settings['limiting_rules'];

		foreach( $limit_rules as $rule ){
			$roles = $rule['limit_by_role'];
			$users = $rule['limit_by_user'];
			if( is_string( $users ) ){
				$users = explode( ',', $users );
			}
			$in_rule = false;

			if( $rule['limit_to_everyone'] ){
				$in_rule = true;
			}
			if( is_array( $roles ) ){	
				if ( count( array_intersect( $roles, (array) $active_user->roles ) ) == 0 ) {
					$in_rule = false;
				}else{
					$in_rule = true;
				}
			}
			if( is_array( $users ) ){	
				if( in_array( $active_user->ID, $users ) ){
					$in_rule = true;
				}
			}

			if( $in_rule === true ){
				$submits = (int)$rule['allowed_submits'];
				if( $settings['limit_reached'] == 'show_message' ){
					$message = '<div class="acf-notice -limit acff-limit-message"><p>' . $settings['limit_submit_message'] . '</p></div>';
				}
				elseif( $settings['limit_reached'] == 'custom_content' ){
					$message = $settings['limit_submit_content'];
				}
				else{
					$message = 'NOTHING';
				}

			}
		}
		
		if( ! empty( $submits ) && (int)$submits - (int)$submitted <= 0 ){
			$settings['display'] = false;
			$settings['message'] = $message;
		}

		return $settings;

	}

	public function submit_record( $post_id ){
		if( get_post_field( 'post_status', $post_id ) == 'publish' ){
			$post_author = get_post_field( 'post_author', $post_id );
			$post_form = get_post_meta( $post_id, 'acff_form_source', true );
			$submitted = get_user_meta( $post_author, $post_form . '_submitted', true );

			$submitted++;
			update_user_meta( $post_author, $post_form . '_submitted', $submitted );
		}
	}

	public function subtract_record( $post_id ){
        $post_author = get_post_field( 'post_author', $post_id );
		$post_form = get_post_meta( $post_id, 'acff_form_source', true );
		$submitted = get_user_meta( $post_author, $post_form . '_submitted', true );

		if( $submitted && 'trash' !== get_post_status( $post_id ) ){
			$submitted--;
			update_user_meta( $post_author, $post_form . '_submitted', $submitted );
		}
        
    }

	public function subtract_add_record( $new_status, $old_status, $post ) {
		if ( $old_status == $new_status ) return;

		$post_author = get_post_field( 'post_author', $post->ID );
		$post_form = get_post_meta( $post->ID, 'acff_form_source', true );
		$submitted = get_user_meta( $post_author, $post_form . '_submitted', true );

		if( ( $old_status == 'publish' || $old_status == 'pending' ) && ( $new_status != 'publish' && $new_status != 'pending' ) ){
			$submitted--;
			update_user_meta( $post_author, $post_form . '_submitted', $submitted );
		}
		if( ( $old_status != 'publish' && $old_status != 'pending' ) && ( $new_status == 'publish' || $new_status == 'pending' ) ){
			if( ! $submitted ){
				$submitted = 1;
			}else{
				$submitted++;
			}
			update_user_meta( $post_author, $post_form . '_submitted', $submitted );
		}
	}

	public function __construct() {
		add_action( 'acff/limit_submit_settings', [ $this, 'submit_limit_setting'] );
		add_filter( 'acf_frontend/show_form', [ $this, 'limit_form_render'], 11 );	
		add_action( 'untrash_post' , [ $this, 'submit_record'] );
		add_action( 'before_delete_post' , [ $this, 'subtract_record'] );	
		add_action( 'wp_trash_post' , [ $this, 'subtract_record'] );	
		add_action( 'transition_post_status' , [ $this, 'subtract_add_record'], 10, 3 );	
	}

}

new LimitSubmit();
