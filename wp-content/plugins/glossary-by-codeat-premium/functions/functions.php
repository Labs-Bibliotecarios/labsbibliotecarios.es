<?php
/**
 * Glossary
 *
 * @package   Glossary
 * @author    Codeat <support@codeat.co>
 * @copyright 2020
 * @license   GPL 2.0+
 * @link      https://codeat.co
 */

/**
 * Generate the list of terms
 *
 * @param string $order Order.
 * @param int    $num   Amount.
 * @param string $tax   Taxonomy name.
 * @param string $theme Theme.
 * @return string
 */
function get_glossary_terms_list( string $order, int $num, string $tax = '', string $theme = '' ) {
	$orderby = 'date';

	if ( 'last' !== $order ) {
		$orderby = 'title';

		if ( 'asc' === $order ) {
			$order = 'ASC';
		}
	}

	$args = array(
		'post_type'              => 'glossary',
		'order'                  => $order,
		'orderby'                => $orderby,
		'posts_per_page'         => $num,
		'post_status'            => 'publish',
		'update_post_meta_cache' => false,
		'fields'                 => 'ids',
	);

	if ( !empty( $tax ) && 'any' !== $tax ) {
		$field = 'slug';

		if ( is_numeric( $tax ) ) {
			$tax   = intval( $tax );
			$field = 'term_id';
		}

		$args[ 'tax_query' ] = array( // phpcs:ignore
			array(
				'taxonomy' => 'glossary-cat',
				'terms'    => array( $tax ),
				'field'    => $field,
			),
		);
	}

	$glossary = new WP_Query( $args );

	if ( !empty( $theme ) ) {
		$theme = ' theme-' . $theme;
	}

	if ( $glossary->have_posts() ) {
		$out = '<ul class="glossary-terms-list' . $theme . '">';

		while ( $glossary->have_posts() ) {
			$glossary->the_post();
			$out .= '<li><a href="' . get_glossary_term_url( (int) get_the_ID() ) . '">' . get_the_title() . '</a></li>';
		}

		$out .= '</ul>';
		wp_reset_postdata();

		return $out;
	}

	return '';
}

/**
 * Get the url of the term attached
 *
 * @param int|string $term_id The term ID.
 * @return string
 */
function get_glossary_term_url( $term_id = '' ) {
	if ( empty( $term_id ) ) {
		$term_id = get_the_ID();
	}

	$url_suffix = '';

	if ( gt_fs()->is_plan__premium_only( 'professional' ) ) {
		$settings = gl_get_settings();

		if ( isset( $settings['url_suffix'] ) ) {
			$url_suffix = $settings['url_suffix'];
		}
	}

	$type = esc_html( get_post_meta( (int) $term_id, GT_SETTINGS . '_link_type', true ) );
	$link = esc_html( get_post_meta( (int) $term_id, GT_SETTINGS . '_url', true ) );
	$cpt  = esc_html( get_post_meta( (int) $term_id, GT_SETTINGS . '_cpt', true ) );

	if ( empty( $link ) && empty( $cpt ) ) {
		return (string) get_the_permalink( (int) $term_id ) . $url_suffix;
	}

	if ( 'external' === $type || empty( $type ) ) {
		return (string) $link . $url_suffix;
	}

	if ( 'internal' === $type ) {
		return (string) get_the_permalink( (int) $cpt ) . $url_suffix;
	}

	return '';
}

/**
 * Generate a list of category terms
 *
 * @param string $order Order.
 * @param string $num   Amount.
 * @param string $theme Theme.
 * @return string
 */
function get_glossary_cats_list( string $order = 'ASC', string $num = '0', string $theme = '' ) {
	$num = (int) $num;

	if ( 0 !== $num ) {
		++$num;
	}

	$taxs = get_terms(
		array(
			'hide_empty' => false,
			'taxonomy'   => 'glossary-cat',
			'order'      => $order,
			'number'     => $num,
			'orderby'    => 'title',
		)
	);

	if ( !empty( $theme ) ) {
		$theme = ' theme-' . $theme;
	}

	$out = '<ul class="glossary-cats-list' . $theme . '">';

	if ( !empty( $taxs ) && !is_wp_error( $taxs ) && is_array( $taxs ) ) {
		foreach ( $taxs as $tax ) {
			if ( !empty( $tax->parent ) ) {
				continue;
			}

			$subout   = '';
			$tax_link = get_term_link( $tax );

			if ( !is_wp_error( $tax_link ) && is_object( $tax ) ) {
				$out .= '<li><a href="' . $tax_link . '">' . $tax->name . '</a>';

				foreach ( $taxs as $index => $subcategory ) { //phpcs:ignore
					if ( !is_object( $subcategory ) ) {
						continue;
					}

					if ( $subcategory->parent !== $tax->term_id ) {
						continue;
					}

					$taxsub_link = get_term_link( $subcategory );

					if ( is_wp_error( $taxsub_link ) ) {
						continue;
					}

					$subout .= '<li><a href="' . $taxsub_link . '">' . $subcategory->name . '</a></li>';
				}
			}

			if ( !empty( $subout ) ) {
				$out .= '<ul>' . $subout . '</ul>';
			}

			$out .= '</li>';
		}

		$out .= '</ul>';

		return $out;
	}

	return '';
}

