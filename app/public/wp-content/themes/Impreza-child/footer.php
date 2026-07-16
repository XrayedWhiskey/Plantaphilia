<?php
/**
 * Plantaphilia – global footer (overrides Impreza footer.php)
 * Dark Botanical Gothic design system.
 */
defined('ABSPATH') or die();

$logo_url    = content_url('uploads/2022/01/Logo-Plantaphilia-1.svg');
$footer_bg   = content_url('uploads/2022/01/Footer-Banner-Plantaphilia.jpg');
$shop_url    = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop'))      : home_url('/shop/');
$account_url = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('myaccount')) : home_url('/my-account/');
$checkout_url= function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/');
$cart_url    = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/');

// WooCommerce product categories for nav drawer
$product_cats = get_terms([
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
]);
$product_cats = is_array($product_cats)
    ? array_filter($product_cats, fn($c) => $c->slug !== 'uncategorized')
    : [];

// Cart contents for drawer
$wc_cart = ( function_exists('WC') && WC()->cart ) ? WC()->cart : null;
?>

<!-- ══ FOOTER ════════════════════════════════════════════════════ -->
<footer class="pa-footer">

  <div class="pa-footer-top" style="background-image: url('<?php echo esc_url($footer_bg); ?>');">
    <div class="pa-footer-veil">
    </div>
  </div>

  <div class="pa-footer-main">

    <div class="pa-footer-col">
      <img class="pa-footer-logo" src="<?php echo esc_url($logo_url); ?>" alt="Plantaphilia">
      <div class="pa-footer-h" style="margin-top:16px;">Mehr Information</div>
      <a href="<?php echo esc_url(home_url('/impressum/')); ?>">Impressum</a>
      <a href="<?php echo esc_url(home_url('/datenschutzerklaerung/')); ?>">Datenschutz</a>
      <a href="<?php echo esc_url(home_url('/agb/')); ?>">AGB</a>
      <a href="<?php echo esc_url(home_url('/versandarten/')); ?>">Versand- und Zahlungsbedingungen</a>
      <a href="<?php echo esc_url(home_url('/widerrufsbelehrung/')); ?>">Widerrufsrecht</a>
      <?php if (class_exists('Borlabs_Cookie')) : ?>
        <a href="#" onclick="BorlabsCookie.openCookiePreference(); return false;">Cookie-Einstellungen</a>
      <?php endif; ?>
    </div>

    <div class="pa-footer-col">
      <div class="pa-footer-h">Kontakt</div>
      <a href="<?php echo esc_url(home_url('/kontakt/')); ?>">Kontaktformular</a>
    </div>

    <?php if (current_user_can('manage_options')) : ?>
    <div class="pa-footer-col pa-footer-col--admin">
      <div class="pa-footer-h">Admin</div>
      <a href="<?php echo esc_url(home_url('/produkt-liste/')); ?>">Produktliste</a>
      <a href="<?php echo esc_url(home_url('/bestellungen/')); ?>">Bestellungen</a>
      <a href="<?php echo esc_url(home_url('/neues-produkt/')); ?>">Neues Produkt</a>
      <a href="<?php echo esc_url(home_url('/newsletter/')); ?>">Newsletter</a>
    </div>
    <?php endif; ?>

  </div>

  <div class="pa-footer-bottom">
    <span>© <?php echo date('Y'); ?> Gärtnerei Plantaphilia</span>
  </div>

</footer>

