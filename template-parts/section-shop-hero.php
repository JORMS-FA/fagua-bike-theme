<?php
/**
 * Shop hero — Bicicletería Fagua
 * Compact page header that mirrors the home hero style.
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$title       = is_product_taxonomy() ? single_term_title( '', false ) : get_the_title( wc_get_page_id( 'shop' ) );
$description = is_product_taxonomy() ? term_description() : get_the_excerpt( wc_get_page_id( 'shop' ) );
$total       = (int) ( $GLOBALS['bf_shop_total'] ?? 0 );
?>

<section class="bf-shop-hero">
  <div class="bf-shop-hero__bg" aria-hidden="true"></div>
  <div class="bf-container bf-shop-hero__inner bf-reveal">
    <span class="bf-section-eyebrow"><?php esc_html_e( 'Catálogo', 'bf' ); ?></span>
    <h1 class="bf-shop-hero__title"><?php echo esc_html( $title ); ?></h1>
    <?php if ( $description ) : ?>
      <p class="bf-shop-hero__lead"><?php echo wp_kses_post( wpautop( $description, false ) ); ?></p>
    <?php else : ?>
      <p class="bf-shop-hero__lead"><?php esc_html_e( 'Colección completa de componentes, accesorios y repuestos. Ingeniería para cada rodada.', 'bf' ); ?></p>
    <?php endif; ?>
    <div class="bf-shop-hero__meta">
      <span><?php printf( esc_html( _n( '%s producto disponible', '%s productos disponibles', $total, 'bf' ) ), '<strong>' . esc_html( number_format_i18n( $total ) ) . '</strong>' ); ?></span>
      <span class="bf-shop-hero__sep" aria-hidden="true">·</span>
      <span><?php esc_html_e( 'Envío gratis +$300k', 'bf' ); ?></span>
      <span class="bf-shop-hero__sep" aria-hidden="true">·</span>
      <span><?php esc_html_e( 'Garantía 12 meses', 'bf' ); ?></span>
    </div>
  </div>
</section>
