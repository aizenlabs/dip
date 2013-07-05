<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package dip
 */

get_header(); ?>

<section class="row">
  <article class="large-12 columns not-found" role="main">
    <header class="entry-header">
      <h1 class="entry-title"><?php _e('Oops! That page can&rsquo;t be found.', 'dip'); ?></h1>
    </header><!-- .entry-header -->

    <div class="entry-content">
        <p><?php _e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'dip' ); ?></p>
  
        <?php get_search_form(); ?>
    </div><!-- .entry-content -->
  </article><!-- .large-12 .columns .not-found -->
</section>

<section class="row">
  <div class="large-4 small-12 columns">
    <?php the_widget( 'WP_Widget_Recent_Posts' ); ?>
  </div>
  <div class="large-4 small-12 columns">
     <?php the_widget( 'WP_Widget_Tag_Cloud' ); ?>
  </div>
  <div class="widget widget_categories large-4 small-12 columns">
    <?php if (is_categorized()) : // Only show the widget if site has multiple categories. ?>
    <h2 class="widgettitle"><?php _e( 'Most Used Categories', 'dip' ); ?></h2>
    <ul>
    <?php
      wp_list_categories( array(
        'orderby'    => 'count',
        'order'      => 'DESC',
        'show_count' => 1,
        'title_li'   => '',
        'number'     => 10,
      ) );
    ?>
    </ul>
    <?php endif; ?>
  </div>
</section>

<?php get_footer(); ?>