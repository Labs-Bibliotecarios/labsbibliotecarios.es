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

<div id="tabs-settings" class="metabox-holder">
<?php

$cmb = new_cmb2_box(
	array(
		'id'         => GT_SETTINGS . '_options',
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
		'name' => __( 'Settings for Post Types', 'glossary-by-codeat' ),
		'id'   => 'title_post_types',
		'type' => 'title',
	)
);
$cmb->add_field(
	array(
		'name' => __( 'Enable in:', 'glossary-by-codeat' ),
		'id'   => 'posttypes',
		'type' => 'multicheck_posttype',
	)
);
$where_enable = array(
	'home'         => __( 'Home', 'glossary-by-codeat' ),
	'category'     => __( 'Category archive', 'glossary-by-codeat' ),
	'tag'          => __( 'Tag archive', 'glossary-by-codeat' ),
	'arc_glossary' => __( 'Glossary Archive', 'glossary-by-codeat' ),
	'tax_glossary' => __( 'Glossary Taxonomy', 'glossary-by-codeat' ),
);

if ( gt_fs()->is_plan__premium_only( 'professional' ) ) {
	$where_enable[ 'feed' ] = __( 'RSS Feed', 'glossary-by-codeat' );
}

$cmb->add_field(
	array(
		'name'    => __( 'Enable also in following archives:', 'glossary-by-codeat' ),
		'id'      => 'is',
		'type'    => 'multicheck',
		'options' => $where_enable,
	)
);
$cmb->add_field(
	array(
		'name' => __( 'Alphabetical order in Glossary Archives', 'glossary-by-codeat' ),
		'id'   => 'order_terms',
		'type' => 'checkbox',
	)
);

$temp = array(
	'name' => __( 'Add Alphabetical list in top of Glossary Archives', 'glossary-by-codeat' ),
	'desc' => __( 'After the title of the archive will add a list like ABCDE... with links.<br>', 'glossary-by-codeat' ) . sprintf( $doc, 'http://docs.codeat.co/glossary/' ) . $pro,
	'id'   => 'archive_alphabetical_bar',
	'type' => 'checkbox',
);

if ( !empty( $pro ) ) {
	$temp[ 'attributes' ] = array(
		'readonly' => 'readonly',
		'disabled' => 'disabled',
	);
}

$cmb->add_field( $temp );
$cmb->add_field(
	array(
		'name'    => __( 'Glossary Terms Slug', 'glossary-by-codeat' ),
		'desc'    => __( 'Terms and Categories cannot have the same custom slug.', 'glossary-by-codeat' ),
		'id'      => 'slug',
		'type'    => 'text',
		'default' => 'glossary',
	)
);
$cmb->add_field(
	array(
		'name'    => __( 'Glossary Category Slug', 'glossary-by-codeat' ),
		'desc'    => __( 'Terms and Categories cannot have the same custom base slug.', 'glossary-by-codeat' ),
		'id'      => 'slug_cat',
		'type'    => 'text',
		'default' => 'glossary-cat',
	)
);

if ( gt_fs()->is_plan__premium_only( 'professional' ) ) {
	$cmb->add_field(
		array(
			'name'    => __( 'Singular Post Type Label', 'glossary-by-codeat' ),
			'desc'    => __( 'Change the name of the post type shown in both the backend and the frontend', 'glossary-by-codeat' ),
			'id'      => 'label_single',
			'type'    => 'text',
			'default' => '',
		)
	);
	$cmb->add_field(
		array(
			'name'    => __( 'Plural Post Type Label', 'glossary-by-codeat' ),
			'desc'    => __( 'Change the name of the post type shown in both the backend and the frontend', 'glossary-by-codeat' ),
			'id'      => 'label_multi',
			'type'    => 'text',
			'default' => '',
		)
	);
}

$cmb->add_field(
	array(
		'name' => __( 'Disable Archive in the frontend for Glossary Terms', 'glossary-by-codeat' ),
		'desc' => __( 'Don\'t forget to flush the permalinks in the General Settings.<br>', 'glossary-by-codeat' ) . sprintf( $doc, 'http://docs.codeat.co/glossary/advanced-settings/#disable-archives-in-the-frontend' ),
		'id'   => 'archive',
		'type' => 'checkbox',
	)
);
$cmb->add_field(
	array(
		'name' => __( 'Disable Archive in the frontend for Glossary Categories', 'glossary-by-codeat' ),
		'id'   => 'tax_archive',
		'type' => 'checkbox',
	)
);
$cmb->add_field(
	array(
		'name' => __( 'Remove "Archive/Category" prefix from meta titles in Archive/Category pages', 'glossary-by-codeat' ),
		'desc' => sprintf( $doc, 'http://docs.codeat.co/glossary/advanced-settings/#remove-the-archivecategory-prefix-from-meta-titles' ),
		'id'   => 'remove_archive_label',
		'type' => 'checkbox',
	)
);
$temp = array(
	'name' => __( 'Add total number of terms in the meta title of the page', 'glossary-by-codeat' ),
	'desc' => sprintf( $doc, 'http://docs.codeat.co/glossary/premium-features/#how-to-add-the-total-number-of-terms-in-the-meta-title-of-the-page' ),
	'id'   => 'number_archive_title',
	'type' => 'checkbox',
);

