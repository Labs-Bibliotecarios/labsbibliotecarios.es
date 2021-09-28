<?php
namespace ACFFrontend\Actions;

use ACFFrontend\Plugin;
use ACFFrontend\Classes\ActionBase;
use ACFFrontend\Widgets;
use Elementor\Controls_Manager;
use ElementorPro\Modules\QueryControl\Module as Query_Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( ! class_exists( 'ActionPost' ) ) :

class ActionPost extends ActionBase {
	
	public function get_name() {
		return 'post';
	}

	public function get_label() {
		return __( 'Post', 'acf-frontend-form-element' );
	}

	public function get_fields_display( $form_field, $local_field, $wg_id = ''){
		$field_appearance = isset( $form_field['field_taxonomy_appearance'] ) ? $form_field['field_taxonomy_appearance'] : 'checkbox';
		$field_add_term = isset( $form_field['field_add_term'] ) ? $form_field['field_add_term'] : 0;

		switch( $form_field['field_type'] ){
			case 'title':
				$local_field['type'] = 'post_title';
			break;
			case 'slug':
				$local_field['type'] = 'post_slug';
				$local_field['wrapper']['class'] .= ' post-slug-field';
			break;
			case 'content':
				$local_field['type'] = 'post_content';
				$local_field['field_type'] = isset( $form_field['editor_type'] ) ? $form_field['editor_type'] : 'wysiwyg';
			break;
			case 'featured_image':
				$local_field['type'] = 'featured_image';
				$local_field['default_value'] = empty( $form_field['default_featured_image']['id'] ) ? '' : $form_field['default_featured_image']['id'];
			break;
			case 'excerpt':
				$local_field['type'] = 'post_excerpt';
			break;
			case 'author':
				$local_field['type'] = 'post_author';
			break;
			case 'published_on':
				$local_field['type'] = 'post_date';
			break;
			case 'menu_order':
				$local_field['type'] = 'menu_order';
			break;
			case 'taxonomy':
				$taxonomy = isset( $form_field['field_taxonomy'] ) ? $form_field['field_taxonomy'] : 'category';
				$local_field['type'] = 'taxonomy';
				$local_field['taxonomy'] = $taxonomy;
				$local_field['field_type'] = $field_appearance;
				$local_field['allow_null'] = 0;
				$local_field['add_term'] = $field_add_term;
				$local_field['load_post_terms'] = 1;
				$local_field['save_terms'] = 1;
				$local_field['custom_taxonomy'] = true;
			break;
			case 'categories':
				$local_field['type'] = 'taxonomy';
				$local_field['taxonomy'] = 'category';
				$local_field['field_type'] = $field_appearance;
				$local_field['allow_null'] = 0;
				$local_field['add_term'] = $field_add_term;
				$local_field['load_post_terms'] = 1;
				$local_field['save_terms'] = 1;
				$local_field['custom_taxonomy'] = true;
			break;
			case 'tags':
				$local_field['type'] = 'taxonomy';
				$local_field['taxonomy'] = 'post_tag';
				$local_field['field_type'] = $field_appearance;
				$local_field['allow_null'] = 0;
				$local_field['add_term'] = $field_add_term;
				$local_field['load_post_terms'] = 1;
				$local_field['save_terms'] = 1;
				$local_field['custom_taxonomy'] = true;
			break;
			case 'post_type':				
				$local_field['type'] = 'post_type';
				$local_field['field_type'] = isset( $form_field['role_appearance'] ) ? $form_field['role_appearance'] : 'select';
				$local_field['layout'] = isset( $form_field['role_radio_layout'] ) ? $form_field['role_radio_layout'] : 'vertical';
				$local_field['default_value'] = isset( $form_field['default_post_type'] ) ? $form_field['default_post_type'] : 'post';
				if( isset( $form_field[ 'post_type_field_options' ] ) ){
					$local_field['post_type_options'] = $form_field['post_type_field_options']; 
				}
			break;
		}
		return $local_field;
	}
	

