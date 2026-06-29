<?php
/**
 * Bicicletería Fagua theme bootstrap.
 *
 * @package BicicleteriaFagua
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'BF_VERSION', '1.0.1' );

require_once get_stylesheet_directory() . '/inc/setup.php';
require_once get_stylesheet_directory() . '/inc/enqueue.php';
require_once get_stylesheet_directory() . '/inc/woocommerce.php';
require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/inc/ajax-search.php';
require_once get_stylesheet_directory() . '/inc/assign-image.php';
require_once get_stylesheet_directory() . '/inc/assign-all.php';
require_once get_stylesheet_directory() . '/inc/seed-catalog.php';

// One-off seeder (admin only). Visit /wp-admin/?bf_seed=1 once.
if ( is_admin() ) {
    require_once get_stylesheet_directory() . '/inc/seed-runner.php';
}

/**
 * Remove the legacy Storefront site-header markup we override with our own.
 */
add_action( 'init', function () {
    remove_action( 'storefront_header', 'storefront_header_container',    0 );
    remove_action( 'storefront_header', 'storefront_skip_links',          5 );
    remove_action( 'storefront_header', 'storefront_site_branding',      20 );
    remove_action( 'storefront_header', 'storefront_secondary_navigation', 30 );
    remove_action( 'storefront_header', 'storefront_product_search',     40 );
    remove_action( 'storefront_header', 'storefront_header_container_close', 41 );
    remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper', 42 );
    remove_action( 'storefront_header', 'storefront_primary_navigation',  50 );
    remove_action( 'storefront_header', 'storefront_header_cart',         60 );
    remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper_close', 68 );
}, 99 );

/**
 * Custom Fagua favicon & apple-touch-icon.
 * Pulls the SVG straight from the theme's assets folder so it never
 * relies on the WP Customizer site_icon (which would re-introduce
 * the default WordPress logo).
 */
add_action( 'wp_head', function () {
    $theme_uri = get_stylesheet_directory_uri();
    $v = defined( 'BF_VERSION' ) ? BF_VERSION : '1.0.1';
    $favicon_svg  = $theme_uri . '/assets/icons/logo-fagua-32.svg?v=' . $v;
    $apple_svg    = $theme_uri . '/assets/favicon/apple-touch-icon.svg?v=' . $v;
    ?>
    <link rel="icon" type="image/svg+xml" href="<?php echo esc_url( $favicon_svg ); ?>" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( $apple_svg ); ?>" />
    <meta name="theme-color" content="#000000" />
    <meta name="msapplication-TileColor" content="#000000" />
    <?php
}, 1 );

/**
 * Remove the default WP site icon (favicon) so it never falls back
 * to the bundled w-logo-blue-white-bg.png.
 */
add_action( 'init', function () {
    remove_action( 'wp_head', 'wp_site_icon', 99 );
    remove_action( 'login_head', 'wp_site_icon', 99 );
}, 98 );

/**
 * Add a small polyfill for window.crypto.randomUUID in old browsers
 * (some WP plugins throw without it). Defer-safe via wp_footer.
 */
add_action( 'wp_footer', function () { ?>
<script>
(function(){
  if (typeof window === 'undefined' || !window.crypto) return;
  if (typeof window.crypto.randomUUID === 'function') return;
  if (typeof window.crypto.getRandomValues !== 'function') return;
  window.crypto.randomUUID = function(){
    var b = new Uint8Array(16);
    window.crypto.getRandomValues(b);
    b[6] = (b[6] & 0x0f) | 0x40;
    b[8] = (b[8] & 0x3f) | 0x80;
    var h = '';
    for (var i=0;i<16;i++){
      var s = b[i].toString(16);
      h += (s.length===1?'0':'') + s;
      if (i===3||i===5||i===7||i===9) h += '-';
    }
    return h;
  };
})();
</script>
<?php }, 5 );
