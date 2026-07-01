<?php
/**
 * Blocksy Fagua Pro — Theme functions and definitions
 */

// Enqueue parent + child theme styles
add_action( 'wp_enqueue_scripts', 'blocksy_fagua_pro_enqueue_styles', 100 );
function blocksy_fagua_pro_enqueue_styles() {
    // Parent theme style (Blocksy)
    wp_enqueue_style( 
        'blocksy-parent-style', 
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme()->parent()->get('Version')
    );

    // Child theme style (Vercel AMOLED)
    wp_enqueue_style( 
        'blocksy-fagua-pro-css', 
        get_stylesheet_uri(),
        ['blocksy-parent-style'],
        wp_get_theme()->get('Version')
    );
}

// Google Fonts: Inter
add_action( 'wp_enqueue_scripts', 'blocksy_fagua_pro_enqueue_fonts', 5 );
function blocksy_fagua_pro_enqueue_fonts() {
    wp_enqueue_style( 
        'bf-fonts', 
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap', 
        [], 
        null 
    );
}

// Custom favicon & apple-touch-icon
add_action( 'wp_head', function () {
    $theme_uri = get_stylesheet_directory_uri();
    ?>
    <link rel="icon" type="image/svg+xml" href="<?php echo esc_url( $theme_uri . '/assets/icons/favicon.svg' ); ?>" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( $theme_uri . '/assets/favicon/apple-touch-icon.svg' ); ?>" />
    <meta name="theme-color" content="#000000" />
    <meta name="msapplication-TileColor" content="#000000" />
    <?php
}, 1 );

// Remove default WP site icon
add_action( 'init', function () {
    remove_action( 'wp_head', 'wp_site_icon', 99 );
    remove_action( 'login_head', 'wp_site_icon', 99 );
}, 98 );

/* ============================================================
 * BOOTSTRAP: Create missing pages, flush rewrites, clean demo content
 * Runs once on theme setup (after_switch_theme hook)
 * ============================================================ */
