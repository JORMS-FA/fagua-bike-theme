<?php
/**
 * Enqueue scripts and styles.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts', function () {
    $theme_uri = get_stylesheet_directory_uri();
    $theme_dir = get_stylesheet_directory();
    $ver = wp_get_theme()->get( 'Version' );

    // Main stylesheet (Theme header for WP)
    wp_enqueue_style( 'bf-style', get_stylesheet_uri(), array(), $ver );

    // Theme CSS (design system)
    $css = '/assets/css/theme.css';
    if ( file_exists( $theme_dir . $css ) ) {
        wp_enqueue_style( 'bf-theme', $theme_uri . $css, array( 'bf-style' ), filemtime( $theme_dir . $css ) );
    }
    // Shop CSS (loaded after main theme so it can override)
    $shop_css = '/assets/css/shop.css';
    if ( file_exists( $theme_dir . $shop_css ) ) {
        wp_enqueue_style( 'bf-shop', $theme_uri . $shop_css, array( 'bf-theme' ), filemtime( $theme_dir . $shop_css ) );
    }

    // Components CSS (drawer, toast, modal, skeleton, qty, buttons, badges) — premium UI kit
    $components_css = '/assets/css/components.css';
    if ( file_exists( $theme_dir . $components_css ) ) {
        wp_enqueue_style( 'bf-components', $theme_uri . $components_css, array( 'bf-theme' ), filemtime( $theme_dir . $components_css ) );
    }

    // Google Fonts: Inter
    wp_enqueue_style( 'bf-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap', array(), null );

    // Theme JS (defer)
    $js = '/assets/js/theme.js';
    if ( file_exists( $theme_dir . $js ) ) {
        wp_enqueue_script( 'bf-theme', $theme_uri . $js, array(), filemtime( $theme_dir . $js ), true );
    }

    // Localize: nonce + ajax URL + theme info for JS modules
    wp_localize_script( 'bf-theme', 'bfData', array(
        'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'bf_nonce' ),
        'homeUrl'  => home_url( '/' ),
        'themeUrl' => $theme_uri,
        'i18n'     => array(
            'added'    => __( 'Producto añadido', 'bf' ),
            'removed'  => __( 'Producto eliminado', 'bf' ),
            'error'    => __( 'Algo salió mal. Inténtalo de nuevo.', 'bf' ),
            'loading'  => __( 'Cargando…', 'bf' ),
            'addedTo'  => __( 'Añadido al carrito', 'bf' ),
        ),
    ) );
}, 20 );
