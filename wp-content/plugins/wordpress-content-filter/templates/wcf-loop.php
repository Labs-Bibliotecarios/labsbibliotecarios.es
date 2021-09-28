<?php
$form_id = $id;

$results_columns = $columns;

$classes = array();

$classes[]='wcf-item-result';
$classes[]='wcf-column-' . $results_columns;
$query_string = get_search_query();
if (is_search() && $query_string != '') { ?>
    <header class="wcf-page-header">
            <h2 class="wcf-page-title"><?php printf( esc_html__( 'Resultados para: %s', 'wcf' ), '<span>' . $query_string . '</span>' ); ?></h2>
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
<?php  
		$post_type = get_post_type(); 			
		if ($post_type == 'laboratorio') { $style_type = 'styleLab'; }
		elseif ($post_type == 'proyecto') { $style_type = 'stylePro';}
		else { $style_type ='styleGen'; }				
?>
		
		
		<article id="post-<?php the_ID(); ?>" <?php post_class($classes); ?>>

            <header class="wcf-entry-header  <?php echo $style_type; ?>">
                <!-- post thumbnail -->

                <?php if ( has_post_thumbnail()) : // Check if thumbnail exists ?>
                    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
						 <div style="height:14em;background-image:url(' <?php the_post_thumbnail_url(); ?>');background-size:cover;background-position:center;border:2px solid black;"></div>  
						 
                    </a>
                <?php endif; ?>
                
                <!-- /post thumbnail -->

			</header>

            <div class="wcf-entry-content">
                <!-- post type -->
				<?php echo ucfirst($post_type); ?>
				
				<!-- post title -->
                <h4><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h4>
                <!-- /post title -->

                <!-- post details -->
                <span class="date"><!--<?php the_time('F j, Y'); ?> <?php the_time('g:i a'); ?>--></span>

                
                <!-- post author -->    
                <!-- <span class="author"><?php esc_html_e( 'Promovido por', 'wcf' ); ?> <?php the_author_posts_link(); ?></span> -->
                <!-- /post details -->

<!-- LOCALIZACION -->
<span class="localizacion">             
<?php

	
$terms =	 get_the_terms($post->ID, 'localizacion');
rsort($terms); //ordena el array por id de mayor (comunidades autonomas) a menor (país)
foreach ($terms as $term) {

	if ($term->parent != 0) { 	 // Child - CA o Provincia  ?>
		<a class="localizacion-comunidad" href="https://labsbibliotecarios.es/localizacion/<?php echo $term -> slug; ?>">
			<?php echo $term -> name.", "; // Comunidad autónoma ?>
		</a>
		
	<?php 
	} else { // En caso de no tener parent (sería un país)...	?>
		<a class="localizacion-pais" href="https://labsbibliotecarios.es/localizacion/<?php echo $term -> slug; ?>">
			<?php echo $term -> name; // Escribe el nombre del país ?>
		</a>
	<?php 	
	}
	
}
?>
</span>

            </div>

            <footer class="wcf-entry-meta">
                <!--<p><a class="" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Leer más', 'wcf' ); ?></a></p>-->
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
		<h2><?php esc_html_e( 'Lo sentimos, no hay ningún resultado. Tal vez es momento de pensar en crear un proceso con esas características', 'wcf' ); ?></h2>
	</article>
	<!-- /article -->

<?php endif; ?>