/**
 * Check if text is RTL
 *
 * @param string $string The string.
 * @return int|bool
 */
function gl_text_is_rtl( string $string ) {
	$rtl_chars_pattern = '/[\x{0590}-\x{05ff}\x{0600}-\x{06ff}]/u';

	return preg_match( $rtl_chars_pattern, $string );
}

/**
 * Check if word is written with latin characters
 *
 * @param string $string The string to validate.
 * @return bool
 */
function gl_is_latin( string $string ) {
	return !preg_match( '/[^\\p{Common}\\p{Latin}]/u', $string );
}

/**
 * Return the cached value of terms count
 *
 * @return string
 */
function gl_get_terms_count() {
	return get_option( GT_SETTINGS . '_count_terms', true );
}

/**
 * Return the cached value of related terms count
 *
 * @return string
 */
function gl_get_related_terms_count() {
	return get_option( GT_SETTINGS . '_count_related_terms', true );
}

/**
 * Update the database with cached value for count of terms and related terms
 *
 * @return void
 */
function gl_update_counter() {
	$args  = array(
		'post_type'      => 'glossary',
		'posts_per_page' => -1,
		'order'          => 'asc',
		'post_status'    => 'publish',
	);
	$query = new WP_Query( $args );

	$count         = 0;
	$count_related = 0;

	foreach ( $query->posts as $post ) {
		++$count;
		$post_id = $post;

		if ( is_object( $post ) ) {
			$post_id = $post->ID;
		}

		if ( !is_int( $post_id ) ) {
			continue;
		}

		$related = gl_related_post_meta( $post_id );

		if ( empty( $related ) ) {
			continue;
		}

		$count_related += count( $related );
	}

	update_option( GT_SETTINGS . '_count_terms', $count );
	update_option( GT_SETTINGS . '_count_related_terms', $count_related );
}

/**
 * Get the list of terms by A2Z index
 *
 * @param array $atts The parameters.
 * @return array The terms.
 */
function gl_get_a2z_initial( array $atts = array() ) {
	global $wpdb;
	$default   = array(
		'show_counts' => false,
		'taxonomy'    => '',
		'letters'     => '',
	);
	$atts      = array_merge( $default, $atts );
	$count_col = $join = $tax_slug = '';

	if ( $atts[ 'show_counts' ] ) {
		$count_col = ", COUNT( substring( TRIM( UPPER( $wpdb->posts.post_title ) ), 1, 1) ) as counts";
	}

	if ( $atts[ 'taxonomy' ] !== 'any' ) {
		$tax_slug = " AND $wpdb->terms.slug = '" . $atts[ 'taxonomy' ] . "' AND $wpdb->term_taxonomy.taxonomy = 'glossary-cat' ";

		$join = " LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) LEFT JOIN $wpdb->terms ON ($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id)";
	}

	$filter_initial = '';

	if ( !empty( $atts[ 'letters' ] ) ) {
		$filter_initial    = ' AND (';
		$atts[ 'letters' ] = explode( ',', $atts[ 'letters' ] );

		foreach ( $atts[ 'letters' ] as $key => $initial ) {
			$filter_initial .= ' SUBSTRING(' . $wpdb->posts . '.post_title,1,1) = "' . $initial . '" OR';

			if ( count( $atts[ 'letters' ] ) !== $key + 1 ) {
				continue;
			}

			$filter_initial = mb_substr( $filter_initial, 0, -2 );
		}

		$filter_initial .= ')';
	}

	$querystr = "SELECT DISTINCT SUBSTRING( TRIM( UPPER( $wpdb->posts.post_title ) ), 1, 1) as initial" . $count_col . " FROM $wpdb->posts" . $join . " WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'glossary'" . $tax_slug . $filter_initial . " GROUP BY initial ORDER BY TRIM( UPPER( $wpdb->posts.post_title ) );";

	return $wpdb->get_results( $querystr, ARRAY_A ); // phpcs:ignore
}

