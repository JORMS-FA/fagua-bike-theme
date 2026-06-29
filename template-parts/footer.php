<?php
/**
 * Footer — Bicicletería Fagua
 *
 * @package BicicleteriaFagua
 */

if ( ! defined( 'ABSPATH' ) ) exit;
$year = date( 'Y' );
?>

<footer class="bf-footer" id="contacto" role="contentinfo">
  <div class="bf-container">

    <div class="bf-footer__top">

      <div class="bf-footer__brand">
        <a class="bf-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
          <span class="bf-logo-mark" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="6" cy="17" r="4"/>
              <circle cx="18" cy="17" r="4"/>
              <path d="M6 17 L10 8 L14 17 M10 8 L14 8 M14 17 L18 17"/>
            </svg>
          </span>
          <span class="bf-logo-text">Fagua</span>
        </a>
        <p><?php esc_html_e( 'Bicicletería especializada en ruta, mountain bike y servicio técnico. Ingeniería y pasión sobre dos ruedas.', 'bf' ); ?></p>
        <div class="bf-footer__socials" aria-label="<?php esc_attr_e( 'Redes sociales', 'bf' ); ?>">
          <a href="#" aria-label="Instagram">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
          </a>
          <a href="#" aria-label="Facebook">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
          </a>
          <a href="#" aria-label="WhatsApp">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21l1.7-4.2A8 8 0 1 1 8 19.5z"/><path d="M9 9c.5 0 1 .2 1.4.6l.8 1c.3.4.3.9 0 1.3l-.4.5c.5 1 1.4 1.9 2.4 2.4l.5-.4c.4-.3.9-.3 1.3 0l1 .8c.4.4.6.9.6 1.4 0 1-1 2-2.3 2-3.4 0-7-3.6-7-7 0-1.3 1-2.3 2-2.3z"/></svg>
          </a>
          <a href="#" aria-label="YouTube">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="3"/><path d="M10 9l5 3-5 3z" fill="currentColor"/></svg>
          </a>
        </div>
      </div>

      <div class="bf-footer__col">
        <h4><?php esc_html_e( 'Tienda', 'bf' ); ?></h4>
        <?php
        if ( has_nav_menu( 'footer' ) ) {
          wp_nav_menu( array(
            'theme_location' => 'footer',
            'container'      => false,
            'depth'          => 1,
            'fallback_cb'    => false,
            'walker'         => new BF_Footer_Walker(),
          ) );
        } else { ?>
          <ul>
            <li><a href="<?php echo esc_url( function_exists( 'bf_shop_url' ) ? bf_shop_url() : home_url( '/tienda' ) ); ?>"><?php esc_html_e( 'Catálogo', 'bf' ); ?></a></li>
            <li><a href="#categorias"><?php esc_html_e( 'Categorías', 'bf' ); ?></a></li>
            <li><a href="#"><?php esc_html_e( 'Ofertas', 'bf' ); ?></a></li>
            <li><a href="#"><?php esc_html_e( 'Novedades', 'bf' ); ?></a></li>
          </ul>
        <?php } ?>
      </div>

      <div class="bf-footer__col">
        <h4><?php esc_html_e( 'Servicio', 'bf' ); ?></h4>
        <ul>
          <li><a href="#servicio"><?php esc_html_e( 'Service & tune-up', 'bf' ); ?></a></li>
          <li><a href="#"><?php esc_html_e( 'Armado profesional', 'bf' ); ?></a></li>
          <li><a href="#"><?php esc_html_e( 'Asesoría custom', 'bf' ); ?></a></li>
          <li><a href="#"><?php esc_html_e( 'Garantía y postventa', 'bf' ); ?></a></li>
        </ul>
      </div>

      <div class="bf-footer__col">
        <h4><?php esc_html_e( 'Contacto', 'bf' ); ?></h4>
        <ul>
          <li>
            <a class="bf-footer__contact" href="https://wa.me/573223652738" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'WhatsApp 322 365 2738', 'bf' ); ?>">
              <span class="bf-footer__contact-icon bf-footer__contact-icon--wa" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="#ffffff" aria-hidden="true"><path d="M.057 24l1.687-6.163a11.867 11.867 0 0 1-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 0 1 8.413 3.488 11.824 11.824 0 0 1 3.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 0 1-5.688-1.448L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884a9.86 9.86 0 0 0 1.51 5.26l-.999 3.648 3.978-.607zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
              </span>
              +57 322 365 2738
            </a>
          </li>
          <li>
            <a class="bf-footer__contact" href="mailto:fagua.bike@gmail.com" aria-label="<?php esc_attr_e( 'Email fagua.bike@gmail.com', 'bf' ); ?>">
              <span class="bf-footer__contact-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              </span>
              fagua.bike@gmail.com
            </a>
          </li>
          <li>
            <span class="bf-footer__contact">
              <span class="bf-footer__contact-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              </span>
              Calle 6 Cra. 5 - 8, La Macarena, Meta
            </span>
          </li>
        </ul>
      </div>

    </div>

    <div class="bf-footer__bottom">
      <span class="bf-footer__copy">© <?php echo esc_html( $year ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Todos los derechos reservados.', 'bf' ); ?></span>
      <span class="bf-footer__dev">
        <?php esc_html_e( 'Desarrollado por', 'bf' ); ?>
        <a href="https://github.com/JORMS-FA" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Perfil de GitHub de Jorman Fagua', 'bf' ); ?>">
          Jorman Fagua
          <svg class="bf-footer__gh" viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M12 .3a12 12 0 0 0-3.8 23.4c.6.1.8-.3.8-.6v-2.2c-3.3.7-4-1.4-4-1.4-.5-1.4-1.3-1.7-1.3-1.7-1.1-.7.1-.7.1-.7 1.2.1 1.8 1.2 1.8 1.2 1.1 1.8 2.8 1.3 3.5 1 .1-.8.4-1.3.8-1.6-2.7-.3-5.5-1.3-5.5-6 0-1.3.5-2.4 1.2-3.2-.1-.3-.5-1.5.1-3.2 0 0 1-.3 3.3 1.2a11.5 11.5 0 0 1 6 0c2.3-1.5 3.3-1.2 3.3-1.2.6 1.7.2 2.9.1 3.2.8.8 1.2 1.9 1.2 3.2 0 4.7-2.8 5.7-5.5 6 .4.4.8 1.1.8 2.2v3.3c0 .3.2.7.8.6A12 12 0 0 0 12 .3"/></svg>
        </a>
      </span>
      <span class="bf-footer__legal"><a href="#"><?php esc_html_e( 'Política de privacidad', 'bf' ); ?></a> <span aria-hidden="true">·</span> <a href="#"><?php esc_html_e( 'Términos', 'bf' ); ?></a></span>
    </div>

  </div>
</footer>

<?php
/**
 * Componentes reusables: Toast container + Mini Cart drawer.
 * Vacíos al inicio; JS los rellena dinámicamente.
 */
get_template_part( 'template-parts/components/toast' );
get_template_part( 'template-parts/components/search' );

$cart_url  = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/carrito' );
$checkout_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/finalizar-compra' );

ob_start();
?>
  <div class="bf-drawer-empty" data-cart-empty>
    <div class="bf-drawer-empty__icon" aria-hidden="true">
      <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>
    </div>
    <p class="bf-drawer-empty__title"><?php esc_html_e( 'Tu carrito está vacío', 'bf' ); ?></p>
    <p class="bf-drawer-empty__text"><?php esc_html_e( 'Aún no has agregado productos. Explora la tienda y encuentra tu próxima rodada.', 'bf' ); ?></p>
    <a class="bf-btn bf-btn--primary" href="<?php echo esc_url( function_exists( 'bf_shop_url' ) ? bf_shop_url() : home_url( '/tienda' ) ); ?>" data-drawer-close>
      <?php esc_html_e( 'Explorar tienda', 'bf' ); ?>
    </a>
  </div>

  <ul class="bf-drawer-list" data-cart-list hidden></ul>
<?php
$cart_content = ob_get_clean();

ob_start();
?>
  <div class="bf-drawer-summary" data-cart-summary hidden>
    <div class="bf-drawer-summary__row">
      <span><?php esc_html_e( 'Subtotal', 'bf' ); ?></span>
      <span data-cart-subtotal>$0</span>
    </div>
    <div class="bf-drawer-summary__row">
      <span><?php esc_html_e( 'Envío', 'bf' ); ?></span>
      <span data-cart-shipping><?php esc_html_e( 'Calculado al checkout', 'bf' ); ?></span>
    </div>
    <div class="bf-drawer-summary__row bf-drawer-summary__row--total">
      <span><?php esc_html_e( 'Total', 'bf' ); ?></span>
      <span data-cart-total>$0</span>
    </div>
  </div>
  <a class="bf-btn bf-btn--primary bf-btn--block" href="<?php echo esc_url( $checkout_url ); ?>">
    <?php esc_html_e( 'Ir al checkout', 'bf' ); ?>
    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
  </a>
  <a class="bf-btn bf-btn--quiet bf-btn--block" href="<?php echo esc_url( $cart_url ); ?>" data-drawer-close style="margin-top:8px;">
    <?php esc_html_e( 'Ver carrito completo', 'bf' ); ?>
  </a>
<?php
$cart_footer = ob_get_clean();

get_template_part( 'template-parts/components/drawer', null, array(
  'id'       => 'cart',
  'title'    => __( 'Tu carrito', 'bf' ),
  'subtitle' => __( 'Revisa y ajusta antes de pagar.', 'bf' ),
  'content'  => $cart_content,
  'footer'   => $cart_footer,
  'width'    => 'md',
) );
?>

<?php
/**
 * Minimal walker for footer menu to render <ul><li>.
 */
if ( ! class_exists( 'BF_Footer_Walker' ) ) {
  class BF_Footer_Walker extends Walker_Nav_Menu {
    public function start_lvl( &$output, $depth = 0, $args = null ) { $output .= '<ul>'; }
    public function end_lvl( &$output, $depth = 0, $args = null ) { $output .= '</ul>'; }
    public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
      $item = $data_object;
      $output .= '<li><a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a></li>';
    }
    public function end_el( &$output, $data_object, $depth = 0, $args = null ) {}
  }
}
