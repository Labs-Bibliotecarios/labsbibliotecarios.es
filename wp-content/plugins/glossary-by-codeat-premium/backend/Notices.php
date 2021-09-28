<?php
/**
 * Plugin_name
 *
 * @package   Plugin_name
 * @author    Codeat <support@codeat.co>
 * @copyright 2020
 * @license   GPL 2.0+
 * @link      https://codeat.co
 */

namespace Glossary\Backend;

use Glossary\Engine;
use Yoast_I18n_WordPressOrg_v3;

/**
 * Everything that involves notification on the WordPress dashboard
 */
class Notices extends Engine\Base {

	/**
	 * Initialize the class
	 *
	 * @return bool
	 */
	public function initialize() {
		if ( !parent::initialize() ) {
			return false;
		}

		new \WP_Review_Me(
			array(
				'days_after' => 15,
				'type'       => 'plugin',
				'slug'       => 'glossary-by-codeat',
				'rating'     => 5,
				'message'    => \__(
					'Hey! It\'s been a little while that you\'ve been using Glossary for WordPress. You might not realize it, but user reviews are such a great help to us. We would be so grateful if you could take a minute to leave a review on WordPress.org.<br>Many thanks in advance :)<br>',
					'glossary-by-codeat'
				),
				'link_label' => \__( 'Click here to review', 'glossary-by-codeat' ),
			)
		);

		if ( \is_multisite() ) {
			\wpdesk_init_wp_notice_ajax_handler();
			\wpdesk_permanent_dismissible_wp_notice(
				\__(
					'Hey, we noticed that you are in a multi-site network. Glossary now supports WordPress multi-site feature!<br>Please, read our <a href="http://docs.codeat.co/glossary/faq/#are-you-compatible-with-wordpress-multisite">documentation</a>.',
					GT_TEXTDOMAIN
				),
				GT_TEXTDOMAIN . '_pro_multisite',
				'updated'
			);
		}

		if ( $this->content_excerpt_empty() ) {
			\wpdesk_wp_notice(
				\__(
					'The content and the excerpt of this term are both empty, this will generate empty Tooltips!',
					GT_TEXTDOMAIN
				),
			'error'
			);
		}

		if ( \gt_fs()->is_plan__premium_only( 'professional' ) ) {
			\wpdesk_init_wp_notice_ajax_handler();
			\wpdesk_permanent_dismissible_wp_notice(
				\__(
					'Dear user, read the documentation and be sure to not miss all the glossary pro features that comes along with your license. <a href="http://docs.codeat.co/glossary/shortcodes/#glossary-index-premium">E.g. How to create an alphabetical ordered Index.</a>.',
					GT_TEXTDOMAIN
				),
				GT_TEXTDOMAIN . '_pro_shortcode',
				'updated'
			);
		}

		/*
		 * Alert after few days to suggest to contribute to the localization if it is incomplete
		 * on translate.wordpress.org, the filter enables to remove globally.
		 */
		if ( !\apply_filters( 'glossary_alert_localization', true ) ) {
			return false;
		}

		if ( \is_array( $this->settings ) && !\is_null( \get_page_by_path( $this->settings[ 'slug' ], OBJECT ) )
			&& !isset( $this->settings[ 'archive' ] ) ) {
			$page = \get_page_by_path( $this->settings[ 'slug' ], OBJECT );
			\wpdesk_wp_notice(
				\sprintf(
					/* translators: the link to the edit page version */
					\__(
						'Hey, we noticed that one of your pages is using the same slug as the Glossary plugin Archive post type. This can create a conflict. To fix it, <a href="http://docs.codeat.co/glossary/advanced-settings/#disable-archives-in-the-frontend">disable the archive in the frontend</a> or change the <a href="%s">slug of your page</a>.',
						GT_TEXTDOMAIN
					),
					\get_edit_post_link( $page->ID )
				),
					'error'
			);
		}

		new Yoast_I18n_WordPressOrg_v3(
			array(
				'textdomain' => GT_TEXTDOMAIN,
				'glossary'   => GT_NAME,
				'hook'       => 'admin_notices',
			),
			true
		);

		return true;
	}

	/**
	 * Check if the Term's content or excerpt are empty
	 *
	 * @return bool
	 */
	public function content_excerpt_empty() {
		global $post;

		if ( !isset( $post->post_type ) ) {
			return false;
		}

		return $post->post_type === 'glossary' && empty( \trim( $post->post_content ) ) && empty( \trim( $post->post_excerpt ) );
	}

}
