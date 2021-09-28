<?php
/*
Plugin Name: WordPress Content Filter
Plugin URI: http://codecanyon.net/item/wordpress-content-filter/12098450
Description: WordPress Content Filter lets you filter by rating, attribute, custom fields, taxonomies, meta fields, authors, dates, post types, sort and more.
Version: 2.7.4
Author: ZuFusion
Author URI: http://zufusion.com
*/

// define
define('WCF_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WCF_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('WCF_PLUGIN_FILE', plugin_dir_path(__FILE__) . 'wordpress-content-filter.php');
define('WCF_THEME_PATH', get_template_directory() . '/');
define('WCF_THEME_URL', get_template_directory_uri() . '/');
// Require Core
require_once(WCF_PLUGIN_PATH . 'inc/core.php');
$GLOBALS['WCF'] = WCF_Site::get_instance();

