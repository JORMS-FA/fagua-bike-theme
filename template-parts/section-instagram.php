<?php
/**
 * Instagram strip — Bicicletería Fagua
 * Muestra el grid de imágenes que ya tenemos.
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$ig_imgs = array(
  content_url( '/uploads/2026/06/hero-cyclist.jpg' ),
  content_url( '/uploads/2026/06/ciclista-noche.jpg' ),
  content_url( '/uploads/2026/06/grupo-rodando.jpg' ),
  content_url( '/uploads/2026/06/mtb-bosque.jpg' ),
  content_url( '/uploads/2026/06/taller-mecanico.jpg' ),
  content_url( '/uploads/2026/06/repuestos-mesa.jpg' ),
);
?>

<section class="bf-section bf-section--instagram" id="instagram" aria-label="Síguenos en Instagram">
  <div class="bf-container">
    <div class="bf-section-header bf-reveal">
      <div>
        <span class="bf-section-eyebrow"><?php esc_html_e( '@bicicleteriafagua', 'bf' ); ?></span>
        <h2 class="bf-section-title"><?php esc_html_e( 'Síguenos en Instagram', 'bf' ); ?></h2>
        <p class="bf-section-subtitle"><?php esc_html_e( 'Rutas, mecánica y clientes rodando. Únete a la comunidad.', 'bf' ); ?></p>
      </div>
      <a class="bf-section-link" href="https://instagram.com" target="_blank" rel="noopener">
        <?php esc_html_e( 'Ver Instagram', 'bf' ); ?>
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17L17 7M9 7h8v8"/></svg>
      </a>
    </div>

    <div class="bf-ig-grid">
      <?php foreach ( $ig_imgs as $i => $src ) : ?>
        <a class="bf-ig-tile bf-reveal" href="https://instagram.com" target="_blank" rel="noopener" style="transition-delay: <?php echo esc_attr( $i * 60 ); ?>ms;" aria-label="Ver en Instagram">
          <img src="<?php echo esc_url( $src ); ?>" alt="" loading="lazy" />
          <div class="bf-ig-tile__overlay" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.4a4 4 0 1 1-8 0 4 4 0 0 1 8 0z"/><circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/></svg>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
