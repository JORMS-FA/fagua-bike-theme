<?php
/**
 * Promos section — Bicicletería Fagua
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<section class="bf-section bf-section--promos" id="promos" aria-label="Promociones">
  <div class="bf-container">
    <div class="bf-promo-grid">
      <article class="bf-promo bf-promo--lg bf-reveal">
        <div class="bf-promo__bg" style="background-image: linear-gradient(135deg, rgba(5,5,5,0.6) 0%, rgba(5,5,5,0.2) 100%), url('<?php echo esc_url( content_url( '/uploads/2026/06/hero-cyclist.jpg' ) ); ?>');"></div>
        <div class="bf-promo__content">
          <span class="bf-promo__tag">Envío gratis</span>
          <h3 class="bf-promo__title">Compras sobre $500.000</h3>
          <p class="bf-promo__text">A todo Colombia. Repuestos y componentes con envío express 24-48h.</p>
          <a class="bf-btn bf-btn--primary" href="<?php echo esc_url( function_exists( 'bf_shop_url' ) ? bf_shop_url() : home_url( '/tienda' ) ); ?>">
            <?php esc_html_e( 'Aprovechar', 'bf' ); ?>
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </a>
        </div>
      </article>

      <article class="bf-promo bf-reveal" style="transition-delay: 80ms;">
        <div class="bf-promo__bg" style="background-image: linear-gradient(135deg, rgba(5,5,5,0.7) 0%, rgba(26,144,255,0.3) 100%), url('<?php echo esc_url( content_url( '/uploads/2026/06/taller-mecanico.jpg' ) ); ?>');"></div>
        <div class="bf-promo__content">
          <span class="bf-promo__tag">Service & tune-up</span>
          <h3 class="bf-promo__title">20% off primera revisión</h3>
          <p class="bf-promo__text">Mecánica profesional, repuestos originales, garantía por escrito.</p>
          <a class="bf-btn bf-btn--ghost" href="https://wa.me/573223652738" target="_blank" rel="noopener">
            <?php esc_html_e( 'Agendar por WhatsApp', 'bf' ); ?>
          </a>
        </div>
      </article>

      <article class="bf-promo bf-reveal" style="transition-delay: 160ms;">
        <div class="bf-promo__bg" style="background-image: linear-gradient(135deg, rgba(5,5,5,0.7) 0%, rgba(5,5,5,0.4) 100%), url('<?php echo esc_url( content_url( '/uploads/2026/06/grupo-rodando.jpg' ) ); ?>');"></div>
        <div class="bf-promo__content">
          <span class="bf-promo__tag">Bicis armadas</span>
          <h3 class="bf-promo__title">Financiación directa</h3>
          <p class="bf-promo__text">Lleva tu bici nueva pagando en cuotas. Sin banco, sin papeleo.</p>
          <a class="bf-btn bf-btn--ghost" href="#contacto">
            <?php esc_html_e( 'Consultar', 'bf' ); ?>
          </a>
        </div>
      </article>
    </div>
  </div>
</section>
