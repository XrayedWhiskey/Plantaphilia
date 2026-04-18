<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output popup html.
 *
 * @param string $overriding_link The overriding link.
 * @param string $popup_width The popup width.
 * @param int $preloader_type The global preloader type.
 * @param int $popup_arrows The Prev/Next arrows.
 *
 */

if ( $preloader_type == 'custom' AND $preloader_image = us_get_option( 'preloader_image', '' ) ) {
	$preloader_image_html = wp_get_attachment_image( $preloader_image, 'medium' );
	if ( empty( $preloader_image_html ) ) {
		$preloader_image_html = us_get_img_placeholder( 'medium' );
	}
} else {
	$preloader_image_html = '';
}

if ( ! us_amp() AND strpos( $overriding_link, 'popup_post' ) !== FALSE ) { ?>
<div class="l-popup">
	<div class="l-popup-overlay"></div>
	<div class="l-popup-wrap">
		<div class="l-popup-box">
			<div class="l-popup-box-content"<?php echo us_prepare_inline_css( array( 'max-width' => $popup_width ) ); ?>>
				<div class="g-preloader type_<?php echo $preloader_type; ?>">
					<div><?php echo $preloader_image_html ?></div>
				</div>
				<iframe class="l-popup-box-content-frame" allowfullscreen></iframe>
			</div>
		</div>
		<?php if ( ! empty( $popup_arrows ) ) { ?>
			<div class="l-popup-arrow to_next hidden" title="<?php echo us_translate( 'Next' ) ?>"></div>
			<div class="l-popup-arrow to_prev hidden" title="<?php echo us_translate( 'Previous' ) ?>"></div>
		<?php } ?>
		<div class="l-popup-closer"></div>
	</div>
</div>
<?php } ?>