add_action( 'after_switch_theme', 'bf_bootstrap_site' );
function bf_bootstrap_site() {
    // 1. Create missing pages (info + legal)
    $pages = [
        'contacto'        => [ 'title' => 'Contacto',        'content' => bf_get_contacto_content() ],
        'nosotros'        => [ 'title' => 'Nosotros',        'content' => bf_get_nosotros_content() ],
        'servicio-tecnico'=> [ 'title' => 'Servicio Técnico', 'content' => bf_get_servicio_tecnico_content() ],
        'aviso-legal'     => [ 'title' => 'Aviso Legal',     'content' => bf_get_aviso_legal_content() ],
        'politica-de-privacidad' => [ 'title' => 'Política de Privacidad', 'content' => bf_get_privacidad_content() ],
        'politica-cookies'    => [ 'title' => 'Política de Cookies',    'content' => bf_get_cookies_content() ],
        'terminos-condiciones'=> [ 'title' => 'Términos y Condiciones', 'content' => bf_get_terminos_content() ],
    ];

    foreach ( $pages as $slug => $data ) {
        if ( ! get_page_by_path( $slug ) ) {
            wp_insert_post( [
                'post_title'   => $data['title'],
                'post_name'    => $slug,
                'post_content' => $data['content'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'comment_status' => 'closed',
            ] );
        }
    }

    // 2. Flush rewrite rules (fix single product permalinks)
    flush_rewrite_rules();

    // 3. Clean demo content from products (remove "Producto de demostración", "CaliBike", etc.)
    bf_clean_product_demo_content();
}

/* Also run on init if pages don't exist (fallback) */
add_action( 'init', function() {
    if ( ! get_page_by_path( 'contacto' ) || isset($_GET['bf_force_bootstrap']) ) {
        bf_bootstrap_site();
    }
}, 20 );

/* ============================================================
 * SHOP FILTER BAR: Horizontal filter bar above product grid
 * Replaces sidebar. Uses WC hooks, no template override needed.
 * ============================================================ */

// Disable sidebar on shop pages — use filter bar instead
add_action( 'after_switch_theme', function() {
    set_theme_mod( 'woo_categories_has_sidebar', 'no' );
}, 25 );
add_action( 'admin_init', function() {
    // Sync on seed trigger
    if ( isset( $_GET['bf_seed_sidebar'] ) ) {
        set_theme_mod( 'woo_categories_has_sidebar', 'no' );
    }
} );
// Runtime override — always hide sidebar on shop/archive pages
add_filter( 'blocksy:general:sidebar-position', function( $position ) {
    if ( is_shop() || is_product_category() || is_product_tag() ) {
        return 'none';
    }
    return $position;
} );

// Inject horizontal filter bar before product loop
add_action( 'woocommerce_before_shop_loop', 'bf_render_filter_bar', 25 );
function bf_render_filter_bar() {
    if ( ! is_shop() && ! is_product_category() && ! is_product_tag() ) {
        return;
    }

    // Collect active categories
    $categories   = get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => true, 'exclude' => get_option( 'default_product_cat' ) ] );
    $current_cat  = isset( $_GET['product_cat'] ) ? sanitize_text_field( $_GET['product_cat'] ) : '';

    // Collect active attributes (marca, color)
    $attr_marca   = get_terms( [ 'taxonomy' => 'pa_marca', 'hide_empty' => true ] );
    $attr_color   = get_terms( [ 'taxonomy' => 'pa_color', 'hide_empty' => true ] );
    $current_marca = isset( $_GET['filter_marca'] ) ? sanitize_text_field( $_GET['filter_marca'] ) : '';
    $current_color = isset( $_GET['filter_color'] ) ? sanitize_text_field( $_GET['filter_color'] ) : '';

    // Build base URL (current page without filter params)
    $base_url = strtok( $_SERVER['REQUEST_URI'], '?' );
    $params   = $_GET;

    ?>
    <div class="bf-filter-bar" role="search" aria-label="Filtros de tienda">

        <?php /* ── Categoría ── */ ?>
        <div class="bf-filter-group" data-filter="category">
            <button class="bf-filter-btn <?php echo $current_cat ? 'is-active' : ''; ?>" aria-expanded="false" aria-haspopup="listbox">
                <?php echo $current_cat ? esc_html( ucfirst( str_replace( '-', ' ', $current_cat ) ) ) : 'Categoría'; ?>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="bf-filter-dropdown" role="listbox">
                <?php
                $p = $params;
                unset( $p['product_cat'] );
                $clear_url = $base_url . ( $p ? '?' . http_build_query( $p ) : '' );
                ?>
                <a class="bf-filter-option <?php echo ! $current_cat ? 'is-selected' : ''; ?>" href="<?php echo esc_url( $clear_url ); ?>">Todas</a>
                <?php if ( ! is_wp_error( $categories ) ) : foreach ( $categories as $cat ) :
                    $p2 = array_merge( $params, [ 'product_cat' => $cat->slug ] );
                    $cat_url = $base_url . '?' . http_build_query( $p2 );
                    ?>
                    <a class="bf-filter-option <?php echo $current_cat === $cat->slug ? 'is-selected' : ''; ?>"
                       href="<?php echo esc_url( $cat_url ); ?>">
                        <?php echo esc_html( $cat->name ); ?>
                        <span class="bf-filter-count"><?php echo absint( $cat->count ); ?></span>
                    </a>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <?php /* ── Marca ── */ ?>
        <?php if ( ! is_wp_error( $attr_marca ) && ! empty( $attr_marca ) ) : ?>
        <div class="bf-filter-group" data-filter="marca">
            <button class="bf-filter-btn <?php echo $current_marca ? 'is-active' : ''; ?>" aria-expanded="false" aria-haspopup="listbox">
                <?php echo $current_marca ? esc_html( ucfirst( $current_marca ) ) : 'Marca'; ?>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="bf-filter-dropdown" role="listbox">
                <?php
                $p = $params;
                unset( $p['filter_marca'] );
                $clear_url = $base_url . ( $p ? '?' . http_build_query( $p ) : '' );
                ?>
                <a class="bf-filter-option <?php echo ! $current_marca ? 'is-selected' : ''; ?>" href="<?php echo esc_url( $clear_url ); ?>">Todas</a>
                <?php foreach ( $attr_marca as $term ) :
                    $p2 = array_merge( $params, [ 'filter_marca' => $term->slug ] );
                    $term_url = $base_url . '?' . http_build_query( $p2 );
                    ?>
                    <a class="bf-filter-option <?php echo $current_marca === $term->slug ? 'is-selected' : ''; ?>"
                       href="<?php echo esc_url( $term_url ); ?>">
                        <?php echo esc_html( $term->name ); ?>
                        <span class="bf-filter-count"><?php echo absint( $term->count ); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php /* ── Limpiar filtros ── */ ?>
        <?php if ( $current_cat || $current_marca || $current_color ) : ?>
        <a class="bf-filter-clear" href="<?php echo esc_url( $base_url ); ?>">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 2l8 8M10 2l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            Limpiar
        </a>
        <?php endif; ?>

    </div>
    <?php
}

