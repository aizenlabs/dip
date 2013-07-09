<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package dip
 */
?>
<?php 
  if ( is_active_sidebar('sidebar') ) {
    echo '<ul>';
    dynamic_sidebar('footer');
    echo '</ul>';
  }
?>