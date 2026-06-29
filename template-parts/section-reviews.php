<?php
/**
 * Reviews section — Bicicletería Fagua
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$reviews = array(
  array(
    'name'    => 'Carlos Mendoza',
    'loc'     => 'Bogotá',
    'rating'  => 5,
    'text'    => 'Compré mi Shimano Ultegra y el servicio fue impecable. Asesoría técnica real, me ayudaron a elegir exactamente lo que mi bici necesitaba.',
    'product' => 'Shimano Ultegra R8100',
  ),
  array(
    'name'    => 'Andrea Ruiz',
    'loc'     => 'La Macarena',
    'rating'  => 5,
    'text'    => 'Llevo 3 años llevando mis bicis al taller. Son los únicos que de verdad saben y usan repuestos originales. 100% recomendados.',
    'product' => 'Service & tune-up',
  ),
  array(
    'name'    => 'Felipe Ortiz',
    'loc'     => 'Villavicencio',
    'rating'  => 5,
    'text'    => 'Pedí un cassette XT y llegó en 24h a Villavicencio. Empaque perfecto, factura clara, precio justo. La tienda online funciona como debe.',
    'product' => 'Shimano XT CS-M8100',
  ),
);
?>

<section class="bf-section bf-section--reviews" id="opiniones" aria-label="Opiniones de clientes">
  <div class="bf-container">
    <div class="bf-section-header bf-reveal">
      <div>
        <span class="bf-section-eyebrow"><?php esc_html_e( 'Confianza real', 'bf' ); ?></span>
        <h2 class="bf-section-title"><?php esc_html_e( 'Lo que dicen nuestros clientes', 'bf' ); ?></h2>
        <p class="bf-section-subtitle"><?php esc_html_e( 'Más de 3.000 ciclistas activos confían en nosotros.', 'bf' ); ?></p>
      </div>
    </div>

    <div class="bf-reviews">
      <?php foreach ( $reviews as $i => $r ) : ?>
        <article class="bf-review bf-reveal" style="transition-delay: <?php echo esc_attr( $i * 80 ); ?>ms;">
          <div class="bf-review__stars" aria-label="<?php echo esc_attr( $r['rating'] ); ?> de 5 estrellas">
            <?php for ( $s = 0; $s < 5; $s++ ) : ?>
              <svg viewBox="0 0 24 24" width="16" height="16" fill="<?php echo $s < $r['rating'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" aria-hidden="true"><path d="M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8l-6.2 3.2L7 14.2 2 9.3l6.9-1z"/></svg>
            <?php endfor; ?>
          </div>
          <p class="bf-review__text">"<?php echo esc_html( $r['text'] ); ?>"</p>
          <div class="bf-review__foot">
            <div class="bf-review__avatar" aria-hidden="true"><?php echo esc_html( mb_substr( $r['name'], 0, 1 ) ); ?></div>
            <div>
              <p class="bf-review__name"><?php echo esc_html( $r['name'] ); ?></p>
              <p class="bf-review__meta"><?php echo esc_html( $r['loc'] ); ?> · <span><?php echo esc_html( $r['product'] ); ?></span></p>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
