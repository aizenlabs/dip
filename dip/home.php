<?php
/**
 * The home template file.
 *
 * @package dip
 */

get_header(); ?>

  <div class="large-12 hide-for-small">
    <?php dp_orbit('featured'); ?>
  </div>

  <div class="large-12 columns">
  <?php if ( is_active_sidebar('home') ) {
    echo '<ul class="large-block-grid-4">';
    dynamic_sidebar('home');
    echo '</ul>';
  } ?>
  </div>

<?php get_footer(); ?>