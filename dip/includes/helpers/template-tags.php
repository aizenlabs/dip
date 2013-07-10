<?php

if ( ! function_exists( 'dp_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable
 */
function dp_content_nav( $nav_id ) {
  global $wp_query, $post;

  // Don't print empty markup on single pages if there's nowhere to navigate.
  if ( is_single() ) {
    $previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
    $next = get_adjacent_post( false, '', false );

    if ( ! $next && ! $previous )
      return;
  }

  // Don't print empty markup in archives if there's only one page.
  if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) )
    return;

  $nav_class = ( is_single() ) ? 'navigation-post' : 'navigation-paging';

  ?>
  <nav role="navigation" id="<?php echo esc_attr( $nav_id ); ?>" class="<?php echo $nav_class; ?>">
    <h1 class="screen-reader-text"><?php _e( 'Post navigation', 'dip' ); ?></h1>

  <?php if ( is_single() ) : // navigation links for single posts ?>

    <?php previous_post_link( '<div class="nav-previous">%link</div>', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'dip' ) . '</span> %title' ); ?>
    <?php next_post_link( '<div class="nav-next">%link</div>', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'dip' ) . '</span>' ); ?>

  <?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : // navigation links for home, archive, and search pages ?>

    <?php if ( get_next_posts_link() ) : ?>
    <div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'dip' ) ); ?></div>
    <?php endif; ?>

    <?php if ( get_previous_posts_link() ) : ?>
    <div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'dip' ) ); ?></div>
    <?php endif; ?>

  <?php endif; ?>

  </nav><!-- #<?php echo esc_html( $nav_id ); ?> -->
  <?php
}
endif; // dp_content_nav


if ( ! function_exists( 'dp_comment' ) ) :
/**
 * Template for comments and pingbacks.
 * Used as a callback by wp_list_comments() for displaying the comments.
 */
function dp_comment( $comment, $args, $depth ) {
  global $dip;
  $GLOBALS['comment'] = $comment;

  // change avatar size
  if(!empty($dip->config['avatars'])) $args['avatar_size'] = $dip->config['avatars']['size'];

  if ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) : ?>

  <li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
    <div class="comment-body">
      <?php _e( 'Pingback:', 'dip' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'dip' ), '<span class="edit-link">', '</span>' ); ?>
    </div>

  <?php else : ?>

  <li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
    <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
      <footer class="comment-meta">
        <div class="comment-author-avatar">
          <?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
        </div>
        <div class="comment-author vcard">
          <?php printf( __( '%s <span class="says">says:</span>', 'dip' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
        </div><!-- .comment-author -->

        <div class="comment-metadata">
          <a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
            <time datetime="<?php comment_time( 'c' ); ?>">
              <?php printf( _x( '%1$s at %2$s', '1: date, 2: time', 'dip' ), get_comment_date(), get_comment_time() ); ?>
            </time>
          </a>
          <?php edit_comment_link( __( 'edit', 'dip' ), '<span class="edit-link">- ', '</span>' ); ?>
        </div><!-- .comment-metadata -->

        <?php if ( '0' == $comment->comment_approved ) : ?>
        <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'dip' ); ?></p>
        <?php endif; ?>
      </footer><!-- .comment-meta -->

      <div class="comment-content">
        <?php comment_text(); ?>

        <?php comment_reply_link( array_merge( $args, array( 'add_below' => 'div-comment', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
      </div><!-- .comment-content -->

      
    </article><!-- .comment-body -->

  <?php
  endif;
}
endif; // ends check for dp_comment()


if ( ! function_exists( 'dp_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function dp_posted_on() {
  $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
  if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) )
    $time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';

  $time_string = sprintf( $time_string,
    esc_attr( get_the_date( 'c' ) ),
    esc_html( get_the_date() ),
    esc_attr( get_the_modified_date( 'c' ) ),
    esc_html( get_the_modified_date() )
  );

  printf( __( 'Posted on <a href="%1$s" title="%2$s" rel="bookmark">%3$s</a><span class="byline"> by <span class="author vcard"><a class="url fn n" href="%4$s" title="%5$s" rel="author">%6$s</a></span></span>', 'dip' ),
    esc_url( get_permalink() ),
    esc_attr( get_the_time() ),
    $time_string,
    esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
    esc_attr( sprintf( __( 'View all posts by %s', 'dip' ), get_the_author() ) ),
    get_the_author()
  );
}
endif; // ends check for dp_posted_on()