if ( !empty( $pro ) ) {
	$temp[ 'attributes' ] = array(
		'readonly' => 'readonly',
		'disabled' => 'disabled',
	);
}

$cmb->add_field( $temp );
$cmb->add_field(
	array(
		'name' => __( 'Behaviour', 'glossary-by-codeat' ),
		'id'   => 'title_behaviour',
		'type' => 'title',
	)
);
$cmb->add_field(
	array(
		'name' => __( 'Ignore &lt;span&gt; tags during the term search in the content', 'glossary-by-codeat' ),
		'id'   => 'ignore_span',
		'type' => 'checkbox',
		'desc' => __( 'Use this option only when you see issues or conflicts with other plugins or component of the page. Or if you want to avoid to have more then 1 term in a single sentence.', 'glossary-by-codeat' ),
	)
);
$temp = array(
	'name' => __( 'Link only the first occurrence of all key terms', 'glossary-by-codeat' ),
	'desc' => __( 'Prevents duplicating links and tooltips for all key terms that point to the same definition.<br>', 'glossary-by-codeat' ) . sprintf( $doc, 'http://docs.codeat.co/glossary/premium-features/#link-only-the-first-occurrence-of-all-terms-keys' ) . $pro,
	'id'   => 'first_occurrence',
	'type' => 'checkbox',
);

if ( !empty( $pro ) ) {
	$temp[ 'attributes' ] = array(
		'readonly' => 'readonly',
		'disabled' => 'disabled',
	);
}

$cmb->add_field( $temp );
$temp = array(
	'name' => __( 'Link only the first occurrence of all the term keys', 'glossary-by-codeat' ),
	'desc' => __( 'Prevent duplicate links and tooltips for the same term, even if has more than one key, in a single post.<br>', 'glossary-by-codeat' ) . sprintf( $doc, 'http://docs.codeat.co/glossary/advanced-settings/#link-only-the-first-occurrence-of-the-same-key-term' ) . $pro,
	'id'   => 'first_all_occurrence',
	'type' => 'checkbox',
);

if ( !empty( $pro ) ) {
	$temp[ 'attributes' ] = array(
		'readonly' => 'readonly',
		'disabled' => 'disabled',
	);
}

$cmb->add_field( $temp );
$cmb->add_field(
	array(
		'name' => __( 'Add icon to external link', 'glossary-by-codeat' ),
		'desc' => __( 'Add a css class with an icon to external link', 'glossary-by-codeat' ),
		'id'   => 'external_icon',
		'type' => 'checkbox',
	)
);
$cmb->add_field(
	array(
		'name' => __( 'Force Glossary terms to be included within WordPress search results', 'glossary-by-codeat' ),
		'desc' => __( 'Choose this option if you don\'t see your terms while searching for them in WordPress.<br>', 'glossary-by-codeat' ) . sprintf( $doc, 'http://docs.codeat.co/glossary/advanced-settings/#force-glossary-terms-to-be-included-within-wordpress-search-results' ),
		'id'   => 'search',
		'type' => 'checkbox',
	)
);
$temp = array(
	'name' => __( 'Match case-sensitive terms', 'glossary-by-codeat' ),
	'id'   => 'case_sensitive',
	'type' => 'checkbox',
	'desc' => $pro,
);

if ( !empty( $pro ) ) {
	$temp[ 'attributes' ] = array(
		'readonly' => 'readonly',
		'disabled' => 'disabled',
	);
}