/**
 * Return initials and ids
 *
 * @param array $atts The parameters.
 * @return array Initial and Terms.
 */
function gl_get_a2z_ids( array $atts = array() ) {
	global $wpdb;
	$default = array(
		'show_counts' => false,
		'taxonomy'    => '',
		'letters'     => '',
	);
	$atts    = array_merge( $default, $atts );
	$join    = $tax_slug = '';

	if ( !empty( $atts[ 'taxonomy' ] ) ) {
		$tax_slug = " AND $wpdb->terms.slug = '" . $atts[ 'taxonomy' ] . "' AND $wpdb->term_taxonomy.taxonomy = 'glossary-cat' ";
		$join     = " LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) LEFT JOIN $wpdb->terms ON ($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id)";
	}

	$filter_initial = '';

	if ( !empty( $atts[ 'letters' ] ) ) {
		$filter_initial    = ' AND (';
		$atts[ 'letters' ] = explode( ',', $atts[ 'letters' ] );

		foreach ( $atts[ 'letters' ]as $key => $initial ) {
			$filter_initial .= ' SUBSTRING(' . $wpdb->posts . '.post_title,1,1) = "' . $initial . '" OR';

			if ( count( $atts[ 'letters' ] ) !== $key + 1 ) {
				continue;
			}

			$filter_initial = substr( $filter_initial, 0, -2 );
		}

		$filter_initial .= ')';
	}

	$querystr = "SELECT ID FROM $wpdb->posts" . $join . " WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'glossary'" . $tax_slug . $filter_initial . " ORDER BY TRIM( UPPER( $wpdb->posts.post_title ) );";

	$ids = $wpdb->get_results( $querystr, ARRAY_A ); // phpcs:ignore

	$id_cleaned = array();

	foreach ( $ids as $id ) {
		$id_cleaned[] = $id[ 'ID' ];
	}

	return $id_cleaned;
}

/**
 * Length of the string based on encode
 *
 * @param string $string The string to get the length.
 * @return int
 */
function gl_get_len( string $string ) {
	if ( gl_text_is_rtl( $string ) ) {
		return mb_strlen( $string );
	}

	return mb_strlen( $string, 'latin1' );
}

/**
 * Get a checkbox settings as boolean
 *
 * @param string $value The ID label of the settings.
 * @return bool
 */
function gl_get_bool_settings( string $value ) {
	$settings = gl_get_settings();

	return isset( $settings[ $value ] ) && (bool) $settings[ $value ];
}

/**
 * Check the settings and if is a single term page
 *
 * @param int $post_id The ID to get this setting.
 * @return array
 */
function gl_related_post_meta( int $post_id ) {
	$value = get_post_meta( $post_id, GT_SETTINGS . '_tag', true );
	$value = array_map( 'trim', explode( ',', $value ) );

	if ( empty( $value[ 0 ] ) ) {
		$value = array();
	}

	return $value;
}

/**
 * Get the settings of the plugin in a filterable way
 *
 * @since 1.0.0
 * @return array
 */
function gl_get_settings() {
	$settings = get_option( GT_SETTINGS . '-settings' );

	/**
	 * Alter the global settings
	 *
	 * @param array $settings The settingss.
	 * @since 1.5.0
	 * @return array $term_queue We need the settings.
	 */
	return apply_filters( 'glossary_settings', $settings );
}

/**
 * Return the base url for glossary post type
 *
 * @return string
 */
function gl_get_base_url() {
	$base_url = get_post_type_archive_link( 'glossary' );

	if ( !$base_url ) {
		$base_url = esc_url( home_url( '/' ) );

		if ( 'page' === get_option( 'show_on_front' ) ) {
			$base_url = esc_url( (string) get_permalink( get_option( 'page_for_posts' ) ) );
		}
	}

	return $base_url;
}

/**
 * Return the tooltip type
 *
 * @param string $type    The type of tooltip.
 * @param bool   $as_true As inverse.
 * @return bool
 */
function is_type_inject_set_as( string $type, bool $as_true = true ) {
	$settings = gl_get_settings();

	if ( !$as_true ) {
		return isset( $settings[ 'tooltip' ] ) && $settings[ 'tooltip' ] !== $type;
	}

	return isset( $settings[ 'tooltip' ] ) && $settings[ 'tooltip' ] === $type;
}
