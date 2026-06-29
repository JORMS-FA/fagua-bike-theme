<?php
/**
 * Theme setup: support features, image sizes, nav menus.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'after_setup_theme', function () {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'editor-styles' );

    load_theme_textdomain( 'bf', get_stylesheet_directory() . '/languages' );

    // Image sizes
    add_image_size( 'bf-card', 600, 600, true );
    add_image_size( 'bf-hero', 1920, 1080, true );
    add_image_size( 'bf-cat', 800, 600, true );

    // Menus
    register_nav_menus( array(
        'primary'  => __( 'Menú principal', 'bf' ),
        'footer'   => __( 'Menú footer', 'bf' ),
        'mobile'   => __( 'Menú mobile', 'bf' ),
    ) );
} );
