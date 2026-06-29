<?php
/**
 * Front page — Bicicletería Fagua
 * ESTRUCTURA ECOMMERCE PROFESIONAL (no landing).
 * El usuario debe entender qué vendemos y poder comprar en < 3 segundos.
 *
 * Orden: Categorías → Productos destacados → Ofertas → Bicicletas →
 *        Marcas → Confianza (strip) → Hero compacto → Servicio → Reviews → Instagram
 *
 * @package BicicleteriaFagua
 */

if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<main id="bfHome" class="bf-home">

  <?php get_template_part( 'template-parts/section', 'categories' ); ?>

  <?php get_template_part( 'template-parts/section', 'products' ); ?>

  <?php get_template_part( 'template-parts/section', 'offers' ); ?>

  <?php get_template_part( 'template-parts/section', 'bikes' ); ?>

  <?php get_template_part( 'template-parts/section', 'brands' ); ?>

  <?php get_template_part( 'template-parts/section', 'strip' ); ?>

  <?php get_template_part( 'template-parts/section', 'hero' ); ?>

  <?php get_template_part( 'template-parts/section', 'banner' ); ?>

  <?php get_template_part( 'template-parts/section', 'reviews' ); ?>

  <?php get_template_part( 'template-parts/section', 'instagram' ); ?>

</main>

<?php get_footer(); ?>
