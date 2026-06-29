<?php
/**
 * Componente: Toast container
 * Notificaciones premium (success, error, info)
 * Vacío al inicio, JS lo rellena dinámicamente.
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="bf-toast-container" id="bf-toast-container" data-toast-container aria-live="polite" aria-atomic="true"></div>

<template id="bf-toast-template">
  <div class="bf-toast" role="status" data-toast>
    <div class="bf-toast__icon" data-toast-icon></div>
    <div class="bf-toast__content">
      <p class="bf-toast__title" data-toast-title></p>
      <p class="bf-toast__message" data-toast-message></p>
    </div>
    <button type="button" class="bf-toast__close" data-toast-close aria-label="<?php esc_attr_e( 'Cerrar', 'bf' ); ?>">
      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
</template>
