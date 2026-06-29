<?php
/**
 * Section: Productos destacados — Bicicletería Fagua
 * Productos REALES del catálogo WC con imagen, nombre, precio, botón añadir.
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// Query productos destacados: 8 productos con stock
$args = array(
  'post_type'      => 'product',
  'posts_per_page' => 8,
  'post_status'    => 'publish',
  'meta_query'     => array(
    array( 'key' => '_stock_status', 'value' => 'instock', 'compare' => '=' ),
  ),
  'orderby'        => 'menu_order date',
  'order'          => 'DESC',
);
$loop = new WP_Query( $args );

$shop_url = function_exists('bf_shop_url') ? bf_shop_url() : home_url('/tienda');
?>

<section class="bf-section bf-products" id="destacados" aria-label="Productos destacados">
  <div class="bf-container">

    <header class="bf-section__header bf-reveal">
      <p class="bf-section__eyebrow">Lo más vendido</p>
      <h2 class="bf-section__title">Productos destacados</h2>
      <p class="bf-section__lead">Selección curada de los productos que más piden nuestros clientes.</p>
    </header>

    <?php if ( $loop->have_posts() ) : ?>
      <div class="bf-products__grid bf-reveal" data-reveal-delay="80">
        <?php while ( $loop->have_posts() ) : $loop->the_post();
          global $product;
          $pid    = get_the_ID();
          $name   = get_the_title();
          $price  = $product->get_price_html();
          $img_id = $product->get_image_id();
          $img    = $img_id ? wp_get_attachment_image( $img_id, 'woocommerce_thumbnail', false, array( 'loading' => 'lazy' ) ) : wc_placeholder_img();
          $cats   = wp_get_post_terms( $pid, 'product_cat', array( 'fields' => 'names' ) );
          $cat    = $cats && !is_wp_error($cats) ? $cats[0] : '';
          $link   = get_permalink();
        ?>
          <article class="bf-product-card">
            <a class="bf-product-card__media" href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( $name ); ?>">
              <?php echo $img; ?>
            </a>
            <div class="bf-product-card__body">
              <?php if ( $cat ) : ?>
                <p class="bf-product-card__cat"><?php echo esc_html( $cat ); ?></p>
              <?php endif; ?>
              <h3 class="bf-product-card__name">
                <a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $name ); ?></a>
              </h3>
              <div class="bf-product-card__price"><?php echo $price; ?></div>
              <a class="bf-product-card__cta" href="<?php echo esc_url( $link ); ?>" data-product-id="<?php echo esc_attr( $pid ); ?>" data-bf-add-to-cart>
                Ver producto
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
              </a>
            </div>
          </article>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>

      <div class="bf-products__more bf-reveal" data-reveal-delay="160">
        <a class="bf-btn bf-btn--ghost" href="<?php echo esc_url( $shop_url ); ?>">
          Ver todo el catálogo
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
      </div>
    <?php else : ?>
      <p class="bf-empty">No hay productos disponibles en este momento.</p>
    <?php endif; ?>

  </div>
</section>
