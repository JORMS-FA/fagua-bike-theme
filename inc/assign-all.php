<?php
/**
 * One-shot: asignar las 11 imágenes reales a los productos correctos.
 * Solo admin. Crea attachment y asigna featured image.
 * URL: /wp-admin/?bf_assign_all=1
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;
add_action( 'admin_init', function () {
    if ( ! is_admin() || empty( $_GET['bf_assign_all'] ) ) return;
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'No tienes permisos.', 'BF', array( 'response' => 403 ) );

    $map = array(
        'botella-750ml.png'        => 52,
        'maurten-gel.png'          => 50,
        'garmin-edge-1040.png'     => 48,
        'poc-procen-air.png'       => 44,
        'rapha-pro-team.png'       => 42,
        'continental-gp5000.png'   => 38,
        'sram-force.png'           => 36,
        'cadena-shimano-xtr.png'   => 34,
        'shimano-105-r7100.png'    => 30,
        'ultegra-r8100.png'        => 26,
        'giant-talon-4.png'        => 25,
    );

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $uploads = wp_upload_dir();
    $results = array();
    foreach ( $map as $file => $pid ) {
        $path = $uploads['basedir'] . '/2026/06/' . $file;
        if ( ! file_exists( $path ) ) { $results[] = "❌ $file: no existe en $path"; continue; }
        $ft = wp_check_filetype( basename( $path ) );
        if ( empty( $ft['type'] ) ) $ft = array( 'ext' => 'png', 'type' => 'image/png' );
        $attach_id = wp_insert_attachment( array(
            'guid'           => $uploads['url'] . '/2026/06/' . basename( $path ),
            'post_mime_type' => $ft['type'],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $path ) ),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ), $path, 0 );
        if ( is_wp_error( $attach_id ) ) { $results[] = "❌ $file: " . $attach_id->get_error_message(); continue; }
        $meta = wp_generate_attachment_metadata( $attach_id, $path );
        wp_update_attachment_metadata( $attach_id, $meta );
        set_post_thumbnail( $pid, $attach_id );
        update_post_meta( $pid, '_product_image_gallery', '' );
        $results[] = "✅ $file → producto $pid (attach $attach_id)";
    }

    echo '<h1>Resultados</h1><pre style="font:14px monospace;line-height:1.6">';
    echo implode( "\n", $results );
    echo '</pre>';
    echo '<p><a href="' . esc_url( admin_url( 'edit.php?post_type=product' ) ) . '">Ver productos</a></p>';
    exit;
} );
