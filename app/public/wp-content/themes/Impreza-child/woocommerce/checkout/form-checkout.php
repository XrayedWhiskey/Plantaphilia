<?php
/**
 * Plantaphilia — Kasse / Checkout (überschreibt WooCommerce checkout/form-checkout.php)
 */
defined( 'ABSPATH' ) || exit;

$checkout = WC()->checkout();

// Notices
wc_print_notices();

if ( ! $checkout->is_registration_required() && is_user_logged_in() ) {
  $current_user = wp_get_current_user();
}
?>
<div class="cc-page">
<div class="cc-wrap">

  <!-- Steps -->
  <nav class="cc-steps" aria-label="Bestellschritte">
    <div class="cc-step done">
      <span class="cc-step-num">&#10003;</span>
      <span class="cc-step-label">Warenkorb</span>
    </div>
    <span class="cc-step-rule cc-step-done-rule"></span>
    <div class="cc-step active">
      <span class="cc-step-num">2</span>
      <span class="cc-step-label">Versand &amp; Zahlung</span>
    </div>
    <span class="cc-step-rule"></span>
    <div class="cc-step">
      <span class="cc-step-num">3</span>
      <span class="cc-step-label">Bestätigung</span>
    </div>
  </nav>

  <!-- Login bar (guest checkout) -->
  <?php if ( ! is_user_logged_in() && 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) : ?>
  <div class="cc-loginbar">
    <div class="cc-loginbar-text">
      <b>Schon Sammler:in bei uns?</b>
      Melden Sie sich an, damit Ihre Adresse und Bestellhistorie übernommen werden.
    </div>
    <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">Anmelden &rarr;</a>
  </div>
  <?php endif; ?>

  <form name="checkout" method="post" class="checkout woocommerce-checkout"
        action="<?php echo esc_url( wc_get_checkout_url() ); ?>"
        enctype="multipart/form-data">

    <div class="cc-grid">

      <!-- ── Linke Spalte: Formular ── -->
      <div class="cc-main" id="customer_details">

        <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

        <!-- I. Kontakt & Rechnung -->
        <div class="cc-section pa-billing-section">
          <div class="cc-sect-head">
            <h2><em>I.</em> Kontakt &amp; Rechnung</h2>
          </div>
          <?php do_action( 'woocommerce_checkout_billing' ); ?>
        </div>

        <!-- II. Lieferadresse -->
        <?php if ( true === WC()->cart->needs_shipping_address() ) : ?>
        <div class="cc-section pa-shipping-section">
          <div class="cc-sect-head">
            <h2><em>II.</em> Lieferadresse</h2>
          </div>
          <?php do_action( 'woocommerce_checkout_shipping' ); ?>
        </div>
        <?php endif; ?>

        <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

        <!-- III. Versandart & Zahlung + Rabattcode -->
        <div class="cc-section" id="order_review_heading">
          <div class="cc-sect-head">
            <h2><em>III.</em> Versand &amp; Zahlung</h2>
          </div>
          <div class="pa-coupon-row" style="margin-top:18px;">
            <label class="pa-coupon-label" for="pa_coupon_code">Rabattcode</label>
            <div class="pa-coupon-fields">
              <input type="text" id="pa_coupon_code" class="pa-coupon-input input-text" placeholder="Code eingeben…">
              <button type="button" class="pa-coupon-btn" id="pa_apply_coupon">Einlösen</button>
            </div>
          </div>
          <div class="pa-coupon-msg" id="pa_coupon_msg" style="display:none; margin-top:10px;"></div>
        </div>

        <div id="order_review" class="cc-section">
          <?php do_action( 'woocommerce_checkout_order_review' ); ?>
        </div>

      </div><!-- .cc-main -->

      <!-- ── Rechte Spalte: Zusammenfassung ── -->
      <aside class="cc-aside">
        <div class="cc-summary">
          <div class="cc-summary-stamp">Tafel &middot; Bestellung</div>
          <div class="cc-summary-eyebrow">&middot; Ihre Sammlung &middot;</div>
          <h2 class="cc-summary-h">Über<em>sicht</em></h2>

          <!-- WooCommerce order review table (tfoot hidden via CSS — we use custom totals below) -->
          <div class="cc-review-products">
            <?php woocommerce_order_review(); ?>
          </div>

          <?php
          $cart      = WC()->cart;
          $subtotal  = (float) $cart->get_subtotal() + (float) $cart->get_subtotal_tax();
          $ship_net  = (float) $cart->get_shipping_total();
          $ship_tot  = $ship_net + (float) $cart->get_shipping_tax();
          $is_free   = $ship_net === 0.0;
          ?>

          <!-- Subtotal -->
          <div class="cc-summary-row">
            <span class="lab">Zwischensumme</span>
            <span class="val"><?php echo wc_price( $subtotal ); ?></span>
          </div>

          <!-- Fees -->
          <?php foreach ( $cart->get_fees() as $fee ) : ?>
          <div class="cc-summary-row">
            <span class="lab"><?php echo esc_html( $fee->name ); ?></span>
            <span class="val"><?php echo wc_price( $fee->total ); ?></span>
          </div>
          <?php endforeach; ?>

          <!-- Shipping -->
          <?php if ( $cart->needs_shipping() && $cart->show_shipping() ) : ?>
          <div class="cc-summary-row <?php echo $is_free ? 'free' : ''; ?>">
            <span class="lab">Versand</span>
            <span class="val"><?php echo $is_free ? 'Frei' : wc_price( $ship_tot ); ?></span>
          </div>
          <?php endif; ?>

          <!-- Coupons -->
          <?php foreach ( $cart->get_coupons() as $code => $coupon ) : ?>
          <div class="cc-summary-row discount">
            <span class="lab">Gutschein &middot; <?php echo esc_html( strtoupper( $code ) ); ?></span>
            <span class="val">&minus; <?php echo wc_price( $cart->get_coupon_discount_amount( $code, $cart->display_prices_including_tax() ) ); ?></span>
          </div>
          <?php endforeach; ?>

          <div class="cc-summary-divider"></div>

          <?php
          $grand_total = $subtotal + $ship_tot;
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
          <div class="cc-summary-tax">davon <?php echo wc_price( $cart->get_taxes_total() ); ?> MwSt. (19&nbsp;%)</div>
          <?php endif; ?>

          <!-- Trust strip -->
          <div class="cc-trust" style="margin-top:28px;">
            <div class="cc-trust-item">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--amethyst)" stroke-width="1.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
              <span>
                <b style="font-style:normal;font-family:var(--sans-body);font-size:10px;letter-spacing:0.22em;text-transform:uppercase;color:var(--creme);display:block;margin-bottom:2px;">Pflanzengarantie &middot; 14 Tage</b>
                Sollte etwas nicht in voller Pracht ankommen, schicken wir Ersatz.
              </span>
            </div>
            <div class="cc-trust-item">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--amethyst)" stroke-width="1.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
              <span>
                <b style="font-style:normal;font-family:var(--sans-body);font-size:10px;letter-spacing:0.22em;text-transform:uppercase;color:var(--creme);display:block;margin-bottom:2px;">SSL &middot; DSGVO</b>
                Verschlüsselte Übertragung, sicherer Einkauf.
              </span>
            </div>
          </div>

        </div><!-- .cc-summary -->
      </aside>

    </div><!-- .cc-grid -->

  </form>

<script>
(function() {
  var btn = document.getElementById('pa_apply_coupon');
  var inp = document.getElementById('pa_coupon_code');
  var msg = document.getElementById('pa_coupon_msg');
  if (!btn || !inp) return;
  btn.addEventListener('click', function() {
    var code = inp.value.trim();
    if (!code) return;
    btn.disabled = true;
    btn.textContent = '…';
    var data = new FormData();
    data.append('action', 'woocommerce_apply_coupon');
    data.append('security', wc_checkout_params.apply_coupon_nonce || '');
    data.append('coupon_code', code);
    fetch(wc_checkout_params.ajax_url, { method: 'POST', body: data })
      .then(function(r) { return r.text(); })
      .then(function(html) {
        msg.innerHTML = html;
        msg.style.display = 'block';
        btn.disabled = false; btn.textContent = 'Einlösen';
        jQuery(document.body).trigger('update_checkout');
      })
      .catch(function() { btn.disabled = false; btn.textContent = 'Einlösen'; });
  });
}());
</script>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

</div><!-- .cc-wrap -->
</div><!-- .cc-page -->
