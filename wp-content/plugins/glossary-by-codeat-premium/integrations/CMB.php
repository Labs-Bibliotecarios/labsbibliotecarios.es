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

namespace Glossary\Integrations;

use Glossary\Engine;

/**
 * All the CMB related code.
 */
class CMB extends Engine\Base {

	/**
	 * Initialize class.
	 *
	 * @since 2.0
	 * @return bool
	 */
	public function initialize() {
		parent::initialize();

		require_once GT_PLUGIN_ROOT . 'vendor/cmb2/init.php';
		\add_filter( 'multicheck_posttype_posttypes', array( $this, 'hide_glossary' ) );

		\add_action( 'cmb2_save_options-page_fields', array( $this, 'permalink_alert' ), 4, 9999 );

		if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
			\add_action( 'cmb2_save_options-page_fields', array( $this, 'cache_alert__premium_only' ), 4, 9999 );

			return false;
		}

		return true;
	}

	/**
	 * Hide glossary post type from settings
	 *
	 * @param array $cpts The cpts.
	 * @return array
	 */
	public function hide_glossary( array $cpts ) {
		unset( $cpts[ 'attachment' ] );

		return $cpts;
	}

	/**
	 * Prompt a reminder to clean the cache
	 *
	 * @param string $object_id CMB Object ID.
	 * @param string $cmb_id    CMB ID.
	 * @param string $updated   Status.
	 * @param array  $object    The CMB object.
	 * @return void
	 */
	public function cache_alert__premium_only( $object_id, $cmb_id, $updated, $object ) { //phpcs:ignore
		if ( $cmb_id !== GT_SETTINGS . '_options2' ) {
			return;
		}

		\wpdesk_wp_notice(
			\__(
				'You must empty the cache, if you have a caching system, in order to apply changes!',
				'glossary-by-codeat'
			),
			'updated'
		);
		\update_option( GT_SETTINGS . '_css_last_edit', \time() );
	}

	/**
	 * Prompt a reminder to flush the pernalink
	 *
	 * @param string $object_id CMB Object ID.
	 * @param string $cmb_id    CMB ID.
	 * @param string $updated   Status.
	 * @param array  $object    The CMB object.
	 * @return void
	 */
	public function permalink_alert( $object_id, $cmb_id, $updated, $object ) { //phpcs:ignore
		if ( $cmb_id !== GT_SETTINGS . '_options' ) {
			return;
		}

		\wpdesk_wp_notice(
			\__(
				'You must flush the permalink if you changed the slug, go on Settings->Permalink and press Save changes!',
				'glossary-by-codeat'
			),
			'updated'
		);
	}

}
