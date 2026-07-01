<?php
/**
 * Template Name: Homepage Fagua Pro
 * Description: Template personalizado para la homepage de Bicicletería Fagua — Diseño oscuro premium con acentos azules
 */

get_header();

// Get latest products (no featured filter — show all)
$featured_products = wc_get_products(array(
    'limit' => 12,
    'status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
));
?>

<!-- HOMEPAGE FAGUA PRO -->
<div class="bf-homepage">

    <!-- ===== HERO BANNER ===== -->
    <div class="bf-section bf-hero-section">
        <div class="bf-hero-bg">
            <div class="bf-hero-carousel">
                <div class="bf-hero-slide" style="background-image: url('https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=1920&q=80');"></div>
                <div class="bf-hero-slide" style="background-image: url('https://images.unsplash.com/photo-1532298229144-0ec0c57515c7?w=1920&q=80');"></div>
                <div class="bf-hero-slide" style="background-image: url('https://images.unsplash.com/photo-1511994298241-608e28f14fde?w=1920&q=80');"></div>
                <div class="bf-hero-slide" style="background-image: url('https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=1920&q=80');"></div>
            </div>
            <div class="bf-hero-overlay"></div>
        </div>
        <div class="bf-container bf-hero-content">
            <span class="bf-hero-tag">REPUESTOS ORIGINALES</span>
            <h1 class="bf-hero-title">
                LO MEJOR PARA<br/>
                <span class="bf-hero-highlight">TU PASIÓN</span>
            </h1>
            <p class="bf-hero-desc">Bicicletas, componentes y servicio técnico.<br/>Todo lo que necesitas para rodar más y mejor.</p>
            <div class="bf-hero-buttons">
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="bf-btn bf-btn-primary">
                    Ver productos
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
                <a href="<?php echo esc_url(home_url('/contacto')); ?>" class="bf-btn bf-btn-secondary">
                    Servicio técnico
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>
            <div class="bf-hero-dots">
                <span class="bf-dot active"></span>
                <span class="bf-dot"></span>
                <span class="bf-dot"></span>
                <span class="bf-dot"></span>
            </div>
        </div>
    </div>

    <!-- ===== PRODUCTS CAROUSEL ===== -->
    <div class="bf-section bf-products-carousel-section">
        <div class="bf-container">
            <div class="bf-products-carousel-header">
                <h2 class="bf-products-carousel-title">Productos destacados</h2>
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="bf-products-carousel-link">
                    Ver todos
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>
            <div class="bf-products-carousel">
                <?php
                $featured_products = wc_get_products(array(
                    'limit' => 12,
                    'status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC',
                ));
                foreach ($featured_products as $product):
                    $product_link = $product->get_permalink();
                    $product_title = $product->get_name();
                    $product_price = $product->get_price_html();
                    $product_image = $product->get_image_id() ? wp_get_attachment_image_url($product->get_image_id(), 'medium') : wc_placeholder_img_src();
                    $sale_price = $product->get_sale_price();
                    $regular_price = $product->get_regular_price();
                    $on_sale = $product->is_on_sale();
                ?>
                <a href="<?php echo esc_url($product_link); ?>" class="bf-product-card">
                    <div class="bf-product-badges">
                        <?php if ($on_sale && $sale_price && $regular_price): ?>
                        <span class="bf-badge bf-badge-sale"><?php echo round((($regular_price - $sale_price) / $regular_price) * 100); ?>%</span>
                        <?php endif; ?>
                    </div>
                    <div class="bf-product-image">
                        <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product_title); ?>" loading="lazy"/>
                    </div>
                    <div class="bf-product-details">
                        <h3 class="bf-product-title"><?php echo esc_html($product_title); ?></h3>
                        <div class="bf-product-price-row">
                            <span class="bf-product-price"><?php echo wp_kses_post($product_price); ?></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ===== COMPRA POR CATEGORÍA ===== -->
    <div class="bf-section bf-categories-section">
        <div class="bf-container">
            <div class="bf-section-header">
                <h2 class="bf-section-title">Compra por categoría</h2>
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="bf-section-link">
                    Ver todas
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>
            <div class="bf-categories-scroll">
                <?php
                $categories = array(
                    array('name' => 'Bicicletas', 'icon' => 'bike', 'brands' => 'GW, Trek, Specialized', 'slug' => 'bicicletas', 'image' => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=400&q=80'),
                    array('name' => 'Componentes', 'icon' => 'gear', 'brands' => 'Shimano, SRAM, FSA', 'slug' => 'componentes', 'image' => 'https://images.unsplash.com/photo-1511994298241-608e28f14fde?w=400&q=80'),
                    array('name' => 'Ruedas y Llantas', 'icon' => 'wheel', 'brands' => 'Maxxis, Continental, Pirelli', 'slug' => 'ruedas-llantas', 'image' => 'https://placehold.co/400x300/111111/ffffff?text=Ruedas+y+Llantas'),
                    array('name' => 'Cascos y Seguridad', 'icon' => 'helmet', 'brands' => 'POC, Giro, Bell, Fox', 'slug' => 'cascos-seguridad', 'image' => 'https://placehold.co/400x300/111111/ffffff?text=Cascos+y+Seguridad'),
                    array('name' => 'Indumentaria', 'icon' => 'shirt', 'brands' => 'Ropa, Guantes, Gafas, Calzado', 'slug' => 'indumentaria', 'image' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=400&q=80'),
                    array('name' => 'Servicio Técnico', 'icon' => 'wrench', 'brands' => 'Taller, Mantenimiento, Garantía', 'slug' => 'servicio-tecnico', 'image' => 'https://placehold.co/400x300/111111/ffffff?text=Taller'),
                );
                foreach ($categories as $cat):
                    $cat_link = get_term_link($cat['slug'], 'product_cat');
                    if (is_wp_error($cat_link)) $cat_link = wc_get_page_permalink('shop');
                ?>
                <a href="<?php echo esc_url($cat_link); ?>" class="bf-cat-card">
                    <div class="bf-cat-image">
                        <img src="<?php echo esc_url($cat['image']); ?>" alt="<?php echo esc_attr($cat['name']); ?>" loading="lazy"/>
                    </div>
                    <div class="bf-cat-info">
                        <div class="bf-cat-icon">
                            <?php if ($cat['icon'] === 'bike'): ?>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="18" r="3"/><circle cx="19" cy="18" r="3"/><polyline points="12 19 12 15 9 12 12 8 20 8"/><path d="M5 18l5-10 2 3"/></svg>
                            <?php elseif ($cat['icon'] === 'gear'): ?>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                            <?php elseif ($cat['icon'] === 'wheel'): ?>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="7"/></svg>
                            <?php elseif ($cat['icon'] === 'helmet'): ?>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a8 8 0 0 0-8 8v4a4 4 0 0 0 4 4h8a4 4 0 0 0 4-4v-4a8 8 0 0 0-8-8z"/><path d="M12 18v4"/></svg>
                            <?php elseif ($cat['icon'] === 'shirt'): ?>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 3.59A2 2 0 0 1 22 5v14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 1.41-1.41L6 2l3 3 3-3 3 3 3-3z"/><path d="M12 2v16"/></svg>
                            <?php else: ?>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                            <?php endif; ?>
                        </div>
                        <strong class="bf-cat-name"><?php echo esc_html($cat['name']); ?></strong>
                        <span class="bf-cat-brands"><?php echo esc_html($cat['brands']); ?></span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ===== MARCAS OFICIALES ===== -->
    <div class="bf-section bf-brands-section">
        <div class="bf-container">
            <div class="bf-section-header">
                <h2 class="bf-section-title">Marcas oficiales</h2>
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="bf-section-link">
                    Ver todas
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>
            <div class="bf-brands-grid">
                <?php
                $brands_svg = array(
                    'SHIMANO' => '<svg viewBox="0 0 120 30" xmlns="http://www.w3.org/2000/svg"><text x="0" y="22" font-family="Arial,sans-serif" font-weight="700" font-size="16" fill="white">SHIMANO</text></svg>',
                    'SRAM' => '<svg viewBox="0 0 80 30" xmlns="http://www.w3.org/2000/svg"><text x="0" y="22" font-family="Arial,sans-serif" font-weight="700" font-size="16" fill="white">SRAM</text></svg>',
                    'GW' => '<svg viewBox="0 0 60 30" xmlns="http://www.w3.org/2000/svg"><text x="0" y="22" font-family="Arial,sans-serif" font-weight="700" font-size="16" fill="white">GW</text></svg>',
                    'TREK' => '<svg viewBox="0 0 80 30" xmlns="http://www.w3.org/2000/svg"><text x="0" y="22" font-family="Arial,sans-serif" font-weight="700" font-size="16" fill="white">TREK</text></svg>',
                    'SPECIALIZED' => '<svg viewBox="0 0 140 30" xmlns="http://www.w3.org/2000/svg"><text x="0" y="22" font-family="Arial,sans-serif" font-weight="700" font-size="14" fill="white">SPECIALIZED</text></svg>',
                    'FOX' => '<svg viewBox="0 0 60 30" xmlns="http://www.w3.org/2000/svg"><text x="0" y="22" font-family="Arial,sans-serif" font-weight="700" font-size="16" fill="white">FOX</text></svg>',
                    'MAXXIS' => '<svg viewBox="0 0 90 30" xmlns="http://www.w3.org/2000/svg"><text x="0" y="22" font-family="Arial,sans-serif" font-weight="700" font-size="16" fill="white">MAXXIS</text></svg>',
                    'Continental' => '<svg viewBox="0 0 120 30" xmlns="http://www.w3.org/2000/svg"><text x="0" y="22" font-family="Arial,sans-serif" font-weight="700" font-size="14" fill="white">Continental</text></svg>',
                    'RACEFACE' => '<svg viewBox="0 0 110 30" xmlns="http://www.w3.org/2000/svg"><text x="0" y="22" font-family="Arial,sans-serif" font-weight="700" font-size="14" fill="white">RACEFACE</text></svg>',
                    'PIRELLI' => '<svg viewBox="0 0 90 30" xmlns="http://www.w3.org/2000/svg"><text x="0" y="22" font-family="Arial,sans-serif" font-weight="700" font-size="15" fill="white">PIRELLI</text></svg>',
                );
                foreach ($brands_svg as $name => $svg): ?>
                <div class="bf-brand-item">
                    <?php echo $svg; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ===== PRODUCTOS DESTACADOS ===== -->
    <div class="bf-section bf-products-section">
        <div class="bf-container">
            <div class="bf-section-header">
                <h2 class="bf-section-title">Productos destacados</h2>
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="bf-section-link">
                    Ver todos
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>
            <div class="bf-products-scroll" id="bf-products-scroll">
                <?php foreach ($featured_products as $product):
                    $product_id = $product->get_id();
                    $product_name = $product->get_name();
                    $product_price = $product->get_price();
                    $product_regular_price = $product->get_regular_price();
                    $product_sale_price = $product->get_sale_price();
                    $product_image = wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');
                    if (!$product_image) $product_image = wc_placeholder_img_src();
                    $product_url = get_permalink($product_id);
                    $rating_count = $product->get_rating_count();
                    $average_rating = $product->get_average_rating();
                    $is_on_sale = $product->is_on_sale();
                    $is_featured = $product->is_featured();
                    $product_cats = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'names'));
                    $badge_text = '';
                    $badge_class = '';
                    if ($is_on_sale && $product_sale_price && $product_regular_price > 0) {
                        $discount = round((($product_regular_price - $product_sale_price) / $product_regular_price) * 100);
                        $badge_text = '-' . $discount . '%';
                        $badge_class = 'bf-badge-sale';
                    } elseif ($is_featured) {
                        $badge_text = 'NUEVO';
                        $badge_class = 'bf-badge-new';
                    }
                ?>
                <div class="bf-product-card">
                    <a href="<?php echo esc_url($product_url); ?>" class="bf-product-link">
                        <?php if ($badge_text): ?>
                        <span class="bf-product-badge <?php echo $badge_class; ?>"><?php echo $badge_text; ?></span>
                        <?php endif; ?>
                        <button class="bf-product-wishlist" onclick="event.preventDefault(); event.stopPropagation();">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                        </button>
                        <div class="bf-product-image">
                            <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product_name); ?>" loading="lazy"/>
                        </div>
                        <div class="bf-product-details">
                            <h3 class="bf-product-title"><?php echo esc_html($product_name); ?></h3>
                            <?php if (!empty($product_cats)): ?>
                            <span class="bf-product-cat"><?php echo esc_html($product_cats[0]); ?></span>
                            <?php endif; ?>
                            <div class="bf-product-price-row">
                                <span class="bf-product-price"><?php echo wc_price($product_price); ?></span>
                                <?php if ($is_on_sale && $product_regular_price > $product_price): ?>
                                <span class="bf-product-price-old"><?php echo wc_price($product_regular_price); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="bf-product-rating">
                                <?php if ($rating_count > 0): ?>
                                <div class="bf-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="<?php echo $i <= round($average_rating) ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                    <?php endfor; ?>
                                </div>
                                <span class="bf-product-reviews">(<?php echo $rating_count; ?>)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                    <a href="?add-to-cart=<?php echo $product_id; ?>" class="bf-add-to-cart" data-product_id="<?php echo $product_id; ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ===== PAGO SEGURO / FOOTER BADGES ===== -->
    <div class="bf-section bf-payment-section">
        <div class="bf-container">
            <div class="bf-payment-grid">
                <div class="bf-payment-item">
                    <div class="bf-payment-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                    </div>
                    <div class="bf-payment-text">
                        <strong>Pago seguro</strong>
                        <span>Mercado Pago, Addi, Sistecredito</span>
                    </div>
                </div>
                <div class="bf-payment-item">
                    <div class="bf-payment-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="23 4 23 10 17 10"></polyline>
                            <polyline points="1 20 1 14 7 14"></polyline>
                            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                        </svg>
                    </div>
                    <div class="bf-payment-text">
                        <strong>Devoluciones fáciles</strong>
                        <span>Hasta 7 días</span>
                    </div>
                </div>
                <div class="bf-payment-item">
                    <div class="bf-payment-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div class="bf-payment-text">
                        <strong>Garantía oficial</strong>
                        <span>En todos los productos</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== FOOTER LEGAL ===== -->
    <footer class="bf-footer-legal" role="contentinfo">
        <div class="bf-container">
            <div class="bf-footer-grid">
                <div class="bf-footer-brand">
                    <div class="bf-footer-logo">
                        <img src="http://biz-thoughts-ability-bumper.trycloudflare.com/wp-content/themes/blocksy-fagua-pro/assets/logo-fagua.svg" alt="Bicicletería Fagua" width="140" height="40"/>
                    </div>
                    <p class="bf-footer-desc">Tu bicicletería de confianza. Repuestos originales, servicio técnico especializado y las mejores marcas.</p>
                    <div class="bf-footer-social">
                        <a href="https://github.com/jormanfagua" target="_blank" rel="noopener noreferrer" class="bf-social-link" aria-label="GitHub Jorman Fagua">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg>
                        </a>
                    </div>
                </div>
                <nav class="bf-footer-nav" aria-label="Enlaces legales">
                    <h4 class="bf-footer-title">Legal</h4>
                    <ul class="bf-footer-links">
                        <li><a href="<?php echo esc_url(home_url('/aviso-legal')); ?>">Aviso legal</a></li>
                        <li><a href="<?php echo esc_url(home_url('/politica-privacidad')); ?>">Política de privacidad</a></li>
                        <li><a href="<?php echo esc_url(home_url('/politica-cookies')); ?>">Política de cookies</a></li>
                        <li><a href="<?php echo esc_url(home_url('/terminos-condiciones')); ?>">Términos y condiciones</a></li>
                    </ul>
                </nav>
                <nav class="bf-footer-nav" aria-label="Información">
                    <h4 class="bf-footer-title">Nosotros</h4>
                    <ul class="bf-footer-links">
                        <li><a href="<?php echo esc_url(home_url('/nosotros')); ?>">Quiénes somos</a></li>
                        <li><a href="<?php echo esc_url(home_url('/contacto')); ?>">Contacto</a></li>
                        <li><a href="<?php echo esc_url(home_url('/servicio-tecnico')); ?>">Servicio técnico</a></li>
                        <li><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Tienda</a></li>
                    </ul>
                </nav>
                <div class="bf-footer-contact">
                    <h4 class="bf-footer-title">Contacto</h4>
                    <address class="bf-footer-address">
                        <p><strong>Bicicletería Fagua</strong></p>
                        <p>Calle 123 #45-67, Bogotá</p>
                        <p>Tel: +57 1 234 5678</p>
                        <p><a href="mailto:hola@bicicleteriafagua.com">hola@bicicleteriafagua.com</a></p>
                    </address>
                </div>
            </div>
            <div class="bf-footer-bottom">
                <p class="bf-footer-copyright">&copy; <?php echo date('Y'); ?> Bicicletería Fagua. Todos los derechos reservados.</p>
                <p class="bf-footer-credits">Creado por <a href="https://github.com/jormanfagua" target="_blank" rel="noopener noreferrer">Jorman Fagua</a> <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg></p>
            </div>
        </div>
    </footer>

</div>

<?php get_footer(); ?>