	public function register_settings_section( $widget ) {		
						
		$widget->start_controls_section(
			'section_edit_post',
			[
				'label' => $this->get_label(),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->action_controls( $widget );		
				
		$widget->end_controls_section();
	}
	
	public function action_controls( $widget, $step = false, $type = '' ){
		if( ! empty( $widget->form_defaults['save_to_post'] ) ){
			$type = $widget->form_defaults['save_to_post'];
		}

		if( $step ){
			$condition = [
				'field_type' => 'step',
				'overwrite_settings' => 'true',
			];
		}
		$args = [
            'label' => __( 'Post', 'acf-frontend-form-element' ),
            'type'      => Controls_Manager::SELECT,
            'options'   => [
				'edit_post' => __( 'Edit Post', 'acf-frontend-form-element' ),
				'new_post' => __( 'New Post', 'acf-frontend-form-element' ),
			],
            'default'   => 'edit_post',
        ];
		if( $step ){
			$condition = [
				'field_type' => 'step',
				'overwrite_settings' => 'true',
			];
			$args['condition'] = $condition;
		}else{
			$condition = array();
		}

		if( $type ){
			$args = [
				'type' => Controls_Manager::HIDDEN,
				'default' => $type,
			];
		}

		$widget->add_control( 'save_to_post', $args );
		
		$condition['save_to_post'] = ['edit_post', 'delete_post', 'duplicate_post'];

		$widget->add_control(
			'post_to_edit',
			[
				'label' => __( 'Select Post', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'current_post',
				'options' => [
					'current_post'  => __( 'Current Post', 'acf-frontend-form-element' ),
					'url_query' => __( 'Url Query', 'acf-frontend-form-element' ),
					'select_post' => __( 'Select Post', 'acf-frontend-form-element' ),
				],
				'condition' => $condition,
			]
		);

		$condition['post_to_edit'] = 'url_query';
		$widget->add_control(
			'url_query_post',
			[
				'label' => __( 'URL Query', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'post_id', 'acf-frontend-form-element' ),
				'default' => __( 'post_id', 'acf-frontend-form-element' ),
				'required' => true,
				'description' => __( 'Enter the URL query parameter containing the id of the post you want to edit', 'acf-frontend-form-element' ),
				'condition' => $condition,
			]
		);	
		$condition['post_to_edit'] = 'select_post';
		$widget->add_control(
			'post_select',
			[
				'label' => __( 'Post', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( '18', 'acf-frontend-form-element' ),
				'description' => __( 'Enter the post ID', 'acf-frontend-form-element' ),
				'condition' => $condition,
			]
		);		
	
		unset( $condition['post_to_edit'] );
		$condition['save_to_post'] = 'new_post';

		$post_type_choices = acf_frontend_get_post_type_choices();    
		
		$widget->add_control(
			'new_post_type',
			[
				'label' => __( 'New Post Type', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => 'post',
				'options' => $post_type_choices,
				'condition' => $condition,
			]
		);
		$widget->add_control(
			'new_post_terms',
			[
				'label' => __( 'New Post Terms', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => 'post',
				'options' => [
					'current_term'  => __( 'Current Term', 'acf-frontend-form-element' ),
					'select_terms' => __( 'Select Term', 'acf-frontend-form-element' ),
				],
				'condition' => $condition,
			]
		);

		$condition['new_post_terms'] = 'select_terms';
		if( ! class_exists( 'ElementorPro\Modules\QueryControl\Module' ) ){
			$widget->add_control(
				'new_terms_select',
				[
					'label' => __( 'Terms', 'acf-frontend-form-element' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => __( '18, 12, 11', 'acf-frontend-form-element' ),
					'description' => __( 'Enter the a comma-seperated list of term ids', 'acf-frontend-form-element' ),
					'condition' => $condition,
				]
			);		
		}else{		
			$widget->add_control(
				'new_terms_select',
				[
					'label' => __( 'Terms', 'acf-frontend-form-element' ),
					'type' => Query_Module::QUERY_CONTROL_ID,
					'label_block' => true,
					'autocomplete' => [
						'object' => Query_Module::QUERY_OBJECT_TAX,
						'display' => 'detailed',
					],		
					'multiple' => true,
					'condition' => $condition,
				]
			);
		}
		
		unset( $condition['new_post_terms'] );

		$condition['save_to_post'] = [ 'new_post', 'edit_post', 'duplicate_post' ];

		$widget->add_control(
			'new_post_status',
			[
				'label' => __( 'Post Status', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => 'no_change',
				'options' => [
					'draft' => __( 'Draft', 'acf-frontend-form-element' ),
					'private' => __( 'Private', 'acf-frontend-form-element' ),
					'pending' => __( 'Pending Review', 'acf-frontend-form-element' ),
					'publish'  => __( 'Published', 'acf-frontend-form-element' ),
				],
				'condition' => $condition
			]
		);	
		$condition['new_post_status!'] = 'draft';

		$widget->add_control(
			'save_progress_button',
			[
				'label' => __( 'Save Progress Option', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'acf-frontend-form-element' ),
				'label_off' => __( 'No','acf-frontend-form-element' ),
				'return_value' => 'true',
				'condition' => $condition,
			]
		);
		$condition['save_progress_button'] = 'true';
		$widget->add_control(
			'saved_draft_text',
			[
				'label' => __( 'Save Progress Text', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Save Progress', 'acf-frontend-form-element' ),
				'placeholder' => __( 'Save Progress', 'acf-frontend-form-element' ),
				'dynamic' => [
					'active' => true,
				],				
				'condition' => $condition,
			]
		);
		$widget->add_control(
			'saved_draft_desc',
			[
				'label' => __( 'Save Progress Description', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Want to finish later?', 'acf-frontend-form-element' ),
				'dynamic' => [
					'active' => true,
				],				
				'condition' => $condition,
			]
		);
 		$widget->add_control(
			'saved_draft_message',
			[
				'label' => __( 'Progress Saved Message', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Progress Saved', 'acf-frontend-form-element' ),
				'placeholder' => __( 'Progress Saved', 'acf-frontend-form-element' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => $condition,
			]
		);  

		$condition['save_to_post'] = 'new_post';
		$widget->add_control(
			'saved_drafts',
			[
				'label' => __( 'Show Saved Drafts Selection', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'acf-frontend-form-element' ),
				'label_off' => __( 'No','acf-frontend-form-element' ),
				'return_value' => 'true',
				'condition' => $condition,
				'seperator' => 'before',
			]
		);
		$condition['saved_drafts'] = 'true';
		$widget->add_control(
			'saved_drafts_label',
			[
				'label' => __( 'Edit Draft Label', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Edit a draft', 'acf-frontend-form-element' ),
				'placeholder' => __( 'Edit a draft', 'acf-frontend-form-element' ),
				'condition' => $condition,
			]
		);		
		$widget->add_control(
			'saved_drafts_new',
			[
				'label' => __( 'New Draft Text', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( '&mdash; New Post &mdash;', 'acf-frontend-form-element' ),
				'placeholder' => __( '&mdash; New Post &mdash;', 'acf-frontend-form-element' ),
				'condition' => $condition,
			]
		);
		unset( $condition['saved_drafts'] );

		$condition['save_to_post'] = 'edit_post';
		$widget->add_control(
			'saved_revisions',
			[
				'label' => __( 'Show Saved Revisions Selection', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'acf-frontend-form-element' ),
				'label_off' => __( 'No','acf-frontend-form-element' ),
				'return_value' => 'true',
				'condition' => $condition,
				'seperator' => 'before',
			]
		);
 		$condition['saved_revisions'] = 'true';
		$widget->add_control(
			'saved_revisions_label',
			[
				'label' => __( 'Edit Revisions Label', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Edit a revision', 'acf-frontend-form-element' ),
				'placeholder' => __( 'Edit a revision', 'acf-frontend-form-element' ),
				'condition' => $condition,
			]
		);		
		$widget->add_control(
			'saved_revisions_edit_main',
			[
				'label' => __( 'Edit Post Text', 'acf-frontend-form-element' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( '&mdash; Edit Main Post &mdash;', 'acf-frontend-form-element' ),
				'placeholder' => __( '&mdash; Edit Main Post &mdash;', 'acf-frontend-form-element' ),
				'condition' => $condition,
			]
		);
 
	}

	public function save_form( $form ){	
		if( empty( $_POST['_acf_post'] ) ) return $form;

		if( empty( $_POST['acff']['post'] ) && empty( $_POST['_acf_step'] ) ) return $form;

		$post_id = wp_kses( $_POST['_acf_post'], 'strip' );

		// allow for custom save
		$post_id = apply_filters('acf/pre_save_post', $post_id, $form);
	
		$post_to_edit = array();

		$wg_id = isset( $_POST['_acf_element_id'] ) ? '_' . $_POST['_acf_element_id'] : '';

		if( isset( $form['step_index'] ) ){
			$current_step = $form['fields']['steps'][$form['step_index']];
		}

		$old_status = '';
		
		switch( $form['save_to_post'] ){
			case 'edit_post':
				if( get_post_type( $post_id ) == 'revision' && isset( $_POST['_acf_status'] ) && $_POST['_acf_status'] == 'publish' ){
					$revision_id = $post_id;
					$post_id = wp_get_post_parent_id( $revision_id );
					wp_delete_post_revision( $revision_id );
				}
				$old_status = get_post_field( 'post_status', $post_id );
				$post_to_edit['ID'] = $post_id;
			break;
			case 'new_post':
				$post_to_edit['ID'] = 0;	
				$post_to_edit['post_type'] = $form['new_post_type'];
				if( ! empty( $current_step['overwrite_settings'] ) ) $post_to_edit['post_type'] = $current_step['new_post_type'];	
			break;
			case 'duplicate_post':				
				$post_to_duplicate = get_post( $post_id );
				$post_to_edit = get_object_vars( $post_to_duplicate );	
				$post_to_edit['ID'] = 0;	
				$post_to_edit['post_author'] = get_current_user_id();
			break;
			default: 
				return $form;
		}
		
		$metas = array();
		
		$core_fields = array(
			'post_title',
			'post_slug',
			'post_content',
			'post_author',
			'post_excerpt',
			'post_date',
			'post_type',
			'menu_order',
			'allow_comments',
		);

		if( ! empty( $_POST['acff']['post'] ) ){
			foreach( $_POST['acff']['post'] as $key => $value ){
				$field = acf_get_field( $key );

				if( ! $field ) continue;

				$field_type = $field['type'];
				
				if( ! in_array( $field_type, $core_fields ) ){
					$metas[] = array( 'value' => $value, 'field' => $field ); 
					continue;
				} 

				$field_value = acf_extract_var( $_POST['acff']['post'], $field['key'] );

				if( is_string( $field_value ) && strpos( $field_value, '[' ) !== false ){
					$dynamic_value = acff()->dynamic_values->get_dynamic_values( $field['default_value'] ); 
					if( $dynamic_value ) $field_value = $dynamic_value;
				} 

				$submit_key = $field_type == 'post_slug' ? 'post_name' : $field_type;

				$post_to_edit[ $submit_key ] = $field_value;
			}
		}

		if( $form['save_to_post'] == 'duplicate_post' ){
			if( $post_to_edit[ 'post_name' ] == $post_to_duplicate->post_name ){
				$post_name = sanitize_title( $post_to_edit['post_title'] );
				if( ! acf_frontend_slug_exists( $post_name ) ){				
					$post_to_edit['post_name'] = $post_name;
				}else{
					$post_to_edit['post_name'] = acf_frontend_duplicate_slug( $post_to_duplicate->post_name );
				}
			}
		}

		if( isset( $current_step ) && empty( $form['last_step'] ) && empty( $current_step['overwrite_settings'] ) ){
			$post_to_edit['post_status'] = 'auto-draft';
		}else{		
			if( isset( $_POST['_acf_status'] ) && $_POST['_acf_status'] == 'draft' ){
				$post_to_edit['post_status'] = 'draft';
			}else{
				$status = $form['new_post_status'];

				if( ! empty( $current_step['overwrite_settings'] ) ) $status = $current_step['new_post_status'];

				if( $status != 'no_change' ){
					$post_to_edit['post_status'] = $status;
				}elseif( empty( $old_status ) || $old_status == 'auto-draft' ){
					$post_to_edit['post_status'] = 'publish';
				}
			}
		}
		if( $post_to_edit['ID'] == 0 ){
			$post_to_edit['meta_input'] = array(
				'acff_form_source' => str_replace( '_', '', $wg_id ),
			);
			if( empty( $post_to_edit['post_title'] ) ){
				$post_to_edit['post_title'] = '(no-name)';
			}
			$post_id = wp_insert_post( $post_to_edit );

		}else{
			wp_update_post( $post_to_edit );
			update_metadata( 'post', $post_id, 'acff_form_edited', $wg_id );
		}

		if( isset( $form['post_terms'] ) && $form['post_terms'] != '' ){
			$new_terms = $form['post_terms'];				
			if( is_string( $new_terms ) ){
				$new_terms = explode( ',', $new_terms );
			}
			if( is_array( $new_terms ) ){
				foreach( $new_terms as $term_id ){
					$term = get_term( $term_id );
					if( $term ){
						wp_set_object_terms( $post_id, $term->term_id, $term->taxonomy, true );
					}
				}
			}
		}

		if( $form['save_to_post'] == 'duplicate_post' ){
			$taxonomies = get_object_taxonomies($post_to_duplicate->post_type);
			foreach ($taxonomies as $taxonomy) {
			  $post_terms = wp_get_object_terms($post_to_duplicate->ID, $taxonomy, array('fields' => 'slugs'));
			  wp_set_object_terms($post_id, $post_terms, $taxonomy, false);		
			}
 
			global $wpdb;
			$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_to_duplicate->ID");
			if( count($post_meta_infos) != 0 ) {
				$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
				foreach($post_meta_infos as $meta_info) {
					$meta_key        = $meta_info->meta_key;
					$meta_value      = addslashes($meta_info->meta_value);
					$sql_query_sel[] = "SELECT $post_id, '$meta_key', '$meta_value'";
				}
				$sql_query .= implode(" UNION ALL ", $sql_query_sel);
				$wpdb->query($sql_query);
			}
		}

		if( ! empty( $metas ) ){
			foreach( $metas as $meta ){
				acf_update_value( $meta['value'], $post_id, $meta['field'] );
			}
		}

		$form['record']['post'] = $post_id;
		
		do_action( 'acf_frontend/save_post', $form, $post_id );

		return $form;
	}

	public function __construct(){
		add_filter( 'acf_frontend/save_form', array( $this, 'save_form' ), 4 );
	}
	
}

acff()->local_actions['post'] = new ActionPost();

endif;	