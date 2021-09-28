<?php

/**
 * @package   Glossary
 * @author    Codeat <support@codeat.co>
 * @copyright 2020
 * @license   GPL 2.0+
 * @link      https://codeat.co
 *
 * Plugin Name: Glossary (Premium)
 * Plugin URI:        https://glossary.codeat.co/
 * Description:       Easily add and manage a glossary with auto-link, tooltips and more. Improve your internal link building for a better SEO.
 * Version:           2.0.20
 * Author:            Codeat
 * Author URI:        http://codeat.co
 * Text Domain:       glossary-by-codeat
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * Requires PHP:      7.0
 * WordPress-Plugin-Boilerplate-Powered: v3.2.0
 * @fs_premium_only backend/views/preview.php, integrations/ACF.php, backend/views/tabs/customizer.php, backend/views/tabs/extra.php, assets/js/customizer.js, assets/js/mobile-tooltip.js, assets/js/preview.js, assets/css/css-pro, frontend/Css_Customizer.php, frontend/Term_Content.php, rest/, integrations/Widgets/Search.php, /languages
 */
// If this file is called directly, abort.

if ( !defined( 'ABSPATH' ) ) {
    die( 'We\'re sorry, but you can not directly access this file.' );
    //phpcs:ignore
}

define( 'GT_VERSION', '2.0.20' );
define( 'GT_SETTINGS', 'glossary' );
define( 'GT_TEXTDOMAIN', 'glossary-by-codeat' );
define( 'GT_NAME', 'Glossary' );
define( 'GT_PLUGIN_ROOT', plugin_dir_path( __FILE__ ) );
define( 'GT_PLUGIN_ABSOLUTE', __FILE__ );
$glossary_libraries = (require_once GT_PLUGIN_ROOT . 'vendor/autoload.php');
require_once GT_PLUGIN_ROOT . 'functions/functions.php';
$requirements = new \Micropackage\Requirements\Requirements( 'Glossary', array(
    'php'            => '7.0',
    'php_extensions' => array( 'mbstring' ),
    'wp'             => '5.3',
) );

if ( !$requirements->satisfied() ) {
    $requirements->print_notice();
    return;
}


if ( function_exists( 'gt_fs' ) ) {
    gt_fs()->set_basename( true, __FILE__ );
} else {
    
    if ( !function_exists( 'gt_fs' ) ) {
        /**
         * Create a helper function for easy SDK access.
         *
         * @global type $gt_fs
         * @return object
         */
        function gt_fs()
        {
            global  $gt_fs ;
            
            if ( !isset( $gt_fs ) ) {
                // Activate multisite network integration.
                if ( !defined( 'WP_FS__PRODUCT_594_MULTISITE' ) ) {
                    define( 'WP_FS__PRODUCT_594_MULTISITE', true );
                }
                require_once dirname( __FILE__ ) . '/vendor/freemius/wordpress-sdk/start.php';
                $gt_fs = fs_dynamic_init( array(
                    'id'             => '594',
                    'slug'           => 'glossary-by-codeat',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_229177eead299a4c9212f5837675e',
                    'is_premium'     => true,
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'menu'           => array(
                    'slug'    => 'glossary',
                    'contact' => false,
                    'parent'  => array(
                    'slug' => 'edit.php?post_type=glossary',
                ),
                ),
                    'is_live'        => true,
                ) );
                
                if ( $gt_fs->is_premium() ) {
                    add_action( 'init', static function () {
                        load_plugin_textdomain( GT_TEXTDOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
                    } );
                    $gt_fs->add_filter( 'support_forum_url', static function ( $wp_org_support_forum_url ) {
                        //phpcs:ignore
                        return 'http://support.codeat.co/';
                    } );
                }
            
            }
            
            return $gt_fs;
        }
        
        /**
         * Uninstall action
         *
         * @global object $wpdb
         * @return void|array
         */
        function gt_uninstall()
        {
            global  $wpdb ;
            
            if ( is_multisite() ) {
                $blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );
                // WPCS: cache ok, db call ok.
                if ( $blogs ) {
                    foreach ( $blogs as $blog ) {
                        switch_to_blog( $blog['blog_id'] );
                        gt_remove_settings();
                        restore_current_blog();
                    }
                }
                return;
            }
            
            return gt_remove_settings();
        }
        
        /**
         * Remove all the settings of the plugin, used on the uninstall hook
         */
        function gt_remove_settings()
        {
            delete_option( 'glossary-settings' );
            
            if ( !gt_fs()->is_plan__premium_only( 'professional' ) ) {
                delete_option( 'glossary-customizer' );
                delete_option( 'glossary-extra' );
                return;
            }
        
        }
    
    }

}

gt_fs();
gt_fs()->add_action( 'after_uninstall', 'gt_uninstall' );
if ( !wp_installing() ) {
    add_action( 'plugins_loaded', static function () use( $glossary_libraries ) {
        if ( is_bool( $glossary_libraries ) ) {
            return;
        }
        new \Glossary\Engine\Initialize( $glossary_libraries );
    } );
}