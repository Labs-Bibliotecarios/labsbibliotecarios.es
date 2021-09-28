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
        <div id="wcf-table-pager"></div>
        <table id="grid" class="table table-hover wcftablesorter wcftablepager" data-pager-id="wcf-table-pager" data-pager-template-id="wcf-table-pager-template">
            <thead class="thead-light">
            <tr>
                <th data-wcftablesorter="false"><?php echo esc_html__( 'Image', 'wcf' );?></th>
                <th><?php echo esc_html__( 'Title', 'wcf' );?></th>
                <th><?php echo esc_html__( 'Category', 'wcf' );?></th>
                <th><?php echo esc_html__( 'Date', 'wcf' );?></th>
                <th><?php echo esc_html__( 'Author', 'wcf' );?></th>
            </tr>
            </thead>
            <tbody>

        <?php
        $count_item = 0;
        while (have_posts()) : the_post();

            ?>
        <!-- article -->
            <tr id="post-<?php the_ID(); ?>" >
                <td><?php if ( has_post_thumbnail()) : // Check if thumbnail exists ?>
                        <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail(array(100,80)); ?></a>
                    <?php endif; ?></td>
                <td><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></td>
                <td class="category"><?php echo wcf_get_categories(', '); ?></td>
                <td class="date"><?php the_time('F j, Y'); ?> <?php the_time('g:i a'); ?></td>
                <td class="author"><?php the_author_posts_link(); ?></td>
            </tr>

    <?php endwhile; ?>
            </tbody>
        </table>
        <div id="wcf-table-pager-template" class="hide">
            <div class="wcftablepager-pager">
                <button type="button" class="wcfbtn wcftablepager-first"></button>
                <button type="button" class="wcfbtn wcftablepager-prev"></button>
                <input type="text" class="wcftablepager-display" readonly="">
                <button type="button" class="wcfbtn wcftablepager-next"></button>
                <button type="button" class="wcfbtn wcftablepager-last"></button>
                <select class="wcftablepager-pagesize">
                    <option selected="" value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="999999">All</option>
                </select>
            </div>
        </div>
    </div>
<?php else: ?>

	<!-- article -->
	<article>
		<h2><?php esc_html_e( 'Sorry, not found.', 'wcf' ); ?></h2>
	</article>
	<!-- /article -->

<?php endif; ?>
