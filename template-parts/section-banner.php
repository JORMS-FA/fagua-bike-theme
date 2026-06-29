<?php
/**
 * Banner "Tu bici, en manos expertas" — con foto de taller de fondo
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$bg_path = get_stylesheet_directory() . '/assets/images/taller-mecanico.jpg';
$bg_uri  = file_exists( $bg_path ) ? get_stylesheet_directory_uri() . '/assets/images/taller-mecanico.jpg' : '';
?>

<section class="bf-section" id="servicio">
  <div class="bf-container">
    <div class="bf-banner bf-banner--taller bf-reveal" style="<?php echo $bg_uri ? '--bf-banner-bg:url(' . esc_url( $bg_uri ) . ');' : ''; ?>">
      <div class="bf-banner__overlay" aria-hidden="true"></div>
      <div class="bf-banner__content">
        <span class="bf-section-eyebrow"><?php esc_html_e( 'Servicio técnico', 'bf' ); ?></span>
        <h2>Tu bici, en manos expertas.</h2>
        <p>Diagnóstico gratuito en 24h. Tune-up completo, armado profesional, suspensión, frenos, transmisión. Mecánicos certificados y repuestos originales.</p>
        <div class="bf-banner__actions">
          <a class="bf-btn bf-btn--primary bf-btn--lg" href="#contacto">
            <?php esc_html_e( 'Agendar service', 'bf' ); ?>
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </a>
          <a class="bf-btn bf-btn--ghost bf-btn--lg" href="<?php echo esc_url( function_exists( 'bf_shop_url' ) ? bf_shop_url() : home_url( '/tienda' ) ); ?>">
            <?php esc_html_e( 'Ver repuestos', 'bf' ); ?>
          </a>
        </div>
        <div class="bf-banner__chips" aria-label="<?php esc_attr_e( 'Garantías', 'bf' ); ?>">
          <span class="bf-banner__chip"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg> <?php esc_html_e( 'Diagnóstico 24h', 'bf' ); ?></span>
          <span class="bf-banner__chip"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg> <?php esc_html_e( 'Repuestos originales', 'bf' ); ?></span>
          <span class="bf-banner__chip"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg> <?php esc_html_e( 'Garantía 90 días', 'bf' ); ?></span>
        </div>
      </div>
    </div>
  </div>
</section>
