<?php
/**
 * Small helper functions.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'bf_logo' ) ) {
    function bf_logo( $class = 'bf-logo' ) {
        if ( has_custom_logo() ) {
            the_custom_logo();
            return;
        }
        $name = get_bloginfo( 'name' );
        $url  = home_url( '/' );
        echo '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '">';
        echo '<span class="bf-logo-mark" aria-hidden="true">◆</span>';
        echo '<span class="bf-logo-text">' . esc_html( $name ) . '</span>';
        echo '</a>';
    }
}

if ( ! function_exists( 'bf_cart_url' ) ) {
    function bf_cart_url() {
        return function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/carrito' );
    }
}

if ( ! function_exists( 'bf_shop_url' ) ) {
    function bf_shop_url() {
        return function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/tienda' );
    }
}
