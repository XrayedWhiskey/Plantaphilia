<?php
/**
 * Plantaphilia — Bestellbestätigung (Thank You Page)
 * Überschreibt WooCommerce's Standard-Template.
 */
defined( 'ABSPATH' ) || exit;

// wc_get_template() bindet dieses Template innerhalb einer Funktion ein (anders als
// WordPress' eigenes load_template()) — $wp muss daher explizit globalisiert werden,
// sonst ist $wp->query_vars hier immer leer und die Bestellung wird nie gefunden.
global $wp;

// Dieses Template wird per template_redirect (Priority 1) direkt eingebunden und
// überspringt damit WordPress' normale Seitenvorlage — ohne get_header()/get_footer()
// fehlen Design/CSS und Navigation komplett (wie bei single-product.php).
get_header();

// Bestellung laden
$order = false;
if ( $order_id = absint( $wp->query_vars['order-received'] ?? 0 ) ) {
    $order = wc_get_order( $order_id );
    if ( $order && isset( $_GET['key'] ) ) {
        $key = sanitize_text_field( wp_unslash( $_GET['key'] ) );
        if ( ! hash_equals( $order->get_order_key(), $key ) ) {
            $order = false;
        }
    }
}

do_action( 'woocommerce_before_thankyou', $order ? $order->get_id() : 0 );
?>

<div class="pa-thankyou-page">

