<?php
/**
 * @package dip
 */
?>

  <header class="entry-header">
    <h1 class="entry-title"><?php the_title(); ?></h1>
    <div class="entry-meta">
      <?php dp_posted_on(); ?>
    </div><!-- .entry-meta -->
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
 