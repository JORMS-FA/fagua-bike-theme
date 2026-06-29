<?php
/**
 * Header — Bicicletería Fagua
 * Premium header: logo + categorías + buscador grande + cuenta + favoritos + carrito.
 * Mobile: 2-row layout (logo+actions top, search full-width bottom).
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<header class="bf-header" id="bfHeader" role="banner">
  <div class="bf-container bf-header__inner">

    <a class="bf-logo bf-logo--wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
      <span class="bf-logo-mark" aria-hidden="true">
        <img class="bf-logo-svg" src="<?php echo esc_url( content_url( '/uploads/2026/06/logo-fagua.svg' ) ); ?>" alt="Bicicletería Fagua" />
      </span>
    </a>

    <button type="button" class="bf-categories-btn" id="bfCategoriesBtn" aria-label="<?php esc_attr_e( 'Categorías', 'bf' ); ?>" aria-expanded="false">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <line x1="3" y1="6" x2="21" y2="6"/>
        <line x1="3" y1="12" x2="21" y2="12"/>
        <line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
      <span><?php esc_html_e( 'Categorías', 'bf' ); ?></span>
    </button>

    <form class="bf-search-form" role="search" method="get" action="<?php echo esc_url( function_exists( 'bf_shop_url' ) ? bf_shop_url() : home_url( '/tienda' ) ); ?>">
      <svg class="bf-search-form__icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="11" cy="11" r="7"/>
        <path d="m20 20-3.5-3.5"/>
      </svg>
      <input
        type="search"
        name="s"
        class="bf-search-form__input"
        placeholder="<?php esc_attr_e( 'Buscar productos, marcas o categorías...', 'bf' ); ?>"
        value="<?php echo esc_attr( get_search_query() ); ?>"
        autocomplete="off"
        data-bf-search-input
      />
      <button type="submit" class="bf-search-form__submit" aria-label="<?php esc_attr_e( 'Buscar', 'bf' ); ?>">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M5 12h14M13 6l6 6-6 6"/>
        </svg>
      </button>
    </form>

    <div class="bf-header__actions">
      <a class="bf-header__action" href="<?php echo esc_url( function_exists( 'bf_myaccount_url' ) ? bf_myaccount_url() : home_url( '/mi-cuenta' ) ); ?>" aria-label="<?php esc_attr_e( 'Ingresar', 'bf' ); ?>">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
        <span class="bf-header__action-label"><?php esc_html_e( 'Ingresar', 'bf' ); ?></span>
      </a>

      <a class="bf-header__action" href="<?php echo esc_url( function_exists( 'bf_wishlist_url' ) ? bf_wishlist_url() : home_url( '/favoritos' ) ); ?>" aria-label="<?php esc_attr_e( 'Favoritos', 'bf' ); ?>">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        <span class="bf-header__action-label"><?php esc_html_e( 'Favoritos', 'bf' ); ?></span>
      </a>

      <a class="bf-header__action bf-cart-btn" href="<?php echo esc_url( function_exists( 'bf_cart_url' ) ? bf_cart_url() : home_url( '/carrito' ) ); ?>" aria-label="<?php esc_attr_e( 'Carrito', 'bf' ); ?>" data-bf-cart-toggle>
        <span class="bf-header__action-icon">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M3 3h2l2.4 12.4a2 2 0 0 0 2 1.6h8.2a2 2 0 0 0 2-1.6L21 7H6"/>
            <circle cx="9" cy="20" r="1.5"/>
            <circle cx="18" cy="20" r="1.5"/>
          </svg>
          <span class="bf-cart-count" data-count="0">0</span>
        </span>
        <span class="bf-header__action-label"><?php esc_html_e( 'Carrito', 'bf' ); ?></span>
      </a>

      <button type="button" class="bf-menu-toggle" id="bfMenuToggle" aria-label="<?php esc_attr_e( 'Abrir menú', 'bf' ); ?>" aria-expanded="false" aria-controls="bfMobileMenu">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>

  </div>

  <nav class="bf-subnav" aria-label="<?php esc_attr_e( 'Menú secundario', 'bf' ); ?>">
    <div class="bf-container bf-subnav__inner">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="bf-subnav__link bf-subnav__link--active"><?php esc_html_e( 'Inicio', 'bf' ); ?></a>
      <a href="<?php echo esc_url( function_exists( 'bf_shop_url' ) ? bf_shop_url() : home_url( '/tienda' ) ); ?>" class="bf-subnav__link"><?php esc_html_e( 'Bicicletas', 'bf' ); ?></a>
      <a href="<?php echo esc_url( function_exists( 'bf_shop_url' ) ? bf_shop_url() : home_url( '/tienda' ) ); ?>?categoria=componentes" class="bf-subnav__link"><?php esc_html_e( 'Componentes', 'bf' ); ?></a>
      <a href="<?php echo esc_url( function_exists( 'bf_shop_url' ) ? bf_shop_url() : home_url( '/tienda' ) ); ?>?categoria=ruedas" class="bf-subnav__link"><?php esc_html_e( 'Ruedas', 'bf' ); ?></a>
      <a href="<?php echo esc_url( function_exists( 'bf_shop_url' ) ? bf_shop_url() : home_url( '/tienda' ) ); ?>?categoria=accesorios" class="bf-subnav__link"><?php esc_html_e( 'Accesorios', 'bf' ); ?></a>
      <a href="#servicio" class="bf-subnav__link"><?php esc_html_e( 'Servicio técnico', 'bf' ); ?></a>
      <a href="#marcas" class="bf-subnav__link"><?php esc_html_e( 'Marcas', 'bf' ); ?></a>
      <a href="#contacto" class="bf-subnav__link"><?php esc_html_e( 'Contacto', 'bf' ); ?></a>
    </div>
  </nav>

  <div class="bf-mobile-menu" id="bfMobileMenu" hidden>
    <form class="bf-mobile-menu__search" role="search" method="get" action="<?php echo esc_url( function_exists( 'bf_shop_url' ) ? bf_shop_url() : home_url( '/tienda' ) ); ?>">
      <input type="search" name="s" placeholder="<?php esc_attr_e( 'Buscar productos...', 'bf' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" />
      <button type="submit" aria-label="<?php esc_attr_e( 'Buscar', 'bf' ); ?>">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
      </button>
    </form>
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Inicio', 'bf' ); ?></a>
    <a href="<?php echo esc_url( function_exists( 'bf_shop_url' ) ? bf_shop_url() : home_url( '/tienda' ) ); ?>"><?php esc_html_e( 'Tienda', 'bf' ); ?></a>
    <a href="#categorias"><?php esc_html_e( 'Categorías', 'bf' ); ?></a>
    <a href="#servicio"><?php esc_html_e( 'Servicio técnico', 'bf' ); ?></a>
    <a href="#contacto"><?php esc_html_e( 'Contacto', 'bf' ); ?></a>
    <a href="<?php echo esc_url( function_exists( 'bf_myaccount_url' ) ? bf_myaccount_url() : home_url( '/mi-cuenta' ) ); ?>"><?php esc_html_e( 'Mi cuenta', 'bf' ); ?></a>
    <a href="<?php echo esc_url( function_exists( 'bf_wishlist_url' ) ? bf_wishlist_url() : home_url( '/favoritos' ) ); ?>"><?php esc_html_e( 'Favoritos', 'bf' ); ?></a>
    <a href="<?php echo esc_url( function_exists( 'bf_cart_url' ) ? bf_cart_url() : home_url( '/carrito' ) ); ?>"><?php esc_html_e( 'Carrito', 'bf' ); ?></a>
  </div>
</header>
