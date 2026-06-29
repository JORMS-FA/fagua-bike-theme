<?php
/**
 * Checkout page — Bicicletería Fagua
 * Custom layout: 2-col form + sticky order summary, mobile-first.
 *
 * @package BicicleteriaFagua
 */

if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

// Make sure checkout is loaded
if ( ! function_exists( 'WC' ) ) return;
?>

<main class="bf-container bf-checkout" id="bfCheckout">
  <?php do_action( 'woocommerce_before_checkout_form', WC()->checkout() ); ?>

  <div class="bf-checkout__form">
    <?php woocommerce_checkout_login_form(); ?>
    <?php woocommerce_checkout_coupon_form(); ?>

    <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
      <?php do_action( 'woocommerce_checkout_billing' ); ?>
      <?php do_action( 'woocommerce_checkout_shipping' ); ?>

      <h3 id="order_review_heading" style="margin-top: var(--bf-space-8);"><?php esc_html_e( 'Tu pedido', 'bf' ); ?></h3>
      <div id="order_review">
        <?php do_action( 'woocommerce_checkout_order_review' ); ?>
      </div>
    </form>
  </div>

  <aside class="bf-checkout__sidebar">
    <h3><?php esc_html_e( 'Resumen', 'bf' ); ?></h3>
    <div id="bf-checkout-summary">
      <?php woocommerce_order_review(); ?>
    </div>
  </aside>

  <?php do_action( 'woocommerce_after_checkout_form', WC()->checkout() ); ?>
</main>

<?php get_footer(); ?>