// Simple JS to toggle dropdowns (no dependencies)
add_action( 'wp_footer', 'bf_filter_bar_js' );
function bf_filter_bar_js() {
    if ( ! is_shop() && ! is_product_category() && ! is_product_tag() ) return;
    ?>
    <script>
    (function(){
        document.querySelectorAll('.bf-filter-btn').forEach(function(btn){
            btn.addEventListener('click', function(e){
                e.stopPropagation();
                var group = this.closest('.bf-filter-group');
                var isOpen = group.classList.contains('is-open');
                // Close all
                document.querySelectorAll('.bf-filter-group').forEach(function(g){ g.classList.remove('is-open'); g.querySelector('.bf-filter-btn').setAttribute('aria-expanded','false'); });
                if(!isOpen){ group.classList.add('is-open'); this.setAttribute('aria-expanded','true'); }
            });
        });
        document.addEventListener('click', function(){
            document.querySelectorAll('.bf-filter-group').forEach(function(g){ g.classList.remove('is-open'); g.querySelector('.bf-filter-btn').setAttribute('aria-expanded','false'); });
        });
    })();
    </script>
    <?php
}

/* ============================================================
 * SHOP SIDEBAR: Seed WooCommerce widgets into Blocksy's
 * native 'sidebar-woocommerce' sidebar.
 *
 * Trigger manually: visit /wp-admin/?bf_seed_sidebar=1
 * Safe to run multiple times — always overwrites the sidebar
 * widget list but does not duplicate widget instances.
 * ============================================================ */
