<?php
/**
 * @version    $Id$
 * @package    WordPress Content Filter
 * @author     ZuFusion
 * @copyright  Copyright (C) 2021 ZuFusion All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */


if ( ! class_exists( 'WCF_Site_Query' ) ) {


	/**
	 * Class WCF_Site_Query base
	 */
	class WCF_Site_Query {

		public function __construct() {

			if ( ! is_admin() ) {
				add_action( 'pre_get_posts', array( $this, 'query_post' ), 20, 1 );
			}

			add_action('wp_ajax_nopriv_wcf_search_ajax', array($this, 'wcf_search_ajax' ));
			add_action('wp_ajax_wcf_search_ajax', array($this, 'wcf_search_ajax' ));
		}

		/**
		 * Build Query for search
		 * @param $query
		 */
		function query_post( $query ) {

			if ( is_admin() ) {
				return;
			}


			if ( $query->is_main_query() && $query->is_search() ) {

				if (isset($_GET['form_id'])) {
					$vars = $this->process_vars($_GET['form_id'],$_GET);

					if (!empty($vars)) {
						foreach($vars as $key => $var) {
							$query->set($key, $var);
						}
					}
				}
			}

		}

		/**
		 * Process vars for query posts
		 * @param int   $form_id
		 * @param array $vars
		 * @return mixed
		 */
		function process_vars( $form_id = 0, $vars = array()) {

			global $wcf_register_fields;

			$var_processed = array();

			$settings    = wcf_get_form_search_settings( $form_id );
			$form_values = wcf_get_form_search_values( $form_id );

			if ( !isset($vars['post_types'])) {
				$vars['post_types'] = implode(',', $settings['post_type']);
			}
			if ( !isset($vars['posts_per_page'])) {
				$vars['posts_per_page'] = $settings['per_page'];
			}

			// Filter by post types
			$post_types  = $vars['post_types'] != '' ? explode( ',', $vars['post_types'] ) : '';
			$posts_per_page  = $vars['posts_per_page'] != '' ? $vars['posts_per_page'] : '';


			if ( !empty($post_types)) {
				$var_processed['post_type'] = $post_types;
			}
			if ( !empty($posts_per_page)) {
				$var_processed['posts_per_page'] = $posts_per_page;
			}

			//filter default value
			$default_filter = array();

			foreach ( $form_values as $_field => $_field_setting ) {
				$field_data = explode('::', $_field);
				$field_id = $field_data[0];
				$field_type = $field_data[1];

				switch ($field_type) {
					case 'taxonomy':
						if ($_field_setting['default_value'] != '') {
							if ($_field_setting['display_type'] == 'radio' || $_field_setting['display_type'] == 'select') {
								$default_filter['wcf_taxonomy'][$field_id] = $_field_setting['default_value'];
							} else {
								$default_filter['wcf_taxonomy'][$field_id] = preg_split( '/\r\n|\r|\n/', $_field_setting['default_value'] );
							}
						}

						break;
					case 'acf':
						$acf_field =  acf_get_field($_field_setting['field']  );
						$value = $acf_field['default_value'];
						if ($acf_field['type'] == 'select') {
							if ($acf_field['multiple'] == 0) {
								$value = is_array($acf_field['default_value']) ? $acf_field['default_value'][0] : $acf_field['default_value'];
							}
						}

						if ($value != '' || !empty($value)) {
							$default_filter['wcf_acf'][$field_id] = $value;
						}
						break;
					case 'meta_field':

						if ($_field_setting['default_value'] != '') {
							if ($_field_setting['display_type'] == 'radio' || $_field_setting['display_type'] == 'select') {
								$default_filter['wcf_meta_field'][$field_id] = $_field_setting['default_value'];
							} else {
								$default_filter['wcf_meta_field'][$field_id] = preg_split( '/\r\n|\r|\n/', $_field_setting['default_value'] );
							}
						}

						break;
					case 'author':

						if ($_field_setting['default_value'] != '') {
							$default_filter['wcf_author'][$field_id] = $_field_setting['default_value'];
						}

						break;
					case 'sort':
						$default_filter['wcf_sort'][$field_id] = array(
							'order_by' => $_field_setting['order_by'],
							'order' => $_field_setting['sorting_order'],
						);
						break;
				}
			}


			// Advanced Custom Fields/ Meta Fields
			$acf_default = isset($default_filter['wcf_acf']) ? $default_filter['wcf_acf'] : array();
			$meta_field_default = isset($default_filter['wcf_meta_field']) ? $default_filter['wcf_meta_field'] : array();
			$acf        = isset( $vars['wcf_acf'] ) ? $vars['wcf_acf'] : $acf_default;
			$meta_field = isset( $vars['wcf_meta_field'] ) ? $vars['wcf_meta_field'] : $meta_field_default;

			$meta_fields = array_merge( $acf, $meta_field );

			if ( count( $meta_fields ) ) {

				$meta_query_append = array();
				$meta_count        = 0;

				foreach ( $meta_fields as $field_id => $val ) {

					$field_key_exist = wcf_search_key_array( $field_id, $form_values, true );
					$field_setting   = isset( $form_values[ $field_key_exist ] ) ? $form_values[ $field_key_exist ] : array();
					if ( empty( $field_setting )) {
						continue;
					}

					$field_compare = strtoupper( str_replace( '_', ' ', $field_setting['compare'] ) );

					$val_bk     = $val;
					$compare_bk = $field_compare;

					if ( is_array( $val ) ) {

						if (in_array('', $val)) {
							continue;
						} else if (in_array('all', $val)) {

							if ($field_setting['field_type'] == 'meta_field') {
								$opts    = explode( "\n", $field_setting['options'] );
								$arr_tem = array();
								if ( ! empty( $opts ) ) {
									foreach ( $opts as $opt ) {
										$opt_tem   = explode( '::', $opt );
										$arr_tem[] = $opt_tem[0];
									}

									$val_bk = $arr_tem;
								}

							} else if ($field_setting['field_type'] == 'acf') {

								$field =  acf_get_field($field_setting['field']  );
								if (isset($field['choices']) && !empty($field['choices'])) {
									$arr_tem = array();
									foreach ( $field['choices'] as $key => $choices ) {
										$arr_tem[] = $key;
									}
									$val_bk = $arr_tem;

								}
							}

						}

						// for IN, NOT IN
						if ( $field_compare == 'IN' || $field_compare == 'NOT IN' ) {

							$val_bk = implode( '|', $val_bk );

							if ( $compare_bk == 'IN' ) {
								$compare_bk = 'REGEXP';
							} else if ( $compare_bk == 'NOT IN' ) {
								$compare_bk = 'NOT REGEXP';
							}

						}

					} else {

						if ( $val == 'all' ) {
							// todo

							continue;

						} elseif ($val == '') {
							continue;
						} else {
							// for BETWEEN, NOT BETWEEN
							if ( $field_compare == 'BETWEEN' || $field_compare == 'NOT BETWEEN' ) {

								preg_match_all( '/([0-9]+)-([0-9]+)/i', $val_bk, $matches );

								if ( count( $matches[0] ) == 1 ) {
									$between_data  = explode( '-', $matches[0][0] );
									$between_start = $between_data[0];
									$between_end   = $between_data[1];
									$val_bk        = array( $between_start, $between_end );
								}
							}
						}

					}

					$meta_query_append[] = array(
						'key'     => $field_setting['meta_key'],
						'value'   => $val_bk,
						'compare' => $compare_bk
					);

					do_action( 'wcf_meta_field_query', $meta_query_append, $val_bk, $compare_bk, $field_setting );

					$meta_count ++;
				}

				if ( $meta_count > 1 ) {
					$meta_query_append['relation'] = strtoupper( $settings['meta_relation'] );
				}

				if (!empty($meta_query_append)) {
					$var_processed['meta_query'] = $meta_query_append ;
				}

			}

			// Taxonomy
			$taxonomy_default = isset($default_filter['wcf_taxonomy']) ? $default_filter['wcf_taxonomy'] : array();
			$wcf_taxonomy = isset( $vars['wcf_taxonomy'] ) ? $vars['wcf_taxonomy'] : $taxonomy_default;

			if ( count( $wcf_taxonomy ) ) {

				$tax_query_append = array();
				$tax_count        = 0;

				foreach ( $wcf_taxonomy as $field_id => $val ) {

					$field_key_exist = wcf_search_key_array( $field_id, $form_values, true );
					$field_setting   = isset( $form_values[ $field_key_exist ] ) ? $form_values[ $field_key_exist ] : array();

					if ( empty( $field_setting ) ) {
						continue;
					}

					$taxonomy_operator = strtoupper( str_replace( '_', ' ', $field_setting['taxonomy_operator'] ) );

					$val_bk = $val;

					if ( is_array( $val ) ) {

						if (in_array('', $val)) {
							continue;
						} else if (in_array('all', $val)) {

							$args = array(
								'exclude' => $field_setting['exclude_terms'],
								'hide_empty'    => $field_setting['hide_empty_terms'] == 'yes' ? true : false,
							);

							$terms = get_terms( $field_setting['taxonomy'], $args );

							$val_bk = array();
							if ( ! empty( $terms ) ) {
								foreach ( $terms as $term ) {
									$val_bk[] = $term->slug;
								}
							}

						}

					} else {
						// for select all

						if ( $val == 'all' ) {

							$args = array(
								'exclude' => $field_setting['exclude_terms'],
								'hide_empty'    => $field_setting['hide_empty_terms'] == 'yes' ? true : false,
							);

							$terms = get_terms( $field_setting['taxonomy'], $args );

							$val_bk = array();
							if ( ! empty( $terms ) ) {
								foreach ( $terms as $term ) {
									$val_bk[] = $term->slug;
								}
							}

						} elseif ($val == '') {
							continue;
						} else {
							$val_bk = array( $val );
						}
					}

					$tax_query_append[] = array(
						'taxonomy' => $field_setting['taxonomy'],
						'field'    => 'slug',
						'terms'    => $val_bk,
						'operator' => $taxonomy_operator,
					);

					do_action( 'wcf_tax_field_query', $tax_query_append, $val_bk, $taxonomy_operator, $field_setting );

					$tax_count ++;
				}

				if ( $tax_count > 1 ) {
					$tax_query_append['relation'] = strtoupper( $settings['taxonomy_relation'] );
				}

				if (!empty($tax_query_append)) {
					$var_processed['tax_query'] = $tax_query_append;
				}

			}

			// date
			$wcf_date = isset( $vars['wcf_date'] ) ? $vars['wcf_date'] : array();

			if ( count( $wcf_date ) ) {

				$date_query = array();
				$date_count = 0;

				foreach ( $wcf_date as $field_id => $date) {

					$date_from = $date['date_from'];
					$date_to = $date['date_to'];

					$date_data = array();

					if ($date_from != '') {

						$date_data['after'] = date( 'F j, Y', strtotime( $date_from ) );
					}
					if ($date_to != '') {
						$date_data['before'] = date( 'F j, Y', strtotime( $date_to ) );
					}

					if (count($date_data) > 1) {
						$date_data['inclusive'] = true;
					}
					if (!empty($date_data)) {
						$date_query[] = $date_data;
						$date_count++;
					}
				}

				if ($date_count > 1) {
					$date_query['relation'] = strtoupper($settings['date_relation']);
				}

				if (!empty($date_query)) {
					$var_processed['date_query'] = $date_query;
				}
			}

			//Author
			$wcf_author_default = isset($default_filter['wcf_author']) ? $default_filter['wcf_author'] : array();
			$wcf_author = isset( $vars['wcf_author'] ) ? $vars['wcf_author'] : $wcf_author_default;
			$author_ids = array();

			if (count($wcf_author)) {
				foreach ( $wcf_author as $field_id => $author ) {

					if ($author == 'all') {
					} else {
						$author_ids[] = $author;
					}
				}
			}

			//Sort
			$wcf_sort_default = isset($default_filter['wcf_sort']) ? $default_filter['wcf_sort'] : array();
			$wcf_sort = isset( $vars['wcf_sort'] ) ? $vars['wcf_sort'] : $wcf_sort_default;

			if (count($wcf_sort)) {

				$order_bys = array();
				foreach ( $wcf_sort as $field_id => $sort_data ) {
					$order_by = $sort_data['order_by'];
					$order = $sort_data['order'];
					$order_bys[$order_by] = $order;
				}

				if (!empty($order_bys)) {
					$var_processed['orderby'] = $order_bys;
				}

			}

			//Sort Results
            $wcf_sort_results = isset( $vars['wcf_results_sort'] ) ? $vars['wcf_results_sort'] : '';

			if ($wcf_sort_results != '') {
                $order_bys = array();
                switch ($wcf_sort_results) {
                    case '' :
                    default :
                        break;
                    case 'date-desc' :
                        $order_by = 'date';
                        $order = 'DESC';
                        $order_bys[$order_by] = $order;
                        break;
                    case 'date-asc' :
                        $order_by = 'date';
                        $order = 'ASC';
                        $order_bys[$order_by] = $order;
                        break;
                    case 'a-z' :
                        $order_by = 'title';
                        $order = 'ASC';
                        $order_bys[$order_by] = $order;
                        break;
                    case 'z-a' :
                        $order_by = 'title';
                        $order = 'DESC';
                        $order_bys[$order_by] = $order;
                        break;

                }

				if (!empty($order_bys)) {
					$var_processed['orderby'] = $order_bys;
				}

			}

			//Price
			$wcf_range_price = isset( $vars['wcf_range_price'] ) ? $vars['wcf_range_price'] : array();
			$product_ids = array();

			if (count($wcf_range_price)) {

				$prices = array();

				foreach ( $wcf_range_price as $field_id => $range_price ) {

					$field_key_exist = wcf_search_key_array( $field_id, $form_values, true );
					$field_setting   = isset( $form_values[ $field_key_exist ] ) ? $form_values[ $field_key_exist ] : array();

					if ( empty( $field_setting ) ) {
						continue;
					}

					$price_min = $range_price['min'];
					$price_max = $range_price['max'];

					if (in_array($field_setting['shop'], $post_types)) {
						$product_ids = array_merge($product_ids, $this->price_filter( $price_min, $price_max, $field_setting['shop'] ));
					}

				}

				$var_processed['is_shop_price'] = true;

				if ($product_ids) {
					$var_processed['post__in'] = $product_ids;
				} else {
					$var_processed['post__in'] = array(0);
				}
			}

			//Rating
			$wcf_rating = isset( $vars['wcf_rating'] ) ? $vars['wcf_rating'] : array();

			$rating_products = array();

			if (count($wcf_rating)) {

				foreach ( $wcf_rating as $field_id => $rating ) {

					if (isset($rating['filter']) && $rating['filter'] == 'yes') {

						$field_key_exist = wcf_search_key_array( $field_id, $form_values, true );
						$field_setting   = isset( $form_values[ $field_key_exist ] ) ? $form_values[ $field_key_exist ] : array();
						if ( empty( $field_setting ) ) {
							continue;
						}

						if (in_array($field_setting['shop'], $post_types)) {
							$rating_products = array_merge($rating_products, $this->rating_filter($field_setting['shop'], 1, $rating['star'] ));
						}
						break; // only support a rating field on a search form
					}

				}


//				var_dump($rating_products);die;

				if (isset($var_processed['is_shop_price'])) {

//					var_dump($rating_products);die;
					if ($product_ids) {

						if (!empty($rating_products)) {

							$product_ids_new = array();
							foreach ( $product_ids as $pro_id ) {
								if (in_array($pro_id, $rating_products)) {
									$product_ids_new[] = $pro_id;
								}
							}
							$var_processed['post__in'] = $product_ids_new;
						}

					}

				} else {

					if ($rating_products) {
						$var_processed['post__in'] = $rating_products;
					} else {
						$var_processed['post__in'] = array(0);
					}

				}
			}



			$author_ids     = array_unique( $author_ids );

			if ( ! empty( $author_ids ) ) {
				$var_processed['author__in'] = $author_ids;
			}

			//by roles
            //display all public/private posts
            $var_processed['post_status'][] = 'publish';
            $var_processed['wcf_content_filter'] = true;
            $var_processed['wcf_settings'] = $settings;

			if (is_user_logged_in()) {
                $role = isset($settings['roles_show_private']) && $settings['roles_show_private'] != '' ? $settings['roles_show_private'] : 'no';
                $user_show_private = isset($settings['user_show_private']) && $settings['user_show_private'] != '' ? $settings['user_show_private'] : 'no';
                if ($role !== 'no' || $user_show_private == 'yes') {
                    $var_processed['wcf_private'] = true;
                }
            }

            add_filter( 'posts_where', array( $this, 'posts_where' ), 30, 2 );

			return apply_filters( 'wcf_vars_processed', $var_processed, $vars );
		}

		function rating_filter($post_type, $from, $to) {

			global $wpdb;
			$rating_posts = array();


			// for woocommerce
			if ($post_type == 'product') {

				$matched_products = array();
				$matched_products_query = $wpdb->get_results( $wpdb->prepare("
			        SELECT DISTINCT ID, post_parent, post_type FROM $wpdb->posts
			        INNER JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id)
			        INNER JOIN $wpdb->comments ON ($wpdb->posts.ID = $wpdb->comments.comment_post_ID)
			        INNER JOIN $wpdb->commentmeta ON ($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
			        WHERE post_type IN ( 'product' ) AND post_status = 'publish' AND $wpdb->commentmeta.meta_key = %s
			        GROUP BY $wpdb->posts.ID
			        HAVING avg($wpdb->commentmeta.meta_value) BETWEEN %d AND %d
			    ", 'rating', $from, $to), OBJECT_K );

				if ( $matched_products_query ) {
					foreach ( $matched_products_query as $product ) {
						if ( $product->post_type == 'product' )
							$matched_products[] = $product->ID;
						if ( $product->post_parent > 0 && ! in_array( $product->post_parent, $matched_products ) )
							$matched_products[] = $product->post_parent;
					}
				}

				// Filter the id's
				if ( sizeof( $rating_posts ) == 0) {
					$rating_posts = $matched_products;
				} else {
					$rating_posts = array_intersect( $rating_posts, $matched_products );
				}
			}

			$rating_posts = array_unique( apply_filters( 'wcf_rating_post_in', $rating_posts, $post_type, $from, $to ));

			return (array) $rating_posts ;

		}

		/**
		 * Get a list of post id's which match the current filters
		 * @param $min
		 * @param $max
		 * @param $post_type
		 * @return array post_ids
		 */
		function price_filter($min, $max, $post_type) {

			global $wpdb;
			$price_posts = array();

			if ($post_type != '') {

				// for woocommerce
				if ($post_type == 'product') {

					$matched_products = array();
					$matched_products_query = $wpdb->get_results( $wpdb->prepare("
            SELECT DISTINCT ID, post_parent, post_type FROM $wpdb->posts
            INNER JOIN $wpdb->postmeta ON ID = post_id
            WHERE post_type IN ( 'product', 'product_variation' ) AND post_status = 'publish' AND meta_key = %s AND meta_value BETWEEN %d AND %d
        ", '_price', $min, $max ), OBJECT_K );

					if ( $matched_products_query ) {
						foreach ( $matched_products_query as $product ) {
							if ( $product->post_type == 'product' )
								$matched_products[] = $product->ID;
							if ( $product->post_parent > 0 && ! in_array( $product->post_parent, $matched_products ) )
								$matched_products[] = $product->post_parent;
						}
					}

					// Filter the id's
					if ( sizeof( $price_posts ) == 0) {
						$price_posts = $matched_products;
					} else {
						$price_posts = array_intersect( $price_posts, $matched_products );
					}
				} else if ($post_type == 'download') {

					// Easy digital download
					$matched_products_query = $wpdb->get_results( $wpdb->prepare("
            SELECT DISTINCT ID, post_parent, post_type FROM $wpdb->posts
            INNER JOIN $wpdb->postmeta ON ID = post_id
            WHERE post_type = 'download' AND post_status = 'publish' AND (meta_key = %s OR meta_key = %s)
        ", 'edd_price', '_variable_pricing'  ), OBJECT_K );

					$prices_filter = array();
					if ( $matched_products_query ) {
						foreach ( $matched_products_query as $product_id => $product ) {

							if (edd_has_variable_prices($product_id)) {

								$price_lowest  = edd_get_lowest_price_option($product_id);
								$price_highest = edd_get_highest_price_option($product_id);

								if (($price_lowest >= $min) && ($price_lowest <= $max) || ($price_highest >= $min) && ($price_highest <= $max)) {
									$prices_filter[] = $product_id;
								}

							} else {
								$prices_regular = edd_get_download_price($product_id);
								if (($prices_regular >= $min) && ($prices_regular <= $max)) {
									$prices_filter[] = $product_id;
								}
							}
						}

						$price_posts = $prices_filter;

					}

				}

			}

			$price_posts = array_unique( apply_filters( 'wcf_price_post_in', $price_posts, $post_type, $min, $max ));

			return (array) $price_posts ;
		}

		function wcf_search_ajax() {


			wp_parse_str( $_POST['search'], $search );
			$form_id = $_POST['form_id'];
			$template_loop = $_POST['loop'];
			$result_columns = $_POST['result_columns'];
			$gridtype = isset($_POST['gridtype']) ? $_POST['gridtype'] : 'default';
			$sort_value = isset($_POST['sort_result']) ? $_POST['sort_result'] : '';
            $paged = isset($_POST['page_number']) ? intval( $_POST['page_number'] ) : 1;

			check_ajax_referer( 'wcf_ajax', 'wcf_ajax_nonce' );

			$args = array(
			);

			if (isset($_POST['page_number'])) {
				$args['paged'] = (int)$_POST['page_number'];
			}

            if (isset($search['s'])) {
                $args['s'] = esc_html($search['s']);
            }
            $search['wcf_results_sort'] = $sort_value;

            $args = wp_parse_args($this->process_vars($form_id, $search), $args);

			query_posts($args);
			$id = $form_id;
			$columns = $result_columns;
            $settings = wcf_get_form_search_settings($id);
            $masonry = isset($settings['enable_masonry_grid']) && $settings['enable_masonry_grid'] != '' ? $settings['enable_masonry_grid'] : 'no';

			if ($_POST['search'] != '') {
				$static_url = $_POST['pathname'] . '?' . $_POST['search'];
			} else {
				$static_url = $_POST['pathname'];
			}


			ob_start();
            if ($gridtype != 'table') {
                include wcf_get_template('wcf-header-grid', false, false);
            }
			include wcf_get_template($template_loop, false, false);
			if ($gridtype != 'table') {
                wcf_pagination(0, 0, true, $static_url);
            }

			$output = ob_get_clean();

			wp_reset_query();

			echo $output;
			die();
		}

		function posts_where( $where, $ob ) {
			global $wpdb;

            if (isset($ob->query['wcf_content_filter'])) {

                if (isset($ob->query['wcf_private'])) {
                    $settings = $ob->query['wcf_settings'];
                    $role = isset($settings['roles_show_private']) && $settings['roles_show_private'] != '' ? $settings['roles_show_private'] : 'no';
                    $user_show_private = isset($settings['user_show_private']) && $settings['user_show_private'] != '' ? $settings['user_show_private'] : 'no';
                    $user = wp_get_current_user();

                    if (count($ob->query['post_type']) > 1) {
                        $bk_post_type = "AND {$wpdb->posts}.post_type IN ('" . join( "', '", esc_sql( $ob->query['post_type'] ) ) . "')";
                    } else {
                        $post_type = reset( $ob->query['post_type'] );
                        $bk_post_type = "AND {$wpdb->posts}.post_type = '{$post_type}'";
                    }

                    if (in_array( $role, $user->roles )) {
                        $where .= " OR ({$wpdb->posts}.post_status = 'private' $bk_post_type) ";
                    }

                    if ($user_show_private == 'yes') {

                        $where .= " OR ({$wpdb->posts}.post_status = 'private' AND {$wpdb->posts}.post_author = $user->ID $bk_post_type) ";
                    }

                }

            }

			return $where;
		}

		function posts_join( $join, $ob ) {
			return $join;
		}

		/**
		 * Meta SQL
		 * @param $meta
		 * @param $queries
		 * @param $type
		 * @param $primary_table
		 * @param $primary_id_column
		 * @param $context
		 * @return mixed
		 */
		function meta_sql( $meta, $queries, $type, $primary_table, $primary_id_column, $context ) {

			return $meta;
		}

		/**
		 * Request SQL
		 * @param $request
		 * @return mixed
		 */
		function posts_request( $request ) {

			return $request;
		}


		function get_fields_serialize( $form_values ) {

			global $wpdb;
			$table_postmeta    = $wpdb->prefix . 'postmeta';
			$fields_serialized = array();

			if ( ! empty( $form_values ) && is_array( $form_values ) ) {

				foreach ( $form_values as $key => $key_values ) {
					$field_data = explode( "::", $key );
					$field_id   = $field_data[0];
					$field_type = $field_data[1];

					if ( is_array( $key_values ) && ! empty( $key_values ) ) {

						$results = array();

						if ( ! isset( $fields_serialized[ $key_values['meta_key'] ] ) && ( $field_type == 'acf' || $field_type == 'meta_field' ) ) {
							$results = $wpdb->get_results( "SELECT meta_value, post_id FROM {$table_postmeta} WHERE meta_key = '" . $key_values['meta_key'] . "'" );
						}

						if ( ! empty( $results ) ) {

							foreach ( $results as $result ) {
								if ( is_serialized( $result->meta_value ) ) {
									//                                        $meta_data = maybe_unserialize($result->meta_value);
									$fields_serialized[ $key_values['meta_key'] ][] = array(
										'key'     => $key_values['meta_key'],
										'value'   => $result->meta_value,
										'post_id' => $result->post_id,
									);
								}
							}
						}


					}

				}

			}

			return $fields_serialized;

		}

	}

}