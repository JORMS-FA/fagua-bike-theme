<?php
/**
 * Componente: Búsqueda instantánea premium
 * Panel que se abre con la lupa del header (o `/` desde teclado).
 * Resultados AJAX con debounce, highlight, navegación por teclado.
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="bf-search" id="bf-search" data-search aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="bf-search-title">
  <div class="bf-search__backdrop" data-search-close aria-hidden="true"></div>
  <div class="bf-search__panel">
    <div class="bf-search__head">
      <label for="bf-search-input" class="bf-search__label" id="bf-search-title">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <span><?php esc_html_e( 'Buscar productos', 'bf' ); ?></span>
      </label>
      <div class="bf-search__input-wrap">
        <input
          type="search"
          id="bf-search-input"
          class="bf-search__input"
          data-search-input
          placeholder="<?php esc_attr_e( '¿Qué estás buscando?', 'bf' ); ?>"
          autocomplete="off"
          spellcheck="false"
          aria-label="<?php esc_attr_e( 'Buscar productos', 'bf' ); ?>"
          aria-controls="bf-search-results"
          aria-autocomplete="list"
        />
        <button type="button" class="bf-search__clear" data-search-clear aria-label="<?php esc_attr_e( 'Limpiar búsqueda', 'bf' ); ?>" hidden>
          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <kbd class="bf-search__kbd" data-search-kbd>ESC</kbd>
      </div>
    </div>
    <div class="bf-search__body" id="bf-search-results" data-search-body aria-live="polite" aria-busy="false">
      <div class="bf-search__hint" data-search-hint>
        <p class="bf-search__hint-title"><?php esc_html_e( 'Empieza a escribir para buscar', 'bf' ); ?></p>
        <ul class="bf-search__suggestions" data-search-suggestions>
          <?php
          // Categorías top como sugerencias
          $cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 6, 'orderby' => 'count', 'order' => 'DESC' ) );
          if ( ! is_wp_error( $cats ) && $cats ) {
            echo '<li class="bf-search__hint-label">' . esc_html__( 'Categorías populares', 'bf' ) . '</li>';
            foreach ( $cats as $c ) {
              printf(
                '<li><button type="button" data-search-suggestion="%1$s"><span class="bf-search__suggestion-dot"></span>%1$s <em>%2$d</em></button></li>',
                esc_html( $c->name ),
                intval( $c->count )
              );
            }
          }
          ?>
        </ul>
      </div>
      <ul class="bf-search__results" data-search-results hidden></ul>
      <div class="bf-search__empty" data-search-empty hidden>
        <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
        <p class="bf-search__empty-title" data-search-empty-title>Sin resultados</p>
        <p class="bf-search__empty-text" data-search-empty-text></p>
      </div>
      <div class="bf-search__skeleton" data-search-skeleton hidden>
        <?php for ( $i = 0; $i < 4; $i++ ) : ?>
          <div class="bf-search__sk-item">
            <div class="bf-search__sk-thumb"></div>
            <div class="bf-search__sk-lines">
              <div class="bf-search__sk-line bf-search__sk-line--lg"></div>
              <div class="bf-search__sk-line bf-search__sk-line--sm"></div>
            </div>
            <div class="bf-search__sk-price"></div>
          </div>
        <?php endfor; ?>
      </div>
      <div class="bf-search__foot" data-search-foot hidden>
        <a href="#" data-search-viewall>
          <?php esc_html_e( 'Ver todos los resultados', 'bf' ); ?>
          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
        <span class="bf-search__hint-keys">
          <kbd>↑</kbd><kbd>↓</kbd> <?php esc_html_e( 'navegar', 'bf' ); ?>
          <kbd>↵</kbd> <?php esc_html_e( 'abrir', 'bf' ); ?>
          <kbd>ESC</kbd> <?php esc_html_e( 'cerrar', 'bf' ); ?>
        </span>
      </div>
    </div>
  </div>
</div>
