<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DCE_Widget_Template extends \DynamicContentForElementor\Widgets\DCE_Widget_Prototype
{
    protected function _register_controls()
    {
        $this->start_controls_section('section_dynamictemplate', ['label' => __('Template', 'dynamic-content-for-elementor')]);
        $this->add_control('dynamic_template', ['label' => __('Select Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library']);
        $this->add_control('data_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'description' => __('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => __('Same', 'dynamic-content-for-elementor'), 'label_off' => __('other', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'separator' => 'before']);
        $this->add_control('other_post_source', ['label' => __('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->add_control('other_user_id', ['label' => __('Select other User', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Force User content', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'users', 'condition' => ['data_source' => '']]);
        $this->add_control('other_author_id', ['label' => __('Select other Author', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Force Author content', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'users', 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
    }
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id();
        $dce_default_template = $settings['dynamic_template'];
        $inlinecss = '';
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $inlinecss = ' inlinecss="true"';
        }
        $post_id = '';
        if (empty($settings['data_source'])) {
            if ($settings['other_post_source']) {
                $post_id .= ' post_id="' . $settings['other_post_source'] . '"';
            }
            if ($settings['other_user_id']) {
                $post_id .= ' user_id="' . $settings['other_user_id'] . '"';
            }
            if ($settings['other_author_id']) {
                $post_id .= ' author_id="' . $settings['other_author_id'] . '"';
            }
        }
        ?>
		<div class="dce-template">
		<?php 
        if (!empty($dce_default_template)) {
            echo do_shortcode('[dce-elementor-template id="' . $dce_default_template . '"' . $post_id . $inlinecss . ']');
        }
        ?>
		</div>
		<?php 
    }
}
