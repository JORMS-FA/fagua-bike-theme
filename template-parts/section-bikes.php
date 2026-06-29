<?php
/**
 * Section: Bicicletas destacadas — Bicicletería Fagua
 * Grid de 4 bicicletas destacadas del catálogo.
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$loop = new WP_Query( array(
  'post_type'      => 'product',
  'posts_per_page' => 4,
  'post_status'    => 'publish',
  'tax_query'      => array(
    array(
      'taxonomy' => 'product_cat',
      'field'    => 'slug',
      'terms'    => array( 'bicicletas' ),
    ),
  ),
  'orderby' => 'date',
  'order'   => 'DESC',
) );

$shop_url = function_exists('bf_shop_url') ? bf_shop_url() : home_url('/tienda');
?>

<section class="bf-section bf-bikes" id="bicicletas" aria-label="Bicicletas destacadas">
  <div class="bf-container">

    <header class="bf-section__header bf-reveal">
      <p class="bf-section__eyebrow">Para rodar hoy</p>
      <h2 class="bf-section__title">Bicicletas destacadas</h2>
      <p class="bf-section__lead">Road, MTB, gravel. Las bicicletas que están pidiendo nuestros clientes.</p>
    </header>

    <?php if ( $loop->have_posts() ) : ?>
      <div class="bf-bikes__grid bf-reveal" data-reveal-delay="80">
        <?php while ( $loop->have_posts() ) : $loop->the_post();
          global $product;
          $pid    = get_the_ID();
          $name   = get_the_title();
          $price  = $product->get_price_html();
          $img_id = $product->get_image_id();
          $img    = $img_id ? wp_get_attachment_image( $img_id, 'woocommerce_thumbnail', false, array( 'loading' => 'lazy' ) ) : wc_placeholder_img();
          $link   = get_permalink();
        ?>
          <a class="bf-bike-card" href="<?php echo esc_url( $link ); ?>">
            <div class="bf-bike-card__media"><?php echo $img; ?></div>
            <div class="bf-bike-card__body">
              <h3 class="bf-bike-card__name"><?php echo esc_html( $name ); ?></h3>
              <div class="bf-bike-card__price"><?php echo $price; ?></div>
            </div>
          </a>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
      <div class="bf-products__more bf-reveal" data-reveal-delay="160">
        <a class="bf-btn bf-btn--ghost" href="<?php echo esc_url( $shop_url . '?categoria=bicicletas' ); ?>">Ver todas las bicicletas →</a>
      </div>
    <?php else : ?>
      <p class="bf-empty">Pronto más bicicletas.</p>
    <?php endif; ?>

  </div>
</section>
