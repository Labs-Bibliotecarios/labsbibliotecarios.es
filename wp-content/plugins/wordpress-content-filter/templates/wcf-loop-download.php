<?php
$form_id = $id;

$results_columns = $columns;
$classes = array();

$classes[]='wcf-item-result';
$classes[]='wcf-column-' . $results_columns;
$query_string = get_search_query();
if (is_search() && $query_string != '') { ?>
    <header class="wcf-page-header">
            <h2 class="wcf-page-title"><?php printf( esc_html__( 'Search Results for: %s', 'wcf' ), '<span>' . $query_string . '</span>' ); ?></h2>
        </header>
<?php }

if (have_posts()) :
    ?>

    <div class="wcf-row wcf-items-results wcf-items-edd">
        <?php
        $count_item = 0;
        while (have_posts()) : the_post();

            ?>
            <div id="post-<?php the_ID(); ?>" <?php post_class($classes); ?>>
                <div class="edd_download_inner">
                    <?php

                    do_action( 'edd_download_before' );

                    edd_get_template_part( 'shortcode', 'content-image' );
                    do_action( 'edd_download_after_thumbnail' );


                    edd_get_template_part( 'shortcode', 'content-title' );
                    do_action( 'edd_download_after_title' );

//                    edd_get_template_part( 'shortcode', 'content-price' );
                    ?>
                    <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                        <div itemprop="price" class="edd_price">
                            <?php edd_price( get_the_ID() ); ?>
                        </div>
                    </div>
                    <?php
                    do_action( 'edd_download_after_price' );


                    do_action( 'edd_download_after' );
                    ?>
                </div>
            </div>
            <?php
            if ($masonry == 'no') {
                $count_item++;
                if ($count_item%(int)$results_columns == 0) {?>
                    <div class="wcf-clear"></div>
                <?php }
            }
            ?>
        <?php endwhile; ?>
    </div>

<?php else: ?>

    <h2><?php esc_html_e( 'Sorry, not found.', 'wcf' ); ?></h2>

<?php endif; ?>
