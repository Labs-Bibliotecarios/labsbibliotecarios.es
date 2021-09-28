<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Glossary
 * @author    Codeat <support@codeat.co>
 * @copyright 2016 GPL 2.0+
 * @license   GPL-2.0+
 * @link      http://codeat.co
 * @phpcs:disable WordPress.Security.EscapeOutput
 */
?>
<div id="tabs-extra" class="metabox-holder">
<?php
	$cmb = new_cmb2_box(
		array(
			'id'         => GT_SETTINGS . '_options3',
			'hookup'     => false,
			'show_on'    => array(
				'key'   => 'options-page',
				'value' => array( 'glossary-by-codeat' ),
			),
			'show_names' => true,
		)
	);
	$cmb->add_field(
		array(
			'name' => __( 'Custom Fields', 'glossary-by-codeat' ),
			'id'   => 'text_custom_field',
			'desc' => __( 'Do you need to create custom fields for your key terms? If so, please head over to <a href="http://docs.codeat.co/glossary/premium-features/#how-to-add-custom-fields-to-your-glossary" target="_blank">the dedicated documentation page</a> to see how to best implement them.', 'glossary-by-codeat' ),
			'type' => 'title',
		)
	);
	$cmb->add_field(
		array(
			'name'       => __( 'Fields', 'glossary-by-codeat' ),
			'id'         => 'custom_fields',
			'type'       => 'text_small',
			'default'    => '',
			'repeatable' => true,
		)
	);

	$cmb->add_field(
		array(
			'name' => __( 'Footnotes', 'glossary-by-codeat' ),
			'id'   => 'text_footnotes',
			'desc' => __( 'Do you want footnotes with the links of your terms? Check our <a href="https://docs.codeat.co/glossary/premium-features/#footnotes" target="_blank">dedicated documentation page</a> how this feature works.', 'glossary-by-codeat' ),
			'type' => 'title',
		)
	);
	$cmb->add_field(
		array(
			'name' => __( 'Links in footnotes list', 'glossary-by-codeat' ),
			'id'   => 'footnotes_list_links',
			'type' => 'checkbox',
		)
	);
	$cmb->add_field(
		array(
			'name' => __( 'Excerpt in footnotes list', 'glossary-by-codeat' ),
			'id'   => 'footnotes_list_excerpt',
			'type' => 'checkbox',
		)
	);
	$cmb->add_field(
		array(
			'name' => __( 'Post content in footnotes list', 'glossary-by-codeat' ),
			'id'   => 'footnotes_list_content',
			'type' => 'checkbox',
		)
	);
	$cmb->add_field(
		array(
			'name' => __( 'Footnotes Title', 'glossary-by-codeat' ),
			'id'   => 'footnotes_title',
			'type' => 'text',
			'desc' => __( 'Change the default title on top of the Footnotes', 'glossary-by-codeat' ),
		)
	);

	cmb2_metabox_form( GT_SETTINGS . '_options3', GT_SETTINGS . '-extra' );
	?>
</div>
