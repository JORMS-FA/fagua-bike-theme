<?php
/**
 * AJAX endpoints premium — Búsqueda instantánea.
 *
 * Endpoint: wp_ajax_bf_search
 * Devuelve hasta 6 productos con: id, nombre, slug, precio, precio_html, imagen, categoría, sku.
 * Resalta el término en el nombre (se hace en cliente).
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_ajax_bf_search',        'bf_ajax_search' );
add_action( 'wp_ajax_nopriv_bf_search', 'bf_ajax_search' );

function bf_ajax_search() {
    check_ajax_referer( 'bf_nonce', 'nonce' );

    $term = isset( $_GET['q'] ) ? trim( sanitize_text_field( wp_unslash( $_GET['q'] ) ) ) : '';
    $cat  = isset( $_GET['cat'] ) ? absint( $_GET['cat'] ) : 0;

    if ( strlen( $term ) < 2 && ! $cat ) {
        wp_send_json_success( array( 'term' => $term, 'items' => array(), 'total' => 0, 'viewall' => '' ) );
    }

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 6,
        'no_found_rows'  => true,
        'orderby'        => 'relevance',
        'order'          => 'DESC',
    );

    if ( $term ) {
        $args['s'] = $term;
    } else {
        $args['post__in'] = array( 0 ); // vacío si no hay término
    }

    if ( $cat ) {
        $args['tax_query'] = array( array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $cat,
        ) );
    }

    // Si no hay término, devolver productos destacados
    if ( ! $term ) {
        $args = array(
            'post_type'           => 'product',
            'post_status'         => 'publish',
            'posts_per_page'      => 6,
            'no_found_rows'       => true,
            'orderby'             => 'date',
            'order'               => 'DESC',
            'ignore_sticky_posts' => true,
            'tax_query'           => $cat ? array( array(
                'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $cat,
            ) ) : array(),
        );
    }

    $q = new WP_Query( $args );
    $items = array();

    if ( $q->have_posts() ) {
        while ( $q->have_posts() ) { $q->the_post();
            $pid = get_the_ID();
            $p   = wc_get_product( $pid );
            if ( ! $p || 'publish' !== get_post_status( $pid ) ) continue;

            $img_id = $p->get_image_id();
            $thumb  = $img_id ? wp_get_attachment_image( $img_id, 'bf-card', false, array( 'loading' => 'lazy' ) ) : '';
            if ( ! $thumb ) {
                $thumb = '<div class="bf-product__placeholder" aria-hidden="true">' .
                  '<svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="17" r="4"/><circle cx="18" cy="17" r="4"/><path d="M6 17 L10 8 L14 17 M10 8 L14 8 M14 17 L18 17"/></svg></div>';
            }

            $terms = get_the_terms( $pid, 'product_cat' );
            $cat_name = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';

            $on_sale = $p->is_on_sale();
            $regular = (float) $p->get_regular_price();
            $price   = (float) $p->get_price();
            $price_html = $p->get_price_html();

            $items[] = array(
                'id'         => $pid,
                'name'       => get_the_title(),
                'slug'       => get_post_field( 'post_name', $pid ),
                'url'        => get_permalink( $pid ),
                'thumb'      => $thumb,
                'price'      => $price,
                'price_html' => $price_html,
                'regular'    => $regular,
                'on_sale'    => $on_sale,
                'category'   => $cat_name,
                'sku'        => $p->get_sku(),
                'rating'     => (float) $p->get_average_rating(),
                'review'     => (int) $p->get_review_count(),
                'in_stock'   => $p->is_in_stock(),
            );
        }
        wp_reset_postdata();
    }

    // Conteo total (para el "Ver todos los resultados")
    $count_args = $args;
    $count_args['posts_per_page'] = 1;
    $count_args['fields']         = 'ids';
    $count_args['no_found_rows']  = false;
    $count_q = new WP_Query( $count_args );
    $total = (int) $count_q->found_posts;
    wp_reset_postdata();

    $viewall = $term ? add_query_arg( array( 's' => $term, 'post_type' => 'product' ), home_url( '/' ) ) : ( function_exists( 'bf_shop_url' ) ? bf_shop_url() : home_url( '/tienda' ) );

    wp_send_json_success( array(
        'term'    => $term,
        'cat'     => $cat,
        'items'   => $items,
        'total'   => $total,
        'viewall' => $viewall,
    ) );
}

/* ============================================================
   Filtros AJAX (categoría, marca, precio, disponibilidad)
   Endpoint: wp_ajax_bf_filter
   Devuelve el HTML del grid + el conteo.
   ============================================================ */
