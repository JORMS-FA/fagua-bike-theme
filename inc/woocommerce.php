<?php
/**
 * WooCommerce customizations.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Forzar nuestros templates premium de Cart y Checkout,
 * incluso si la página tiene el shortcode clásico [woocommerce_cart] o [woocommerce_checkout].
 */
add_filter( 'template_include', function( $template ) {
    if ( function_exists( 'is_cart' ) && is_cart() ) {
        $custom = locate_template( array( 'woocommerce/cart/cart.php' ) );
        if ( $custom ) {
            // Eliminar shortcode de la página cuando se está renderizando el cart
            remove_shortcode( 'woocommerce_cart' );
            return $custom;
        }
    }
    if ( function_exists( 'is_checkout' ) && is_checkout() ) {
        $custom = locate_template( array( 'woocommerce/checkout/form-checkout.php' ) );
        if ( $custom ) {
            remove_shortcode( 'woocommerce_checkout' );
            return $custom;
        }
    }
    return $template;
}, 99 );

/**
 * Remove Storefront default wrappers that fight our layout.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

/**
 * Set the total product count for the shop hero.
 * Runs early so the template can use $GLOBALS['bf_shop_total'].
 */
add_action( 'woocommerce_before_main_content', function () {
    global $wp_query;
    $GLOBALS['bf_shop_total'] = (int) ( $wp_query->found_posts ?? 0 );
} );

/**
 * Wrap non-archive/non-single product views with our container.
 * Our archive-product.php and single-product.php manage their own markup.
 */
add_action( 'woocommerce_before_main_content', function () {
    if ( is_shop() || is_product() || is_product_taxonomy() ) {
        return;
    }
    echo '<main class="bf-section">';
    echo '<div class="bf-container">';
} );

add_action( 'woocommerce_after_main_content', function () {
    if ( is_shop() || is_product() || is_product_taxonomy() ) {
        return;
    }
    echo '</div></main>';
} );

/**
 * Remove Storefront sidebars by default (we want full-width).
 */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Cart fragments: use a custom count for the header cart icon.
 */
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
    $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    ob_start();
    ?>
    <span class="bf-cart-count" data-count="<?php echo esc_attr( $count ); ?>"><?php echo (int) $count; ?></span>
    <?php
    $fragments['.bf-cart-count'] = ob_get_clean();
    return $fragments;
} );

/**
 * Number of products per page on the shop.
 */
add_filter( 'loop_shop_per_page', function () {
    return 12;
}, 20 );

/**
 * Number of columns on the shop.
 */
add_filter( 'loop_shop_columns', function () {
    return 4;
} );

/* ============================================================
   BF AJAX: Mini Cart Drawer
   Endpoint unificado que devuelve:
   - count: número de items en el carrito
   - total: total formateado (HTML WC)
   - subtotal: subtotal formateado
   - items_html: HTML listo para inyectar en el drawer
   - fragments: WC cart fragments (para refrescar el header)
   ============================================================ */
add_action( 'wp_ajax_bf_get_cart',        'bf_ajax_get_cart' );
add_action( 'wp_ajax_nopriv_bf_get_cart', 'bf_ajax_get_cart' );

