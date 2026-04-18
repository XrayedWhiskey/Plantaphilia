<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Generates and outputs EDITOR styles based on theme options, used in TinyMCE and Gutenberg
 *
 * @var $editor string EDITOR type: 'tinymce' / 'gutenberg'
 */

if ( ! isset( $editor ) ) {
	return;

} elseif ( $editor == 'gutenberg' ) {
	$prefix = 'div.editor-styles-wrapper ';

} elseif ( $editor == 'tinymce' ) {
	$prefix = 'body.mce-content-body[data-id=content] ';

} else {
	return;
}
?>
/* Separated styles
 =============================================================================================================================== */
 
<?php if ( $editor == 'gutenberg' ) { ?>
figure {
	margin: 0;
	}
.wp-block-image figcaption,
.wp-block-embed figcaption,
.wp-block-pullquote {
	color: inherit;
	border-color: currentColor;
	}
blockquote.is-style-large,
.wp-block-pullquote blockquote {
	padding: 0 !important;
	}
blockquote.is-style-large:before,
.wp-block-pullquote blockquote:before {
	display: none !important;
	}
.wp-block,
.wp-block[data-align="wide"] {
	max-width: <?= us_get_option( 'site_content_width' ) ?>;
	}
.wp-block[data-align="full"] {
	max-width: none;
	}
.editor-post-title__block .editor-post-title__input {
	font-family: inherit !important;
	color: inherit !important;
	}
.editor-styles-wrapper .wp-block-quote__citation,
.editor-styles-wrapper .wp-block-quote cite,
.editor-styles-wrapper .wp-block-quote footer {
	font-size: 1rem;
	margin-top: 0.5rem;
	color: inherit;
	}

<?php } elseif ( $editor == 'tinymce' ) { ?>

strong {
	font-weight: 600;
	}
.mce-content-body {
	max-width: <?= us_get_option( 'site_content_width' ) ?>;
	}
.mce-content-body a[data-mce-selected] {
	box-shadow: none;
	background: <?= us_hex2rgba( us_get_color( '_content_link', FALSE, FALSE ), 0.2 ) ?>;
	}
h1, h2, h3, h4, h5, h6 {
	font-family: inherit;
	line-height: 1.4;
	margin: 0 0 1.5rem;
	padding-top: 1.5rem;
	}
h1:first-child,
h2:first-child,
h3:first-child,
h4:first-child,
h5:first-child,
h6:first-child,
h1 + h2, h1 + h3, h1 + h4, h1 + h5, h1 + h6,
h2 + h3, h2 + h4, h2 + h5, h2 + h6,
h3 + h4, h3 + h5, h3 + h6,
h4 + h5, h4 + h6,
h5 + h6 {
	padding-top: 0;
	}
p,
ul,
ol,
dl,
address,
pre,
table,
blockquote,
fieldset {
	margin: 0 0 1.5rem;
	}

<?php } ?>

/* Common styles
 =============================================================================================================================== */

<?= $prefix ?>a {
	color: <?= us_get_color( '_content_link', FALSE, FALSE ) ?>;
	}
<?= $prefix ?>ul li,
<?= $prefix ?>ol li {
	margin: 0 0 0.5rem;
	}
<?= $prefix ?>li > ul,
<?= $prefix ?>li > ol {
	margin-bottom: 0.5rem;
	margin-top: 0.5rem;
	}
<?= $editor == 'tinymce' ? '' : $prefix ?>blockquote {
	position: relative;
	padding: 0 3rem;
	font-size: 1.3em;
	line-height: 1.7;
	border: none;
	}
<?= $editor == 'tinymce' ? '' : $prefix ?>blockquote:before {
	content: '\201C';
	display: block;
	font-size: 6rem;
	line-height: 0.8;
	font-family: Georgia, serif;
	position: absolute;
	left: 0;
	opacity: .5;
	}
<?= $editor == 'tinymce' ? '' : $prefix ?>blockquote p,
<?= $editor == 'tinymce' ? '' : $prefix ?>blockquote ul,
<?= $editor == 'tinymce' ? '' : $prefix ?>blockquote ol {
	margin-top: 0;
	margin-bottom: 0.5em;
	}
<?= $editor == 'tinymce' ? '' : $prefix ?>pre {
	display: block;
	font-family: Consolas, Lucida Console, monospace;
	font-size: 0.9em;
	line-height: 1.65;
	padding: 0.8em 1em;
	width: 100%;
	overflow: auto;
	background: <?= us_get_color( '_content_bg_alt', TRUE, FALSE ) ?>;
	}
<?= $prefix ?>h1,
<?= $prefix ?>h2,
<?= $prefix ?>h3,
<?= $prefix ?>h4,
<?= $prefix ?>h5,
<?= $prefix ?>h6 {
	color: <?= us_get_color( '_content_heading', FALSE, FALSE ) ?>;
	}
<?= $prefix ?>td,
<?= $prefix ?>th {
	border-color: <?= us_get_color( '_content_border', FALSE, FALSE ) ?>;
	}

<?php

// Global Text
$css = $prefix . '{';
$css .= 'background:' . us_get_color( '_content_bg', TRUE, FALSE ) . ';';
$css .= 'color:' . us_get_color( '_content_text', FALSE, FALSE ) . ';';
$css .= '}';

// Add color inline styles for Block Editor (Gutenberg)
if ( $editor == 'gutenberg' ) {
	$predefined_colors = array(
		'color_content_primary',
		'color_content_secondary',
		'color_content_heading',
		'color_content_text',
		'color_content_faded',
		'color_content_border',
		'color_content_bg_alt',
		'color_content_bg',
	);
	foreach ( $predefined_colors as $color ) {
		$color_name = str_replace( 'color_', '', $color );
		$color_name = str_replace( '_', '-', $color_name );

		$css .= '.has-' . $color_name . '-color {';
		$css .= 'color:' . us_get_color( $color, FALSE, FALSE ) . ';';
		$css .= '}';

		// Gradients are possible for background
		$css .= '.has-' . $color_name . '-background-color {';
		$css .= 'background:' . us_get_color( $color, TRUE, FALSE ) . ';';
		$css .= '}';
	}
}

// Typography styles (Default/Desktops responsive state only)
foreach ( (array) us_get_typography_option_values( /* screen */'default' ) as $tagname => $tag_options ) {
	if ( $tagname == 'body' ) {
		$css .= $prefix . '{';
	} else {
		$css .= $prefix . ' ' . $tagname . '{';
	}
	foreach ( $tag_options as $prop_name => $prop_value ) {
		if ( $prop_name == 'bold-font-weight' ) {
			continue;
		}
		if ( ! empty( $prop_value ) ) {
			$css .= sprintf( '%s: %s;', $prop_name, $prop_value );
		}
	}
	$css .= '}';
}

echo strip_tags( $css );