function bf_seed_woocommerce_sidebar_widgets() {
    // Helper: get next available integer key for a widget option array.
    $next_key = function( array $instances ) {
        $int_keys = array_filter( array_keys( $instances ), 'is_int' );
        return empty( $int_keys ) ? 2 : ( max( $int_keys ) + 1 );
    };

    $new_widget_ids = [];

    // ── 1. Product Categories ──────────────────────────────────────
    $cats = get_option( 'widget_woocommerce_product_categories', [] );
    $k    = $next_key( $cats );
    $cats[ $k ] = [
        'title'              => 'Categorías',
        'orderby'            => 'name',
        'dropdown'           => 0,
        'count'              => 1,
        'hierarchical'       => 1,
        'show_children_only' => 0,
        'hide_empty'         => 1,
        'max_depth'          => '',
    ];
    update_option( 'widget_woocommerce_product_categories', $cats );
    $new_widget_ids[] = 'woocommerce_product_categories-' . $k;

    // ── 2. Price Filter ────────────────────────────────────────────
    $price = get_option( 'widget_woocommerce_price_filter', [] );
    $k     = $next_key( $price );
    $price[ $k ] = [ 'title' => 'Filtrar por Precio' ];
    update_option( 'widget_woocommerce_price_filter', $price );
    $new_widget_ids[] = 'woocommerce_price_filter-' . $k;

    // ── 3. Layered Nav — Marca ─────────────────────────────────────
    $nav    = get_option( 'widget_woocommerce_layered_nav', [] );
    $k_marca = $next_key( $nav );
    $nav[ $k_marca ] = [
        'title'        => 'Marca',
        'attribute'    => 'pa_marca',
        'display_type' => 'list',
        'query_type'   => 'or',
    ];
    $new_widget_ids[] = 'woocommerce_layered_nav-' . $k_marca;

    // ── 4. Layered Nav — Color ─────────────────────────────────────
    $k_color = $k_marca + 1;
    $nav[ $k_color ] = [
        'title'        => 'Color',
        'attribute'    => 'pa_color',
        'display_type' => 'list',
        'query_type'   => 'or',
    ];
    update_option( 'widget_woocommerce_layered_nav', $nav );
    $new_widget_ids[] = 'woocommerce_layered_nav-' . $k_color;

    // ── Assign to Blocksy's WooCommerce sidebar ────────────────────
    $sidebars = get_option( 'sidebars_widgets', [] );
    $sidebars['sidebar-woocommerce'] = $new_widget_ids;
    update_option( 'sidebars_widgets', $sidebars );
}

// Manual trigger: /wp-admin/?bf_seed_sidebar=1
add_action( 'admin_init', function () {
    if ( isset( $_GET['bf_seed_sidebar'] ) && current_user_can( 'manage_options' ) ) {
        bf_seed_woocommerce_sidebar_widgets();

        // Ensure Blocksy shows the sidebar on WooCommerce shop/category pages
        set_theme_mod( 'woo_categories_has_sidebar', 'yes' );
        set_theme_mod( 'woo_categories_sidebar_position', 'left' );

        // Compact hero on shop/category pages
        set_theme_mod( 'woo_categories_hero_height', '80px' );
        set_theme_mod( 'woo_categories_has_hero_section', 'enabled' );

        wp_redirect( admin_url( 'widgets.php' ) );
        exit;
    }
} );

// Add body classes for shop pages (for CSS targeting)
add_filter( 'body_class', 'bf_add_shop_sidebar_class' );
function bf_add_shop_sidebar_class( $classes ) {
    if ( is_shop() || is_product_category() || is_product_tag() ) {
        $classes[] = 'bf-has-shop-sidebar';
    }
    return $classes;
}

/* ============================================================
 * CLEAN PRODUCT DEMO CONTENT
 * ============================================================ */
function bf_clean_product_demo_content() {
    $products = get_posts( [
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ] );

    $demo_patterns = [
        '/Producto de demostración\..*?catálogo de demostración de/si',
        '/Somos distribuidores Oficiales de las marcas mas importantes en el ciclismo recreativo como:.*?muchas mas\./si',
        '/Bienvenidos a CaliBike.*?replicas o falsificaciones\./si',
        '/Lea con atención:.*?costo adicional\./si',
    ];

    foreach ( $products as $product_id ) {
        $post = get_post( $product_id );
        $content = $post->post_content;
        $original = $content;

        foreach ( $demo_patterns as $pattern ) {
            $content = preg_replace( $pattern, '', $content );
        }

        // Clean up multiple empty paragraphs
        $content = preg_replace( '/(<p>\s*<\/p>)+/', '', $content );
        $content = preg_replace( '/\n{3,}/', "\n\n", $content );

        if ( $content !== $original ) {
            wp_update_post( [
                'ID'           => $product_id,
                'post_content' => trim( $content ),
            ] );
        }
    }
}

/* ============================================================
 * PAGE CONTENT HELPERS
 * ============================================================ */
