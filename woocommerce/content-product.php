<?php
/**
 * Single product card — Bicicletería Fagua
 * Override of WooCommerce's content-product.php with our premium style.
 *
 * @package BicicleteriaFagua
 */

if ( ! defined( 'ABSPATH' ) ) exit;

global $product;

if ( ! $product || ! $product instanceof WC_Product ) return;

$rating  = $product->get_average_rating();
$review  = $product->get_review_count();
$on_sale = $product->is_on_sale();
$regular = (float) $product->get_regular_price();
$price   = (float) $product->get_price();
$sku     = $product->get_sku();
$stock   = $product->get_stock_status();
?>

<li <?php wc_product_class( 'bf-product', $product ); ?>>

  <a class="bf-product__inner" href="<?php the_permalink(); ?>">

    <div class="bf-product__media">
      <?php
      if ( has_post_thumbnail() ) {
        the_post_thumbnail( 'bf-card', array( 'loading' => 'lazy', 'decoding' => 'async' ) );
      } else { ?>
        <div class="bf-product__placeholder" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="17" r="4"/><circle cx="18" cy="17" r="4"/><path d="M6 17 L10 8 L14 17 M10 8 L14 8 M14 17 L18 17"/></svg>
        </div>
      <?php } ?>

      <?php if ( $on_sale && $regular > 0 ) :
        $pct = round( ( ( $regular - $price ) / $regular ) * 100 ); ?>
        <span class="bf-product__badge bf-product__badge--sale">-<?php echo esc_html( $pct ); ?>%</span>
      <?php elseif ( $product->is_featured() ) : ?>
        <span class="bf-product__badge">Top</span>
      <?php elseif ( strtotime( $product->get_date_created() ) > strtotime( '-30 days' ) ) : ?>
        <span class="bf-product__badge bf-product__badge--new">Nuevo</span>
      <?php endif; ?>

      <?php if ( $stock === 'outofstock' ) : ?>
        <span class="bf-product__badge bf-product__badge--out">Agotado</span>
      <?php endif; ?>

      <div class="bf-product__actions">
        <button type="button" class="bf-product__quick" data-quick-view="<?php echo esc_attr( $product->get_id() ); ?>" aria-label="<?php esc_attr_e( 'Vista rápida', 'bf' ); ?>">
          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
      </div>
    </div>

    <div class="bf-product__info">
      <?php
      $terms = get_the_terms( $product->get_id(), 'product_cat' );
      if ( $terms && ! is_wp_error( $terms ) ) {
        echo '<span class="bf-product__cat">' . esc_html( $terms[0]->name ) . '</span>';
      } else {
        echo '<span class="bf-product__cat">Fagua</span>';
      }
      ?>

      <h3 class="bf-product__title"><?php the_title(); ?></h3>

      <?php if ( $review > 0 ) : ?>
        <div class="bf-product__rating" aria-label="<?php echo esc_attr( sprintf( __( '%s de 5 estrellas', 'bf' ), $rating ) ); ?>">
          <span class="bf-product__stars">
            <?php for ( $i = 1; $i <= 5; $i++ ) {
              echo $i <= round( $rating )
                ? '<svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" aria-hidden="true"><path d="M12 2l2.4 7.4H22l-6.2 4.5L18.2 22 12 17.4 5.8 22l2.4-8.1L2 9.4h7.6z"/></svg>'
                : '<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 2l2.4 7.4H22l-6.2 4.5L18.2 22 12 17.4 5.8 22l2.4-8.1L2 9.4h7.6z"/></svg>';
            } ?>
          </span>
          <span class="bf-product__review-count">(<?php echo intval( $review ); ?>)</span>
        </div>
      <?php endif; ?>

      <div class="bf-product__price">
        <?php if ( $on_sale && $regular > 0 ) : ?>
          <del><?php echo wc_price( $regular ); ?></del>
        <?php endif; ?>
        <ins><?php echo wc_price( $price ); ?></ins>
        <?php if ( $sku ) : ?>
          <span class="bf-product__sku">SKU: <?php echo esc_html( $sku ); ?></span>
        <?php endif; ?>
      </div>
    </div>

  </a>

  <?php if ( $stock !== 'outofstock' ) : ?>
    <div class="bf-product__cta">
      <?php woocommerce_template_loop_add_to_cart( array( 'class' => 'bf-btn bf-btn--primary bf-btn--sm bf-btn--block' ) ); ?>
    </div>
  <?php endif; ?>

</li>
