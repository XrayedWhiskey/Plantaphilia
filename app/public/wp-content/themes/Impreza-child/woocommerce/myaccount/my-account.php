<?php
/**
 * Plantaphilia – Custom My Account Page
 * Consolidates all account info with sticky navigation
 */
defined('ABSPATH') or die();

$current_user = wp_get_current_user();
$customer_id = get_current_user_id();

// Get orders
$orders = wc_get_orders([
    'customer_id' => $customer_id,
    'limit' => -1,
    'orderby' => 'date',
    'order' => 'DESC',
]);

// Get addresses
$billing_address = wc_get_account_formatted_address('billing');
$shipping_address = wc_get_account_formatted_address('shipping');

?>

<main class="pa-account-page">
  <div class="pa-account-container">
    
    <!-- Sticky Navigation -->
    <aside class="pa-account-nav" id="pa-account-nav">
      <div class="pa-account-nav-header">
        <h2><?php echo esc_html($current_user->display_name); ?></h2>
        <p class="pa-account-email"><?php echo esc_html($current_user->user_email); ?></p>
      </div>
      
      <nav class="pa-account-nav-links">
        <a href="#orders" class="pa-nav-link active">Bestellungen</a>
        <a href="#addresses" class="pa-nav-link">Adressen</a>
        <a href="#details" class="pa-nav-link">Konto-Details</a>
        <a href="#newsletter" class="pa-nav-link">Newsletter</a>
        <a href="#downloads" class="pa-nav-link">Downloads</a>
      </nav>
    </aside>

    <!-- Account Content -->
    <div class="pa-account-content">
      
      <!-- Orders Section -->
      <section id="orders" class="pa-account-section">
        <h2 class="pa-section-title">Bestellungen</h2>
        
        <?php if (!empty($orders)) : ?>
          <div class="pa-orders-list">
            <?php 
            $display_count = 0;
            foreach ($orders as $order) : 
              $display_count++;
              if ($display_count > 3) break;
            ?>
              <div class="pa-order-item">
                <div class="pa-order-header">
                  <span class="pa-order-number">#<?php echo $order->get_order_number(); ?></span>
                  <span class="pa-order-date"><?php echo $order->get_date_created()->date_i18n('d.m.Y'); ?></span>
                </div>
                <div class="pa-order-status"><?php echo wc_get_order_status_name($order->get_status()); ?></div>
                <div class="pa-order-total"><?php echo $order->get_formatted_order_total(); ?></div>
                <a href="<?php echo $order->get_view_order_url(); ?>" class="pa-btn-out">Details</a>
              </div>
            <?php endforeach; ?>
            
            <?php if (count($orders) > 3) : ?>
              <button class="pa-load-more" id="pa-load-more-orders">Mehr anzeigen</button>
              <div class="pa-orders-hidden" id="pa-orders-hidden">
                <?php 
                $hidden_count = 0;
                foreach ($orders as $order) : 
                  $hidden_count++;
                  if ($hidden_count <= 3) continue;
                ?>
                  <div class="pa-order-item">
                    <div class="pa-order-header">
                      <span class="pa-order-number">#<?php echo $order->get_order_number(); ?></span>
                      <span class="pa-order-date"><?php echo $order->get_date_created()->date_i18n('d.m.Y'); ?></span>
                    </div>
                    <div class="pa-order-status"><?php echo wc_get_order_status_name($order->get_status()); ?></div>
                    <div class="pa-order-total"><?php echo $order->get_formatted_order_total(); ?></div>
                    <a href="<?php echo $order->get_view_order_url(); ?>" class="pa-btn-out">Details</a>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php else : ?>
          <p class="pa-empty-state">Sie haben noch keine Bestellungen.</p>
        <?php endif; ?>
      </section>

      <!-- Addresses Section -->
      <section id="addresses" class="pa-account-section">
        <h2 class="pa-section-title">Adressen</h2>
        
        <div class="pa-addresses-grid">
          <div class="pa-address-card">
            <h3>Rechnungsadresse</h3>
            <address><?php echo $billing_address ?: 'Nicht angegeben'; ?></address>
            <a href="<?php echo wc_get_endpoint_url('edit-address', 'billing'); ?>" class="pa-btn-out">Bearbeiten</a>
          </div>
          
          <div class="pa-address-card">
            <h3>Lieferadresse</h3>
            <address><?php echo $shipping_address ?: 'Nicht angegeben'; ?></address>
            <a href="<?php echo wc_get_endpoint_url('edit-address', 'shipping'); ?>" class="pa-btn-out">Bearbeiten</a>
          </div>
        </div>
      </section>

      <!-- Account Details Section -->
      <section id="details" class="pa-account-section">
        <h2 class="pa-section-title">Konto-Details</h2>
        
        <div class="pa-details-card">
          <div class="pa-detail-row">
            <span class="pa-detail-label">Benutzername:</span>
            <span class="pa-detail-value"><?php echo esc_html($current_user->user_login); ?></span>
          </div>
          <div class="pa-detail-row">
            <span class="pa-detail-label">E-Mail:</span>
            <span class="pa-detail-value"><?php echo esc_html($current_user->user_email); ?></span>
          </div>
          <div class="pa-detail-row">
            <span class="pa-detail-label">Anzeigename:</span>
            <span class="pa-detail-value"><?php echo esc_html($current_user->display_name); ?></span>
          </div>
          
          <a href="<?php echo wc_get_endpoint_url('edit-account'); ?>" class="pa-btn-filled">Konto bearbeiten</a>
        </div>
      </section>

      <!-- Newsletter Section -->
      <?php
      $nl_pref   = get_user_meta($customer_id, '_pa_newsletter_pref', true);
      $nl_active = ($nl_pref === 'yes');
      $nl_nonce  = wp_create_nonce('pa_newsletter_pref_nonce');
      ?>
      <section id="newsletter" class="pa-account-section">
        <h2 class="pa-section-title">Newsletter</h2>
        <div class="pa-details-card" style="display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap;">
          <div>
            <div style="color:var(--creme);font-size:14px;font-weight:600;margin-bottom:4px;">Newsletter-Abonnement</div>
            <div style="color:var(--creme-dim);font-size:13px;">Erhalten Sie Neuigkeiten zu Pflanzen, Angeboten und saisonalen Kollektionen.</div>
          </div>
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer;flex-shrink:0;">
            <div style="position:relative;display:inline-block;width:46px;height:26px;">
              <input type="checkbox" id="pa-nl-toggle" <?php checked($nl_active); ?> style="opacity:0;width:0;height:0;position:absolute;">
              <span id="pa-nl-track" style="position:absolute;inset:0;background:<?php echo $nl_active ? 'var(--plum-hot)' : 'var(--bg-raised)'; ?>;border:1px solid var(--border-thin);border-radius:var(--r-pill);transition:background .2s;cursor:pointer;"></span>
              <span id="pa-nl-thumb" style="position:absolute;top:3px;left:<?php echo $nl_active ? '23px' : '3px'; ?>;width:18px;height:18px;background:var(--creme);border-radius:50%;transition:left .2s;pointer-events:none;"></span>
            </div>
            <span style="color:var(--creme-dim);font-size:13px;"><?php echo $nl_active ? 'Abonniert' : 'Abgemeldet'; ?></span>
          </label>
        </div>
        <p id="pa-nl-msg" style="margin:12px 0 0;font-size:13px;color:var(--plum-hot);display:none;"></p>
      </section>

      <!-- Downloads Section -->
      <section id="downloads" class="pa-account-section">
        <h2 class="pa-section-title">Downloads</h2>
        
        <?php 
        $downloads = WC()->customer->get_downloadable_products();
        if (!empty($downloads)) : 
        ?>
          <div class="pa-downloads-list">
            <?php foreach ($downloads as $download) : ?>
              <div class="pa-download-item">
                <span class="pa-download-name"><?php echo esc_html($download['download_name']); ?></span>
                <a href="<?php echo esc_url($download['download_url']); ?>" class="pa-btn-out">Herunterladen</a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else : ?>
          <p class="pa-empty-state">Keine Downloads verfügbar.</p>
        <?php endif; ?>
      </section>

    </div>
  </div>
