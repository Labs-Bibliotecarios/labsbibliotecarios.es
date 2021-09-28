<?php
/**
 * Represents the view for the administration dashboard.
 *
 * @package   Glossary
 * @author    Codeat <support@codeat.co>
 * @copyright 2016 GPL 2.0+
 * @license   GPL-2.0+
 * @link      http://codeat.co
 * @phpcs:disable WordPress.Security.EscapeOutput
 */
?>
<div id="tabs-shortcodes" class="metabox-holder">
	<div class="postbox">
		<h3 class="hndle"><span><?php _e( 'Purge the shortcode/widget plugin cache', 'glossary-by-codeat' ); ?></span>
		</h3>
		<div class="inside"><p>
			<?php _e( 'Glossary uses internally WordPress transients to improve the performance in shortcodes or widgets. Usually they last 24 hours but is possible to clean them manually pressing this button.', 'glossary-by-codeat' ); ?>
			<a href="<?php echo esc_html( add_query_arg( 'gl_purge_transient', true ) ); ?>#tabs-shortcodes" class="button button-primary" style="float:right"><?php _e( 'Purge plugin trasients', 'glossary-by-codeat' ); ?></a>
		</p></div>
	</div>
	<div class="postbox">
		<h3 class="hndle"><span><?php _e( 'Shortcodes available in Free version', 'glossary-by-codeat' ); ?></span>
		</h3>
		<div class="inside">
			<ul>
				<li><b>[glossary-cats]</b> - <?php _e( 'This shortcode will generate an index for your Glossary that will create an indexed page, for all your key terms.', 'glossary-by-codeat' ); ?> [<a href='http://docs.codeat.co/glossary/shortcodes/#list-of-categories' target='_blank'>Documentation</a>]</li>
				<li><b>[glossary-terms]</b> - <?php _e( 'This shortcode will generate a list of your glossary\'s terms.', 'glossary-by-codeat' ); ?> [<a href='http://docs.codeat.co/glossary/shortcodes/#list-of-terms' target='_blank'>Documentation</a>]</li>
			</ul>
		</div>
	</div>

	<div class="postbox">
		<h3 class="hndle"><span><?php _e( 'Shortcodes available in PRO version', 'glossary-by-codeat' ); ?></span></h3>
		<div class="inside">
			<ul>
				<li><b>[glossary-list]</b> - <?php _e( 'This PRO shortcode will generate an index for your Glossary that will create an indexed page.', 'glossary-by-codeat' ); ?> [<a href='http://docs.codeat.co/glossary/shortcodes/#glossary-index-premium' target='_blank'>Documentation</a>]</li>
				<li><b>[glossary]</b> - <?php _e( 'This parse a specific content wrap with this shortcode, useful for Visual Composer or Page Builder.', 'glossary-by-codeat' ); ?> [<a href='https://docs.codeat.co/glossary/shortcodes/#parse-content-premium' target='_blank'>Documentation</a>]</li>
				<li><b>[glossary-ignore]</b> - <?php _e( 'To prevent Glossary from processing a term, wrap with this shortcode.', 'glossary-by-codeat' ); ?> [<a href='https://docs.codeat.co/glossary/shortcodes/#ignore-terms-premium' target='_blank'>Documentation</a>]</li>
			</ul>
		</div>
	</div>
</div>
