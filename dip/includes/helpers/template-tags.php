<?php

if ( ! function_exists( 'dp_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 */
function dp_comment( $comment, $args, $depth ) {
  $GLOBALS['comment'] = $comment;

  if ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) : ?>

  <li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
    <div class="comment-body">
      <?php _e( 'Pingback:', 'dip' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'dip' ), '<span class="edit-link">', '</span>' ); ?>
    </div>

  <?php else : ?>

  <li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
    <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
      <footer class="comment-meta">
        <div class="comment-author vcard">
          <?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
          <?php printf( __( '%s <span class="says">says:</span>', 'dip' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
        </div><!-- .comment-author -->

        <div class="comment-metadata">
          <a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
            <time datetime="<?php comment_time( 'c' ); ?>">
              <?php printf( _x( '%1$s at %2$s', '1: date, 2: time', 'dip' ), get_comment_date(), get_comment_time() ); ?>
            </time>
          </a>
          <?php edit_comment_link( __( 'Edit', 'dip' ), '<span class="edit-link">', '</span>' ); ?>
        </div><!-- .comment-metadata -->

        <?php if ( '0' == $comment->comment_approved ) : ?>
        <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'dip' ); ?></p>
        <?php endif; ?>
      </footer><!-- .comment-meta -->

      <div class="comment-content">
        <?php comment_text(); ?>
      </div><!-- .comment-content -->

      <div class="reply">
        <?php comment_reply_link( array_merge( $args, array( 'add_below' => 'div-comment', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
      </div><!-- .reply -->
    </article><!-- .comment-body -->

  <?php
  endif;
}
endif; // ends check for dip_comment()



if ( ! function_exists( 'dip_posted_on' ) ) :
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
endif;