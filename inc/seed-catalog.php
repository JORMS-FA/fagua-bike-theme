<?php
/**
 * Seeder del catálogo profesional de demostración.
 *
 * - Limpia los productos actuales (los 18 de muestra)
 * - Carga los productos desde los JSON en inc/catalog/
 * - Crea categorías, marcas, atributos si no existen
 * - Asigna imágenes de los 11 productos reales ya en disco
 * - Idempotente: borra lo previo y reemplaza
 *
 * Uso: /wp-admin/?bf_seed_catalog=1
 *
 * @package BicicleteriaFagua
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function () {
    if ( empty( $_GET['bf_seed_catalog'] ) ) return;

    // Auth: token SHA256 o admin logueado
    $token = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
    $expected = hash( 'sha256', 'bf-seed-2026' );
    $is_authorized = ( $token === $expected ) || current_user_can( 'manage_options' );
    if ( ! $is_authorized ) {
        $hint = '?bf_seed_catalog=1&key=' . $expected;
        wp_die( 'Token inválido. URL correcta: ' . esc_html( $hint ), 'BF', array( 'response' => 403 ) );
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';

    $theme_dir = get_stylesheet_directory();
    $catalog_dir = $theme_dir . '/inc/catalog/';

    echo '<pre style="font:13px monospace;line-height:1.6;background:#141414;color:#fafafa;padding:20px">';
    echo '<h1 style="color:#1a90ff">🌱 Seeder de catálogo — FAGUA</h1>';
    echo '<p>Borrando productos de muestra existentes...</p>';

    // 1. Borrar todos los productos existentes
    $existing = get_posts( array(
        'post_type'      => 'product',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ) );
    foreach ( $existing as $pid ) {
        wp_delete_post( $pid, true );
    }
    echo 'Productos borrados: ' . count( $existing ) . PHP_EOL;

    // 2. Asegurar categorías
    $categories = array(
        'Bicicletas'   => 'bicicletas',
        'Repuestos'    => 'repuestos',
        'Componentes'  => 'componentes',
        'Indumentaria' => 'indumentaria',
        'Accesorios'   => 'accesorios',
        'Llantas'      => 'llantas',
    );
    $cat_ids = array();
    foreach ( $categories as $name => $slug ) {
        $term = get_term_by( 'slug', $slug, 'product_cat' );
        if ( ! $term ) {
            $term = wp_insert_term( $name, 'product_cat', array( 'slug' => $slug ) );
            if ( is_wp_error( $term ) ) { echo "ERR cat $name\n"; continue; }
            $term = get_term( $term['term_id'], 'product_cat' );
        }
        $cat_ids[ $slug ] = $term->term_id;
        echo "Cat ok: $name ({$term->term_id})\n";
    }

    // 3. Asegurar atributo pa_marca (ya existe, pero por si acaso)
    if ( ! taxonomy_exists( 'pa_marca' ) ) {
        register_taxonomy( 'pa_marca', 'product', array( 'label' => 'Marca', 'hierarchical' => false ) );
    }

    // 4. Cargar JSONs
    $files = array(
        'gw-bikes.json'         => 20,  // esperado
        'shimano-parts-1.json'  => 36,
        'shimano-parts-2.json'  => 64,
        'sram-parts.json'       => 60,
        'tires.json'            => 40,
        'accessories.json'      => 30,
    );

    $all_products = array();
    foreach ( $files as $filename => $expected ) {
        $path = $catalog_dir . $filename;
        if ( ! file_exists( $path ) ) { echo "⚠ Falta $filename\n"; continue; }
        $json = file_get_contents( $path );
        $items = json_decode( $json, true );
        if ( ! is_array( $items ) ) { echo "⚠ $filename JSON inválido\n"; continue; }
        echo "  → $filename: " . count( $items ) . " productos (esperados $expected)\n";
        $all_products = array_merge( $all_products, $items );
    }

    echo "\nTotal productos a crear: " . count( $all_products ) . "\n\n";

    // 5. Crear productos
    $created = 0; $skipped = 0;
    foreach ( $all_products as $p ) {
        $sku   = $p['sku'] ?? '';
        $name  = $p['name'] ?? '';
        if ( ! $sku || ! $name ) { $skipped++; continue; }

        // Categoría
        $cat_slug = strtolower( $p['category'] ?? 'repuestos' );
        $cat_id = $cat_ids[ $cat_slug ] ?? $cat_ids['repuestos'];

        $post_id = wp_insert_post( array(
            'post_title'   => $name,
            'post_content' => 'Producto de demostración. ' . ( $p['subcategory'] ?? '' ) . ' de la marca ' . ( $p['brand'] ?? '' ) . '. Especificaciones técnicas objetivas para referencia. Este producto es parte del catálogo de demostración de Bicicletería Fagua.',
            'post_status'  => 'publish',
            'post_type'    => 'product',
        ) );
        if ( is_wp_error( $post_id ) || ! $post_id ) { $skipped++; continue; }

        update_post_meta( $post_id, '_sku', $sku );
        update_post_meta( $post_id, '_regular_price', (string) ( $p['regular'] ?? $p['price'] ) );
        update_post_meta( $post_id, '_sale_price',    (string) ( $p['price'] ?? 0 ) );
        update_post_meta( $post_id, '_price',        (string) ( $p['price'] ?? 0 ) );
        update_post_meta( $post_id, '_manage_stock', 'no' );
        update_post_meta( $post_id, '_stock_status', $p['stock'] ?? 'instock' );
        update_post_meta( $post_id, '_visibility',   'visible' );

        // Atributos
        if ( ! empty( $p['brand'] ) ) {
            wp_set_object_terms( $post_id, $p['brand'], 'pa_marca', false );
        }
        if ( ! empty( $p['subcategory'] ) ) {
            wp_set_object_terms( $post_id, $p['subcategory'], 'product_cat', true ); // append
        }

        // Categoría principal
        wp_set_object_terms( $post_id, array( $cat_id ), 'product_cat', false );

        // Specs como meta (para mostrar en single-product)
        $spec_keys = array( 'subcategory', 'speeds', 'weight_g', 'material', 'compatibility', 'diameter_mm', 'mount', 'range', 'links', 'pistons', 'rotor_mm', 'type', 'max_tooth', 'size', 'tpi', 'compound', 'tubeless_ready', 'frame', 'fork', 'brakes', 'wheels', 'weight_kg', 'color', 'year', 'ratio' );
        foreach ( $spec_keys as $k ) {
            if ( isset( $p[ $k ] ) && $p[ $k ] !== '' && $p[ $k ] !== null ) {
                update_post_meta( $post_id, '_bf_spec_' . $k, $p[ $k ] );
            }
        }

        $created++;
    }

    echo "✓ Productos creados: $created\n";
    echo "⚠ Saltados: $skipped\n\n";

    // 6. Asignar imágenes por afinidad
    $image_map = array(
        'Botella hidratación 750ml' => 'botella-750ml.png',
        'Pack 24 geles Maurten'    => 'maurten-gel.png',
        'Garmin Edge 1040'         => 'garmin-edge-1040.png',
        'Casco POC Procen Air'     => 'poc-procen-air.png',
        'Maillot Rapha Pro Team'   => 'rapha-pro-team.png',
        'Llantas Continental'      => 'continental-gp5000.png',
        'Cadena SRAM'              => 'sram-force.png',
        'Cadena Shimano XTR'       => 'cadena-shimano-xtr.png',
        'Shimano 105'              => 'shimano-105-r7100.png',
        'Shimano Ultegra'          => 'ultegra-r8100.png',
        'Giant Talon'              => 'giant-talon-4.png',
    );
    // Re-map por categoría/subcategoría
    $category_image = array(
        'Computadores' => 'garmin-edge-1040.png',
        'Sensores'     => 'garmin-edge-1040.png',
        'MTB'          => 'maxxis-equivalent-or-cassette', // we'll fall back
        'Llantas'      => 'continental-gp5000.png',
        'Bicicletas'   => 'giant-talon-4.png',
        'Cadenas'      => 'sram-force.png',
        'Cadena'       => 'sram-force.png',
        'Cassettes'    => 'shimano-105-r7100.png',
        'Cassette'     => 'shimano-105-r7100.png',
        'Frenos'       => 'ultegra-r8100.png',
        'Cambios Traseros' => 'ultegra-r8100.png',
        'Cambios'      => 'ultegra-r8100.png',
        'Rotores'      => 'ultegra-r8100.png',
        'Indumentaria' => 'rapha-pro-team.png',
        'Accesorios'   => 'botella-750ml.png',
    );
    $uploads = wp_upload_dir();
    $used_attachments = array();
    $img_count = 0;
    $all_pids = get_posts( array( 'post_type' => 'product', 'posts_per_page' => -1, 'fields' => 'ids' ) );
    foreach ( $all_pids as $pid ) {
        $title = get_the_title( $pid );
        $cats  = wp_get_post_terms( $pid, 'product_cat', array( 'fields' => 'slugs' ) );
        $img   = null;
        // buscar por nombre
        foreach ( $image_map as $key => $file ) {
            if ( stripos( $title, $key ) !== false ) { $img = $file; break; }
        }
        // si no, por categoría
        if ( ! $img && ! empty( $cats ) ) {
            foreach ( $cats as $cslug ) {
                if ( isset( $category_image[ $cslug ] ) ) { $img = $category_image[ $cslug ]; break; }
            }
        }
        if ( ! $img ) { $img = 'giant-talon-4.png'; } // fallback
        $src = $uploads['basedir'] . '/2026/06/' . $img;
        if ( ! file_exists( $src ) ) continue;
        if ( ! isset( $used_attachments[ $img ] ) ) {
            $ft = wp_check_filetype( basename( $src ) );
            $aid = wp_insert_attachment( array(
                'guid'           => $uploads['url'] . '/2026/06/' . basename( $src ),
                'post_mime_type' => $ft['type'] ?: 'image/png',
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $src ) ),
                'post_status'    => 'inherit',
            ), $src, 0 );
            if ( ! is_wp_error( $aid ) ) {
                $meta = wp_generate_attachment_metadata( $aid, $src );
                wp_update_attachment_metadata( $aid, $meta );
                $used_attachments[ $img ] = $aid;
            }
        }
        if ( isset( $used_attachments[ $img ] ) ) {
            set_post_thumbnail( $pid, $used_attachments[ $img ] );
            $img_count++;
        }
    }
    echo "✓ Imágenes asignadas: $img_count\n";

    echo "\n✅ Catálogo creado. Refresca la home y la tienda.\n";
    echo '<a href="' . esc_url( home_url( '/tienda/' ) ) . '">Ver tienda</a> | ';
    echo '<a href="' . esc_url( home_url( '/' ) ) . '">Ver home</a>';
    echo '</pre>';
    exit;
} );
