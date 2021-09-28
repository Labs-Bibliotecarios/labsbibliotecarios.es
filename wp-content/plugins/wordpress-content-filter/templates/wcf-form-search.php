<?php
/**
 * @version    $Id$
 * @package    WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */


$wcf_form_class = 'wcf-form-search';
$wcf_form_class .= $settings['toggle_field'] == 'yes' ? ' wcf-arrow-field' : '';
$toggle_searchform = isset($settings['toggle_searchform']) && $settings['toggle_searchform'] != '' ? $settings['toggle_searchform'] : 'yes';
$check_toggle_mobile = $settings['toggle_searchform'] == 'yes' && wp_is_mobile() ? 1 : 0;
$wcf_form_class .= $check_toggle_mobile && wp_is_mobile() ? ' wcf-off-menu' : '';
if ($check_toggle_mobile) {
    echo '<button type="button" class="wcf-toggle-searchform">'.esc_html__('Show Filter', 'wcf').'</button>';
}
echo '<form name="wcf-form-'.esc_attr($id).'" id="wcf-form-'.esc_attr($id).'" class="'.esc_attr($wcf_form_class).'" method="get" action="'.home_url().'" role="search" data-form="'.esc_attr($id).'" data-ajax="'.esc_attr($settings['display_results']).'" data-auto="'.esc_attr($settings['auto_filter']).'">';

do_action( 'wcf_form_search_before', $id, $settings);

$form_title = '';
if (isset($title)) {

	if ($title != '') {
		$form_title = $title;
	} else {
		$form = get_post($id);
		$form_title = $form->post_title;
	}

	if ($form_title != '') {
		echo '<h3 class="wcf-form-title">'.$form_title.'</h3>';
	}
}
if ($check_toggle_mobile) {
    echo '<div class="wcf-off-menu-top"><label class="search-query">'.esc_html__('Filter Fields', 'wcf').'</label><a href="#" class="wcf-off-menu-toggle"><i class="fa fa-times"></i></a> </div>';
}
wcf_display_form_fields($id, $fields, $settings);

do_action( 'wcf_form_search_after', $id, $settings);

echo '</form>';
echo '<div class="wcf-clear"></div>';