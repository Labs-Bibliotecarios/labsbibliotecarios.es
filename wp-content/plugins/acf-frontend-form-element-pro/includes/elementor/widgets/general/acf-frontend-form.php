<?php

namespace ACFFrontend\Widgets;

use  ACFFrontend\Plugin ;
use  ACFFrontend\ACFF_Module ;
use  ACFFrontend\Classes ;
use  Elementor\Controls_Manager ;
use  Elementor\Controls_Stack ;
use  Elementor\Widget_Base ;
use  ElementorPro\Modules\QueryControl\Module as Query_Module ;
use  ACFFrontend\Controls ;
use  Elementor\Group_Control_Typography ;
use  Elementor\Group_Control_Background ;
use  Elementor\Group_Control_Border ;
use  Elementor\Group_Control_Text_Shadow ;
use  Elementor\Group_Control_Box_Shadow ;

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/**
 * Elementor ACF Frontend Form Widget.
 *
 * Elementor widget that inserts an ACF frontend form into the page.
 *
 * @since 1.0.0
 */
class ACF_Frontend_Form_Widget extends Widget_Base
{
    public  $form_defaults ;
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
    public function get_name()
    {
        return 'acf_ele_form';
    }
    
    /**
     * Get widget defaults.
     *
     * Retrieve acf form widget defaults.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget defaults.
     */
    public function get_form_defaults()
    {
        return [
            'custom_fields_save' => 'all',
            'form_title'      => '',
            'submit'          => __( 'Update', 'acf-frontend-form-element' ),
            'success_message' => __( 'Your site has been updated successfully.', 'acf-frontend-form-element' ),
            'field_type'      => 'ACF_fields',
            'fields'          => array( 
                array(
                    'field_type'     => 'ACF_fields',
                )
            ),
        ];
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
    public function get_title()
    {
        return __( 'ACF Frontend Form', 'acf-frontend-form-element' );
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
    public function get_keywords()
    {
        return [
            'frontend editing',
            'edit post',
            'add post',
            'add user',
            'edit user',
            'edit site'
        ];
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
    public function get_icon()
    {
        return 'fa fa-wpforms frontend-icon';
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
    public function get_categories()
    {
        return array('acff-general');
    }
    
    /**
     * Register acf ele form widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _register_controls()
    {
        $this->register_form_structure_controls();
        $this->register_steps_controls();
        $this->register_actions_controls();
        $this->action_controls_section();
        do_action( 'acff/permissions_section', $this );
        do_action( 'acff/display_section', $this );

        $this->register_limit_controls();
        $this->register_shortcodes_section();
        $this->register_style_tab_controls();               
        do_action( 'acff/content_controls', $this );
        do_action( 'acff/styles_controls', $this );
        
    
    }
    
    protected function register_form_structure_controls()
    {
        //get all field group choices
        $field_group_choices = acf_frontend_get_acf_field_group_choices();
        $field_choices = acf_frontend_get_acf_field_choices();
        $this->start_controls_section( 'fields_section', [
            'label' => __( 'Form Fields', 'acf-frontend-form-element' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        $this->add_control( 'form_title', [
            'label'       => __( 'Form Title', 'acf-frontend-form-element' ),
            'label_block' => true,
            'type'        => Controls_Manager::TEXT,
            'default'     => $this->form_defaults['form_title'],
            'placeholder' => $this->form_defaults['form_title'],
            'dynamic'     => [
            'active' => true,
        ],
        ] );     
        $this->custom_fields_control();
        do_action( 'acff/fields_controls', $this );
        $this->add_control( 'submit_button_text', [
            'label'       => __( 'Submit Button Text', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::TEXT,
            'label_block' => true,
            'default'     => $this->form_defaults['submit'],
            'placeholder' => $this->form_defaults['submit'],
            'condition' => ['multi!' => 'true'],
            'dynamic'     => [
            'active' => true,
        ],
        ] );
        $this->add_control( 'submit_button_desc', [
            'label'       => __( 'Submit Button Description', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => __( 'All done?', 'acf-frontend-form-element' ),
            'condition' => ['multi!' => 'true'],
            'dynamic'     => [
            'active' => true,
        ],
        ] );
        $this->add_control(
            'allow_unfiltered_html',
            [
                'label' => __( 'Allow Unfiltered HTML', 'acf-frontend-form-element' ),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'true',
            ]
        );
        $this->end_controls_section();

        do_action( 'acff/sub_fields_controls', $this );

    }
    
   
    public function register_steps_controls()
    {
        if ( acff()->is__premium_only() ) {
			$this->start_controls_section(
				'multi_step_section',
				[
					'label' => __( 'Steps Settings', 'acf-frontend-form-element' ),
					'tab' => Controls_Manager::TAB_CONTENT,
					'condition' => ['multi' => 'true'],
				]
			);
	
			do_action( 'acff/multi_step_settings', $this );
		
			$this->end_controls_section();		
		}	 
    }
    
    protected function register_actions_controls()
    {
        $this->start_controls_section( 'actions_section', [
            'label' => __( 'Actions', 'acf-frontend-form-element' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        if ( acff()->is__premium_only() ){
			if( get_option( 'acff_payments_active' ) && ( get_option( 'acff_stripe_active' ) || get_option( 'acff_paypal_active' ) ) ){	
				$this->add_control(
					'pay_for_submission',
					[
						'label' => __( 'Collect Payment for Submssion', 'acf-frontend-form-element' ),
						'type' => Controls_Manager::SWITCHER,
						'return_value' => 'true',
						'condition' => [
							'custom_fields_save' => 'post',
							'multi!' => 'true',
						],
					]
				);
			}

			$this->add_control(
				'more_actions',
				[
					'label' => __( 'Submit Actions', 'acf-frontend-form-element' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'options' => [
						'email' => __( 'Emails', 'acf-frontend-form-element' ),
						//'webhook' => __( 'Webhook', 'acf-frontend-form-element' ),
					],
				]
			);
		}else{
			$this->add_control(
				'more_actions_promo',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __( '<p><a target="_blank" href="https://www.frontendform.com/"><b>Go pro</b></a> to unlock more actions.</p>', 'acf-frontend-form-element' ),
					'content_classes' => 'acf-fields-note',
				]
			);
		}
        $this->add_control( 'redirect', [
            'label'   => __( 'Redirect After Submit', 'acf-frontend-form-element' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'current',
            'options' => [
                'current'     => __( 'Stay on Current Page/Post', 'acf-frontend-form-element' ),
                'custom_url'  => __( 'Custom Url', 'acf-frontend-form-element' ),
                'referer_url' => __( 'Referer', 'acf-frontend-form-element' ),
                'post_url'    => __( 'Post Url', 'acf-frontend-form-element' ),
            ],
        ] );
        if ( acff()->is__premium_only() ) {
			$this->add_control(
				'no_reload',
				[
					'label' => __( 'No Page Reload', 'acf-frontend-form-element' ),
					'type' => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'condition' => [
						'redirect' => 'current',
						'multi!' => 'true',
					],
				]
			);
		}
        $this->add_control( 'open_modal', [
            'label'        => __( 'Leave Modal Open After Submit', 'acf-frontend-form-element' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'true',
            'condition'    => [
            'show_in_modal' => 'true',
        ],
        ] );
        $this->add_control( 'redirect_action', [
            'label'     => __( 'After Reload', 'acf-frontend-form-element' ),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'clear',
            'options'   => [
                'clear' => __( 'Clear Form', 'acf-frontend-form-element' ),
                'edit'  => __( 'Edit Form', 'acf-frontend-form-element' ),
            ],
            'condition' => [
                'redirect'    => 'current',
            ],
        ] );
        $this->add_control( 'custom_url', [
            'label'       => __( 'Custom Url', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::URL,
            'placeholder' => __( 'Enter Url Here', 'acf-frontend-form-element' ),
            'options'     => false,
            'show_label'  => false,
            'condition'   => [
                'redirect' => 'custom_url',
            ],
            'dynamic'     => [
                'active' => true,
            ],
        ] );
        $repeater = new \Elementor\Repeater();
        $repeater->add_control( 'param_key', [
            'label'       => __( 'Key', 'acf-frontend-form-element' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'placeholder' => __( 'page_id', 'acf-frontend-form-element' ),
            'dynamic'     => [
                'active' => true,
            ],
        ] );
        $repeater->add_control( 'param_value', [
            'label'       => __( 'Value', 'acf-frontend-form-element' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'placeholder' => __( '18', 'acf-frontend-form-element' ),
            'dynamic'     => [
            'active' => true,
        ],
        ] );
        $this->add_control( 'url_parameters', [
            'label'         => __( 'URL Parameters', 'acf-frontend-form-element' ),
            'type'          => Controls_Manager::REPEATER,
            'fields'        => $repeater->get_controls(),
            'prevent_empty' => false,
            'title_field'   => '{{{ param_key }}}',
        ] );
        $this->add_control( 'show_success_message', [
            'label'        => __( 'Show Success Message', 'acf-frontend-form-element' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', 'acf-frontend-form-element' ),
            'label_off'    => __( 'No', 'acf-frontend-form-element' ),
            'default'      => 'true',
            'return_value' => 'true',
        ] );
        $this->add_control( 'update_message', [
            'label'       => __( 'Submit Message', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::TEXTAREA,
            'default'     => $this->form_defaults['success_message'],
            'placeholder' => $this->form_defaults['success_message'],
            'dynamic'     => [
                'active' => true,
            ],
            'condition' => [
				'show_success_message' => 'true',
			],	
        ] );
        $this->add_control( 'error_message', [
            'label'       => __( 'Error Message', 'acf-frontend-form-element' ),
            'type'        => Controls_Manager::TEXTAREA,
            'description' => __( 'There shouldn\'t be any problems with the form submission, but if there are, this is what your users will see. If you are expeiencing issues, try and changing your cache settings and reach out to ', 'acf-frontend-form-element' ) . 'support@frontendform.com',
            'default'     => __( 'There has been an error. Form has been submitted successfully, but some actions might not have been completed.', 'acf-frontend-form-element' ),
            'placeholder' => __( 'There has been an error. Form has been submitted successfully, but some actions might not have been completed.', 'acf-frontend-form-element' ),
            'dynamic'     => [
                'active' => true,
            ],
        ] );
        $this->end_controls_section();
    }
    
    protected function action_controls_section()
    {
        $local_actions = acff()->local_actions;
        foreach ( $local_actions as $name => $action ) {
            $object = $this->form_defaults['custom_fields_save'];
            if ( $object == $name || $object == 'all' ) {
                $action->register_settings_section( $this );
            }
        }
        if ( acff()->is__premium_only() ) {
            $remote_actions = acff()->remote_actions;
            foreach ( $remote_actions as $action ) {
                $action->register_settings_section( $this );
            }
        }
    }

    public function custom_fields_control( $repeater = false )
    {
        $controls = $this;
        $continue_action = [];
        $controls_settings = [
            'label'   => __( 'Save Custom Fields to...', 'acf-frontend-form-element' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'post',
        ];
        
        if ( $repeater ) {
            $controls = $repeater;
            $controls_settings['condition'] = [
                'field_type'         => 'step',
                'overwrite_settings' => 'true',
            ];
        }
        
        
        if ( $this->form_defaults['custom_fields_save'] == 'all' ) {
            $custom_fields_options = array(
                'post' => __( 'Post', 'acf-frontend-form-element' ),
                'user' => __( 'User', 'acf-frontend-form-element' ),
                'term' => __( 'Term', 'acf-frontend-form-element' ),
            );
            if ( acff()->is__premium_only() ) {
				$custom_fields_options['options'] = __( 'Site Options', 'acf-frontend-form-element' );
				if ( class_exists( 'woocommerce' ) ){
					$custom_fields_options['product'] = __( 'Product', 'acf-frontend-form-element' );
				}
			}
            $controls_settings['options'] = $custom_fields_options;
            $controls->add_control( 'custom_fields_save', $controls_settings );
        } else {
            $controls->add_control( 'custom_fields_save', [
                'type'    => Controls_Manager::HIDDEN,
                'default' => $this->form_defaults['custom_fields_save'],
            ] );
        }
    
    }
    
    protected function register_display_controls()
    {
        $this->start_controls_section( 'display_section', [
            'label' => __( 'Display Options', 'acf-frontend-form-element' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        $this->add_control( 'hide_field_labels', [
            'label'        => __( 'Hide Field Labels', 'acf-frontend-form-element' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Hide', 'acf-frontend-form-element' ),
            'label_off'    => __( 'Show', 'acf-frontend-form-element' ),
            'return_value' => 'true',
            'separator'    => 'before',
            'selectors'    => [
            '{{WRAPPER}} .acf-label' => 'display: none',
        ],
        ] );
        $this->add_control( 'field_label_position', [
            'label'     => __( 'Label Position', 'elementor-pro' ),
            'type'      => Controls_Manager::SELECT,
            'options'   => [
            'top'  => __( 'Above', 'elementor-pro' ),
            'left' => __( 'Inline', 'elementor-pro' ),
        ],
            'default'   => 'top',
            'condition' => [
            'hide_field_labels!' => 'true',
        ],
        ] );
        $this->add_control( 'hide_mark_required', [
            'label'        => __( 'Hide Required Mark', 'elementor-pro' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Hide', 'elementor-pro' ),
            'label_off'    => __( 'Show', 'elementor-pro' ),
            'return_value' => 'true',
            'condition'    => [
            'hide_field_labels!' => 'true',
        ],
            'selectors'    => [
            '{{WRAPPER}} .acf-required' => 'display: none',
        ],
        ] );
        $this->add_control( 'field_instruction_position', [
            'label'     => __( 'Instruction Position', 'elementor-pro' ),
            'type'      => Controls_Manager::SELECT,
            'options'   => [
            'label' => __( 'Above Field', 'elementor-pro' ),
            'field' => __( 'Below Field', 'elementor-pro' ),
        ],
            'default'   => 'label',
            'separator' => 'before',
        ] );
        $this->add_control( 'field_seperator', [
            'label'        => __( 'Field Seperator', 'elementor-pro' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Hide', 'elementor-pro' ),
            'label_off'    => __( 'Show', 'elementor-pro' ),
            'default'      => 'true',
            'return_value' => 'true',
            'separator'    => 'before',
            'selectors'    => [
            '{{WRAPPER}} .acf-fields>.acf-field'                        => 'border-top: none',
            '{{WRAPPER}} .acf-field[data-width]+.acf-field[data-width]' => 'border-left: none',
        ],
        ] );
        $this->end_controls_section();
    }
    
    public function register_limit_controls()
    {
        $this->start_controls_section( 'limit_submit_section', [
            'label' => __( 'Limit Submissions', 'acf-frontend-form-element' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        if ( ! acff()->is__premium_only() ) {
			$this->add_control(
				'limit_submit_promo',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __( '<p><a target="_blank" href="https://www.frontendform.com/"><b>Go pro</b></a> to unlock limit submissions.</p>', 'acf-frontend-form-element' ),
					'content_classes' => 'acf-fields-note',
				]
			);
		}
        do_action( 'acff/limit_submit_settings', $this );
        $this->end_controls_section();
    }
    
    public function register_shortcodes_section()
    {
        $this->start_controls_section( 'shortcodes_section', [
            'label' => __( 'Shortcodes for Dynamic Values', 'acf-frontend-form-element' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        $this->add_control( 'custom_field_shortcode', [
            'label'       => __( 'ACF Text Field', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[acf:field_name]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'image_field_shortcode', [
            'label'       => __( 'ACF Image Field', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[acf:field_name:image]" readonly /><br><input class="elementor-form-field-shortcode" value="[acf:field_name:image_link]" readonly /><br><input class="elementor-form-field-shortcode" value="[acf:field_name:image_id]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'post_title_shortcode', [
            'label'       => __( 'Post Title', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[post:title]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'post_id_shortcode', [
            'label'       => __( 'Post ID', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[post:id]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'post_content_shortcode', [
            'label'       => __( 'Post Content', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[post:content]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'post_excerpt_shortcode', [
            'label'       => __( 'Post Excerpt', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[post:excerpt]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'featured_image_shortcode', [
            'label'       => __( 'Featured Image', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[post:featured_image]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'post_url_shortcode', [
            'label'       => __( 'Post URL', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[post:url]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'username_shortcode', [
            'label'       => __( 'Username', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[user:username]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'user_email_shortcode', [
            'label'       => __( 'User Email', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[user:email]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'user_first_shortcode', [
            'label'       => __( 'User First Name', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[user:first_name]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'user_last_shortcode', [
            'label'       => __( 'User Last Name', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[user:last_name]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'user_role_shortcode', [
            'label'       => __( 'User Role', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[user:role]" readonly />',
            'separator'   => 'after',
        ] );
        $this->add_control( 'user_bio_shortcode', [
            'label'       => __( 'User Bio', 'elementor-pro' ),
            'type'        => Controls_Manager::RAW_HTML,
            'label_block' => true,
            'raw'         => '<input class="elementor-form-field-shortcode" value="[user:bio]" readonly />',
            'separator'   => 'after',
        ] );
        $this->end_controls_section();
    }
    
    public function register_style_tab_controls(){				

		if ( ! acff()->is__premium_only() ) {

			$this->start_controls_section(
				'style_promo_section',
				[
					'label' => __( 'Styles', 'acf-frontend-form-elements' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
					
			$this->add_control(
				'styles_promo',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __( '<p><a target="_blank" href="https://www.frontendform.com/"><b>Go Pro</b></a> to unlock styles.</p>', 'acf-frontend-form-element' ),
					'content_classes' => 'acf-fields-note',
				]
			);
				
			$this->end_controls_section();
		
		}else{			
			do_action( 'acff/style_tab_settings', $this );	
		}
	}
    
    public function get_field_type_options(){
		$groups = acf_frontend_get_field_type_groups();
		$fields = [
			'acf' => $groups['acf'],
			'layout' => $groups['layout'],
		];

		switch( $this->form_defaults['custom_fields_save'] ){
			case 'post':
				$fields['post'] = $groups['post'];
			break;
			case 'user':
				$fields['user'] = $groups['user'];				
			break;
			case 'options':
				$fields['options'] = $groups['options'];
			break;
			case 'term':
					$fields['term'] = $groups['term'];
			break;			
			case 'comment':
				$fields['comment'] = $groups['comment'];
			break;
			case 'product':
				$fields = array_merge( $fields, [
					'product_type' => $groups['product_type'],
					'product' => $groups['product'],
					'inventory' => $groups['product_inventory'],
					'shipping' => $groups['product_shipping'],
					'downloadable' => $groups['product_downloadable'],
					'external' => $groups['product_external'],
					'linked' => $groups['product_linked'],
					'attributes' => $groups['product_attributes'],
					'advanced' => $groups['product_advanced'],
				] );
			break;
			default: 
				$fields = array_merge( $fields, [
					'post' => $groups['post'], 
					'user' => $groups['user'], 
					'term' => $groups['term'],
				] );
				if ( acff()->is__premium_only() ) {
					$fields['options'] = $groups['options'];
					//$fields['comment'] = $groups['comment'];
					if( class_exists( 'woocommerce' ) ){
						$fields['product_type'] = $groups['product_type']; 
						$fields['product'] = $groups['product']; 
						$fields['product_inventory'] = $groups['product_inventory']; 
                        $fields['product_downloadable'] = $groups['product_downloadable'];
                        $fields['product_shipping'] = $groups['product_shipping'];
						$fields['product_external'] = $groups['product_external']; 
						$fields['product_attributes'] = $groups['product_attributes']; 
						$fields['product_advanced'] = $groups['product_advanced']; 
					}
					$fields['security'] = $groups['security'];
				}
		}
		if ( acff()->is__premium_only() ) {
			$fields['security'] = $groups['security'];
		}

		return $fields;
	}
    
    public function get_form_fields( $settings, $wg_id, $form_args = array() )
    {
        $preview_mode = \Elementor\Plugin::$instance->preview->is_preview_mode();
        $edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();
        $current_step = 0;
        $form = [];
        if ( !isset( $settings['multi'] ) ) {
            $settings['multi'] = 'false';
        }
        
        if ( $settings['multi'] == 'true' ) {
            $current_step++;
            $form['steps'][$current_step] = $settings['first_step'][0];
            $form['steps'][$current_step]['fields'] = [];
        }
        
        foreach ( $settings['fields_selection'] as $key => $form_field ) {
            $field_keys = [];
            
            if ( $settings['multi'] == 'true' ) {
                $fields = $form['steps'][$current_step]['fields'];
            } else {
                $fields = $form;
            }
            
            $local_field = $acf_field_groups = $acf_fields = [];
            switch ( $form_field['field_type'] ) {
                case 'ACF_field_groups':
                    if ( $form_field['field_groups_select'] ) {
                        $acf_field_groups = acf_frontend_get_acf_field_choices( $form_field['field_groups_select'] );
                    }
                    break;
                case 'ACF_fields':
                    $acf_fields = $form_field['fields_select'];
                    if ( $acf_fields ) {
                        $field_keys = array_merge( $field_keys, $acf_fields );
                    }
                    break;
                case 'step':                    
                    if ( $settings['multi'] !== 'true' ) {                        
                        if ( $current_step == 0 && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                            echo  '<div class="acf-notice -error acf-error-message"><p>' . __( 'Note: You must turn on "Multi Step" for your steps to work.', 'acf-frontend-form-element' ) . '</p></div>' ;
                            $current_step++;
                        }
                    
                    } else {
                        $current_step++;
                        $form['steps'][$current_step] = $form_field;
                        $fields = [];
                    }
                    
                    break;
                case 'column':
                    
                    if ( $form_field['endpoint'] == 'true' ) {
                        $fields[] = [
                            'column' => 'endpoint',
                        ];
                    } else {
                        $column = [
                            'column' => $form_field['_id'],
                        ];
                        if( $form_field['nested'] ){
                            $column['nested'] = true;
                        }

                        $fields[] = $column;
                    }
                    
                    break;
                case 'tab':
                
                    if ( $form_field['endpoint'] == 'true' ) {
                        $fields[] = [
                            'tab' => 'endpoint',
                        ];
                    } else {
                        $tab = [
                            'tab' => $form_field['_id'],
                        ];
                        $fields[] = $tab;
                    }
                    
                    break;
                case 'message':
                    $fields[] = ['render_content' => $form_field['field_message'] ];
                    break;
                case 'recaptcha':
                    $local_field = array(
                        'key'          => $wg_id .'_'. $form_field['field_type'] .'_'. $form_field['_id'],
                        'type'         => 'recaptcha',
                        'wrapper'      => [
                            'class' => '',
                            'id'    => '',
                            'width' => '',
                        ],
                        'required'     => 0,
                        'version'      => $form_field['recaptcha_version'],
                        'v2_theme'     => $form_field['recaptcha_theme'],
                        'v2_size'      => $form_field['recaptcha_size'],
                        'site_key'     => $form_field['recaptcha_site_key'],
                        'secret_key'   => $form_field['recaptcha_secret_key'],
                        'disabled'     => 0,
                        'readonly'     => 0,
                        'v3_hide_logo' => $form_field['recaptcha_hide_logo'],
                    );
                    break;
                default:
                    $default_value = $form_field['field_default_value'];
                    
                    $local_field = array(
                        'label'         => '',
                        'wrapper'       => [
                            'class' => '',
                            'id'    => '',
                            'width' => '',
                        ],
                        'instructions'  => $form_field['field_instruction'],
                        'required'      => ( $form_field['field_required'] ? 1 : 0 ),
                        'placeholder'   => $form_field['field_placeholder'],
                        'default_value' => $default_value,
                        'disabled'      => $form_field['field_disabled'],
                        'readonly'      => $form_field['field_readonly'],
                        'min'           => $form_field['minimum'],
                        'max'           => $form_field['maximum'],
                        'prepend'        => $form_field['prepend'],
                        'append'        => $form_field['append'],
                    );
                    
                    if ( isset( $data_default ) ) {
                        $local_field['wrapper']['data-default'] = $data_default;
                        $local_field['wrapper']['data-dynamic_value'] = $default_value;
                    }
                    
                    if ( $form_field['field_hidden'] ) {
                        $local_field['wrapper']['class'] = 'acf-hidden';
                    }
                    break;
            }
            
            
            if ( $acf_field_groups ) {
                $fields_exclude = $form_field['fields_select_exclude'];
                
                if ( $fields_exclude ) {
                    $acf_fields = array_diff( $acf_field_groups, $fields_exclude );
                } else {
                    $acf_fields = $acf_field_groups;
                }
                
                $field_keys = array_merge( $field_keys, $acf_fields );
            }
            
            if ( isset( $local_field ) ) {

                $sub_fields = false;
                if( $form_field['field_type'] == 'attributes' ){
                    $sub_fields = $settings['attribute_fields'];     
                } 
                if( $form_field['field_type'] == 'variations' ){
                    $sub_fields = $settings['variable_fields'];     
                }     

                foreach ( acf_frontend_get_field_type_groups() as $name => $group ) {
                    
                    if ( in_array( $form_field['field_type'], array_keys( $group['options'] ) ) ) {
                        $action_name = explode( '_', $name )[0];
                        if( isset( acff()->local_actions[$action_name] ) ){
                            $action = acff()->local_actions[$action_name];
                            $local_field = $action->get_fields_display(
                                $form_field,
                                $local_field,
                                $wg_id,
                                $sub_fields
                            );
                            
                            if ( isset( $form_field['field_label_on'] ) ) {
                                $field_label = ucwords( str_replace( '_', ' ', $form_field['field_type'] ) );
                                $local_field['label'] = ( $form_field['field_label'] ? $form_field['field_label'] : $field_label );
                            }
                            
                            
                            if ( isset( $local_field['type'] ) ) {    
                                
                                if ( $local_field['type'] == 'number' ) {
                                    $local_field['placeholder'] = $form_field['number_placeholder'];
                                    $local_field['default_value'] = $form_field['number_default_value'];
                                }
                                
                                if ( $form_field['field_type'] == 'taxonomy' ) {
                                    $taxonomy = ( isset( $form_field['field_taxonomy'] ) ? $form_field['field_taxonomy'] : 'category' );
                                    $local_field['name'] = $wg_id . '_' . $taxonomy;
                                    $local_field['key'] = $wg_id . '_' . $taxonomy;
                                } else {
                                    $local_field['name'] = $wg_id . '_' . $form_field['field_type'];
                                    $local_field['key'] = $wg_id . '_' . $form_field['field_type'];
                                }
                            
                            }
            
                            if( ! empty( $form_field['default_terms'] ) ){
                                $local_field['default_terms'] = $form_field['default_terms'];
                            }
                        }
                        break;
                    }
                
                }
            }

            if ( isset( $local_field['label'] ) ) {
                
                if ( !$form_field['field_label_on'] ) {
                    $local_field['field_label_hide'] = 1;
                } else {
                    $local_field['field_label_hide'] = 0;
                }
            
            }
            if ( isset( $form_field['button_text'] ) && $form_field['button_text'] ) {
                $local_field['button_text'] = $form_field['button_text'];
            }
            
            if ( isset( $local_field['key'] ) ) {
                $field_key = '';
                
                if ( $edit_mode || !acf_get_field( 'acfef_' . $local_field['key'] ) ) {
                    acf_add_local_field( $local_field );
                    $field_key = $local_field['key'];
                } else {
                    $field_key = 'acfef_' . $local_field['key'];
                }
                
                $field_keys[] = $field_key;
            }
            
            if ( $field_keys ) {
                foreach ( $field_keys as $acf_key ) {
                    $field_data = [
                        'acf'       => $acf_key,
                        'class'     => 'elementor-repeater-item-' .$form_field['_id'],
                        'form'      => $wg_id,
                        'type'      => $form_field['field_type'],
                    ];
                    $fields[] = $field_data;
                }
            }
            
            if ( $settings['multi'] == 'true' ) {
                $form['steps'][$current_step]['fields'] = $fields;
            } else {
                $form = $fields;
            }
            
        }
        return $form;
    }
    
    
    /**
     * Render acf ele form widget output on the frontend.
     *
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render()
    {
        $wg_id = $this->get_id();
        global $post ;
        $current_post_id = acff()->elementor->get_current_post_id();
        $settings = $this->get_settings_for_display();

        $defaults = $new_post = $show_title = $show_content = $show_form = $display = $message = $fields = $fields_exclude = false;
        $hidden_submit = $disabled_submit = '';
        
        

        $form_attributes['data-widget'] = $wg_id;
        
        $form_args = array(
            'id'                    => $wg_id,
            'post_title'            => $show_title,
            'form_attributes'       => $form_attributes,
            'post_content'          => $show_content,
            'submit_value'          => $settings['submit_button_text'],
            'instruction_placement' => $settings['field_instruction_position'],
            'html_submit_spinner'   => '',
            'label_placement'       => 'top',
            'field_el'              => 'div',
            'kses'                  => ! $settings['allow_unfiltered_html'],
            'html_after_fields'     => '',
        );
  
        $form_args = $this->get_settings_to_pass( $form_args, $settings );
        		
		if( isset( $settings['saved_drafts'] ) && $settings['saved_drafts'] && $settings['save_to_post'] == 'new_post'  ){
			$form_args['saved_drafts'] = [
				'saved_drafts_label' => $settings['saved_drafts_label'],
				'saved_drafts_new' => $settings['saved_drafts_new'],
			];
        }
        if( isset( $settings['saved_revisions'] ) && $settings['saved_revisions'] && $settings['save_to_post'] == 'edit_post' ){
			$form_args['saved_revisions'] = [
				'saved_revisions_label' => $settings['saved_revisions_label'],
				'saved_revisions_edit_main' => $settings['saved_revisions_edit_main'],
			];
        }
        if ( isset( $settings['save_progress_button'] ) && $settings['save_progress_button'] ) {
            $form_args['save_progress'] = [ 
                'desc' => $settings['saved_draft_desc'],
                'text' => $settings['saved_draft_text'],                
            ];
        }
        
        if ( $settings['wp_uploader'] ) {
            $form_args['uploader'] = 'wp';
        } else {
            $form_args['uploader'] = 'basic';
        }
        
        if ( ! empty( $settings['emails_to_send'] ) ) {
            $form_args['emails'] = $settings['emails_to_send'];
        }
        if ( $settings['url_parameters'] ) {
            foreach ( $settings['url_parameters'] as $param ) {
                $form_args['redirect_params'][$param['param_key']] = $param['param_value'];
            }
        }
        
        if ( $settings['show_in_modal'] && $settings['open_modal'] ) {
            $form_args['redirect_params']['modal'] = 1;
        }
        
        if ( acff()->is__premium_only() ){	
            if ( isset( $settings['no_reload'] ) && $settings['no_reload'] == 'true' || isset( $ajax_submit ) ) {
                $form_args['ajax_submit'] = true;
            }
        }

        if ( isset( $args['parent_form'] ) ) {
            $form_args['parent_form'] = $args['parent_form'];
        }
    
        
        $form_fields = [];
        if ( $settings['fields_selection'] ) {
            $form_fields = $this->get_form_fields( $settings, $wg_id, $form_args );
        }
        
        if ( $form_fields ) {
            $form_args['fields'] = $form_fields;
        } else {
            $form_args['fields'] = ['none'];
        }
        
        if ( isset( $settings['user_manager'] ) && $settings['user_manager'] != 'none' ) {
            
            if ( $settings['user_manager'] == 'current_user' ) {
                $user_manager = get_current_user_id();
            } else {
                $user_manager = $settings['manager_select'];
            }
            
            $form_args['user_manager'] = $user_manager;
        }
        
        $fields = $form_args['fields'];
        $fields = apply_filters( 'acff/chosen_fields', $fields, $settings );
        if ( !$settings['hide_field_labels'] ) {
            $form_args['label_placement'] = $settings['field_label_position'];
        }

        $settings['display'] = true;

        $form_args = apply_filters( 'acff/form_args', $form_args, $settings );

        $settings['element_id'] = $wg_id;
        
        $settings = apply_filters( 'acf_frontend/show_form', $settings );               
        
        if( $settings['display'] ){
            acff()->form_display->render_form( $form_args );
 
            if ( acff()->is__premium_only() ) {
				if ( \Elementor\Plugin::$instance->editor->is_edit_mode() && $settings['style_messages'] ) {
					echo '<div class="acf-notice -success acf-sucess-message -dismiss"><p>' . $settings['update_message'] . '</p><a href="#" class="acf-notice-dismiss acf-icon -cancel"></a></div>';
					echo '<div class="acf-notice -error acf-error-message -dismiss"><p>' . __( 'Validation failed.', 'acf-frontend-form-element' ) . '</p><a href="#" class="acf-notice-dismiss acf-icon -cancel"></a></div>';
					echo '<div class="acf-notice -limit acff-limit-message"><p>' . __( 'Limit Reached.', 'acf-frontend-form-element' ) . '</p></div>';
				}
			}
        }else{         
            if ( ! empty( $settings['message'] ) && $settings['message'] !== 'NOTHING' ) {
                echo $settings['message'];
            }else{   
                switch ( $settings['not_allowed'] ) {
                    case 'show_message':
                        echo  '<div class="acf-notice -error acf-error-message"><p>' . $settings['not_allowed_message'] . '</p></div>' ;
                        break;
                    case 'custom_content':
                        echo  '<div class="not_allowed_message">' . $settings['not_allowed_content'] . '</div>' ;
                        break;
                }
            }
            
            if( \Elementor\Plugin::$instance->editor->is_edit_mode() ){
                echo '<div class="preview-display">';
                acff()->form_display->render_form( $form_args );
                echo '</div>';
            }
        }
    
    }

    public function get_settings_to_pass( $form_args, $settings ){
        $settings_to_pass = ['form_title','new_post_type','new_post_status','saved_draft_message','new_post_terms','new_terms_select','post_to_edit','url_query_post','post_select','new_product_status','new_product_terms','new_product_terms_select','product_to_edit','product_select','url_query_product','new_term_taxonomy','url_query_term','term_to_edit','term_select','user_to_edit', 'url_query_user','username_prefix','username_suffix','new_user_role','hide_admin_bar','username_default','login_user','steps_tabs_display','steps_counter_display', 'counter_prefix', 'counter_suffix', 'steps_display','tab_links','step_number', 'dynamic', 'dynamic_manager', 'display_name_default', 'pay_for_submission', 'payment_processor', 'payment_button_text','show_total', 'payment_plans', 'before_total', 'after_total', 'credit_card_fields', 'redirect', 'custom_url', 'error_message', 'custom_fields_save', 'save_to_post', 'save_to_user', 'save_to_term', 'save_to_product', 'redirect_action', 'update_message' ];

        foreach( $settings_to_pass as $setting ){
            if( isset( $settings[ $setting ] ) ){
                $form_args[ $setting ] = $settings[ $setting ]; 
            }
        }
        $form_args['show_update_message'] = $settings['show_success_message'];

        return $form_args;
    }
    
    public function __construct( $data = array(), $args = null )
    {
        parent::__construct( $data, $args );
       
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			acf_enqueue_scripts();
            acf_enqueue_uploader();
		}
        $this->form_defaults = $this->get_form_defaults();

        acff()->elementor->form_widgets[] = $this->get_name();
    }

}