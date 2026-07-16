<?php
/**
 * Plantaphilia — Produktseite (überschreibt WooCommerce content-single-product.php)
 */
defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
  echo get_the_password_form(); // phpcs:ignore
  return;
}

// Open product wrapper (needed for WooCommerce variation JS)
?><div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'pa-pdp-product', $product ); ?>><?php

// ── Daten sammeln ─────────────────────────────────────────────────
$product_id = $product->get_id();
$name       = $product->get_name();
$short_desc = $product->get_short_description();
$long_desc  = $product->get_description();
$sku        = $product->get_sku();
$price_html = $product->get_price_html();
$stock_qty  = $product->get_stock_quantity();
$stock_stat = $product->get_stock_status(); // 'instock', 'outofstock', 'onbackorder'

// Botanische Taxonomie
$pa_gattung   = get_post_meta( $product_id, '_pa_gattung',  true );
$pa_art       = get_post_meta( $product_id, '_pa_art',      true );
$pa_kultivar  = get_post_meta( $product_id, '_pa_kultivar', true );
$pa_genus_line = trim( $pa_gattung . ( $pa_art ? ' ' . $pa_art : '' ) );

// Latin name (from attribute, fallback for older products)
$latin = $product->get_attribute( 'gattung' )
  ?: $product->get_attribute( 'pa_gattung' )
  ?: '';

// Collection (from attribute or category)
$collection = $product->get_attribute( 'sammlung' )
  ?: $product->get_attribute( 'pa_sammlung' )
  ?: '';

// Gallery images
$main_img_id   = $product->get_image_id();
$gallery_ids   = $product->get_gallery_image_ids();
$all_img_ids   = $main_img_id ? array_merge( [ $main_img_id ], $gallery_ids ) : $gallery_ids;

// Stock state classes
if ( $stock_stat === 'outofstock' || $stock_qty === 0 ) {
  $stock_class = 'out';
  $stock_text  = 'Ausverkauft';
} elseif ( $stock_qty !== null && $stock_qty <= 5 ) {
  $stock_class = 'low';
  $stock_text  = 'Nur noch ' . $stock_qty . ' Stück';
} else {
  $stock_class = '';
  $stock_text  = 'Auf Lager';
}

// Care attributes — stored as post meta with underscore prefix (not WC taxonomy attributes)
$care_light       = get_post_meta( $product_id, '_pa_care_light',    true );
$care_water       = get_post_meta( $product_id, '_pa_care_water',    true );
$care_winter      = get_post_meta( $product_id, '_pa_care_winter',   true );
$care_winterhaerte = get_post_meta( $product_id, '_pa_winterhaerte', true );
$has_care         = $care_light || $care_water || $care_winter || $care_winterhaerte;

$temp_min_raw = get_post_meta( $product_id, '_pa_care_temp_min', true );
$temp_max_raw = get_post_meta( $product_id, '_pa_care_temp_max', true );
$temp_min     = $temp_min_raw !== '' ? (float) $temp_min_raw : null;
$temp_max     = $temp_max_raw !== '' ? (float) $temp_max_raw : null;
if ( $temp_min !== null || $temp_max !== null ) $has_care = true;

// Map text values → numeric pip scale
$light_map   = [ 'schatten' => 1, 'halbschatten' => 2, 'sonne' => 3, 'sonnig' => 3, 'vollsonne' => 4 ];
$water_map   = [ 'wenig' => 1, 'maessig' => 2, 'mäßig' => 2, 'mittel' => 2, 'viel' => 3, 'reichlich' => 3 ];
$light_scale = $light_map[ strtolower( $care_light ) ] ?? 0;
$water_scale = $water_map[ strtolower( $care_water ) ] ?? 0;
// Normalize underscore-slugs for display (e.g. 'bedingt_frostfest' → 'Bedingt frostfest')
$care_winter_display = ucfirst( str_replace( '_', ' ', $care_winter ) );

// Reviews average
$rating_count = $product->get_rating_count();
$avg_rating   = $product->get_average_rating();
?>


