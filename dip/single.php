<?php
/**
 * The Template for displaying all single posts.
 *
 * @package dip
 */

get_header(); ?>

<div class="row">
  <article id="post-<?php the_ID(); ?>" <?php post_class('large-9 columns content'); ?> role="main">
<?php while ( have_posts() ) : the_post(); ?>
  <?php get_template_part( 'content', 'single' ); ?>
  <?php //dip_content_nav( 'nav-below' ); ?>
  <?php
    // If comments are open or we have at least one comment, load up the comment template
    if ( comments_open() || '0' != get_comments_number() )
      comments_template();
  ?>
  <?php endwhile; // end of the loop. ?>
  </article><!-- .large-9 .columns .content -->
  
  <aside class="large-3 columns" role="complementary">
    <?php get_sidebar(); ?>
  </aside>
</div><!-- .row -->

<?php get_footer(); ?>