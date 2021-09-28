<?php

global $wp_query;

if ($paged > 1) {
    $position_end = $settings['per_page'] * $paged;
    $position_start = $position_end - $settings['per_page'] + 1 ;
    $total = $wp_query->max_num_pages;
    if ($paged == $total) {
        if ($wp_query->found_posts < $position_end) {
            $r = $position_end - $wp_query->found_posts;
            $position_end = $position_end - $r;
        }
    }
} else {
    $position_start = 1;
    if ($wp_query->found_posts < $settings['per_page']) {
        $position_end = (int)$wp_query->found_posts;
    } else {
        $position_end = (int)$settings['per_page'];
    }
}


$sor_options = array(
        '' => esc_html__('Ordenar por', 'wcf'),
        'date-desc' => esc_html__('El más reciente', 'wcf'),
        'date-asc' => esc_html__('El más antiguo', 'wcf'),
        'a-z' => esc_html__('A-Z', 'wcf'),
        'z-a' => esc_html__('Z-A', 'wcf'),
)
?>
<div class="wcf-header-grid">
    <div class="wcf-results-found">
        <div class="wcf-span-found">
            <span><?php echo $wp_query->found_posts;?> <?php echo esc_html__('resultados encontrados', 'wcf');?></span>
           <!-- <span>(<?php echo esc_html__('Mostrando', 'wcf');?> <?php echo esc_attr($position_start);?> - <?php echo esc_attr($position_end);?>)</span>-->
        </div>
    </div>
    <div id="wcf-search-sort">
        <form method="get" action="" autocomplete="off">
            <select name="wcf_results_sort" id="wcf-results-sort" data-page="<?php echo esc_attr($paged);?>" data-sortvalue="<?php echo esc_attr($sort_value);?>">
                <?php
                foreach ($sor_options as $key => $sor_option) {
                    $selected = selected($key, $sort_value, false);
                    echo '<option value="'.esc_attr($key).'" '.$selected.'>'.esc_attr($sor_option).'</option>';
                }
                ?>
            </select>
        </form>
    </div>
</div>