add_action( 'wp_ajax_bf_filter',        'bf_ajax_filter' );
add_action( 'wp_ajax_nopriv_bf_filter', 'bf_ajax_filter' );

function bf_ajax_filter() {
    check_ajax_referer( 'bf_nonce', 'nonce' );

    $paged  = max( 1, (int) ( $_GET['paged'] ?? 1 ) );
    $cat    = isset( $_GET['cat'] ) ? array_filter( array_map( 'absint', (array) $_GET['cat'] ) ) : array();
    $marca  = isset( $_GET['marca'] ) ? array_filter( array_map( 'sanitize_title', (array) $_GET['marca'] ) ) : array();
    $min_p  = isset( $_GET['min_price'] ) ? (float) $_GET['min_price'] : 0;
    $max_p  = isset( $_GET['max_price'] ) ? (float) $_GET['max_price'] : 0;
    $stock  = isset( $_GET['in_stock'] ) ? (int) $_GET['in_stock'] : 0;
    $order  = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : '';
    $per_page = 12;

    $tax = array();
    if ( $cat ) {
        $tax[] = array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $cat, 'operator' => 'IN' );
    }
    if ( $marca ) {
        $tax[] = array( 'taxonomy' => 'pa_marca', 'field' => 'slug', 'terms' => $marca, 'operator' => 'IN' );
    }
    if ( $tax ) {
        $tax['relation'] = 'AND';
    }

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => $paged,
        'tax_query'      => $tax,
        'meta_query'     => array(),
    );

    if ( $min_p > 0 || $max_p > 0 ) {
        $args['meta_query'][] = array( 'key' => '_price', 'type' => 'NUMERIC' );
        if ( $min_p > 0 ) $args['meta_query'][] = array( 'key' => '_price', 'value' => $min_p, 'compare' => '>=', 'type' => 'NUMERIC' );
        if ( $max_p > 0 ) $args['meta_query'][] = array( 'key' => '_price', 'value' => $max_p, 'compare' => '<=', 'type' => 'NUMERIC' );
    }
    if ( $stock ) {
        $args['meta_query'][] = array( 'key' => '_stock_status', 'value' => 'instock' );
    }

    // Orden
    if ( $order === 'price' ) {
        $args['orderby']  = 'meta_value_num';
        $args['meta_key'] = '_price';
        $args['order']    = 'ASC';
    } elseif ( $order === 'price-desc' ) {
        $args['orderby']  = 'meta_value_num';
        $args['meta_key'] = '_price';
        $args['order']    = 'DESC';
    } elseif ( $order === 'date' ) {
        $args['orderby'] = 'date';
        $args['order']   = 'DESC';
    } elseif ( $order === 'popularity' ) {
        $args['orderby']  = 'meta_value_num';
        $args['meta_key'] = 'total_sales';
        $args['order']    = 'DESC';
    } else {
        $args['orderby'] = 'menu_order title';
        $args['order']   = 'ASC';
    }

    $q = new WP_Query( $args );
    $html = '';

    if ( $q->have_posts() ) {
        ob_start();
        woocommerce_product_loop_start();
        while ( $q->have_posts() ) { $q->the_post();
            wc_get_template_part( 'content', 'product' );
        }
        woocommerce_product_loop_end();
        $html = ob_get_clean();
        wp_reset_postdata();
    } else {
        $html = '<div class="bf-shop__empty bf-reveal"><h2>' . esc_html__( 'Sin resultados', 'bf' ) . '</h2><p>' . esc_html__( 'No encontramos productos con esos filtros. Probá ampliando la búsqueda.', 'bf' ) . '</p></div>';
    }

    // Paginación
    $pag = '';
    $total_pages = (int) $q->max_num_pages;
    if ( $total_pages > 1 ) {
        $pag = paginate_links( array(
            'base'      => add_query_arg( 'paged', '%#%' ),
            'format'    => '',
            'prev_text' => '←',
            'next_text' => '→',
            'total'     => $total_pages,
            'current'   => $paged,
            'type'      => 'array',
        ) );
    }

    wp_send_json_success( array(
        'html'        => $html,
        'count'       => (int) $q->found_posts,
        'total_pages' => $total_pages,
        'pagination'  => $pag,
    ) );
}
