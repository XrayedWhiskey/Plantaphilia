<?php defined('ABSPATH') or die('This script cannot be accessed directly.');

/**
 * Theme Options Field: Adobe Fonts
 *
 * Add / Remove Adobe Fonts Typekit
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 *
 * @var   $value string Current value
 */
if ( $adobe_typekit = get_option( 'usof_adobe_typekit_' . US_THEMENAME ) ) {
	$typekit_id = $adobe_typekit['kit_id'];
	$fonts_list = __( 'Found fonts:', 'us' ) . ' ' . implode( ', ', $adobe_typekit['fonts'] );
} else {
	$typekit_id = $fonts_list = '';
}
?>
<div class="usof-adobe-fonts">
	<input type="text" name="typekit_id" value="<?= $typekit_id ?>">
	<div class="usof-button type_adobe_fonts_apply">
		<span><?= us_translate( 'Apply' ) ?></span>
		<span class="usof-preloader"></span>
	</div>
	<?php if ( $adobe_typekit ) : ?>
	<div class="usof-button type_adobe_fonts_reset">
		<span><?= us_translate( 'Delete' ) ?></span>
		<span class="usof-preloader"></span>
	</div>
	<?php endif; ?>
</div>
<div class="usof-message"><?= $fonts_list ?></div>
