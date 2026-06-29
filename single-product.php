<?php
/**
 * Single product — Bicicletería Fagua
 * Premium layout: gallery + sticky info, variation pills, tabs, related.
 *
 * @package BicicleteriaFagua
 */

if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

while ( have_posts() ) : the_post();
    global $product;
    if ( ! $product || ! $product instanceof WC_Product ) { the_content(); continue; }

    $rating  = $product->get_average_rating();
    $review  = $product->get_review_count();
    $on_sale = $product->is_on_sale();
    $regular = (float) $product->get_regular_price();
    $price   = (float) $product->get_price();
    $sku     = $product->get_sku();
    $stock   = $product->get_stock_status();
    $gallery = $product->get_gallery_image_ids();
    $thumb_id = (int) get_post_thumbnail_id();

    // Brands / cat
    $cat = get_the_terms( $product->get_id(), 'product_cat' );
    $cat_name = ( $cat && ! is_wp_error( $cat ) ) ? $cat[0]->name : '';
?>

<main class="bf-container">

  <?php woocommerce_breadcrumb(); ?>

  <article class="bf-product-page" id="bfProductPage">

    <!-- Gallery -->
    <div class="bf-product-gallery" id="bfGallery">
      <div class="bf-product-gallery__main" id="bfGalleryMain">
        <?php
        if ( $thumb_id ) {
            $full = wp_get_attachment_image_url( $thumb_id, 'large' );
            echo '<img id="bfGalleryImg" src="' . esc_url( $full ) . '" alt="' . esc_attr( get_the_title() ) . '" />';
        } else { ?>
          <div class="bf-product__placeholder" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="80" height="80" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="17" r="4"/><circle cx="18" cy="17" r="4"/><path d="M6 17 L10 8 L14 17 M10 8 L14 8 M14 17 L18 17"/></svg>
          </div>
        <?php } ?>
        <button type="button" class="bf-product-gallery__zoom" data-zoom aria-label="<?php esc_attr_e( 'Zoom', 'bf' ); ?>">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7V5a2 2 0 0 1 2-2h2M21 7V5a2 2 0 0 0-2-2h-2M3 17v2a2 2 0 0 0 2 2h2M21 17v2a2 2 0 0 1-2 2h-2"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
      </div>

      <?php if ( ! empty( $gallery ) || $thumb_id ) : ?>
        <div class="bf-product-gallery__thumbs" id="bfGalleryThumbs">
          <?php if ( $thumb_id ) : ?>
            <button type="button" class="bf-product-gallery__thumb is-active" data-thumb-id="<?php echo esc_attr( $thumb_id ); ?>" data-thumb-src="<?php echo esc_url( wp_get_attachment_image_url( $thumb_id, 'large' ) ); ?>">
              <?php echo wp_get_attachment_image( $thumb_id, 'thumbnail' ); ?>
            </button>
          <?php endif; ?>
          <?php foreach ( $gallery as $gid ) : ?>
            <button type="button" class="bf-product-gallery__thumb" data-thumb-id="<?php echo esc_attr( $gid ); ?>" data-thumb-src="<?php echo esc_url( wp_get_attachment_image_url( $gid, 'large' ) ); ?>">
              <?php echo wp_get_attachment_image( $gid, 'thumbnail' ); ?>
            </button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Info -->
    <div class="bf-product-info">
      <?php if ( $cat_name ) : ?>
        <a class="bf-product-info__cat" href="<?php echo esc_url( get_term_link( $cat[0] ) ); ?>"><?php echo esc_html( $cat_name ); ?></a>
      <?php endif; ?>

      <h1><?php the_title(); ?></h1>

      <?php if ( $review > 0 ) : ?>
        <div class="bf-product-info__rating">
          <span class="bf-product-info__stars" aria-hidden="true">
            <?php for ( $i = 1; $i <= 5; $i++ ) {
              echo $i <= round( $rating ) ? '★' : '☆';
            } ?>
          </span>
          <span><?php echo esc_html( number_format( $rating, 1 ) ); ?></span>
          <a href="#reviews" class="bf-product-info__rating-link"><?php printf( esc_html( _n( '%s reseña', '%s reseñas', $review, 'bf' ) ), esc_html( $review ) ); ?></a>
        </div>
      <?php endif; ?>

      <div class="bf-product-info__price">
        <?php if ( $on_sale && $regular > 0 ) :
          $pct = round( ( ( $regular - $price ) / $regular ) * 100 ); ?>
          <del><?php echo wc_price( $regular ); ?></del>
          <ins><?php echo wc_price( $price ); ?></ins>
          <span class="bf-product-info__price-discount">-<?php echo esc_html( $pct ); ?>%</span>
        <?php else : ?>
          <ins><?php echo wc_price( $price ); ?></ins>
        <?php endif; ?>
        <span style="font-size: 0.8rem; color: var(--bf-gray-400); font-weight: 400; margin-left: auto;">
          <?php if ( $sku ) : ?>SKU: <?php echo esc_html( $sku ); ?><?php endif; ?>
        </span>
      </div>

      <div class="bf-product-info__desc">
        <?php
        $short = $product->get_short_description();
        if ( $short ) {
            echo wp_kses_post( wpautop( $short ) );
        } else {
            echo wp_kses_post( wpautop( $product->get_description() ) );
        }
        ?>
      </div>

      <?php if ( $product->is_type( 'variable' ) ) : ?>
        <div class="bf-product-info__variations">
          <?php woocommerce_template_single_add_to_cart(); ?>
        </div>
      <?php else : ?>
        <div class="bf-product-info__cart">
          <form class="cart" method="post" enctype="multipart/form-data" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>">
            <div class="bf-qty">
              <button type="button" data-qty-minus aria-label="<?php esc_attr_e( 'Disminuir cantidad', 'bf' ); ?>">−</button>
              <input type="number" name="quantity" value="1" min="1" max="99" step="1" inputmode="numeric" />
              <button type="button" data-qty-plus aria-label="<?php esc_attr_e( 'Aumentar cantidad', 'bf' ); ?>">+</button>
            </div>
            <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="bf-btn bf-btn--primary bf-btn--lg single_add_to_cart_button">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3h2l2.4 12.4a2 2 0 0 0 2 1.6h8.2a2 2 0 0 0 2-1.6L21 7H6"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/></svg>
              <?php esc_html_e( 'Añadir al carrito', 'bf' ); ?>
            </button>
          </form>
        </div>
      <?php endif; ?>

      <div class="bf-product-info__features">
        <div class="bf-feature">
          <span class="bf-feature__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h13l3 5v5h-3a2 2 0 1 1-4 0H8a2 2 0 1 1-4 0H3z"/><circle cx="7" cy="17" r="1.5"/><circle cx="16" cy="17" r="1.5"/></svg>
          </span>
          <div class="bf-feature__text"><strong><?php esc_html_e( 'Envío gratis', 'bf' ); ?></strong><?php esc_html_e( 'En pedidos +$300k', 'bf' ); ?></div>
        </div>
        <div class="bf-feature">
          <span class="bf-feature__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12c0 5-4 9-9 9s-9-4-9-9 4-9 9-9c2.4 0 4.6 1 6.3 2.5"/><path d="M9 12l2 2 4-4"/></svg>
          </span>
          <div class="bf-feature__text"><strong><?php esc_html_e( 'Garantía 12 meses', 'bf' ); ?></strong><?php esc_html_e( 'Repuestos originales', 'bf' ); ?></div>
        </div>
        <div class="bf-feature">
          <span class="bf-feature__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
          </span>
          <div class="bf-feature__text"><strong><?php esc_html_e( 'Service en 24h', 'bf' ); ?></strong><?php esc_html_e( 'Diagnóstico gratuito', 'bf' ); ?></div>
        </div>
      </div>
    </div>
  </article>

  <!-- Tabs -->
  <section class="bf-product-tabs" id="tabs">
    <div class="bf-product-tabs__nav" role="tablist">
      <button class="is-active" data-tab="desc"><?php esc_html_e( 'Descripción', 'bf' ); ?></button>
      <button data-tab="specs"><?php esc_html_e( 'Especificaciones', 'bf' ); ?></button>
      <button data-tab="shipping"><?php esc_html_e( 'Envío y devoluciones', 'bf' ); ?></button>
      <button data-tab="reviews" id="reviews"><?php printf( esc_html__( 'Reseñas (%d)', 'bf' ), (int) $review ); ?></button>
    </div>
    <div class="bf-product-tabs__panel is-active" data-panel="desc">
      <?php echo wp_kses_post( wpautop( $product->get_description() ) ); ?>
    </div>
    <div class="bf-product-tabs__panel" data-panel="specs">
      <table>
        <tbody>
          <tr><th><?php esc_html_e( 'SKU', 'bf' ); ?></th><td><?php echo esc_html( $sku ?: 'N/A' ); ?></td></tr>
          <tr><th><?php esc_html_e( 'Marca', 'bf' ); ?></th><td><?php echo esc_html( $product->get_attribute( 'pa_marca' ) ?: 'N/A' ); ?></td></tr>
          <tr><th><?php esc_html_e( 'Categoría', 'bf' ); ?></th><td><?php echo wp_kses_post( wc_get_product_category_list( $product->get_id(), ', ', '', '' ) ?: 'N/A' ); ?></td></tr>
          <tr><th><?php esc_html_e( 'Stock', 'bf' ); ?></th><td><?php echo $stock === 'instock' ? esc_html__( 'Disponible', 'bf' ) : esc_html__( 'Agotado', 'bf' ); ?></td></tr>
        </tbody>
      </table>
    </div>
    <div class="bf-product-tabs__panel" data-panel="shipping">
      <p><?php esc_html_e( 'Envío gratis a todo Colombia en pedidos superiores a $300.000 COP. Entregas en 3-5 días hábiles en ciudades principales.', 'bf' ); ?></p>
      <p><?php esc_html_e( 'Devoluciones: 30 días desde la entrega. Producto sin uso y empaque original. Garantía de fábrica por 12 meses en componentes.', 'bf' ); ?></p>
    </div>
    <div class="bf-product-tabs__panel" data-panel="reviews">
      <?php comments_template( '/woocommerce/single-product-reviews.php' ); ?>
    </div>
  </section>

  <!-- Related -->
  <section class="bf-related">
    <div class="bf-container">
      <h2><?php esc_html_e( 'También te puede interesar', 'bf' ); ?></h2>
      <?php
      $related = new WP_Query( array(
          'post_type'      => 'product',
          'posts_per_page' => 4,
          'post__not_in'   => array( $product->get_id() ),
          'orderby'        => 'rand',
      ) );
      if ( $related->have_posts() ) : ?>
        <ul class="products bf-products">
          <?php while ( $related->have_posts() ) : $related->the_post();
            wc_get_template_part( 'content', 'product' );
          endwhile; wp_reset_postdata(); ?>
        </ul>
      <?php endif; ?>
    </div>
  </section>

</main>

<?php endwhile; ?>

<?php get_footer(); ?>
