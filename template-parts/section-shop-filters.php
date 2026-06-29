<?php
/**
 * Shop filters — sidebar premium, AJAX-driven, sin recargas.
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$cats = get_terms( array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'parent'     => 0,
) );
$brands = get_terms( array( 'taxonomy' => 'pa_marca', 'hide_empty' => true ) );

$shop_url = function_exists( 'bf_shop_url' ) ? bf_shop_url() : home_url( '/tienda' );

// Rangos de precio predefinidos (COP)
$price_ranges = array(
    array( 'min' => 0,    'max' => 500000,  'label' => __( 'Menos de $500k', 'bf' ) ),
    array( 'min' => 500000, 'max' => 2000000,  'label' => __( '$500k – $2M', 'bf' ) ),
    array( 'min' => 2000000, 'max' => 5000000,  'label' => __( '$2M – $5M', 'bf' ) ),
    array( 'min' => 5000000, 'max' => 0,       'label' => __( 'Más de $5M', 'bf' ) ),
);
?>

<div class="bf-filters" data-bf-filters>

  <div class="bf-filters__group">
    <h3 class="bf-filters__title"><?php esc_html_e( 'Categorías', 'bf' ); ?></h3>
    <ul class="bf-filters__list" data-filter-group="cat">
      <li>
        <button type="button" class="bf-filters__item is-active" data-filter-cat="0">
          <?php esc_html_e( 'Todas', 'bf' ); ?>
        </button>
      </li>
      <?php foreach ( $cats as $c ) : ?>
        <li>
          <button type="button" class="bf-filters__item" data-filter-cat="<?php echo esc_attr( $c->term_id ); ?>">
            <?php echo esc_html( $c->name ); ?>
            <span class="bf-filters__count"><?php echo intval( $c->count ); ?></span>
          </button>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <?php if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) : ?>
    <div class="bf-filters__group">
      <h3 class="bf-filters__title"><?php esc_html_e( 'Marca', 'bf' ); ?></h3>
      <ul class="bf-filters__check" data-filter-group="marca">
        <?php foreach ( $brands as $b ) : ?>
          <li>
            <label class="bf-filters__check-label">
              <input type="checkbox" name="filter_marca" value="<?php echo esc_attr( $b->slug ); ?>" data-filter-marca="<?php echo esc_attr( $b->slug ); ?>" />
              <span class="bf-filters__check-box" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              </span>
              <span class="bf-filters__check-text"><?php echo esc_html( $b->name ); ?></span>
              <span class="bf-filters__count"><?php echo intval( $b->count ); ?></span>
            </label>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="bf-filters__group">
    <h3 class="bf-filters__title"><?php esc_html_e( 'Precio', 'bf' ); ?></h3>
    <ul class="bf-filters__list" data-filter-group="price">
      <li>
        <button type="button" class="bf-filters__item is-active" data-filter-price="0,0">
          <?php esc_html_e( 'Cualquier precio', 'bf' ); ?>
        </button>
      </li>
      <?php foreach ( $price_ranges as $r ) : ?>
        <li>
          <button type="button" class="bf-filters__item" data-filter-price="<?php echo esc_attr( $r['min'] . ',' . $r['max'] ); ?>">
            <?php echo esc_html( $r['label'] ); ?>
          </button>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="bf-filters__group">
    <h3 class="bf-filters__title"><?php esc_html_e( 'Disponibilidad', 'bf' ); ?></h3>
    <label class="bf-filters__check-label">
      <input type="checkbox" data-filter-stock />
      <span class="bf-filters__check-box" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
      </span>
      <span class="bf-filters__check-text"><?php esc_html_e( 'Solo en stock', 'bf' ); ?></span>
    </label>
  </div>

  <button type="button" class="bf-btn bf-btn--ghost bf-btn--sm bf-filters__reset" data-filter-reset>
    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
    <?php esc_html_e( 'Limpiar filtros', 'bf' ); ?>
  </button>
</div>

<!-- Chips de filtros activos -->
<div class="bf-filter-chips" data-filter-chips hidden aria-live="polite"></div>