<?php if ( $order && $order->get_status() !== 'failed' ) : ?>

  <?php /* ── Share-Rabatt-Banner ── */ ?>
  <section class="pa-share-banner">
    <div class="pa-share-banner-inner">
      <div class="pa-share-stamp" aria-hidden="true">
        <span class="pct">10&nbsp;%</span>
        <span class="lbl">Dank-Rabatt</span>
      </div>

      <div class="pa-share-text">
        <span class="share-eyebrow">&middot; Eine kleine Geste &middot;</span>
        <h2>Rabattcode <em>verdienen</em>.</h2>
        <p>Teile deine Bestellung in den sozialen Medien, tagge uns und erhalte einen persönlichen Rabattcode für deinen nächsten Besuch im Gewächshaus.</p>
        <div class="pa-share-tag">
          <span>Tagge</span>
          <b>@plantaphilia</b>
          <span>&middot;</span>
          <b>#plantaphilia</b>
        </div>
        <div class="pa-share-socials">
          <a class="pa-share-soc" href="https://instagram.com/plantaphilia" aria-label="Instagram" rel="noopener noreferrer" target="_blank">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="4"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.6" fill="currentColor"/></svg>
          </a>
          <a class="pa-share-soc" href="https://tiktok.com/@plantaphilia" aria-label="TikTok" rel="noopener noreferrer" target="_blank">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4c0 2.5 2 4.5 5 4.5"/></svg>
          </a>
          <a class="pa-share-soc" href="https://pinterest.com/plantaphilia" aria-label="Pinterest" rel="noopener noreferrer" target="_blank">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M11 7.5c2.5-.5 5 1 5 3.5s-2 4-4 4c-1 0-1.5-.5-1.5-.5L9 21"/></svg>
          </a>
        </div>
      </div>

      <a class="pa-share-cta" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
        Jetzt teilen <span>&#8594;</span>
      </a>
    </div>
  </section>

  <?php /* ── Hauptbereich ── */ ?>
  <main class="cc-page">
    <div class="pa-thankyou-wrap">

      <?php echo pa_steps_html( 'confirm' ); ?>

      <?php
      $first_name    = $order->get_billing_first_name();
      $order_number  = $order->get_order_number();
      $order_date    = $order->get_date_created() ? $order->get_date_created()->date_i18n( 'j. F Y, H:i' ) : '';
      $payment_title = $order->get_payment_method_title();
      $email         = $order->get_billing_email();
      ?>

      <header class="pa-confirm-hero">
        <div class="confirm-eyebrow">
          <span class="rule"></span>
          &middot; Bestätigung &middot; Bestellung erhalten &middot;
        </div>
        <h1>Danke, <em><?php echo esc_html( $first_name ); ?></em>.</h1>
        <p class="pa-confirm-sub">
          Ihre Pflanzen warten bereits im Gewächshaus auf die Reise.
          Wir senden Ihnen eine Bestätigung an
          <b style="font-style:normal;font-size:13px;color:var(--lavender);"><?php echo esc_html( $email ); ?></b>.
        </p>
        <dl class="pa-confirm-meta">
          <div>
            <dt>Bestellnummer</dt>
            <dd><b><?php echo esc_html( $order_number ); ?></b></dd>
          </div>
          <?php if ( $order_date ) : ?>
          <div>
            <dt>Aufgegeben</dt>
            <dd><?php echo esc_html( $order_date ); ?></dd>
          </div>
          <?php endif; ?>
          <div>
            <dt>Zahlart</dt>
            <dd><?php echo esc_html( $payment_title ); ?></dd>
          </div>
        </dl>
      </header>

      <div class="pa-confirm-grid">
        <div class="pa-confirm-main">

          <?php /* ── I. Reise der Pflanzen (Timeline) ── */ ?>
          <section class="pa-confirm-card">
            <h2><em>I.</em> Reise Ihrer Pflanzen <span class="meta-r">Status</span></h2>
            <ul class="pa-confirm-timeline">
              <li class="done">
                <span class="dot"></span>
                <span class="t-lbl">Schritt 1</span>
                <span class="t-when">Bestellung aufgegeben</span>
              </li>
              <li class="active">
                <span class="dot"></span>
                <span class="t-lbl">Schritt 2</span>
                <span class="t-when">In der Gärtnerei</span>
              </li>
              <li>
                <span class="dot"></span>
                <span class="t-lbl">Schritt 3</span>
                <span class="t-when">Verpackt &amp; abgeholt</span>
              </li>
              <li>
                <span class="dot"></span>
                <span class="t-lbl">Schritt 4</span>
                <span class="t-when">Bei Ihnen</span>
              </li>
            </ul>
          </section>

          <?php /* ── II. Bestellte Artikel ── */ ?>
          <section class="pa-confirm-card">
            <h2>
              <em>II.</em> Ihre Sammlung
              <span class="meta-r"><?php echo esc_html( $order->get_item_count() ); ?> Artikel</span>
            </h2>
            <ul class="pa-confirm-items">
              <?php foreach ( $order->get_items() as $item_id => $item ) :
                $product = $item->get_product();
                $img_url = '';
                if ( $product ) {
                    $img_id  = $product->get_image_id();
                    $img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'thumbnail' ) : '';
                    if ( ! $img_url ) {
                        $img_url = wc_placeholder_img_src( 'thumbnail' );
                    }
                }
              ?>
              <li class="pa-confirm-item">
                <div class="pa-confirm-item-img"
                  <?php if ( $img_url ) : ?>
                    style="background-image:url(<?php echo esc_url( $img_url ); ?>)"
                  <?php endif; ?>>
                </div>
                <div>
                  <h3 class="pa-confirm-item-name"><?php echo esc_html( $item->get_name() ); ?></h3>
                  <?php
                  $meta = wc_display_item_meta( $item, [ 'echo' => false ] );
                  if ( $meta ) : ?>
                    <div class="pa-confirm-item-spec"><?php echo wp_kses_post( $meta ); ?></div>
                  <?php endif; ?>
                </div>
                <div class="pa-confirm-item-price">
                  <div class="total"><?php echo $order->get_formatted_line_subtotal( $item ); ?></div>
                  <div class="qty">&times; <?php echo esc_html( $item->get_quantity() ); ?> Stück</div>
                </div>
              </li>
              <?php endforeach; ?>
            </ul>
          </section>

          <?php /* ── III. Adressen ── */ ?>
          <section class="pa-confirm-card">
            <h2><em>III.</em> Versand &amp; Rechnung</h2>
            <div class="pa-confirm-addr-grid">
              <div>
                <div class="pa-confirm-addr-eyebrow">&middot; Lieferadresse &middot;</div>
                <address class="pa-confirm-addr">
                  <?php
                  $shipping = $order->get_formatted_shipping_address();
                  echo $shipping
                      ? wp_kses_post( nl2br( $shipping ) )
                      : esc_html__( 'Keine Lieferadresse angegeben.', 'woocommerce' );
                  ?>
                </address>
              </div>
              <div>
                <div class="pa-confirm-addr-eyebrow">&middot; Rechnungsadresse &middot;</div>
                <address class="pa-confirm-addr">
                  <?php
                  $billing  = $order->get_formatted_billing_address();
                  if ( ! $billing || $billing === $shipping ) {
                      echo '<i>Identisch mit der Lieferadresse.</i>';
                  } else {
                      echo wp_kses_post( nl2br( $billing ) );
                  }
                  ?>
                </address>
                <div style="margin-top:16px;">
                  <div class="pa-confirm-addr-eyebrow">&middot; Zahlart &middot;</div>
                  <div class="pa-confirm-addr" style="font-style:normal;">
                    <?php echo esc_html( $payment_title ); ?>
                  </div>
                </div>
              </div>
            </div>
          </section>

        </div>

        <?php /* ── Zusammenfassung Sidebar ── */ ?>
        <aside class="pa-confirm-aside">
          <div class="pa-confirm-card" style="margin:0;padding:36px 32px;position:relative;">
            <div class="pa-confirm-aside-stamp">&middot; Quittung &middot;</div>
            <div class="pa-confirm-aside-eyebrow">&middot; Gesamtbetrag &middot;</div>
            <h2>Eine <em>Quittung</em>.</h2>

            <?php foreach ( $order->get_items() as $item ) : ?>
            <div class="pa-confirm-row">
              <span class="lab">
                <?php echo esc_html( $item->get_name() ); ?>
                &times; <?php echo esc_html( $item->get_quantity() ); ?>
              </span>
              <span class="val"><?php echo $order->get_formatted_line_subtotal( $item ); ?></span>
            </div>
            <?php endforeach; ?>

            <?php foreach ( $order->get_fees() as $fee ) : ?>
            <div class="pa-confirm-row">
              <span class="lab"><?php echo esc_html( $fee->get_name() ); ?></span>
              <span class="val"><?php echo wc_price( $fee->get_total() ); ?></span>
            </div>
            <?php endforeach; ?>

            <div class="pa-confirm-row <?php echo (float) $order->get_shipping_total() === 0.0 ? 'free' : ''; ?>">
              <span class="lab">Versand</span>
              <span class="val">
                <?php echo (float) $order->get_shipping_total() === 0.0
                    ? 'Kostenlos'
                    : wc_price( $order->get_shipping_total() ); ?>
              </span>
            </div>

            <div class="pa-confirm-divider"></div>

            <div class="pa-confirm-total">
              <span class="lab">Gezahlt</span>
              <span class="val"><?php echo $order->get_formatted_order_total(); ?></span>
            </div>
            <?php if ( $order->get_total_tax() > 0 ) : ?>
            <div class="pa-confirm-tax">
              inkl. <?php echo wc_price( $order->get_total_tax() ); ?> MwSt.
            </div>
            <?php endif; ?>

            <div class="pa-confirm-actions">
              <a class="pa-confirm-cta"
                href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
                Bestellung ansehen <span>&#8594;</span>
              </a>
              <a class="pa-confirm-cta secondary"
                href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
                Weiter stöbern <span>&#8594;</span>
              </a>
            </div>
          </div>
        </aside>
      </div>

      <?php
      // Required WooCommerce hooks for payment gateway confirmations and post-order processing
      do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
      do_action( 'woocommerce_thankyou', $order->get_id() );
      ?>

      <section class="pa-confirm-footnote">
        <div class="pa-confirm-foot">
          <h4>&middot; Fragen zur Bestellung &middot;</h4>
          <p>
            Schreiben Sie uns unter
            <a href="mailto:hallo@plantaphilia.de">hallo@plantaphilia.de</a>
            – wir antworten meist binnen eines Tages.
          </p>
        </div>
        <div class="pa-confirm-foot">
          <h4>&middot; Pflegehinweis &middot;</h4>
          <p>Lassen Sie Ihre Pflanzen nach Erhalt einen Tag akklimatisieren, bevor Sie sie ans Fenster stellen.</p>
        </div>
        <div class="pa-confirm-foot">
          <h4>&middot; 14 Tage Pflanzengarantie &middot;</h4>
          <p>Bei Schäden tauschen wir aus oder erstatten – auf unsere Kosten.</p>
        </div>
      </section>

    </div>
  </main>

<?php else : ?>

  <?php /* ── Fehlgeschlagene oder unbekannte Bestellung ── */ ?>
  <main class="cc-page">
    <div class="pa-thankyou-wrap">
      <?php if ( $order && $order->get_status() === 'failed' ) : ?>
        <div class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
          <p><?php esc_html_e( 'Leider konnte Ihre Bestellung nicht verarbeitet werden, da die Zahlung abgelehnt wurde. Bitte versuchen Sie es erneut.', 'woocommerce' ); ?></p>
          <p>
            <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="pa-confirm-cta">
              Zahlung wiederholen <span>&#8594;</span>
            </a>
            <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="pa-confirm-cta secondary" style="margin-top:10px;">
              Zurück zum Shop <span>&#8594;</span>
            </a>
          </p>
        </div>
      <?php else : ?>
        <?php do_action( 'woocommerce_thankyou', 0 ); ?>
      <?php endif; ?>
    </div>
  </main>

<?php endif; ?>

</div>

<?php get_footer(); ?>
