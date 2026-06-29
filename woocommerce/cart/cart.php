<?php
/**
 * Cart page — Bicicletería Fagua
 * Custom layout: items + sticky summary, no tables, modern UX.
 *
 * @package BicicleteriaFagua
 */

if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<main class="bf-cart-wrap" id="bfCart">
  <div class="bf-container">

    <?php woocommerce_output_all_notices(); ?>

    <?php if ( WC()->cart && WC()->cart->get_cart_contents_count() > 0 ) : ?>

      <header class="bf-section-header bf-reveal">
        <div>
          <span class="bf-section-eyebrow"><?php esc_html_e( 'Carrito', 'bf' ); ?></span>
          <h1 class="bf-section-title"><?php esc_html_e( 'Tu carrito', 'bf' ); ?></h1>
          <p class="bf-section-subtitle"><?php printf( esc_html( _n( '%s producto seleccionado', '%s productos seleccionados', WC()->cart->get_cart_contents_count(), 'bf' ) ), esc_html( number_format_i18n( WC()->cart->get_cart_contents_count() ) ) ); ?></p>
        </div>
        <a class="bf-btn bf-btn--ghost bf-btn--sm" href="<?php echo esc_url( function_exists( 'bf_shop_url' ) ? bf_shop_url() : home_url( '/tienda' ) ); ?>">
          <?php esc_html_e( '← Seguir comprando', 'bf' ); ?>
        </a>
      </header>

      <div class="bf-cart">

        <div class="bf-cart__items">
          <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
            if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 ) continue;

            $thumb = wp_get_attachment_image( $_product->get_image_id(), 'bf-card' );
            $link  = $_product->get_permalink();
          ?>
            <div class="bf-cart-item">
              <a class="bf-cart-item__media" href="<?php echo esc_url( $link ); ?>">
                <?php echo $thumb ?: '<div class="bf-product__placeholder" style="height:100%;width:100%;"></div>'; ?>
              </a>
              <div class="bf-cart-item__info">
                <a href="<?php echo esc_url( $link ); ?>" class="bf-cart-item__title"><?php echo esc_html( $_product->get_name() ); ?></a>
                <div class="bf-cart-item__meta">
                  <?php if ( $_product->get_sku() ) : ?><span>SKU: <?php echo esc_html( $_product->get_sku() ); ?></span><?php endif; ?>
                  <?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                </div>
                <div style="display:flex;align-items:center;gap:1rem;margin-top:0.6rem;">
                  <div class="bf-qty" data-cart-key="<?php echo esc_attr( $cart_item_key ); ?>">
                    <button type="button" data-qty-minus aria-label="−">−</button>
                    <input type="number" value="<?php echo intval( $cart_item['quantity'] ); ?>" min="1" max="99" step="1" inputmode="numeric" />
                    <button type="button" data-qty-plus aria-label="+">+</button>
                  </div>
                  <button type="button" class="bf-cart-item__remove" data-cart-remove="<?php echo esc_attr( $cart_item_key ); ?>" aria-label="<?php esc_attr_e( 'Eliminar', 'bf' ); ?>"><?php esc_html_e( 'Eliminar', 'bf' ); ?></button>
                </div>
              </div>
              <div class="bf-cart-item__right">
                <div class="bf-cart-item__price"><?php echo WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ); ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <aside class="bf-cart__summary">
          <h3><?php esc_html_e( 'Resumen del pedido', 'bf' ); ?></h3>

          <div class="cart_totals">
            <table class="shop_table">
              <tbody>
                <tr class="cart-subtotal">
                  <th><?php esc_html_e( 'Subtotal', 'bf' ); ?></th>
                  <td><?php wc_cart_totals_subtotal_html(); ?></td>
                </tr>
                <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                  <tr class="cart-discount">
                    <th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
                    <td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
                  <tr class="shipping">
                    <th><?php esc_html_e( 'Envío', 'bf' ); ?></th>
                    <td><?php wc_cart_totals_shipping_html(); ?></td>
                  </tr>
                <?php endif; ?>
                <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
                  <tr>
                    <th><?php echo esc_html( $fee->name ); ?></th>
                    <td><?php wc_cart_totals_fee_html( $fee ); ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php if ( wc_tax_enabled() && ! WC()->cart->tax_display_cart() ) :
                  foreach ( WC()->cart->get_tax_totals() as $tax ) : ?>
                    <tr>
                      <th><?php echo esc_html( $tax->label ); ?></th>
                      <td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
                    </tr>
                  <?php endforeach;
                endif; ?>
                <tr class="order-total">
                  <th><?php esc_html_e( 'Total', 'bf' ); ?></th>
                  <td><?php wc_cart_totals_order_total_html(); ?></td>
                </tr>
              </tbody>
            </table>
          </div>

          <form class="woocommerce-cart-coupon-form" method="post">
            <div class="bf-coupon-row">
              <input type="text" name="coupon_code" placeholder="<?php esc_attr_e( 'Código de cupón', 'bf' ); ?>" />
              <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Aplicar', 'bf' ); ?>"><?php esc_html_e( 'Aplicar', 'bf' ); ?></button>
            </div>
          </form>

          <div class="wc-proceed-to-checkout">
            <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="checkout-button button alt wc-forward">
              <?php esc_html_e( 'Ir a checkout', 'bf' ); ?>
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-left:6px;vertical-align:middle;"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
          </div>

          <div class="bf-feature" style="margin-top: var(--bf-space-5);">
            <span class="bf-feature__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 5-4 9-9 9s-9-4-9-9 4-9 9-9c2.4 0 4.6 1 6.3 2.5"/></svg>
            </span>
            <div class="bf-feature__text"><?php esc_html_e( 'Pago seguro SSL', 'bf' ); ?></div>
          </div>
        </aside>

      </div>

    <?php else : ?>

      <div class="bf-cart-empty bf-reveal">
        <h2><?php esc_html_e( 'Tu carrito está vacío', 'bf' ); ?></h2>
        <p><?php esc_html_e( 'Aún no has agregado productos. Explora la tienda y encuentra tu próxima rodada.', 'bf' ); ?></p>
        <a class="bf-btn bf-btn--primary bf-btn--lg" href="<?php echo esc_url( function_exists( 'bf_shop_url' ) ? bf_shop_url() : home_url( '/tienda' ) ); ?>">
          <?php esc_html_e( 'Explorar tienda', 'bf' ); ?>
        </a>
      </div>

    <?php endif; ?>

  </div>
</main>

<?php get_footer(); ?>
