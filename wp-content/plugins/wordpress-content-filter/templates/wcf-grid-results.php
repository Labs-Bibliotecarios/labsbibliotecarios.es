<?php
global $wp;
$output = '';
$fields = wcf_get_form_search_values($id);
$settings = wcf_get_form_search_settings($id);
$columns = $settings['columns_per_row'] != '' ? $settings['columns_per_row'] : 3;
$grid_type = isset($settings['grid_type']) && $settings['grid_type'] != '' ? $settings['grid_type'] : 'default';
$masonry = isset($settings['enable_masonry_grid']) && $settings['enable_masonry_grid'] != '' ? $settings['enable_masonry_grid'] : 'no';
if ($masonry == 'yes' && $grid_type != 'table') {
    wp_enqueue_script('wcf-masonry');
}
$template_loop = 'wcf-loop';

$paged = ( get_query_var( 'paged' ) ) ? intval( get_query_var( 'paged' ) ) : 1;
$sort_value = get_query_var('wcf_results_sort') != '' ? get_query_var('wcf_results_sort') : '';


if ( $grid_type == 'posttype') {
    if ( ! post_type_exists( $settings['post_type'][0] ) ) {
	    return esc_html__( 'The post type: ' . $settings['post_type'] . ' doesn\'t existing', 'wcf' );
    }

    $template_loop = $template_loop . '-' . $settings['post_type'][0];
} else if ($grid_type != 'default'){
    $template_loop = $template_loop . '-' . $grid_type;
}

$columns = apply_filters('wcf_result_columns', $columns, $id, $fields, $settings);
$template_loop = apply_filters('wcf_result_loop_template', $template_loop, $id, $fields, $settings);

$loop = wcf_get_template($template_loop, false, false);

if ( $loop == '') {
    $loop = wcf_get_template('wcf-loop', false, false);
    $template_loop = 'wcf-loop';
}

$output .= '<div id="wcf-form-wrapper-' . esc_attr($id) .'" class="wcf-form-wrapper" data-ajax="'.esc_attr($settings['display_results']).'"'
    . ' data-form="'.esc_attr($id).'" data-loop="'.esc_attr($template_loop).'" data-columns="'.esc_attr($columns).'" data-gridtype="'.esc_attr($grid_type).'" data-masonry="'.esc_attr($masonry).'">';

if (is_archive() || is_search()) {
    // display on archive, search template
    ob_start();

    //
    if ($loop != '') {
        if ($grid_type != 'table') {
            include wcf_get_template('wcf-header-grid', false, false);
        }
        include $loop;
        if ($grid_type != 'table') {
            wcf_pagination();
        }

    }
    $output .= ob_get_clean();

} else {
    // display posts as archive
    $paged = ( get_query_var( 'paged' ) ) ? intval( get_query_var( 'paged' ) ) : 1;
    $args = array(
        'paged' => $paged
    );

    $args = wp_parse_args($this->wcf_query->process_vars($id, $_GET), $args);
    query_posts($args);
    ob_start();
    //
    if ($loop != '') {
        if ($grid_type != 'table') {
            include wcf_get_template('wcf-header-grid', false, false);
        }
        include $loop;
        if ($grid_type != 'table') {
            wcf_pagination();
        }
    }
    $output .= ob_get_clean();
    wp_reset_query();

}

$output .= '</div>';

echo $output;