<!-- Hero: Gallery + Info -->
<div class="pdp-hero">

  <!-- Gallery -->
  <div class="pdp-gallery" data-layout="<?php echo count( $all_img_ids ) > 1 ? 'vertical' : 'single'; ?>" id="pdp-gallery">

    <!-- Thumbnails (vertical) -->
    <?php if ( count( $all_img_ids ) > 1 ) : ?>
    <div class="pdp-thumbs">
      <?php foreach ( $all_img_ids as $i => $img_id ) :
        $thumb_url = (string) wp_get_attachment_image_url( $img_id, 'thumbnail' );
      ?>
      <button class="pdp-thumb <?php echo $i === 0 ? 'active' : ''; ?>"
              style="background-image:url(<?php echo esc_url( $thumb_url ); ?>)"
              data-thumb="<?php echo esc_attr( $i ); ?>"
              aria-label="Bild <?php echo esc_attr( $i + 1 ); ?>">
      </button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Main image -->
    <div class="pdp-main-img" id="pdp-main-img" role="img" aria-label="Produktbild">
      <?php if ( $all_img_ids ) :
        $main_url = (string) wp_get_attachment_image_url( $all_img_ids[0], 'large' );
        $all_urls = array_map( function( $id ) {
          return (string) wp_get_attachment_image_url( $id, 'large' );
        }, $all_img_ids );
      ?>
      <div class="pdp-main-img-bg" id="pdp-main-bg"
           style="background-image:url(<?php echo esc_url( $main_url ); ?>)">
      </div>
      <?php else : ?>
      <div class="pdp-main-img-bg" style="background-image:url(<?php echo esc_url( wc_placeholder_img_src( 'large' ) ); ?>)"></div>
      <?php endif; ?>

      <?php
      // Badges
      $badges = [];
      if ( $product->is_on_sale() ) $badges[] = 'Sale';
      if ( $product->is_featured() ) $badges[] = 'Neu';
      if ( $stock_stat === 'outofstock' ) $badges[] = 'Ausverkauft';
      if ( $badges ) : ?>
      <div class="pdp-badge-stack">
        <?php foreach ( $badges as $badge ) : ?>
        <span class="pa-badge"><?php echo esc_html( $badge ); ?></span>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="pdp-zoom-hint">+ Klick zum Vergrößern</div>
    </div>

  </div><!-- .pdp-gallery -->

  <!-- Info panel (buy side) -->
  <div class="pdp-info">

    <?php if ( $collection ) : ?>
    <div class="pdp-eyebrow"><?php echo esc_html( $collection ); ?></div>
    <?php endif; ?>

    <?php if ( $pa_gattung || $pa_art || $pa_kultivar ) : ?>
      <?php if ( $pa_genus_line ) : ?>
      <div class="pdp-taxonomy"><?php echo esc_html( $pa_genus_line ); ?></div>
      <?php endif; ?>
      <h1 class="pdp-title">
        <?php if ( $pa_kultivar ) : ?>
          &lsquo;<?php echo esc_html( $pa_kultivar ); ?>&rsquo;
        <?php elseif ( $pa_art ) : ?>
          <?php echo esc_html( $pa_art ); ?>
        <?php else : ?>
          <?php echo esc_html( $pa_gattung ); ?>
        <?php endif; ?>
      </h1>
    <?php else : ?>
      <h1 class="pdp-title">
        <?php echo esc_html( $name ); ?>
        <?php if ( $latin ) : ?><em><?php echo esc_html( $latin ); ?></em><?php endif; ?>
      </h1>
    <?php endif; ?>

    <!-- Meta row -->
    <div class="pdp-meta-row">
      <?php if ( $sku ) : ?>
      <span>Art.-Nr. <?php echo esc_html( $sku ); ?></span>
      <span class="sep"></span>
      <?php endif; ?>
      <?php if ( $stock_stat === 'instock' ) : ?>
      <span>Versandfertig</span>
      <?php endif; ?>
    </div>

    <!-- Price -->
    <div class="pdp-price-row">
      <span class="pdp-price"><?php echo wp_kses_post( $price_html ); ?></span>
      <span class="pdp-price-tax">inkl. MwSt. &middot; zzgl. Versand</span>
    </div>

    <!-- Stock indicator -->
    <div class="pdp-stock <?php echo esc_attr( $stock_class ); ?>">
      <span class="dot"></span>
      <span><?php echo esc_html( $stock_text ); ?></span>
    </div>

    <!-- Pot sizes / variations or add to cart -->
    <?php
    if ( $product->is_type( 'variable' ) ) {
      // For variable products, render the variation selector styled as pdp-pots
      $variations = $product->get_available_variations();
      $attributes = $product->get_variation_attributes();
      ?>
      <div>
        <?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>
        <form class="variations_form cart pdp-variations-form"
              action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>"
              method="post" enctype='multipart/form-data'
              data-product_id="<?php echo absint( $product_id ); ?>"
              data-product_variations="<?php echo htmlspecialchars( wp_json_encode( $variations ) ); ?>">
          <?php do_action( 'woocommerce_before_variations_form' ); ?>

          <?php if ( empty( $variations ) && false !== $variations ) : ?>
          <p class="stock out-of-stock"><?php echo esc_html__( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>
          <?php else : ?>

          <div class="variations">
            <?php foreach ( $attributes as $attribute_name => $options ) : ?>
            <div class="pdp-opt-label">
              <?php echo wc_attribute_label( $attribute_name ); ?>
            </div>
            <div class="pdp-pots">
              <?php
              wc_dropdown_variation_attribute_options( [
                'options'   => $options,
                'attribute' => $attribute_name,
                'product'   => $product,
              ] );
              ?>
            </div>
            <?php endforeach; ?>
          </div>

          <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

          <div class="single_variation_wrap">
            <?php do_action( 'woocommerce_before_single_variation' ); ?>
            <?php do_action( 'woocommerce_single_variation' ); ?>
            <?php do_action( 'woocommerce_after_single_variation' ); ?>
          </div>

          <?php endif; ?>

          <?php do_action( 'woocommerce_after_variations_form' ); ?>
        </form>
        <?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
      </div>
      <?php
    } else {
      // Simple product — quantity + CTA
      ?>
      <div class="pdp-buy-row">
        <div class="pdp-qty">
          <button type="button" class="pdp-qty-dec" aria-label="Weniger">–</button>
          <span class="val" id="pdp-qty-display">1</span>
          <input type="hidden" id="pdp-qty-input" value="1"
                 min="1" max="<?php echo esc_attr( $stock_qty > 0 ? $stock_qty : 999 ); ?>">
          <button type="button" class="pdp-qty-inc" aria-label="Mehr">+</button>
        </div>
        <?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
        <form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data">
          <input type="hidden" name="product_id" value="<?php echo absint( $product_id ); ?>">
          <input type="hidden" name="quantity" id="pdp-qty-form" value="1">
          <?php wp_nonce_field( 'woocommerce-add_to_cart', '_wpnonce' ); ?>
          <button type="submit" class="pdp-cta" name="add-to-cart" value="<?php echo esc_attr( $product_id ); ?>">
            <span>In den Warenkorb</span>
            <span class="arrow">&rarr;</span>
          </button>
        </form>
        <?php else : ?>
        <button class="pdp-cta" disabled style="opacity:0.5;cursor:not-allowed;">
          <span>Ausverkauft</span>
        </button>
        <?php endif; ?>
      </div>
      <?php
    }
    ?>

    <?php if ( $short_desc ) : ?>
    <div class="pdp-short-desc"><?php echo wp_kses_post( $short_desc ); ?></div>
    <?php endif; ?>

  </div><!-- .pdp-info -->

</div><!-- .pdp-hero -->

<?php if ( $has_care || $long_desc ) :
  $is_split = $has_care && $long_desc;
?>
<div class="pdp-detail-cols<?php echo $is_split ? ' is-split' : ''; ?>">

  <?php if ( $long_desc ) : ?>
  <div class="pdp-detail-main">
    <div class="pdp-steckbrief-heading">
      <span class="pdp-steckbrief-eyebrow">&middot; Beschreibung &middot;</span>
      <h2><?php echo esc_html( $pa_kultivar ?: ( $pa_art ?: ( $pa_gattung ?: $name ) ) ); ?></h2>
    </div>
    <div class="pdp-description-body">
      <?php echo apply_filters( 'the_content', $long_desc ); ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if ( $has_care ) : ?>
  <div class="pdp-detail-side">
    <div class="pdp-detail-sticky">
      <div class="pdp-steckbrief-heading">
        <span class="pdp-steckbrief-eyebrow">&middot; Pflege-Steckbrief &middot;</span>
        <h2><?php echo esc_html( $pa_kultivar ?: ( $pa_art ?: ( $pa_gattung ?: $name ) ) ); ?> kultivieren</h2>
      </div>
      <div class="pdp-iconscale">
        <div class="pdp-iconscale-grid">

          <?php if ( $care_light ) : ?>
          <div class="pdp-iconscale-cell">
            <div class="label">Licht</div>
            <div class="value"><?php echo esc_html( $care_light ); ?></div>
            <?php if ( $light_scale > 0 ) : ?>
            <div class="pdp-scale-block">
              <div class="pdp-scale">
                <?php for ( $i = 1; $i <= 4; $i++ ) : ?>
                <span class="pip<?php echo $i <= $light_scale ? ' on' : ''; ?>"></span>
                <?php endfor; ?>
              </div>
              <div class="pdp-scale-ends"><span>Schatten</span><span>Vollsonne</span></div>
            </div>
            <?php endif; ?>
          </div>
          <?php endif; ?>

          <?php if ( $care_water ) : ?>
          <div class="pdp-iconscale-cell">
            <div class="label">Wasser</div>
            <div class="value"><?php echo esc_html( $care_water ); ?></div>
            <?php if ( $water_scale > 0 ) : ?>
            <div class="pdp-scale-block">
              <div class="pdp-scale">
                <?php for ( $i = 1; $i <= 3; $i++ ) : ?>
                <span class="pip water<?php echo $i <= $water_scale ? ' on' : ''; ?>"></span>
                <?php endfor; ?>
              </div>
              <div class="pdp-scale-ends"><span>Wenig</span><span>Viel</span></div>
            </div>
            <?php endif; ?>
          </div>
          <?php endif; ?>

          <?php if ( $care_temp || ( $temp_min !== null && $temp_max !== null ) ) : ?>
          <div class="pdp-iconscale-cell" style="grid-column:1/-1">
            <div class="label">Temperatur<?php if ( $temp_min !== null && $temp_max !== null ) : ?> · <?php echo intval( $temp_min ); ?>°C — <?php echo intval( $temp_max ); ?>°C<?php endif; ?></div>
            <?php if ( $care_temp ) : ?>
            <div class="value"><?php echo esc_html( $care_temp ); ?></div>
            <?php endif; ?>
            <?php if ( $temp_min !== null && $temp_max !== null ) :
              $left_pct  = max( 0, min( 100, $temp_min / 35 * 100 ) );
              $width_pct = max( 0, min( 100 - $left_pct, ( $temp_max - $temp_min ) / 35 * 100 ) );
            ?>
            <div class="pdp-tempbar">
              <div class="pdp-tempbar-track">
                <div class="pdp-tempbar-fill" style="left:<?php echo round( $left_pct, 1 ); ?>%;width:<?php echo round( $width_pct, 1 ); ?>%"></div>
              </div>
              <div class="pdp-tempbar-labels">
                <span>0°C</span>
                <span>Minimum <b>+<?php echo intval( $temp_min ); ?>°C</b></span>
                <span>Maximum <b>+<?php echo intval( $temp_max ); ?>°C</b></span>
                <span>35°C</span>
              </div>
            </div>
            <?php endif; ?>
          </div>
          <?php endif; ?>

          <?php if ( $care_winter ) : ?>
          <div class="pdp-iconscale-cell pdp-iconscale-wide">
            <div class="label">Überwinterung</div>
            <p class="value"><?php echo esc_html( $care_winter_display ); ?></p>
          </div>
          <?php endif; ?>

          <?php
          $wh_labels = [
            'nicht-wh'   => 'Nicht winterhart',
            'bedingt-wh' => 'Bedingt winterhart (bis ca. −5 °C)',
            'winterhart'  => 'Winterhart (bis ca. −10 °C)',
            'sehr-wh'    => 'Sehr winterhart (bis ca. −15 °C)',
            'voll-wh'    => 'Vollwinterhart (bis −20 °C+)',
          ];
          if ( $care_winterhaerte && isset( $wh_labels[ $care_winterhaerte ] ) ) : ?>
          <div class="pdp-iconscale-cell pdp-iconscale-wide">
            <div class="label">Winterhärte</div>
            <p class="value"><?php echo esc_html( $wh_labels[ $care_winterhaerte ] ); ?></p>
          </div>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

</div>
<?php endif; ?>

<!-- ── Bewertungen ── -->
<?php if ( comments_open() ) : ?>
<div class="pdp-reviews">
  <div class="pdp-reviews-inner">
    <header class="pdp-reviews-head">
      <div class="pdp-reviews-head-l">
        <div class="eyebrow">Sammlerstimmen</div>
        <h2>Bewertungen</h2>
      </div>
      <?php if ( $rating_count > 0 ) : ?>
      <div class="pdp-reviews-head-r">
        <div class="pdp-stars">
          <span class="marks">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
          <span class="num"><?php echo esc_html( number_format( (float) $avg_rating, 1 ) ); ?></span>
          <span class="sep">&middot;</span>
          <span class="count"><?php echo esc_html( $rating_count ); ?> Bewertungen</span>
        </div>
      </div>
      <?php endif; ?>
    </header>

    <!-- Login-Status Banner -->
    <?php
    $current_user   = wp_get_current_user();
    $has_purchased  = wc_customer_bought_product( $current_user->user_email, $current_user->ID, $product_id );
    $already_reviewed = $current_user->ID ? (int) get_comments( [
        'post_id' => $product_id,
        'user_id' => $current_user->ID,
        'status'  => 'any',
        'count'   => true,
    ] ) > 0 : false;
    ?>
    <div class="pdp-write-wrap">
      <?php if ( $has_purchased && $already_reviewed ) : ?>
      <div class="pdp-write-banner pdp-write-banner-user">
        <div class="pdp-write-banner-text">
          <b>Bewertung bereits abgegeben.</b>
          <span>Sie haben dieses Sammlerstück bereits bewertet. Vielen Dank!</span>
        </div>
      </div>
      <?php elseif ( $has_purchased ) : ?>
      <div class="pdp-write-banner pdp-write-banner-buyer">
        <div class="dot"></div>
        <div class="pdp-write-banner-text">
          <b>Sie haben dieses Sammlerstück gekauft.</b>
          <span>Teilen Sie Ihre Erfahrung mit der Community.</span>
        </div>
      </div>
      <?php elseif ( is_user_logged_in() ) : ?>
      <div class="pdp-write-banner pdp-write-banner-user">
        <div class="pdp-write-banner-text">
          <b>Bewertungen nur für Käufer:innen.</b>
          <span>Sie haben diese Sorte (noch) nicht erworben. Nach dem Kauf können Sie hier eine Bewertung hinterlassen.</span>
        </div>
        <a class="pdp-write-banner-cta" href="<?php echo esc_url( get_permalink( $product_id ) ); ?>">Zum Sammlerstück &rarr;</a>
      </div>
      <?php else : ?>
      <div class="pdp-write-banner pdp-write-banner-guest">
        <div class="pdp-write-banner-text">
          <b>Schon einmal bestellt?</b>
          <span>Melden Sie sich an, um eine Bewertung zu hinterlassen.</span>
        </div>
        <div class="pdp-write-banner-actions">
          <a class="pdp-write-banner-cta secondary" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">Anmelden</a>
          <a class="pdp-write-banner-cta" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">Konto erstellen</a>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Bewertungsliste -->
    <?php
    $args = array(
      'post_id' => $product_id,
      'status' => 'approve',
      'number' => 4,
    );
    $comments = get_comments( $args );
    $total_comments = get_comments_number( $product_id );
    ?>
    <?php if ( $comments ) : ?>
    <ol class="pdp-review-list">
      <?php foreach ( $comments as $comment ) :
        $rating = get_comment_meta( $comment->comment_ID, 'rating', true );
        $verified = wc_review_is_from_verified_owner( $comment->comment_ID );
      ?>
      <li class="pdp-review-row" data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>">
        <div class="pdp-review-row-marks">
          <span class="marks"><?php echo wc_get_rating_html( $rating ); ?></span>
          <?php if ( $verified ) : ?>
          <span class="verified">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
            Verifizierter Kauf
          </span>
          <?php endif; ?>
          <?php if ( current_user_can('manage_options') ) : ?>
          <button class="pdp-review-delete"
                  data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>"
                  data-author="<?php echo esc_attr( $comment->comment_author ); ?>">
            Löschen
          </button>
          <?php endif; ?>
        </div>
        <p class="pdp-review-row-quote">"<?php echo esc_html( $comment->comment_content ); ?>"</p>
        <div class="pdp-review-row-by">
          <span class="pdp-review-row-name"><?php echo esc_html( $comment->comment_author ); ?></span>
          <?php if ( $comment->comment_author_url ) : ?>
          <span class="pdp-review-row-city"><?php echo esc_html( $comment->comment_author_url ); ?></span>
          <?php endif; ?>
          <span class="pdp-review-row-date"><?php echo esc_html( get_comment_date( 'j. F Y', $comment->comment_ID ) ); ?></span>
        </div>
      </li>
      <?php endforeach; ?>
    </ol>

    <?php if ( $total_comments > 4 ) : ?>
    <div class="pdp-reviews-more">
      <button class="pdp-more-btn" id="pdp-load-more-reviews">
        <span>Mehr anzeigen — <?php echo esc_html( $total_comments - 4 ); ?> weitere</span>
        <span class="arrow">&darr;</span>
      </button>
    </div>
    <?php endif; ?>
    <?php else : ?>
    <p class="pdp-no-reviews">Noch keine Bewertungen vorhanden.</p>
    <?php endif; ?>

    <?php if ( $has_purchased && ! $already_reviewed ) : ?>
    <!-- Bewertungsformular -->
    <div class="pdp-review-form-wrap">
      <form class="pdp-review-form" id="pdp-review-form" method="post"
            action="<?php echo esc_url( site_url( '/wp-comments-post.php' ) ); ?>">
        <div class="pdp-review-form-head">
          <div class="pdp-review-form-head-l">
            <span class="pdp-review-form-eyebrow">Ihre Bewertung</span>
            <span class="pdp-review-form-as">
              Wird veröffentlicht als <b><?php echo esc_html( $current_user->display_name ); ?></b>
            </span>
          </div>
        </div>
        <div class="pdp-review-form-stars">
          <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
          <button type="button" class="star" data-rating="<?php echo esc_attr( $i ); ?>" aria-label="<?php echo esc_attr( $i ); ?> Sterne">&#9733;</button>
          <?php endfor; ?>
          <span class="pdp-review-form-hint">Bitte Sterne wählen</span>
        </div>
        <textarea
          class="pdp-review-form-text"
          name="comment"
          placeholder="Wie hat sich Ihre Pflanze entwickelt? Was sollten andere Sammler:innen wissen?"
          rows="4"
          required
        ></textarea>
        <input type="hidden" name="rating" id="pdp-rating-input" value="0">
        <input type="hidden" name="comment_post_ID" value="<?php echo absint( $product_id ); ?>">
        <input type="hidden" name="comment_parent" value="0">
        <?php wp_nonce_field( 'add-comment' ); ?>
        <input type="hidden" name="_wp_http_referer" value="<?php echo esc_attr( get_permalink( $product_id ) ); ?>">
        <div class="pdp-review-form-foot">
          <span class="pdp-review-form-legal">
            Mit dem Absenden bestätigen Sie unsere <a href="#">Bewertungsrichtlinien</a>.
          </span>
          <button type="submit" class="pdp-cta pdp-review-form-submit" id="pdp-submit-review">
            Bewertung absenden <span class="arrow">&rarr;</span>
          </button>
        </div>
      </form>
    </div>
    <?php endif; ?>

  </div>
</div>
<?php endif; ?>

<?php if ( current_user_can('manage_options') ) : ?>
<div class="pa-admin-overlay" id="pa-delete-review-overlay" aria-hidden="true">
  <div class="pa-admin-modal" role="dialog" aria-modal="true">
    <h3>Bewertung löschen</h3>
    <p class="pa-admin-modal-sub">Von: <strong id="pa-delete-author"></strong></p>
    <label for="pa-delete-reason">Begründung (wird per E-Mail an den Verfasser gesandt)</label>
    <textarea id="pa-delete-reason" rows="4" placeholder="Bitte Begründung eingeben…"></textarea>
    <div class="pa-admin-modal-foot">
      <button class="pa-admin-modal-cancel" id="pa-delete-cancel">Abbrechen</button>
      <button class="pa-admin-modal-confirm" id="pa-delete-confirm" disabled>Löschen &amp; E-Mail senden</button>
    </div>
  </div>
</div>
<style>
.pa-admin-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.72);z-index:9000;align-items:center;justify-content:center}
.pa-admin-overlay.open{display:flex}
.pa-admin-modal{background:var(--bg-surface,#1a1330);border:1px solid var(--border-hair,rgba(255,255,255,.12));border-radius:8px;padding:32px;width:min(480px,90vw);color:var(--creme,#f5f0e8)}
.pa-admin-modal h3{font-family:var(--serif-display);font-size:20px;margin:0 0 4px}
.pa-admin-modal-sub{font-size:13px;color:var(--lavender,#b8a9d4);margin:0 0 20px}
.pa-admin-modal label{display:block;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--lavender,#b8a9d4);margin-bottom:8px}
.pa-admin-modal textarea{width:100%;box-sizing:border-box;background:rgba(255,255,255,.05);border:1px solid var(--border-hair,rgba(255,255,255,.15));border-radius:4px;color:var(--creme,#f5f0e8);padding:12px;font-size:14px;font-family:inherit;resize:vertical}
.pa-admin-modal-foot{display:flex;gap:12px;margin-top:20px;justify-content:flex-end}
.pa-admin-modal-cancel{background:transparent;border:1px solid rgba(255,255,255,.2);color:var(--lavender,#b8a9d4);padding:10px 20px;border-radius:4px;cursor:pointer;font-size:14px}
.pa-admin-modal-confirm{background:var(--plum-hot,#7c3aed);border:none;color:#fff;padding:10px 20px;border-radius:4px;cursor:pointer;font-size:14px}
.pa-admin-modal-confirm:disabled{opacity:.4;cursor:not-allowed}
.pdp-review-delete{background:none;border:1px solid rgba(220,60,60,.35);color:rgba(220,80,80,.8);font-size:11px;padding:3px 10px;border-radius:3px;cursor:pointer;margin-left:auto;transition:background .15s}
.pdp-review-delete:hover{background:rgba(220,60,60,.12)}
</style>
<?php endif; ?>

<!-- ── Ähnliche Produkte ── -->
<?php
$related_ids = wc_get_related_products( $product_id, 4 );
if ( $related_ids ) :
  $related_products = array_map( 'wc_get_product', $related_ids );
  $related_products = array_filter( $related_products );
?>
<div class="pdp-related">
  <div class="pdp-related-head">
    <h2>Im selben <em>Gewächshaus</em></h2>
    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
      Alle Pelargonien &rarr;
    </a>
  </div>
  <div class="pdp-related-grid">
    <?php foreach ( $related_products as $related ) :
      $r_img_id  = $related->get_image_id();
      $r_img_url = $r_img_id
        ? (string) wp_get_attachment_image_url( $r_img_id, 'medium' )
        : (string) wc_placeholder_img_src( 'medium' );
      $r_latin   = $related->get_attribute( 'gattung' )
        ?: $related->get_attribute( 'pa_gattung' )
        ?: '';
    ?>
    <a href="<?php echo esc_url( get_permalink( $related->get_id() ) ); ?>"
       class="pa-product-card" style="display:block;text-decoration:none;background:var(--bg-surface);border:1px solid var(--border-hair);">
      <div style="aspect-ratio:3/4;background-image:url(<?php echo esc_url( $r_img_url ); ?>);background-size:cover;background-position:center;"></div>
      <div style="padding:16px;">
        <div style="font-family:var(--serif-display);font-size:18px;color:var(--creme);margin-bottom:4px;"><?php echo esc_html( $related->get_name() ); ?></div>
        <?php if ( $r_latin ) : ?>
        <div style="font-family:var(--serif-display);font-style:italic;font-size:13px;color:var(--lavender);margin-bottom:8px;"><?php echo esc_html( $r_latin ); ?></div>
        <?php endif; ?>
        <div style="font-family:var(--serif-display);font-weight:500;font-size:20px;color:var(--accent);"><?php echo wp_kses_post( $related->get_price_html() ); ?></div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

</div><!-- .pa-pdp-product -->

<?php do_action( 'woocommerce_after_single_product' ); ?>

<!-- Gallery + Qty JS -->
<script>
(function(){
  // Gallery thumbnail switching
  var imgs = <?php echo $all_img_ids ? wp_json_encode( array_values( array_map( function( $id ) {
    return (string) wp_get_attachment_image_url( $id, 'large' );
  }, $all_img_ids ) ) ) : '[]'; ?>;

  var mainBg   = document.getElementById('pdp-main-bg');
  var mainImg  = document.getElementById('pdp-main-img');
  var thumbs   = document.querySelectorAll('.pdp-thumb');
  var zoomHint = mainImg ? mainImg.querySelector('.pdp-zoom-hint') : null;
  var zoomed   = false;

  thumbs.forEach(function(btn){
    btn.addEventListener('click', function(){
      var idx = parseInt(this.dataset.thumb) || 0;
      if(mainBg && imgs[idx]) mainBg.style.backgroundImage = 'url(' + imgs[idx] + ')';
      thumbs.forEach(function(t){ t.classList.remove('active'); });
      this.classList.add('active');
      zoomed = false;
      if(mainBg) mainBg.style.transform = '';
      if(zoomHint) zoomHint.textContent = '+ Klick zum Vergrößern';
    });
  });

  if(mainImg){
    mainImg.addEventListener('click', function(){
      zoomed = !zoomed;
      if(mainBg) mainBg.style.transform = zoomed ? 'scale(1.6)' : '';
      mainImg.style.cursor = zoomed ? 'zoom-out' : 'zoom-in';
      if(zoomHint) zoomHint.textContent = zoomed ? '— Klick zum Verkleinern' : '+ Klick zum Vergrößern';
    });
  }

  // Simple product qty +/-
  var dec     = document.querySelector('.pdp-qty-dec');
  var inc     = document.querySelector('.pdp-qty-inc');
  var display = document.getElementById('pdp-qty-display');
  var hidden  = document.getElementById('pdp-qty-input');
  var form    = document.getElementById('pdp-qty-form');

  function syncQty(val){
    if(display) display.textContent = val;
    if(hidden)  hidden.value        = val;
    if(form)    form.value          = val;
    var max = hidden ? parseInt(hidden.max) || 999 : 999;
    if(dec) dec.disabled = val <= 1;
    if(inc) inc.disabled = val >= max;
  }

  if(dec) dec.addEventListener('click', function(){
    var v = parseInt(hidden ? hidden.value : 1) || 1;
    syncQty(Math.max(1, v - 1));
  });
  if(inc) inc.addEventListener('click', function(){
    var v = parseInt(hidden ? hidden.value : 1) || 1;
    var max = hidden ? parseInt(hidden.max) || 999 : 999;
    syncQty(Math.min(max, v + 1));
  });

  // Pflege-Steckbrief Variant Toggle
  var variantBtns = document.querySelectorAll('.pdp-variant-btn');
  var variants = document.querySelectorAll('.pdp-steckbrief-variant');

  variantBtns.forEach(function(btn){
    btn.addEventListener('click', function(){
      var variant = this.dataset.variant;
      variantBtns.forEach(function(b){ b.classList.remove('active'); });
      this.classList.add('active');
      variants.forEach(function(v){ v.classList.remove('active'); });
      document.querySelector('.pdp-steckbrief-' + variant).classList.add('active');
    });
  });

  // Bewertungsformular Sterne
  var stars = document.querySelectorAll('.pdp-review-form-stars .star');
  var ratingInput = document.getElementById('pdp-rating-input');
  var hint = document.querySelector('.pdp-review-form-hint');
  var submitBtn = document.getElementById('pdp-submit-review');
  var reviewText = document.querySelector('.pdp-review-form-text');

  if(stars.length > 0){
    stars.forEach(function(star){
      star.addEventListener('click', function(){
        var rating = parseInt(this.dataset.rating);
        ratingInput.value = rating;
        stars.forEach(function(s, i){
          if(i < rating) s.classList.add('on');
          else s.classList.remove('on');
        });
        hint.textContent = rating + ' von 5';
        checkSubmitBtn();
      });
      star.addEventListener('mouseenter', function(){
        var rating = parseInt(this.dataset.rating);
        stars.forEach(function(s, i){
          if(i < rating) s.classList.add('on');
          else s.classList.remove('on');
        });
      });
      star.addEventListener('mouseleave', function(){
        var currentRating = parseInt(ratingInput.value) || 0;
        stars.forEach(function(s, i){
          if(i < currentRating) s.classList.add('on');
          else s.classList.remove('on');
        });
      });
    });
  }

  function checkSubmitBtn(){
    if(submitBtn && reviewText && ratingInput){
      var rating = parseInt(ratingInput.value) || 0;
      var text = reviewText.value.trim();
      submitBtn.disabled = rating === 0 || text === '';
    }
  }

  if(reviewText){
    reviewText.addEventListener('input', checkSubmitBtn);
  }

  // Load more reviews
  var loadMoreBtn = document.getElementById('pdp-load-more-reviews');
  if(loadMoreBtn){
    loadMoreBtn.addEventListener('click', function(){
      this.style.display = 'none';
    });
  }

  <?php if ( current_user_can('manage_options') ) : ?>
  // Admin: Bewertung löschen
  var paAdminNonce  = <?php echo wp_json_encode( wp_create_nonce('pa_delete_review') ); ?>;
  var paAjaxUrl     = <?php echo wp_json_encode( admin_url('admin-ajax.php') ); ?>;
  var paOverlay     = document.getElementById('pa-delete-review-overlay');
  var paAuthorEl    = document.getElementById('pa-delete-author');
  var paReasonEl    = document.getElementById('pa-delete-reason');
  var paConfirmBtn  = document.getElementById('pa-delete-confirm');
  var paCancelBtn   = document.getElementById('pa-delete-cancel');
  var paTargetId    = null;

  document.querySelectorAll('.pdp-review-delete').forEach(function(btn){
    btn.addEventListener('click', function(){
      paTargetId = this.dataset.commentId;
      paAuthorEl.textContent = this.dataset.author;
      paReasonEl.value = '';
      paConfirmBtn.disabled = true;
      paConfirmBtn.textContent = 'Löschen & E-Mail senden';
      paOverlay.setAttribute('aria-hidden','false');
      paOverlay.classList.add('open');
      paReasonEl.focus();
    });
  });

  if(paReasonEl){
    paReasonEl.addEventListener('input', function(){
      paConfirmBtn.disabled = this.value.trim() === '';
    });
  }

  if(paCancelBtn){
    paCancelBtn.addEventListener('click', function(){
      paOverlay.classList.remove('open');
      paOverlay.setAttribute('aria-hidden','true');
    });
  }

  if(paOverlay){
    paOverlay.addEventListener('click', function(e){
      if(e.target === this){
        this.classList.remove('open');
        this.setAttribute('aria-hidden','true');
      }
    });
  }

  if(paConfirmBtn){
    paConfirmBtn.addEventListener('click', function(){
      var reason = paReasonEl.value.trim();
      if(!reason || !paTargetId) return;
      paConfirmBtn.disabled = true;
      paConfirmBtn.textContent = 'Wird gelöscht…';

      var fd = new FormData();
      fd.append('action',     'pa_delete_review');
      fd.append('comment_id', paTargetId);
      fd.append('reason',     reason);
      fd.append('nonce',      paAdminNonce);

      fetch(paAjaxUrl, { method:'POST', body:fd, credentials:'same-origin' })
        .then(function(r){ return r.json(); })
        .then(function(data){
          if(data.success){
            var li = document.querySelector('.pdp-review-row[data-comment-id="'+paTargetId+'"]');
            if(li){ li.style.transition='opacity .3s'; li.style.opacity='0'; setTimeout(function(){ li.remove(); }, 320); }
            paOverlay.classList.remove('open');
            paOverlay.setAttribute('aria-hidden','true');
          } else {
            alert(data.data && data.data.message ? data.data.message : 'Fehler beim Löschen.');
            paConfirmBtn.disabled = false;
            paConfirmBtn.textContent = 'Löschen & E-Mail senden';
          }
        })
        .catch(function(){
          alert('Verbindungsfehler.');
          paConfirmBtn.disabled = false;
          paConfirmBtn.textContent = 'Löschen & E-Mail senden';
        });
    });
  }
  <?php endif; ?>
})();
</script>
