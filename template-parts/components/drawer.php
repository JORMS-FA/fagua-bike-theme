<?php
/**
 * Componente: Drawer genérico
 * Usado por: Mini Cart, Wishlist, Comparador
 *
 * Variables esperadas (en $args):
 *   string $id        — ID único (ej: 'cart', 'wishlist', 'compare')
 *   string $title     — Título del drawer
 *   string $subtitle  — Subtítulo opcional
 *   string $content   — HTML del body
 *   string $footer    — HTML del footer
 *   string $width     — 'sm' | 'md' | 'lg' (default 'md')
 *   string $position  — 'right' (default) | 'left'
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$id       = $args['id']       ?? 'drawer';
$title    = $args['title']    ?? '';
$subtitle = $args['subtitle'] ?? '';
$content  = $args['content']  ?? '';
$footer   = $args['footer']   ?? '';
$width    = $args['width']    ?? 'md';
$position = $args['position'] ?? 'right';
?>
<div class="bf-drawer" id="bf-drawer-<?php echo esc_attr( $id ); ?>" data-drawer="<?php echo esc_attr( $id ); ?>" data-position="<?php echo esc_attr( $position ); ?>" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="bf-drawer-<?php echo esc_attr( $id ); ?>-title">
  <div class="bf-drawer__backdrop" data-drawer-close></div>
  <aside class="bf-drawer__panel bf-drawer__panel--<?php echo esc_attr( $width ); ?>">
    <header class="bf-drawer__header">
      <div>
        <h2 class="bf-drawer__title" id="bf-drawer-<?php echo esc_attr( $id ); ?>-title"><?php echo esc_html( $title ); ?></h2>
        <?php if ( $subtitle ) : ?>
          <p class="bf-drawer__subtitle"><?php echo esc_html( $subtitle ); ?></p>
        <?php endif; ?>
      </div>
      <button type="button" class="bf-drawer__close" data-drawer-close aria-label="<?php esc_attr_e( 'Cerrar', 'bf' ); ?>">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </header>

    <div class="bf-drawer__body" data-drawer-body>
      <?php echo $content; ?>
    </div>

    <?php if ( $footer ) : ?>
      <footer class="bf-drawer__footer">
        <?php echo $footer; ?>
      </footer>
    <?php endif; ?>
  </aside>
</div>
