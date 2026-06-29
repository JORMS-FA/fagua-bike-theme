<?php
/**
 * Seed runner — runs the seeder via WP REST endpoint, then self-destructs.
 * Visit /wp-admin/?bf_seed=1 once as admin, output goes to the page.
 *
 * Auto-deactivates after first run via option flag.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_init', function () {
    if ( ! current_user_can( 'manage_options' ) ) return;
    if ( ! isset( $_GET['bf_seed'] ) ) return;

    $flag = get_option( 'bf_seed_done_v1', 0 );
    if ( $flag ) {
        wp_die( 'Seed ya ejecutado (flag activo). Borra la opción bf_seed_done_v1 para correrlo de nuevo.' );
    }

    include_once get_stylesheet_directory() . '/_seed.php';

    update_option( 'bf_seed_done_v1', 1 );
    wp_die( 'Seed completado. Recarga tu home para ver los productos.' );
} );
