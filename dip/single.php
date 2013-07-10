<?php
/**
 * The Template for displaying all single posts.
 *
 * @package dip
 */

get_header(); ?>

  <div class="large-8 columns">
    <?php while ( have_posts() ) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="main">
      <header class="entry-header">
        <h1 class="entry-title"><?php the_title(); ?></h1>
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
      
      <footer class="entry-meta">
      <?php
        dp_posted_on();
        echo '<br />';
      
        /* translators: used between list items, there is a space after the comma */
        $category_list = get_the_category_list( __(', ', 'dip') );
  
        /* translators: used between list items, there is a space after the comma */
        $tag_list = get_the_tag_list( '', __(', ', 'dip') );
  
        if ( ! is_categorized() ) {
          // This blog only has 1 category so we just need to worry about tags in the meta text
          if ( '' != $tag_list ) {
            $meta_text = __( 'This entry was tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'dip' );
          } else {
            $meta_text = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'dip' );
          }
  
        } else {
          // But this blog has loads of categories so we should probably display them here
          if ( '' != $tag_list ) {
            $meta_text = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'dip' );
          } else {
            $meta_text = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'dip' );
          }
  
        } // end check for categories on this blog
  
        printf(
          $meta_text,
          $category_list,
          $tag_list,
          get_permalink(),
          the_title_attribute( 'echo=0' )
        );
      ?>
      </footer><!-- .entry-meta -->

    </article><!-- #page-## -->
  
    <?php
      // If comments are open or we have at least one comment, load up the comment template
      if ( comments_open() || '0' != get_comments_number() )
        comments_template();
    ?>
    
    <?php endwhile; // end of the loop. ?>
  </div><!-- .large-8 .columns -->

  <aside id="sidebar" class="widget-area large-4 columns hide-for-small" role="complementary">
    <?php get_sidebar(); ?>
  </aside><!-- #sidebar -->

<?php get_footer(); ?>