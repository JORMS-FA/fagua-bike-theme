<?php
/**
 * One-shot: asignar imagen real a un producto.
 * Uso: /wp-admin/?bf_assign_image=1&pid=26&img=ultegra-r8100
 * Solo admin, expira tras primer uso (variable de opción).
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_init', function () {
    if ( ! is_admin() || empty( $_GET['bf_assign_image'] ) ) return;
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'No tienes permisos.', 'BF', array( 'response' => 403 ) );
    }
    $pid = absint( $_GET['pid'] ?? 0 );
    $slug = sanitize_file_name( $_GET['img'] ?? '' );
    if ( ! $pid || ! $slug ) {
        wp_die( 'Parámetros inválidos.', 'BF', array( 'response' => 400 ) );
    }

    $uploads = wp_upload_dir();
    $ext = 'png';
    $candidates = array(
        $uploads['path'] . '/' . $slug . '.' . $ext,
        $uploads['path'] . '/' . $slug,
        $uploads['basedir'] . '/2026/06/' . $slug . '.' . $ext,
        $uploads['basedir'] . '/2026/06/' . $slug,
    );
    $file = null;
    foreach ( $candidates as $c ) {
        if ( file_exists( $c ) ) { $file = $c; break; }
    }
    if ( ! $file ) {
        wp_die( 'Archivo no encontrado. Probé: <br><pre>' . esc_html( print_r( $candidates, true ) ) . '</pre>', 'BF', array( 'response' => 404 ) );
    }

    // Verificar tipo mime
    $ft = wp_check_filetype( basename( $file ) );
    if ( empty( $ft['type'] ) ) {
        $ft = array( 'ext' => 'png', 'type' => 'image/png' );
    }

    $attachment = array(
        'guid'           => $uploads['url'] . '/' . basename( $file ),
        'post_mime_type' => $ft['type'],
        'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file ) ),
        'post_content'   => '',
        'post_status'    => 'inherit',
        'post_excerpt'   => '',
    );
    $attach_id = wp_insert_attachment( $attachment, $file, 0 );
    if ( is_wp_error( $attach_id ) ) {
        wp_die( 'Error al crear attachment: ' . esc_html( $attach_id->get_error_message() ), 'BF', array( 'response' => 500 ) );
    }
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    wp_update_attachment_metadata( $attach_id, $attach_data );
    set_post_thumbnail( $pid, $attach_id );
    update_post_meta( $pid, '_product_image_gallery', '' );

    echo '<h1>OK</h1>';
    echo '<p>Producto ID: ' . esc_html( $pid ) . '</p>';
    echo '<p>Attachment ID: ' . esc_html( $attach_id ) . '</p>';
    echo '<p>Imagen: <code>' . esc_html( basename( $file ) ) . '</code></p>';
    echo '<p><a href="' . esc_url( get_permalink( $pid ) ) . '">Ver producto</a></p>';
    echo '<p><a href="' . esc_url( admin_url( 'post.php?post=' . $attach_id . '&action=edit' ) ) . '">Ver attachment</a></p>';
    exit;
} );