</main>

<script>
(function() {
  // Sticky navigation
  const nav = document.getElementById('pa-account-nav');
  const navLinks = document.querySelectorAll('.pa-nav-link');
  const sections = document.querySelectorAll('.pa-account-section');
  
  // Smooth scroll to section
  navLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const targetId = this.getAttribute('href').substring(1);
      const targetSection = document.getElementById(targetId);
      
      if (targetSection) {
        const offset = 80;
        const targetPosition = targetSection.offsetTop - offset;
        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth'
        });
      }
    });
  });
  
  // Update active link on scroll
  window.addEventListener('scroll', function() {
    let current = '';
    sections.forEach(section => {
      const sectionTop = section.offsetTop - 100;
      if (window.scrollY >= sectionTop) {
        current = section.getAttribute('id');
      }
    });
    
    navLinks.forEach(link => {
      link.classList.remove('active');
      if (link.getAttribute('href') === '#' + current) {
        link.classList.add('active');
      }
    });
  });
  
  // Load more orders
  const loadMoreBtn = document.getElementById('pa-load-more-orders');
  const hiddenOrders = document.getElementById('pa-orders-hidden');

  if (loadMoreBtn && hiddenOrders) {
    loadMoreBtn.addEventListener('click', function() {
      hiddenOrders.style.display = 'block';
      loadMoreBtn.style.display = 'none';
    });
  }

  // Newsletter toggle
  var nlToggle = document.getElementById('pa-nl-toggle');
  var nlTrack  = document.getElementById('pa-nl-track');
  var nlThumb  = document.getElementById('pa-nl-thumb');
  var nlMsg    = document.getElementById('pa-nl-msg');
  if (nlToggle) {
    var nlNonce = <?php echo json_encode($nl_nonce); ?>;
    var nlAjax  = <?php echo json_encode(admin_url('admin-ajax.php')); ?>;
    nlTrack.addEventListener('click', function() {
      nlToggle.checked = !nlToggle.checked;
      var checked = nlToggle.checked;
      nlTrack.style.background = checked ? 'var(--plum-hot)' : 'var(--bg-raised)';
      nlThumb.style.left = checked ? '23px' : '3px';
      nlTrack.nextElementSibling && (nlTrack.nextElementSibling.textContent = checked ? 'Abonniert' : 'Abgemeldet');
      var fd = new FormData();
      fd.append('action', 'pa_save_newsletter_pref');
      fd.append('nonce', nlNonce);
      fd.append('choice', checked ? 'yes' : 'no');
      fetch(nlAjax, {method:'POST', body:fd})
        .then(function(r){return r.json();})
        .then(function(d){
          if (nlMsg) {
            nlMsg.style.display = 'block';
            nlMsg.textContent = d.success ? (checked ? 'Newsletter abonniert.' : 'Newsletter abgemeldet.') : 'Fehler beim Speichern.';
            setTimeout(function(){ nlMsg.style.display = 'none'; }, 3000);
          }
        });
    });
  }
})();
</script>

