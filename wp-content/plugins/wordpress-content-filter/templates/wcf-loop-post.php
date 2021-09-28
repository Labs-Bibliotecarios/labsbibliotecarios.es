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

    <div class="wcf-row wcf-items-results">
        <?php
        $count_item = 0;
        while (have_posts()) : the_post();

            ?>
            <!-- article -->
            <article id="post-<?php the_ID(); ?>" <?php post_class($classes); ?>>

            <header class="wcf-entry-header">
                <!-- post thumbnail -->
                <?php if ( has_post_thumbnail()) : // Check if thumbnail exists ?>
                    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                        <?php the_post_thumbnail(array(635,245)); ?>
                    </a>
                <?php endif; ?>
                <!-- /post thumbnail -->
            </header>

            <div class="wcf-entry-content">
                <!-- post title -->
                <h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                <!-- /post title -->

                <!-- post details -->
                <span class="date"><?php the_time('F j, Y'); ?> <?php the_time('g:i a'); ?></span>
                <span class="author"><?php esc_html_e( 'Published by', 'wcf' ); ?> <?php the_author_posts_link(); ?></span>
                <!-- /post details -->
            </div>

            <footer class="wcf-entry-meta">
                <p><a class="" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more', 'wcf' ); ?></a></p>
            </footer>

        </article>
        <!-- /article -->
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

    <!-- article -->
    <article>
		<h2><?php esc_html_e( 'Sorry, not found.', 'wcf' ); ?></h2>
	</article>
    <!-- /article -->

<?php endif; ?>
