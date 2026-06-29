<?php
/**
 * Shop / Catalog — Bicicletería Fagua
 *
 * @package BicicleteriaFagua
 */

if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

// Make sure the product count global is set before any template-part reads it.
global $wp_query;
$GLOBALS['bf_shop_total'] = (int) ( $wp_query->found_posts ?? 0 );
?>

<main class="bf-shop" id="bfShop">

  <?php get_template_part( 'template-parts/section', 'shop-hero' ); ?>

  <div class="bf-container bf-shop__body">
    <aside class="bf-shop__sidebar" aria-label="<?php esc_attr_e( 'Filtros de tienda', 'bf' ); ?>">
      <?php get_template_part( 'template-parts/section', 'shop-filters' ); ?>
    </aside>

    <div class="bf-shop__main">
      <div class="bf-shop__toolbar bf-reveal" id="bfShopToolbar">
        <div class="bf-shop__count" data-count="<?php echo (int) ( $GLOBALS['bf_shop_total'] ?? 0 ); ?>">
          <?php
          $total = (int) ( $GLOBALS['bf_shop_total'] ?? 0 );
          printf( esc_html( _n( '%s producto', '%s productos', $total, 'bf' ) ), '<strong>' . esc_html( number_format_i18n( $total ) ) . '</strong>' );
          ?>
        </div>
        <div class="bf-shop__sort">
          <?php woocommerce_catalog_ordering(); ?>
        </div>
      </div>

      <?php if ( woocommerce_product_loop() ) : ?>

        <?php woocommerce_product_loop_start(); ?>

          <?php while ( have_posts() ) : the_post(); ?>
            <?php wc_get_template_part( 'content', 'product' ); ?>
          <?php endwhile; ?>

        <?php woocommerce_product_loop_end(); ?>

        <div class="bf-shop__pagination">
          <?php woocommerce_pagination(); ?>
        </div>

      <?php else : ?>
        <div class="bf-shop__empty bf-reveal">
          <h2><?php esc_html_e( 'Sin resultados', 'bf' ); ?></h2>
          <p><?php esc_html_e( 'No encontramos productos con esos filtros. Probá ampliando la búsqueda.', 'bf' ); ?></p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php get_footer(); ?>