function bf_ajax_get_cart() {
    check_ajax_referer( 'bf_nonce', 'nonce' );

    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        wp_send_json_error( array( 'message' => 'WC no disponible' ), 500 );
    }

    $cart       = WC()->cart;
    $count      = (int) $cart->get_cart_contents_count();
    $subtotal   = $cart->get_cart_subtotal();
    $total_html = $cart->get_total() > 0 ? wc_price( $cart->get_total() ) : wc_price( 0 );

    ob_start();
    if ( $count === 0 ) {
        ?>
        <div class="bf-drawer-empty" data-cart-empty>
          <div class="bf-drawer-empty__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>
          </div>
          <p class="bf-drawer-empty__title"><?php esc_html_e( 'Tu carrito está vacío', 'bf' ); ?></p>
          <p class="bf-drawer-empty__text"><?php esc_html_e( 'Aún no has agregado productos. Explora la tienda y encuentra tu próxima rodada.', 'bf' ); ?></p>
          <a class="bf-btn bf-btn--primary" href="<?php echo esc_url( function_exists( 'bf_shop_url' ) ? bf_shop_url() : home_url( '/tienda' ) ); ?>" data-drawer-close>
            <?php esc_html_e( 'Explorar tienda', 'bf' ); ?>
          </a>
        </div>
        <?php
    } else {
        ?>
        <ul class="bf-drawer-list" data-cart-list>
          <?php foreach ( $cart->get_cart() as $key => $item ) :
            $_product = apply_filters( 'woocommerce_cart_item_product', $item['data'], $item, $key );
            $pid      = apply_filters( 'woocommerce_cart_item_product_id', $item['product_id'], $item, $key );
            $price    = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $item, $key );
            $link     = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $item ) : '', $item, $key );
            $qty      = (int) $item['quantity'];
            $max_qty  = $_product->get_max_purchase_quantity();
            $min_qty  = $_product->get_min_purchase_quantity();
            $row_key  = esc_attr( $key );
            ?>
            <li class="bf-drawer-item" data-cart-item data-key="<?php echo $row_key; ?>">
              <a class="bf-drawer-item__media" href="<?php echo esc_url( $link ); ?>" data-drawer-close>
                <?php
                $thumb = $_product->get_image( array( 72, 72 ) );
                echo $thumb ? $thumb : '<div class="bf-product__placeholder" aria-hidden="true"><svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="17" r="4"/><circle cx="18" cy="17" r="4"/><path d="M6 17 L10 8 L14 17 M10 8 L14 8 M14 17 L18 17"/></svg></div>';
                ?>
              </a>
              <div>
                <h3 class="bf-drawer-item__name">
                  <a href="<?php echo esc_url( $link ); ?>" data-drawer-close><?php echo wp_kses_post( $_product->get_name() ); ?></a>
                </h3>
                <p class="bf-drawer-item__meta"><?php echo wp_kses_post( $subtotal ); ?> total</p>
                <div class="bf-qty bf-qty--sm" data-cart-qty>
                  <button type="button" class="bf-qty__btn" data-cart-qty-minus aria-label="<?php esc_attr_e( 'Disminuir', 'bf' ); ?>" <?php echo $qty <= $min_qty ? 'disabled' : ''; ?>>−</button>
                  <input type="number" class="bf-qty__input" value="<?php echo esc_attr( $qty ); ?>" min="<?php echo esc_attr( $min_qty ); ?>" max="<?php echo esc_attr( $max_qty > 0 ? $max_qty : 99 ); ?>" step="1" inputmode="numeric" data-cart-qty-input aria-label="<?php esc_attr_e( 'Cantidad', 'bf' ); ?>">
                  <button type="button" class="bf-qty__btn" data-cart-qty-plus aria-label="<?php esc_attr_e( 'Aumentar', 'bf' ); ?>" <?php echo ( $max_qty > 0 && $qty >= $max_qty ) ? 'disabled' : ''; ?>>+</button>
                </div>
              </div>
              <div style="display:flex; flex-direction:column; align-items:flex-end; gap:4px;">
                <span class="bf-drawer-item__price"><?php echo wp_kses_post( $price ); ?></span>
                <button type="button" class="bf-drawer-item__remove" data-cart-remove="<?php echo $row_key; ?>" aria-label="<?php esc_attr_e( 'Eliminar', 'bf' ); ?>">
                  <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-2 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                  <?php esc_html_e( 'Eliminar', 'bf' ); ?>
                </button>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
        <?php
    }
    $items_html = ob_get_clean();

    wp_send_json_success( array(
        'count'      => $count,
        'subtotal'   => wp_strip_all_tags( $subtotal ),
        'total_html' => $total_html,
        'items_html' => $items_html,
        'fragments'  => array(
            '.bf-cart-count' => '<span class="bf-cart-count" data-count="' . esc_attr( $count ) . '">' . (int) $count . '</span>',
        ),
    ) );
}

/* AJAX: update qty */
add_action( 'wp_ajax_bf_update_qty',        'bf_ajax_update_qty' );
add_action( 'wp_ajax_nopriv_bf_update_qty', 'bf_ajax_update_qty' );
function bf_ajax_update_qty() {
    check_ajax_referer( 'bf_nonce', 'nonce' );
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) wp_send_json_error( array( 'message' => 'no wc' ), 500 );

    $cart_key = isset( $_POST['cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_key'] ) ) : '';
    $qty      = isset( $_POST['qty'] ) ? max( 0, (int) $_POST['qty'] ) : 0;
    if ( ! $cart_key ) wp_send_json_error( array( 'message' => 'no key' ), 400 );

    $updated = WC()->cart->set_quantity( $cart_key, $qty );
    WC()->cart->calculate_totals();

    if ( ! $updated && $qty !== 0 ) {
        wp_send_json_error( array( 'message' => 'no update' ), 400 );
    }

    bf_ajax_get_cart();
}

/* AJAX: remove item */
add_action( 'wp_ajax_bf_remove_item',        'bf_ajax_remove_item' );
add_action( 'wp_ajax_nopriv_bf_remove_item', 'bf_ajax_remove_item' );
function bf_ajax_remove_item() {
    check_ajax_referer( 'bf_nonce', 'nonce' );
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) wp_send_json_error( array( 'message' => 'no wc' ), 500 );

    $cart_key = isset( $_POST['cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_key'] ) ) : '';
    if ( ! $cart_key ) wp_send_json_error( array( 'message' => 'no key' ), 400 );

    WC()->cart->remove_cart_item( $cart_key );
    WC()->cart->calculate_totals();

    bf_ajax_get_cart();
}
