<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package dip
 */

get_header(); ?>

  <article class="large-9 small-11 small-centered columns not-found" role="main">
    <header class="entry-header">
      <h1 class="entry-title"><?php _e('Oops! That page can&rsquo;t be found.', 'dip'); ?></h1>
    </header><!-- .entry-header -->

    <div class="entry-content">
      <p><?php _e( 'It looks like nothing was found at this location. Maybe try one of the links above or a search?', 'dip' ); ?></p>

      <div class="row">
        <div class="large-6 small-10 small-centered columns"><?php get_search_form(); ?></div>
      </div>
    </div><!-- .entry-content -->
  </article><!-- .large-9 .columns .not-found -->

<?php get_footer(); ?>