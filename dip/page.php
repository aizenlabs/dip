<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package dip
 */

get_header(); ?>

  <?php while ( have_posts() ) : the_post(); ?>
  <article id="page-<?php the_ID(); ?>" <?php post_class('large-8 columns'); ?> role="main">
    <header class="entry-header">
      <h1 class="entry-title"><?php the_title(); ?></h1>
      <?php dp_breadcrumbs('main'); ?>
    </header><!-- .entry-header -->

    <div class="entry-content">
      <?php the_content(); ?>
      <?php
        wp_link_pages( array(
          'before' => '<div class="page-links">' . __( 'Pages:', 'dip' ),
          'after'  => '</div>',
        ) );
      ?>
    </div><!-- .entry-content -->
    
  </article><!-- #page-## -->
  <?php endwhile; // end of the loop. ?>

  <aside id="sidebar" class="widget-area large-4 columns hide-for-small" role="complementary">
    <?php get_sidebar(); ?>
  </aside><!-- #sidebar -->

<?php get_footer(); ?>