$cmb->add_field( $temp );
$cmb->add_field(
	array(
		'name' => __( 'Prevent term links from appearing on their own description page', 'glossary-by-codeat' ),
		'desc' => __( 'Choose this option to avoid redundancy.<br>', 'glossary-by-codeat' ) . sprintf( $doc, 'http://docs.codeat.co/glossary/premium-features/#prevent-term-links-from-appearing-in-their-own-description-page' ),
		'id'   => 'match_same_page',
		'type' => 'checkbox',
	)
);
$cmb->add_field(
	array(
		'name' => __( 'Open external link in a new window', 'glossary-by-codeat' ),
		'desc' => __( 'Choose this option to enable globally the opening of external link in a new tab.<br>', 'glossary-by-codeat' ),
		'id'   => 'open_new_window',
		'type' => 'checkbox',
	)
);
$temp = (
	array(
		'name' => __( 'Append string to injected URL', 'glossary-by-codeat' ),
		'desc' => __( 'Append a string to internal and external URLs for tracking purposes (E.g. utm_content=service).', 'glossary-by-codeat' ) . $pro,
		'id'   => 'url_suffix',
		'type' => 'text_small',
	)
);

if ( !empty( $pro ) ) {
	$temp[ 'attributes' ] = array(
		'readonly' => 'readonly',
		'disabled' => 'disabled',
	);
}

$cmb->add_field( $temp );
$cmb->add_field(
	array(
		'name' => __( 'Settings for Tooltip', 'glossary-by-codeat' ),
		'id'   => 'title_tooltip',
		'type' => 'title',
	)
);
$glossary_tooltip_type = array(
	'link'         => __( 'Only Link', 'glossary-by-codeat' ),
	'link-tooltip' => __( 'Link and Tooltip', 'glossary-by-codeat' ),
);

if ( gt_fs()->is_plan__premium_only( 'professional' ) ) {
	$glossary_tooltip_type[ 'tooltip' ]  = __( 'Only Tooltip', 'glossary-by-codeat' );
	$glossary_tooltip_type[ 'footnote' ] = __( 'Only Footnote', 'glossary-by-codeat' );
}

$cmb->add_field(
	array(
		'name'    => __( 'Enable tooltips on terms', 'glossary-by-codeat' ),
		'desc'    => __( 'Tooltip will popup on hover', 'glossary-by-codeat' ),
		'id'      => 'tooltip',
		'type'    => 'select',
		'options' => $glossary_tooltip_type,
	)
);
$themes = apply_filters(
	'glossary_themes_dropdown',
	array(
		'classic' => 'Classic',
		'box'     => 'Box',
		'line'    => 'Line',
		'simple'  => 'Simple',
	)
);
$cmb->add_field(
	array(
		'name'    => __( 'Tooltip style', 'glossary-by-codeat' ),
		'desc'    => __( 'The featured image will only show with the Classic and all the PRO themes.<br>', 'glossary-by-codeat' ) . sprintf( $doc, 'http://docs.codeat.co/glossary/tooltips/' ),
		'id'      => 'tooltip_style',
		'type'    => 'select',
		'options' => $themes,
	)
);
$cmb->add_field(
	array(
		'name' => __( 'Enable image in tooltips', 'glossary-by-codeat' ),
		'id'   => 't_image',
		'type' => 'checkbox',
	)
);
$cmb->add_field(
	array(
		'name' => __( 'Remove "more" link in tooltips', 'glossary-by-codeat' ),
		'id'   => 'more_link',
		'type' => 'checkbox',
		'desc' => $pro,
	)
);

$temp = array(
	'name' => __( 'Change "more" link text in tooltips', 'glossary-by-codeat' ),
	'id'   => 'more_link_text',
	'type' => 'text',
	'desc' => $pro,
);

if ( !empty( $pro ) ) {
	$temp[ 'attributes' ] = array(
		'readonly' => 'readonly',
		'disabled' => 'disabled',
	);
}

$cmb->add_field( $temp );
$cmb->add_field(
	array(
		'name' => __( 'Excerpt', 'glossary-by-codeat' ),
		'id'   => 'title_excerpt_limit',
		'type' => 'title',
	)
);
$cmb->add_field(
	array(
		'name' => __( 'Limit the excerpt by words', 'glossary-by-codeat' ),
		'desc' => __( 'As opposed to characters', 'glossary-by-codeat' ),
		'id'   => 'excerpt_words',
		'type' => 'checkbox',
	)
);
$cmb->add_field(
	array(
		'name'    => __( 'Excerpt length in characters or words', 'glossary-by-codeat' ),
		'desc'    => __( 'Refers to selection above. If value is 0 the complete content will be used', 'glossary-by-codeat' ),
		'id'      => 'excerpt_limit',
		'type'    => 'text_number',
		'default' => '60',
	)
);
$cmb->add_field(
	array(
		'name' => __( 'Disable ... on excerpt', 'glossary-by-codeat' ),
		'id'   => 'excerpt_dots',
		'type' => 'checkbox',
	)
);
cmb2_metabox_form( GT_SETTINGS . '_options', GT_SETTINGS . '-settings' );
?>

</div>