function bf_get_contacto_content() {
    return '<!-- wp:heading --><h2>Contacto</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>¿Tienes dudas? Escríbenos y te responderemos lo antes posible.</p><!-- /wp:paragraph -->
<!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3>Tienda Física</h3><!-- /wp:heading --><p><strong>Bicicletería Fagua</strong><br>Dirección: [Tu dirección aquí]<br>Ciudad: [Tu ciudad]<br>Teléfono: [Tu teléfono]<br>Email: [Tu email]</p><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3>Horario</h3><!-- /wp:heading --><p>Lunes a Viernes: 9:00 - 18:00<br>Sábados: 9:00 - 13:00<br>Domingos: Cerrado</p><!-- /wp:column --></div><!-- /wp:columns -->
<!-- wp:heading {"level":3} --><h3>Formulario de Contacto</h3><!-- /wp:heading -->
<!-- wp:shortcode -->[contact-form-7 id="contact-form" title="Contacto"]<!-- /wp:shortcode -->';
}

function bf_get_nosotros_content() {
    return '<!-- wp:heading --><h2>Nosotros</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p><strong>Bicicletería Fagua</strong> nace de la pasión por el ciclismo y la necesidad de ofrecer repuestos originales, servicio técnico de calidad y asesoría honesta a ciclistas de todos los niveles.</p><!-- /wp:paragraph -->
<!-- wp:heading {"level":3} --><h3>Nuestra Misión</h3><!-- /wp:heading -->
<!-- wp:paragraph --><p>Ser el referente en repuestos y servicio técnico para bicicletas, garantizando productos 100% originales y atención personalizada.</p><!-- /wp:heading -->
<!-- wp:heading {"level":3} --><h3>Qué Ofrecemos</h3><!-- /wp:heading -->
<!-- wp:list --><ul><li>Repuestos originales Shimano, SRAM, GW, Continental, Maxxis, Vittoria y más</li><li>Servicio técnico especializado: mantenimiento, reparaciones, garantías</li><li>Asesoría para elegir la bicicleta o componente ideal</li><li>Envíos a todo el país</li></ul><!-- /wp:list -->';
}

function bf_get_servicio_tecnico_content() {
    return '<!-- wp:heading --><h2>Servicio Técnico</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Contamos con taller especializado para mantenimiento y reparación de bicicletas de montaña, ruta, urbana y eléctrica.</p><!-- /wp:paragraph -->
<!-- wp:heading {"level":3} --><h3>Servicios Disponibles</h3><!-- /wp:heading -->
<!-- wp:list --><ul><li>Mantenimiento preventivo y correctivo</li><li>Ajuste de cambios y frenos</li><li>Centrado de ruedas</li><li>Sangrado de frenos hidráulicos</li><li>Instalación de componentes</li><li>Diagnóstico y reparación de bicicletas eléctricas</li><li>Gestión de garantías</li></ul><!-- /wp:list -->
<!-- wp:heading {"level":3} --><h3>Agenda tu Cita</h3><!-- /wp:heading -->
<!-- wp:paragraph --><p>Escríbenos por WhatsApp o llámanos para agendar tu servicio. Recogida y entrega disponible.</p><!-- /wp:paragraph -->';
}

function bf_get_aviso_legal_content() {
    return '<!-- wp:heading --><h2>Aviso Legal</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>En cumplimiento de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y de Comercio Electrónico (LSSI-CE), se informa que este sitio web es titularidad de <strong>Bicicletería Fagua</strong>.</p><!-- /wp:paragraph -->
<!-- wp:heading {"level":3} --><h3>Datos del Responsable</h3><!-- /wp:heading -->
<!-- wp:paragraph --><p><strong>Nombre:</strong> Bicicletería Fagua<br><strong>Dirección:</strong> [Tu dirección completa]<br><strong>Email:</strong> [Tu email]<br><strong>Teléfono:</strong> [Tu teléfono]</p><!-- /wp:paragraph -->
<!-- wp:heading {"level":3} --><h3>Propiedad Intelectual</h3><!-- /wp:heading -->
<!-- wp:paragraph --><p>Todos los contenidos, marcas, logotipos, imágenes y diseños son propiedad de Bicicletería Fagua o de terceros que han autorizado su uso. Queda prohibida su reproducción sin autorización expresa.</p><!-- /wp:paragraph -->';
}

