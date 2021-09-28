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


    <ul class="wcf-row wcf-items-results wcf-items-woo">
        <?php
        $count_item = 0;
        while (have_posts()) : the_post();
            ?>
            <li id="post-<?php the_ID(); ?>" <?php post_class($classes); ?>>
                <div class="wcf-product-inner">
                    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                        <div class="product-image">
                            <?php
                            wc_get_template( 'loop/sale-flash.php' );
                            woocommerce_template_loop_product_thumbnail();
                            ?>
                        </div>

                        <h3><?php the_title(); ?></h3>
                        <?php wc_get_template( 'loop/rating.php' );?>
                        <?php wc_get_template( 'loop/price.php' ); ?>
                    </a>
                </div>
            </li>
            <?php
            if ($masonry == 'no') {
                $count_item++;
                if ($count_item%(int)$results_columns == 0) {?>
                    <div class="wcf-clear"></div>
                <?php }
            }
            ?>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <h2><?php esc_html_e( 'Sorry, not found.', 'wcf' ); ?></h2>
<?php endif; ?>
