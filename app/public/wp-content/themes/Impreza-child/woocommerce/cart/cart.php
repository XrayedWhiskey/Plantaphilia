<?php
/**
 * Plantaphilia — Warenkorb (überschreibt WooCommerce cart/cart.php)
 */
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );

$cart = WC()->cart;

// Cart notices (coupons, errors, etc.)
wc_print_notices();
?>
<div class="cc-page">
<div class="cc-wrap">

  <!-- Steps -->
  <nav class="cc-steps" aria-label="Bestellschritte">
    <div class="cc-step active">
      <span class="cc-step-num">1</span>
      <span class="cc-step-label">Warenkorb</span>
    </div>
    <span class="cc-step-rule"></span>
    <div class="cc-step">
      <span class="cc-step-num">2</span>
      <span class="cc-step-label">Versand &amp; Zahlung</span>
    </div>
    <span class="cc-step-rule"></span>
    <div class="cc-step">
      <span class="cc-step-num">3</span>
      <span class="cc-step-label">Bestätigung</span>
    </div>
  </nav>

  <!-- Page heading -->
  <div class="cc-pagehead">
    <div class="cc-pagehead-l">
      <h1>Waren<em>korb</em></h1>
    </div>
  </div>

  <?php if ( $cart->is_empty() ) : ?>

    <!-- Empty state -->
    <div class="cc-empty">
      <div class="glyph">&#10042;</div>
      <h2>Der Warenkorb ist noch leer.</h2>
      <p>Stöbern Sie im Schaubeet &mdash; wir haben diese Woche neue Sorten aufgenommen.</p>
      <a class="cc-cta" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"
         style="display:inline-flex;width:auto;text-decoration:none;margin-top:28px;">
        Zum Schaubeet <span class="arrow">&rarr;</span>
      </a>
    </div>

    <?php do_action( 'woocommerce_cart_is_empty' ); ?>

  <?php else : ?>

  <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

    <div class="cc-grid">

      <!-- ── Linke Spalte: Artikel ── -->
      <div class="cc-main">

        <div class="cc-sect-head">
          <?php $cnt = $cart->get_cart_contents_count(); ?>
          <h2><em><?php echo esc_html( $cnt ); ?></em>&nbsp; Position<?php echo $cnt === 1 ? '' : 'en'; ?></h2>
        </div>

        <div class="cc-lines">
          <?php foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) :
            $product    = $cart_item['data'];
            $product_id = $cart_item['product_id'];
            $qty        = $cart_item['quantity'];

            if ( ! $product || ! $product->exists() || $qty === 0 ) { continue; }

            // Image
            $img_id  = $product->get_image_id();
            $img_url = $img_id
              ? (string) wp_get_attachment_image_url( $img_id, 'medium' )
              : (string) wc_placeholder_img_src( 'medium' );

            // Stock
            $stock       = $product->get_stock_quantity();
            $stock_class = ( $stock !== null && $stock <= 5 ) ? 'low' : '';
            $stock_msg   = ( $stock !== null && $stock <= 5 )
              ? 'Nur noch ' . $stock . ' Exemplar' . ( $stock === 1 ? '' : 'e' ) . ' im Schaubeet'
              : 'Versandfertig in 2 Werktagen';

            // Max qty
            $max_qty = $product->get_max_purchase_quantity();

            // SKU & latin name (attribute)
            $sku   = $product->get_sku();
            $latin = $product->get_attribute( 'gattung' )
              ?: $product->get_attribute( 'pa_gattung' )
              ?: '';

            // Prices (inkl. MwSt., wie im Bestellwert angezeigt)
            $line_gross    = $cart_item['line_subtotal'] + $cart_item['line_subtotal_tax'];
            $line_subtotal = wc_price( $line_gross );
            $unit_price    = $qty > 1 ? wc_price( $line_gross / $qty ) : '';

            // Remove URL
            $remove_url = add_query_arg('remove_item', $cart_item_key, wc_get_cart_url());

            // Item class for WooCommerce hooks
            $item_class = apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key );
          ?>
          <article class="cc-line <?php echo esc_attr( $item_class ); ?>"
                   data-key="<?php echo esc_attr( $cart_item_key ); ?>">

            <!-- Image -->
            <div class="cc-line-img" style="background-image:url(<?php echo esc_url( $img_url ); ?>)">
              <?php if ( $product->is_on_sale() ) : ?>
                <span class="cc-line-badge">Sale</span>
              <?php endif; ?>
            </div>

            <!-- Body -->
            <div class="cc-line-body">

              <h3 class="cc-line-name">
                <?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $product->get_name(), $cart_item, $cart_item_key ) ); ?>
              </h3>

              <?php if ( $latin ) : ?>
              <div class="cc-line-latin"><?php echo esc_html( $latin ); ?></div>
              <?php endif; ?>

              <div class="cc-line-meta">
                <?php if ( $sku ) : ?>
                <span>Art.-Nr. <?php echo esc_html( $sku ); ?></span>
                <span class="sep"></span>
                <?php endif; ?>
                <?php
                // Variation meta (returns string in WC 8+, render directly)
                $item_data_html = wc_get_formatted_cart_item_data( $cart_item );
                if ( $item_data_html ) {
                  echo wp_kses_post( $item_data_html );
                }
                ?>
              </div>

              <!-- Qty controls + remove -->
              <div class="cc-line-controls">
                <div class="cc-qty">
                  <button type="button" class="pa-qty-dec"
                          data-key="<?php echo esc_attr( $cart_item_key ); ?>"
                          aria-label="Weniger"
                          <?php echo $qty <= 1 ? 'disabled' : ''; ?>>
                    &ndash;
                  </button>
                  <span class="val" data-qty-display="<?php echo esc_attr( $cart_item_key ); ?>"><?php echo esc_html( $qty ); ?></span>
                  <button type="button" class="pa-qty-inc"
                          data-key="<?php echo esc_attr( $cart_item_key ); ?>"
                          aria-label="Mehr"
                          <?php echo ( $max_qty > 0 && $qty >= $max_qty ) ? 'disabled' : ''; ?>>
                    +
                  </button>
                  <input type="hidden"
                         class="qty"
                         name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]"
                         value="<?php echo esc_attr( $qty ); ?>"
                         data-qty-input="<?php echo esc_attr( $cart_item_key ); ?>"
                         min="1"
                         max="<?php echo esc_attr( $max_qty > 0 ? $max_qty : 999 ); ?>">
                </div>

                <a class="cc-line-link" href="<?php echo esc_url( $remove_url ); ?>" aria-label="Entfernen">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  Entfernen
                </a>
              </div>

              <div class="cc-line-stock <?php echo esc_attr( $stock_class ); ?>">
                &mdash; <?php echo esc_html( $stock_msg ); ?>
              </div>

            </div><!-- .cc-line-body -->

            <!-- Price -->
            <div class="cc-line-right">
              <div class="cc-line-price"><?php echo $line_subtotal; ?></div>
              <?php if ( $qty > 1 ) : ?>
              <div class="cc-line-unit"><?php echo esc_html( $qty ); ?> &times; <?php echo $unit_price; ?></div>
              <?php endif; ?>
            </div>

          </article>
          <?php endforeach; ?>
        </div><!-- .cc-lines -->

        <?php do_action( 'woocommerce_cart_contents' ); ?>

        <!-- Hidden update button (triggered by JS) -->
        <button type="submit" class="button" name="update_cart" value="update_cart"
                style="display:none" id="pa-update-cart">Update cart</button>
        <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
        <?php do_action( 'woocommerce_after_cart_contents' ); ?>

        <!-- Discount code field -->
        <?php if ( wc_coupons_enabled() ) : ?>
        <?php $applied = WC()->cart->get_applied_coupons(); ?>
        <?php if ( $applied ) : ?>
          <?php foreach ( $applied as $code ) : ?>
          <div class="cc-voucher applied" style="margin-top: 32px;">
            <div class="applied">
              <span><b>Gutschein</b> <?php echo esc_html( strtoupper( $code ) ); ?></span>
              <a href="<?php echo esc_url( add_query_arg( 'remove_coupon', rawurlencode( $code ), wc_get_cart_url() ) ); ?>">&times;</a>
            </div>
          </div>
          <?php endforeach; ?>
        <?php else : ?>
        <div class="cc-voucher" style="margin-top: 32px;">
          <input type="text" name="coupon_code" id="coupon_code" value="" placeholder="Gutscheincode eingeben" class="input-text">
          <button type="submit" class="button" name="apply_coupon" value="Einlösen">Einlösen</button>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <a class="cc-continue" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
          <span class="arr">&larr;</span> Weiter im Schaubeet stöbern
        </a>

      </div><!-- .cc-main -->

      <!-- ── Rechte Spalte: Bestellübersicht ── -->
      <aside class="cc-aside">
        <div class="cc-summary">
          <div class="cc-summary-eyebrow">&middot; Zwischenrechnung &middot;</div>
          <h2 class="cc-summary-h">Ihre <em>Bestellung</em></h2>

          <!-- Line items -->
          <?php foreach ( $cart->get_cart() as $item_key => $item ) :
            $p = $item['data'];
            if ( ! $p || ! $p->exists() ) { continue; }
            $item_tax = (float) $item['line_subtotal_tax'];
          ?>
          <div class="cc-summary-row">
            <span class="lab"><?php echo esc_html( $p->get_name() ); ?> &times; <?php echo esc_html( $item['quantity'] ); ?></span>
            <span class="val"><?php echo wc_price( $item['line_subtotal'] + $item_tax ); ?></span>
          </div>
          <?php if ( $item_tax > 0 ) : ?>
          <div class="cc-summary-subrow">
            <span class="lab">davon Steuern</span>
            <span class="val"><?php echo wc_price( $item_tax ); ?></span>
          </div>
          <?php endif; ?>
          <?php endforeach; ?>

          <!-- Subtotal -->
          <?php
          $subtotal = (float) $cart->get_subtotal() + (float) $cart->get_subtotal_tax();
          ?>
          <div class="cc-summary-row">
            <span class="lab">Zwischensumme</span>
            <span class="val"><?php echo wc_price( $subtotal ); ?></span>
          </div>

          <!-- Fees -->
          <?php foreach ( $cart->get_fees() as $fee ) : ?>
          <div class="cc-summary-row">
            <span class="lab"><?php echo esc_html( $fee->name ); ?></span>
            <span class="val"><?php echo wc_price( $fee->total + $fee->tax ); ?></span>
          </div>
          <?php endforeach; ?>

          <!-- Shipping -->
          <?php if ( $cart->needs_shipping() && $cart->show_shipping() ) :
            $ship_net  = (float) $cart->get_shipping_total();
            $ship_tot  = $ship_net + (float) $cart->get_shipping_tax();
            $is_free   = $ship_net === 0.0;
          ?>
          <div class="cc-summary-row <?php echo $is_free ? 'free' : ''; ?>">
            <span class="lab">Versand &middot; DHL klimaneutral</span>
            <span class="val"><?php echo $is_free ? 'Frei' : wc_price( $ship_tot ); ?></span>
          </div>
          <?php endif; ?>

          <!-- Coupons -->
          <?php foreach ( $cart->get_coupons() as $code => $coupon ) : ?>
          <div class="cc-summary-row discount">
            <span class="lab">Gutschein &middot; <?php echo esc_html( strtoupper( $code ) ); ?></span>
            <span class="val">&minus; <?php echo wc_price( $cart->get_coupon_discount_amount( $code, true ) ); ?></span>
          </div>
          <?php endforeach; ?>

          <div class="cc-summary-divider"></div>

          <!-- Total -->
          <?php
          $grand_total = $subtotal;
          if ( $cart->needs_shipping() && $cart->show_shipping() ) {
            $grand_total += $ship_tot;
          }
          foreach ( $cart->get_coupons() as $code => $_c ) {
            $grand_total -= $cart->get_coupon_discount_amount( $code, true );
          }
          foreach ( $cart->get_fees() as $fee ) {
            $grand_total += (float) $fee->total + (float) $fee->tax;
          }
          ?>
          <div class="cc-summary-total">
            <span class="lab">Gesamt</span>
            <span class="val"><?php echo wc_price( $grand_total ); ?></span>
          </div>
          <?php if ( $cart->get_taxes_total() > 0 ) : ?>
          <div class="cc-summary-tax">inkl. <?php echo wc_price( $cart->get_taxes_total() ); ?> MwSt.</div>
          <?php endif; ?>

          <!-- Checkout CTA -->
          <a class="cc-cta" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
            Zur Kasse <span class="arrow">&rarr;</span>
          </a>

          <!-- Express checkout -->
          <div class="cc-express">
            <div class="cc-express-h">oder express</div>
            <button type="button" class="cc-express-btn" onclick="window.location='<?php echo esc_url( wc_get_checkout_url() ); ?>'">
              <span class="mark">P</span> PayPal
            </button>
          </div>

        </div><!-- .cc-summary -->
      </aside>

    </div><!-- .cc-grid -->

  </form>

  <?php endif; // is_empty ?>