<!-- ══ NAV DRAWER ════════════════════════════════════════════════ -->
<div class="pa-nav" id="pa-nav" role="dialog" aria-modal="true" aria-label="Navigation">
  <div class="pa-nav-inner">

    <button class="pa-nav-close" id="pa-nav-close" aria-label="Menü schließen">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 6l12 12"/><path d="M18 6L6 18"/>
      </svg>
    </button>

    <div class="pa-nav-content">
      <div class="pa-nav-eyebrow">Navigation</div>

      <a class="pa-nav-item" href="<?php echo esc_url(home_url('/')); ?>">Home</a>

      <div class="pa-nav-item pa-nav-group">
        <div class="pa-nav-row" id="pa-subnav-toggle" role="button" tabindex="0" aria-expanded="true">
          <span>Shop · Pelargonium</span>
          <span class="pa-nav-chev ex" id="pa-nav-chev">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 6l6 6-6 6"/>
            </svg>
          </span>
        </div>
        <ul class="pa-subnav" id="pa-subnav">
          <?php foreach ($product_cats as $cat) : ?>
            <li><a href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <a class="pa-nav-item" href="<?php echo esc_url(get_permalink(get_page_by_path('kontakt')) ?: home_url('/kontakt/')); ?>">Kontakt</a>

      <div class="pa-nav-foot">
        <span class="pa-nav-tag">Kuratiert · versandfertig aus Deutschland</span>
      </div>
    </div>

  </div>
</div>

<!-- ══ CART DRAWER ═══════════════════════════════════════════════ -->
<div class="pa-cart" id="pa-cart-drawer" role="dialog" aria-modal="true" aria-label="Warenkorb">

  <div class="pa-cart-head">
    <span class="pa-cart-title">Warenkorb</span>
    <button class="pa-cart-close" id="pa-cart-close" aria-label="Warenkorb schließen">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 6l12 12"/><path d="M18 6L6 18"/>
      </svg>
    </button>
  </div>

  <div class="pa-cart-body">
    <?php if ($wc_cart && ! $wc_cart->is_empty()) :
      foreach ($wc_cart->get_cart() as $key => $item) :
        $item_product = $item['data'];
        $item_img     = get_the_post_thumbnail_url($item['product_id'], 'thumbnail') ?: '';
        $remove_url   = wc_get_cart_remove_url($key);
    ?>
      <div class="pa-cart-row">
        <div class="pa-cart-thumb" style="background-image: url('<?php echo esc_url($item_img); ?>')"></div>
        <div>
          <div class="pa-cart-name"><?php echo esc_html($item_product->get_name()); ?></div>
          <div class="pa-cart-pr">€&nbsp;<?php echo number_format((float)$item_product->get_price(), 2, ',', '.'); ?></div>
        </div>
        <a class="pa-cart-x" href="<?php echo esc_url($remove_url); ?>" aria-label="Entfernen">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 6l12 12"/><path d="M18 6L6 18"/>
          </svg>
        </a>
      </div>
    <?php endforeach; else : ?>
      <p class="pa-cart-empty">Noch leer. Wählen Sie Ihr erstes Sammlerstück.</p>
    <?php endif; ?>
  </div>

  <?php if ($wc_cart && ! $wc_cart->is_empty()) : ?>
  <div class="pa-cart-foot">
    <div class="pa-cart-total">
      <span>Zwischensumme</span>
      <span class="pa-cart-sum"><?php echo $wc_cart->get_cart_subtotal(); ?></span>
    </div>
    <a href="<?php echo esc_url($checkout_url); ?>" class="pa-btn-filled wide">Zur Kasse →</a>
  </div>
  <?php endif; ?>

</div>

<!-- ══ INTERACTIONS ══════════════════════════════════════════════ -->
<script>
(function () {
  function g(id) { return document.getElementById(id); }

  // Language dropdown
  var langBtn = g('pa-lang-btn');
  var langMenu = g('pa-lang-menu');
  if (langBtn && langMenu) {
    langBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      langMenu.classList.toggle('open');
    });
    document.addEventListener('click', function () {
      langMenu.classList.remove('open');
    });
  }

  // Cart drawer
  var cart = g('pa-cart-drawer');
  if (cart) {
    g('pa-cart-btn')   && g('pa-cart-btn').addEventListener('click',   function () { cart.classList.add('open'); });
    g('pa-cart-close') && g('pa-cart-close').addEventListener('click', function () { cart.classList.remove('open'); });
  }
}());
</script>

<?php wp_footer(); ?>
</body>
</html>
