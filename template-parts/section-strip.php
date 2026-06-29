<?php
/**
 * Section: Strip de confianza — Bicicletería Fagua
 * Métodos de pago, envíos, garantía, WhatsApp.
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;
$wa = 'https://wa.me/573223652738?text=' . rawurlencode( 'Hola Bicicletería Fagua, necesito información sobre un producto.' );
?>

<section class="bf-section bf-section--tight bf-strip" aria-label="Información de confianza">
  <div class="bf-container">
    <div class="bf-strip__grid">

      <div class="bf-strip__item">
        <div class="bf-strip__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="6" width="20" height="14" rx="2"/>
            <path d="M2 10h20"/>
            <path d="M6 16h4"/>
          </svg>
        </div>
        <div>
          <p class="bf-strip__title">Paga como quieras</p>
          <p class="bf-strip__text">Efectivo · Nequi · Bancolombia · Tarjeta</p>
        </div>
      </div>

      <div class="bf-strip__item">
        <div class="bf-strip__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 7h13l3 4h2v6h-3"/>
            <circle cx="7.5" cy="17" r="2"/>
            <circle cx="17.5" cy="17" r="2"/>
          </svg>
        </div>
        <div>
          <p class="bf-strip__title">Envíos a toda Colombia</p>
          <p class="bf-strip__text">Recoge en tienda o recibe en tu casa</p>
        </div>
      </div>

      <div class="bf-strip__item">
        <div class="bf-strip__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2 L20 6 V12 C20 17 16 21 12 22 C8 21 4 17 4 12 V6 Z"/>
            <path d="M9 12 L11 14 L15 10"/>
          </svg>
        </div>
        <div>
          <p class="bf-strip__title">Garantía original</p>
          <p class="bf-strip__text">Productos 100% originales con factura</p>
        </div>
      </div>

      <a class="bf-strip__item bf-strip__item--wa" href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener">
        <div class="bf-strip__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor">
            <path d="M17.5 14.4c-.3-.1-1.7-.8-1.9-.9-.3-.1-.4-.1-.6.1-.2.3-.7.9-.8 1-.2.2-.3.2-.6.1-.3-.1-1.2-.4-2.3-1.4-.9-.8-1.4-1.7-1.6-2-.2-.3 0-.5.1-.6.1-.1.3-.3.4-.5.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5 0-.1-.6-1.5-.8-2-.2-.5-.5-.5-.6-.5h-.5c-.2 0-.5.1-.7.3-.2.3-.9.9-.9 2.2 0 1.3.9 2.6 1.1 2.8.1.2 1.8 2.8 4.4 3.9 1.6.7 2.2.7 3 .6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3z M12 2C6.5 2 2 6.5 2 12c0 1.8.5 3.5 1.3 5L2 22l5.2-1.3c1.4.8 3.1 1.2 4.8 1.2 5.5 0 10-4.5 10-10S17.5 2 12 2z"/>
          </svg>
        </div>
        <div>
          <p class="bf-strip__title">WhatsApp directo</p>
          <p class="bf-strip__text">+57 322 365 2738 · Respuesta inmediata</p>
        </div>
      </a>

    </div>
  </div>
</section>
