<?php
/**
 * Section: Ofertas de la semana — Bicicletería Fagua
 * Productos en oferta (en_sale=true).
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$loop = new WP_Query( array(
  'post_type'      => 'product',
  'posts_per_page' => 4,
  'post_status'    => 'publish',
  'meta_query'     => array(
    array( 'key' => '_stock_status', 'value' => 'instock', 'compare' => '=' ),
  ),
  'tax_query'      => array(
    array(
      'taxonomy' => 'product_visibility',
      'field'    => 'name',
      'terms'    => array( 'exclude-from-catalog' ),
      'operator' => 'NOT IN',
    ),
  ),
  'orderby' => 'meta_value_num',
  'meta_key' => '_sale_price',
  'order'   => 'DESC',
) );

// Si no hay productos en oferta, mostrar productos con precio bajo
if ( ! $loop->have_posts() ) {
  $loop = new WP_Query( array(
    'post_type'      => 'product',
    'posts_per_page' => 4,
    'post_status'    => 'publish',
    'orderby' => 'meta_value_num',
    'meta_key' => '_price',
    'order'   => 'ASC',
  ) );
}
?>

<section class="bf-section bf-offers" id="ofertas" aria-label="Ofertas de la semana">
  <div class="bf-container">

    <header class="bf-section__header bf-reveal">
      <span class="bf-offers__badge">🔥 Ofertas</span>
      <h2 class="bf-section__title">Ofertas de la semana</h2>
      <p class="bf-section__lead">Precios especiales por tiempo limitado. Comprá ahora.</p>
    </header>

    <?php if ( $loop->have_posts() ) : ?>
      <div class="bf-offers__grid bf-reveal" data-reveal-delay="80">
        <?php while ( $loop->have_posts() ) : $loop->the_post();
          global $product;
          $pid   = get_the_ID();
          $name  = get_the_title();
          $price = $product->get_price_html();
          $img_id= $product->get_image_id();
          $img   = $img_id ? wp_get_attachment_image( $img_id, 'woocommerce_thumbnail', false, array( 'loading' => 'lazy' ) ) : wc_placeholder_img();
          $link  = get_permalink();
          $regular = $product->get_regular_price();
          $sale    = $product->get_sale_price();
          $pct_off = '';
          if ( $regular && $sale && $regular > 0 ) {
            $pct_off = round( ( ( $regular - $sale ) / $regular ) * 100 );
          }
        ?>
          <a class="bf-offer-card" href="<?php echo esc_url( $link ); ?>">
            <div class="bf-offer-card__media">
              <?php if ( $pct_off ) : ?>
                <span class="bf-offer-card__discount">-<?php echo esc_html( $pct_off ); ?>%</span>
              <?php endif; ?>
              <?php echo $img; ?>
            </div>
            <div class="bf-offer-card__body">
              <h3 class="bf-offer-card__name"><?php echo esc_html( $name ); ?></h3>
              <div class="bf-offer-card__price"><?php echo $price; ?></div>
            </div>
          </a>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
    <?php else : ?>
      <p class="bf-empty">Pronto publicaremos nuevas ofertas.</p>
    <?php endif; ?>

  </div>
</section>
