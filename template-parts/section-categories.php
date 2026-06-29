<?php
/**
 * Section: Categorías — Bicicletería Fagua
 * Primera sección de la home. Cards visuales con iconos SVG grandes
 * estilo ecommerce profesional (Apple, Specialized, Trek).
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;
$shop_url = function_exists('bf_shop_url') ? bf_shop_url() : home_url('/tienda');
?>

<section class="bf-section bf-categories" id="categorias" aria-label="Categorías de productos">
  <div class="bf-container">

    <header class="bf-section__header bf-reveal">
      <p class="bf-section__eyebrow">Compra por categoría</p>
      <h2 class="bf-section__title">¿Qué estás buscando hoy?</h2>
      <p class="bf-section__lead">Bicicletas, componentes y servicio técnico. Todo lo que necesitas para rodar con confianza.</p>
    </header>

    <div class="bf-categories__grid bf-reveal" data-reveal-delay="80">

      <a class="bf-cat-card" href="<?php echo esc_url( $shop_url . '?categoria=bicicletas' ); ?>">
        <div class="bf-cat-card__icon" aria-hidden="true">
          <svg viewBox="0 0 64 64" width="48" height="48" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="18" cy="42" r="11"/>
            <circle cx="46" cy="42" r="11"/>
            <path d="M18 42 L26 24 L40 24 L46 42"/>
            <path d="M26 24 L32 14 L40 24"/>
            <circle cx="32" cy="14" r="2.5" fill="currentColor"/>
            <path d="M40 24 L52 18"/>
          </svg>
        </div>
        <h3 class="bf-cat-card__title">Bicicletas</h3>
        <p class="bf-cat-card__meta">GW, Trek, Specialized</p>
        <span class="bf-cat-card__cta">Ver catálogo →</span>
      </a>

      <a class="bf-cat-card" href="<?php echo esc_url( $shop_url . '?categoria=componentes' ); ?>">
        <div class="bf-cat-card__icon" aria-hidden="true">
          <svg viewBox="0 0 64 64" width="48" height="48" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="32" cy="32" r="6"/>
            <path d="M32 14 L32 22 M32 42 L32 50 M14 32 L22 32 M42 32 L50 32"/>
            <path d="M19 19 L24 24 M40 40 L45 45 M19 45 L24 40 M40 24 L45 19"/>
          </svg>
        </div>
        <h3 class="bf-cat-card__title">Componentes</h3>
        <p class="bf-cat-card__meta">Shimano, SRAM, FSA</p>
        <span class="bf-cat-card__cta">Ver catálogo →</span>
      </a>

      <a class="bf-cat-card" href="<?php echo esc_url( $shop_url . '?categoria=ruedas' ); ?>">
        <div class="bf-cat-card__icon" aria-hidden="true">
          <svg viewBox="0 0 64 64" width="48" height="48" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="32" cy="32" r="20"/>
            <circle cx="32" cy="32" r="4"/>
            <path d="M32 12 L32 28 M32 36 L32 52 M12 32 L28 32 M36 32 L52 32"/>
            <path d="M17 17 L25 25 M39 39 L47 47 M17 47 L25 39 M39 25 L47 17"/>
          </svg>
        </div>
        <h3 class="bf-cat-card__title">Ruedas y Llantas</h3>
        <p class="bf-cat-card__meta">Maxxis, Continental, Pirelli</p>
        <span class="bf-cat-card__cta">Ver catálogo →</span>
      </a>

      <a class="bf-cat-card" href="<?php echo esc_url( $shop_url . '?categoria=accesorios' ); ?>">
        <div class="bf-cat-card__icon" aria-hidden="true">
          <svg viewBox="0 0 64 64" width="48" height="48" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 36 L32 18 L50 36 L46 50 L18 50 Z"/>
            <path d="M28 38 L36 38 M32 34 L32 42"/>
            <path d="M40 28 L44 24"/>
          </svg>
        </div>
        <h3 class="bf-cat-card__title">Cascos y Seguridad</h3>
        <p class="bf-cat-card__meta">POC, Giro, Bell</p>
        <span class="bf-cat-card__cta">Ver catálogo →</span>
      </a>

      <a class="bf-cat-card" href="<?php echo esc_url( $shop_url . '?categoria=indumentaria' ); ?>">
        <div class="bf-cat-card__icon" aria-hidden="true">
          <svg viewBox="0 0 64 64" width="48" height="48" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 14 L42 14 L46 24 L42 30 L42 50 L22 50 L22 30 L18 24 Z"/>
            <path d="M28 14 L28 18 M36 14 L36 18"/>
          </svg>
        </div>
        <h3 class="bf-cat-card__title">Indumentaria</h3>
        <p class="bf-cat-card__meta">Rapha, MAAP, Castelli</p>
        <span class="bf-cat-card__cta">Ver catálogo →</span>
      </a>

      <a class="bf-cat-card" href="#servicio">
        <div class="bf-cat-card__icon" aria-hidden="true">
          <svg viewBox="0 0 64 64" width="48" height="48" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <path d="M40 12 L52 24 L42 34 L40 32 L24 48 L16 40 L32 24 L30 22 Z"/>
            <path d="M16 40 L12 52 L24 48"/>
          </svg>
        </div>
        <h3 class="bf-cat-card__title">Servicio Técnico</h3>
        <p class="bf-cat-card__meta">Taller, mantenimiento, garantía</p>
        <span class="bf-cat-card__cta">Agendar →</span>
      </a>

    </div>

  </div>
</section>
