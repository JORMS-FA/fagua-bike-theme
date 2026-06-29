<?php
/**
 * Section: Marcas — Bicicletería Fagua
 * Strip de marcas premium con logos estilizados monocromáticos.
 * Inspirado en Rapha, Canyon, Specialized.
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// Marcas con SVG path embebido (wordmark estilizado) + fallback texto
$brands = array(
  array( 'name' => 'SHIMANO',     'tag' => 'transmission' ),
  array( 'name' => 'SRAM',        'tag' => 'drivetrain' ),
  array( 'name' => 'GW',          'tag' => 'bikes' ),
  array( 'name' => 'MAXXIS',      'tag' => 'tires' ),
  array( 'name' => 'FOX',         'tag' => 'suspension' ),
  array( 'name' => 'CONTINENTAL', 'tag' => 'tires' ),
  array( 'name' => 'RACEFACE',    'tag' => 'components' ),
  array( 'name' => 'SHIMANO 105', 'tag' => 'groupset' ),
);
?>

<section class="bf-section bf-section--tight bf-brands" aria-label="Marcas que vendemos">
  <div class="bf-container">

    <header class="bf-section__header bf-reveal" style="margin-bottom: 2rem;">
      <p class="bf-section__eyebrow">Marcas premium</p>
      <h2 class="bf-section__title">Las marcas que ruedan con nosotros</h2>
    </header>

    <div class="bf-brands__grid bf-reveal" data-reveal-delay="80">
      <?php foreach ( $brands as $b ) : ?>
        <div class="bf-brand" data-tag="<?php echo esc_attr( $b['tag'] ); ?>">
          <span class="bf-brand__name"><?php echo esc_html( $b['name'] ); ?></span>
        </div>
      <?php endforeach; ?>
    </div>

    <p class="bf-brands__note bf-reveal" data-reveal-delay="160">
      Repuestos <strong>100% originales</strong> con garantía de fabricante.
    </p>

  </div>
</section>