function bf_get_privacidad_content() {
    return '<!-- wp:heading --><h2>Política de Privacidad</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Bicicletería Fagua trata tus datos personales de conformidad con el Reglamento (UE) 2016/679 (GDPR) y la Ley Orgánica 3/2018 (LOPDGDD).</p><!-- /wp:paragraph -->
<!-- wp:heading {"level":3} --><h3>Responsable del Tratamiento</h3><!-- /wp:heading -->
<!-- wp:paragraph --><p>Bicicletería Fagua - [Tu email]</p><!-- /wp:paragraph -->
<!-- wp:heading {"level":3} --><h3>Finalidad y Legitimación</h3><!-- /wp:heading -->
<!-- wp:list --><ul><li>Gestión de pedidos: ejecución de contrato</li><li>Formulario de contacto: consentimiento</li><li>Newsletter: consentimiento explícito</li><li>Obligaciones legales: cumplimiento normativo</li></ul><!-- /wp:list -->
<!-- wp:heading {"level":3} --><h3>Derechos</h3><!-- /wp:heading -->
<!-- wp:paragraph --><p>Acceso, rectificación, supresión, limitación, portabilidad y oposición. Ejercicio: [Tu email]</p><!-- /wp:paragraph -->';
}

function bf_get_cookies_content() {
    return '<!-- wp:heading --><h2>Política de Cookies</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Este sitio utiliza cookies propias y de terceros para mejorar la experiencia de usuario, analizar el tráfico y personalizar contenido.</p><!-- /wp:paragraph -->
<!-- wp:heading {"level":3} --><h3>Tipos de Cookies</h3><!-- /wp:heading -->
<!-- wp:list --><ul><li><strong>Necesarias:</strong> Sesión, carrito, seguridad (siempre activas)</li><li><strong>Analíticas:</strong> Google Analytics, Medida de audiencia</li><li><strong>Marketing:</strong> Remarketing, anuncios personalizados</li></ul><!-- /wp:list -->
<!-- wp:heading {"level":3} --><h3>Gestión</h3><!-- /wp:paragraph --><p>Puedes aceptar, rechazar o configurar cookies en el banner de consentimiento o desde la configuración de tu navegador.</p><!-- /wp:paragraph -->';
}

function bf_get_terminos_content() {
    return '<!-- wp:heading --><h2>Términos y Condiciones</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Al realizar una compra en Bicicletería Fagua, aceptas los siguientes términos:</p><!-- /wp:paragraph -->
<!-- wp:heading {"level":3} --><h3>1. Precios y Pagos</h3><!-- /wp:heading -->
<!-- wp:paragraph --><p>Precios en COP (Pesos Colombianos), IVA incluido. Pago contra entrega, transferencia, tarjeta o link de pago.</p><!-- /wp:paragraph -->
<!-- wp:heading {"level":3} --><h3>2. Envíos</h3><!-- /wp:heading -->
<!-- wp:paragraph --><p>Envíos a toda Colombia. Tiempo estimado: 2-5 días hábiles. Costos según destino y peso.</p><!-- /wp:paragraph -->
<!-- wp:heading {"level":3} --><h3>3. Devoluciones y Garantía</h3><!-- /wp:heading -->
<!-- wp:paragraph --><p>14 días para devolución (producto sin usar, embalaje original). Garantía legal según fabricante (mínimo 1 año). Gestión directa con nosotros.</p><!-- /wp:paragraph -->
<!-- wp:heading {"level":3} --><h3>4. Responsabilidad</h3><!-- /wp:heading -->
<!-- wp:paragraph --><p>No nos hacemos responsables por daños por uso indebido, instalación incorrecta o modificaciones no autorizadas.</p><!-- /wp:paragraph -->';
}
