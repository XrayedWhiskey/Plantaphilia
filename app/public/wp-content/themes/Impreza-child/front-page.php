<?php
/**
 * Plantaphilia Storefront – front page.
 * Implements plantaphilia-design-system/project/ui_kits/web/example.html
 */
defined('ABSPATH') or die();

// ── Helpers ──────────────────────────────────────────────────────
function pa_get_badge($product) {
    if (!$product->is_in_stock()) return ['label' => 'Ausverkauft', 'out' => true];
    $tags = get_the_terms($product->get_id(), 'product_tag');
    if ($tags && !is_wp_error($tags)) {
        foreach ($tags as $tag) {
            $s = strtolower($tag->slug);
            if (in_array($s, ['raritaet', 'raritat', 'raritaeten', 'rarität']))
                return ['label' => 'Rarität', 'out' => false];
            if ($s === 'neu') return ['label' => 'Neu', 'out' => false];
        }
    }
    return null;
}

function pa_get_latin($product) {
    $latin = get_post_meta($product->get_id(), '_latin_name', true);
    if ($latin) return $latin;
    foreach ($product->get_attributes() as $attr) {
        $n = strtolower($attr->get_name());
        if (strpos($n, 'latin') !== false || strpos($n, 'botan') !== false)
            return implode(', ', $attr->get_options());
    }
    return '';
}

// ── Data ─────────────────────────────────────────────────────────

// Carousels for 13.2
$cr_new = wc_get_products(['limit' => 10, 'status' => 'publish', 'orderby' => 'date',       'order' => 'DESC']);
$cr_pop = wc_get_products(['limit' => 10, 'status' => 'publish', 'orderby' => 'popularity', 'order' => 'DESC']);
$cr_sale_ids = array_slice(wc_get_product_ids_on_sale(), 0, 10);
$cr_sale = array_values(array_filter(array_map(function($id) {
    return wc_get_product($id);
}, $cr_sale_ids)));

$product_cats = get_terms([
    'taxonomy' => 'product_cat', 'hide_empty' => true,
    'orderby' => 'name', 'order' => 'ASC',
]);
$product_cats = is_array($product_cats)
    ? array_filter($product_cats, fn($c) => $c->slug !== 'uncategorized')
    : [];

$active_cat = isset($_GET['cat']) ? sanitize_text_field($_GET['cat']) : '';

$query_args = [
    'post_type' => 'product', 'posts_per_page' => 12,
    'post_status' => 'publish', 'orderby' => 'date', 'order' => 'DESC',
];
if ($active_cat) {
    $query_args['tax_query'] = [[
        'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $active_cat,
    ]];
}
$products_query = new WP_Query($query_args);

$hero_url = content_url('uploads/2022/01/Banner-Plantaphilia.jpg');
$cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/');

get_header();
?>

<!-- ══ HERO ══════════════════════════════════════════════════════ -->
<section class="pa-hero" style="background-image: url('<?php echo esc_url($hero_url); ?>');">
  <div class="pa-hero-inner">
    <h1>Selten, samtig,<br><em>aufrichtig botanisch.</em></h1>
  </div>
</section>

<!-- ══ CAROUSELS ═════════════════════════════════════════════════ -->
<?php pa_render_carousel($cr_new,  'Neue Pflanzen'); ?>
<?php pa_render_carousel($cr_pop,  'Beliebt');       ?>
<?php if (!empty($cr_sale)) : ?>
  <?php pa_render_carousel($cr_sale, 'Angebote');    ?>
<?php endif; ?>

<!-- ══ CATEGORY RAIL ═════════════════════════════════════════════ -->
<nav class="pa-rail" aria-label="Kategorien">
  <a href="<?php echo esc_url(home_url('/')); ?>"
     class="pa-chip <?php echo !$active_cat ? 'active' : ''; ?>">Alle</a>
  <?php foreach ($product_cats as $cat) : ?>
    <a href="<?php echo esc_url(add_query_arg('cat', $cat->slug, home_url('/'))); ?>"
       class="pa-chip <?php echo $active_cat === $cat->slug ? 'active' : ''; ?>">
      <?php echo esc_html($cat->name); ?>
    </a>
  <?php endforeach; ?>
</nav>

<!-- ══ PRODUCT GRID ══════════════════════════════════════════════ -->
<main class="pa-grid">
<?php
if ($products_query->have_posts()) :
  while ($products_query->have_posts()) :
    $products_query->the_post();
    $product = wc_get_product(get_the_ID());
    if (!$product) continue;

    $badge     = pa_get_badge($product);
    $latin     = pa_get_latin($product);
    $price_num = (float) $product->get_price();
    $is_sold   = !$product->is_in_stock();
    $permalink = get_permalink();
    $img_url   = get_the_post_thumbnail_url(get_the_ID(), 'woocommerce_single')
                 ?: content_url('uploads/2022/01/Banner-Plantaphilia.jpg');
?>
  <article class="pa-card">
    <div class="pa-card-img"
         style="background-image: url('<?php echo esc_url($img_url); ?>');"
         onclick="location.href='<?php echo esc_url($permalink); ?>'"
         role="img" aria-label="<?php the_title_attribute(); ?>">
      <?php if ($badge) : ?>
        <span class="pa-badge <?php echo $badge['out'] ? 'out' : ''; ?>">
          <?php echo esc_html($badge['label']); ?>
        </span>
      <?php endif; ?>
      <button class="pa-fav" aria-label="Merken" onclick="event.stopPropagation()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 21s-7-4.5-9-9a5 5 0 0 1 9-3 5 5 0 0 1 9 3c-2 4.5-9 9-9 9z"/>
        </svg>
      </button>
    </div>
    <div class="pa-card-body">
      <h3 class="pa-card-name"><?php the_title(); ?></h3>
      <?php if ($latin) : ?>
        <p class="pa-card-latin"><?php echo esc_html($latin); ?></p>
      <?php endif; ?>
      <div class="pa-card-foot">
        <span class="pa-card-price">
          <?php echo $price_num > 0
            ? '€&nbsp;' . number_format($price_num, 2, ',', '.')
            : esc_html(strip_tags($product->get_price_html())); ?>
        </span>
        <?php if ($is_sold) : ?>
          <span class="pa-sold">Ausverkauft</span>
        <?php else : ?>
          <a href="<?php echo esc_url(add_query_arg('add-to-cart', $product->get_id(), $cart_url)); ?>"
             class="pa-btn-out">In den Warenkorb</a>
        <?php endif; ?>
      </div>
    </div>
  </article>
<?php
  endwhile;
  wp_reset_postdata();
else :
?>
  <p style="color:var(--creme-muted);font-family:var(--serif-display);font-style:italic;
            grid-column:1/-1;text-align:center;padding:60px 0;margin:0;">
    Keine Produkte gefunden.
  </p>
<?php endif; ?>
</main>

<?php get_footer(); ?>
