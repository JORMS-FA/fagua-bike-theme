<?php
/**
 * Hero section — Bicicletería Fagua
 * Compacto: muestra productos desde el primer scroll.
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$hero_fallback = 'data:image/svg+xml;utf8,' . rawurlencode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 900" preserveAspectRatio="xMidYMid slice"><defs><radialGradient id="g1" cx="50%" cy="40%" r="70%"><stop offset="0%" stop-color="#0a2540" stop-opacity="0.85"/><stop offset="100%" stop-color="#050505" stop-opacity="1"/></radialGradient><linearGradient id="g2" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#1a90ff" stop-opacity="0.12"/><stop offset="100%" stop-color="#1a90ff" stop-opacity="0"/></linearGradient></defs><rect width="1920" height="900" fill="url(#g1)"/><rect width="1920" height="900" fill="url(#g2)"/></svg>' );

// Detectar imagen de hero disponible
$hero_path = get_stylesheet_directory() . '/assets/images/hero-cyclist.jpg';
$bg_url = file_exists( $hero_path ) ? get_stylesheet_directory_uri() . '/assets/images/hero-cyclist.jpg' : $hero_fallback;

// Productos destacados para el hero
$hero_products = new WP_Query( array(
  'post_type'      => 'product',
  'posts_per_page' => 3,
  'meta_key'       => 'total_sales',
  'orderby'        => 'meta_value_num',
  'order'          => 'DESC',
  'tax_query'      => array(),
  'no_found_rows'  => true,
) );
?>

<section class="bf-hero" aria-label="Hero principal" style="--bf-hero-bg: url('<?php echo esc_url( $bg_url ); ?>');">
  <div class="bf-hero__bg" aria-hidden="true"></div>
  <div class="bf-hero__overlay" aria-hidden="true"></div>

  <div class="bf-container bf-hero__inner">
    <div class="bf-hero__content bf-reveal">

      <span class="bf-hero__eyebrow">
        <span class="bf-hero__eyebrow-dot"></span>
        <?php esc_html_e( 'Nueva temporada · 2026', 'bf' ); ?>
      </span>

      <h1 class="bf-hero__title">
        <?php esc_html_e( 'Ingeniería sobre', 'bf' ); ?><br/>
        <em><?php esc_html_e( 'dos ruedas.', 'bf' ); ?></em>
      </h1>

      <p class="bf-hero__lead">
        <?php esc_html_e( 'Bicicletas, componentes y servicio técnico especializado. Repuestos originales y asesoría real.', 'bf' ); ?>
      </p>

      <div class="bf-hero__actions">
        <a class="bf-btn bf-btn--primary bf-btn--lg" href="<?php echo esc_url( function_exists( 'bf_shop_url' ) ? bf_shop_url() : home_url( '/tienda' ) ); ?>">
          <?php esc_html_e( 'Comprar ahora', 'bf' ); ?>
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <a class="bf-btn bf-btn--ghost bf-btn--lg" href="#servicio">
          <?php esc_html_e( 'Servicio técnico', 'bf' ); ?>
        </a>
      </div>

    </div>

    <aside class="bf-hero__side bf-reveal" data-reveal-delay="120">
      <div class="bf-hero__side-card">
        <div class="bf-hero__side-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
          <p class="bf-hero__side-label"><?php esc_html_e( 'Visítanos', 'bf' ); ?></p>
          <p class="bf-hero__side-text"><?php esc_html_e( 'La Macarena, Meta', 'bf' ); ?></p>
        </div>
      </div>
      <div class="bf-hero__side-card">
        <div class="bf-hero__side-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5h2l3.6 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/></svg>
        </div>
        <div>
          <p class="bf-hero__side-label"><?php esc_html_e( '+250 productos', 'bf' ); ?></p>
          <p class="bf-hero__side-text"><?php esc_html_e( 'Marcas premium', 'bf' ); ?></p>
        </div>
      </div>
    </aside>
  </div>
</section>
