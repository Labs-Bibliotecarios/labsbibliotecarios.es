<?php

namespace ACFFrontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if( ! class_exists( 'ACFFrontend_Elementor' ) ) :

	class ACFFrontend_Elementor{
				
		public $form_widgets = [];
		public $elementor_categories = [];

		public function load_acf_dynamic_settings( $field ){
			if( ! is_array( $field ) ) return $field;
			foreach( $field as $key => $setting ){
				if( is_string( $setting ) && strpos( $setting, '[elementor-tag' ) !== false ){
					$tag_value = \Elementor\Plugin::$instance->dynamic_tags->parse_tags_text( $setting, $field, [ \Elementor\Plugin::$instance->dynamic_tags, 'get_tag_data_content'] );
					$field[ $key ] = $tag_value;
				}
			} 
			return $field;
		}
		
		public static function find_element_recursive( $elements, $widget_id ) {
			foreach ( $elements as $element ) {
				if ( $widget_id == $element['id'] ) {
					return $element;
				}

				if ( ! empty( $element['elements'] ) ) {
					$element = self::find_element_recursive( $element['elements'], $widget_id );

					if ( $element ) {
						return $element;
					}
				}
			}

			return false;
		}
		
		public function widgets() {
			$widget_list = array(
				'general' => array(
					'acf-frontend-form' => 'ACF_Frontend_Form_Widget',
					'edit_button' => 'Edit_Button_Widget',
				),

				//'payment-form' => 'Payment_Form_Widget',

				'posts' => array(
					'edit_post' => 'Edit_Post_Widget',
					'new_post' => 'New_Post_Widget',
					'duplicate_post' => 'Duplicate_Post_Widget',
					'delete_post' => 'Delete_Post_Widget',
					//'posts_list' => 'Posts_List_Widget',
					//'status_button' => 'Status_Button_Widget',
				),
				'terms' => array(
					'edit_term' => 'Edit_Term_Widget',
					'new_term' => 'New_Term_Widget',
					'delete_term' => 'Delete_Term_Widget',
				),
				'users' => array(
					'edit_user' => 'Edit_User_Widget',
					'new_user' => 'New_User_Widget',				
					'delete_user' => 'Delete_User_Widget',
				),

			);

			if ( acff()->is__premium_only() ) {
				if ( class_exists( 'woocommerce' ) ){
					$widget_list['products'] = array(
						'edit_product' => 'Edit_Product_Widget',
						'new_product' => 'New_Product_Widget',
						'duplicate_product' => 'Duplicate_Product_Widget',
					);
				}
				$widget_list['general']['edit_options'] = 'Edit_Options_Widget';
				$widget_list['templates'] = array(
					'acf-fields' => 'ACF_Fields_Widget',
					'submit_button' => 'Submit_Post_Widget',
				);
			}

			$elementor = $this->get_elementor_instance();

			foreach( $widget_list as $folder => $widgets ){
				foreach( $widgets as $filename => $classname ){
					require_once( __DIR__ . "/widgets/$folder/$filename.php" );
					$classname = 'ACFFrontend\Widgets\\' .$classname;
					$elementor->widgets_manager->register_widget_type( new $classname() );
				}
			}

			do_action( 'acff/widget_loaded' );

		}


		public function widget_categories( $elements_manager ) {
			$categories = array(
				'acff-general' => array(
					'title' => __( 'FRONTEND SITE MANAGEMENT', 'acf-frontend-form-element' ),
					'icon' => 'fa fa-plug',
				),
				'acff-posts' => array(
					'title' => __( 'FRONTEND POSTS', 'acf-frontend-form-element' ),
					'icon' => 'fa fa-plug',
				),
				'acff-users' => array(
					'title' => __( 'FRONTEND USERS', 'acf-frontend-form-element' ),
					'icon' => 'fa fa-plug',
				),
				'acff-taxonomies' => array(
					'title' => __( 'FRONTEND TAXONOMIES', 'acf-frontend-form-element' ),
					'icon' => 'fa fa-plug',
				),
			
				/* 'acff-lists' => array(
					'title' => __( 'FRONTEND LISTS', 'acf-frontend-form-element' ),
					'icon' => 'fa fa-plug',
				), */
			);

			if ( acff()->is__premium_only() ) {
				$categories['acff-products'] = array(
					'title' => __( 'FRONTEND PRODUCTS', 'acf-frontend-form-element' ),
					'icon' => 'fa fa-plug',
				);
			}

			foreach( $categories as $name => $args ){
				$this->elementor_categories[ $name ] = $args;
				$elements_manager->add_category( $name, $args );		
			}
		
		}

		public function dynamic_tags( $dynamic_tags ) {
			//if( class_exists( 'ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag' ) ){
				\Elementor\Plugin::$instance->dynamic_tags->register_group( 'acff-user-data', [
					'title' => 'User' 
				] );
				require_once( __DIR__ . '/dynamic-tags/user-local-avatar.php' );
				require_once( __DIR__ . '/dynamic-tags/author-local-avatar.php' );

				$dynamic_tags->register_tag( new DynamicTags\User_Local_Avatar_Tag() );
				$dynamic_tags->register_tag( new DynamicTags\Author_Local_Avatar_Tag() );
			//}
		}

		public function document_types() {
			if ( acff()->is__premium_only() ) {
				require_once( __DIR__ . '/documents/post-form.php' );
				\Elementor\Plugin::$instance->documents->register_document_type( 'post_form', Documents\PostFormTemplate::get_class_full_name() );	
				//require_once( __DIR__ . '/documents/list-item.php' );
				//\Elementor\Plugin::$instance->documents->register_document_type( 'list_item', Documents\ListItemTemplate::get_class_full_name() );	
			}
		}

		public function frontend_scripts(){
			wp_enqueue_style( 'acff-modal' );	
			wp_enqueue_style( 'acf-global' );	
			wp_enqueue_script( 'acff-modal' );
		}
		public function editor_scripts(){
			wp_enqueue_style( 'acff-icon', ACFF_URL . 'assets/css/icon.css', array(), ACFF_ASSETS_VERSION );
			wp_enqueue_style( 'acff-editor', ACFF_URL . 'assets/css/editor-min.css', array(), ACFF_ASSETS_VERSION );
			if ( acff()->dev_mode ){
				$min = '';
			}else{
				$min = '-min';
			}
			wp_enqueue_script( 'acff-editor', ACFF_URL . 'assets/js/editor' .$min. '.js', array( 'elementor-editor' ), ACFF_ASSETS_VERSION, true );
			wp_enqueue_style( 'acf-global' );
		}

		public function get_the_widget( $form_id ){
			if( is_array( $form_id ) ){
				$widget_id = $form_id[0];
				$post_id = $form_id[1];
			}else{
				return false;
			}
			
			if( isset( $post_id ) ){
				$elementor = $this->get_elementor_instance();
				
				$document = $elementor->documents->get( $post_id );
								
				if( $document ){			
					$form = $this->find_element_recursive( $document->get_elements_data(), $widget_id );
				}
				
				if ( ! empty( $form['templateID'] ) ) {
					$template = $elementor->documents->get( $form['templateID'] );

					if ( $template ) {
						$global_meta = $template->get_elements_data();
						$form = $global_meta[0];
					}
				}
				
				if( ! $form ){
					return false;
				}
				
				$widget = $elementor->elements_manager->create_element_instance( $form );
				
				return $widget;
			}
		}

		function get_elementor_instance() {
			return \Elementor\Plugin::$instance;
		}
		
		function get_current_post_id() {
			$el = $this->get_elementor_instance();
			if ( isset( $el->documents ) ) {
				$current_page = $el->documents->get_current();
				if( isset( $current_page ) ){
					return $el->documents->get_current()->get_main_id();
				}
			}
			return get_the_ID();
		}

		public function __construct() {		
			add_filter( 'acf/prepare_field', [ $this, 'load_acf_dynamic_settings'], 3 );
			require_once( __DIR__ . '/classes/save_fields.php' );
			
			require_once( __DIR__ . '/classes/content_tab.php' );
			require_once( __DIR__ . '/classes/modal.php' );

			
			if ( acff()->is__premium_only() ) {
				require_once( __DIR__ . '/classes/pro/limit_submit.php' );
				require_once( __DIR__ . '/classes/pro/multi_step.php' );
				require_once( __DIR__ . '/classes/pro/style_tab.php' );
			}
			

			add_action( 'elementor/elements/categories_registered', array( $this, 'widget_categories' ) );
			add_action( 'elementor/widgets/widgets_registered', [ $this, 'widgets'] );

			add_action( 'elementor/dynamic_tags/register_tags', [ $this, 'dynamic_tags'] );
			add_action( 'elementor/documents/register', [ $this, 'document_types'] );

			add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'frontend_scripts'] );
			add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'editor_scripts'] );
		}
	}

	acff()->elementor = new ACFFrontend_Elementor();

endif;	