</div><!-- .cc-wrap -->
</div><!-- .cc-page -->

<?php do_action( 'woocommerce_after_cart' ); ?>

<script>
(function(){
  // ── Qty stepper ────────────────────────────────────────
  function qtyInit(){
    document.querySelectorAll('.pa-qty-dec, .pa-qty-inc').forEach(function(btn){
      if(btn.dataset.qtyBound) return;
      btn.dataset.qtyBound = '1';
      btn.addEventListener('click', function(){
        var key = this.dataset.key;
        var inp = document.querySelector('[data-qty-input="' + key + '"]');
        var display = document.querySelector('[data-qty-display="' + key + '"]');
        var line = document.querySelector('.cc-line[data-key="' + key + '"]');
        if(!inp) return;
        var val = parseInt(inp.value) || 1;
        var max = parseInt(inp.max) || 999;
        if(this.classList.contains('pa-qty-dec')) val = Math.max(1, val - 1);
        else val = Math.min(max, val + 1);
        inp.value = val;
        if(display) display.textContent = val;
        var dec = document.querySelector('.pa-qty-dec[data-key="' + key + '"]');
        var inc = document.querySelector('.pa-qty-inc[data-key="' + key + '"]');
        if(dec) dec.disabled = val <= 1;
        if(inc) inc.disabled = val >= max;
        clearTimeout(btn._t);
        if(line) line.classList.add('loading');
        btn._t = setTimeout(function(){
          var updateBtn = document.getElementById('pa-update-cart');
          if(updateBtn){ updateBtn.disabled = false; updateBtn.click(); }
        }, 600);
      });
    });
  }
  document.addEventListener('DOMContentLoaded', qtyInit);
  document.body.addEventListener('updated_cart_totals', qtyInit);

  // ── Remove loading state after cart update ─────────────
  document.body.addEventListener('updated_cart_totals', function(){
    document.querySelectorAll('.cc-line.loading').forEach(function(line){
      line.classList.remove('loading');
    });
  });

  // ── Coupon toggle ──────────────────────────────────────
  document.addEventListener('DOMContentLoaded', function(){
    var toggle = document.getElementById('pa-coupon-toggle');
    var form   = document.getElementById('pa-coupon-form');
    if(toggle && form){
      toggle.addEventListener('click', function(){
        form.style.display = form.style.display === 'none' ? 'flex' : 'none';
        if(form.style.display !== 'none'){
          toggle.style.display = 'none';
          form.querySelector('input') && form.querySelector('input').focus();
        }
      });
    }
  });
})();
</script>
