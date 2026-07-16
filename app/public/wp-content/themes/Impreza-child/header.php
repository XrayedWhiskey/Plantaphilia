<?php
/**
 * Plantaphilia – global header (overrides Impreza header.php)
 * Dark Botanical Gothic design system.
 */
defined('ABSPATH') or die();

$cart_count  = ( function_exists('WC') && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
$logo_url    = content_url('uploads/2022/01/Logo-Plantaphilia-1.svg');
$shop_url    = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop'))      : home_url('/shop/');
$account_url = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('myaccount')) : home_url('/my-account/');
$logout_url  = wp_logout_url(home_url());
$is_logged_in = is_user_logged_in();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class('pa-storefront'); ?>>
<?php wp_body_open(); ?>

<header class="pa-header">

  <div class="pa-header-main">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="pa-logo-link">
      <img class="pa-logo" src="<?php echo esc_url($logo_url); ?>" alt="Plantaphilia">
    </a>

    <div class="pa-header-actions">
      <a class="pa-iconbtn" href="<?php echo esc_url($shop_url); ?>" aria-label="Shop">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
        </svg>
        <span class="pa-iconbtn-text">Shop</span>
      </a>

      <?php if ($is_logged_in) : ?>
        <a class="pa-iconbtn" href="<?php echo esc_url($account_url); ?>" aria-label="Konto ansehen">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.5 3.5-8 8-8s8 3.5 8 8"/>
          </svg>
          <span class="pa-iconbtn-text">Konto</span>
        </a>
        <a class="pa-iconbtn" href="<?php echo esc_url($logout_url); ?>" aria-label="Abmelden">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
          <span class="pa-iconbtn-text">Abmelden</span>
        </a>
      <?php else : ?>
        <a class="pa-iconbtn" href="<?php echo esc_url($account_url); ?>" aria-label="Konto">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.5 3.5-8 8-8s8 3.5 8 8"/>
          </svg>
          <span class="pa-iconbtn-text">Konto</span>
        </a>
      <?php endif; ?>

      <button class="pa-iconbtn" id="pa-cart-btn" aria-label="Warenkorb öffnen">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
          <path d="M3 4h3l2.7 11.5a2 2 0 0 0 2 1.5h7.6a2 2 0 0 0 2-1.5L22 8H6"/>
        </svg>
        <span class="pa-iconbtn-text">Warenkorb</span>
        <?php if ($cart_count > 0) : ?>
          <em class="pa-cart-badge"><?php echo intval($cart_count); ?></em>
        <?php endif; ?>
      </button>

      <div class="pa-lang-dropdown">
        <button class="pa-iconbtn pa-lang-btn" id="pa-lang-btn" aria-label="Sprache wählen">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
          </svg>
          <span class="pa-iconbtn-text">DE</span>
        </button>
        <div class="pa-lang-menu" id="pa-lang-menu">
          <a href="#" class="pa-lang-item active">DE</a>
          <a href="#" class="pa-lang-item">EN</a>
        </div>
      </div>
    </div>
  </div>

  <div class="pa-divider"></div>
</header>
