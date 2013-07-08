<?php
/**
 * The template for displaying search forms in dip
 *
 * @package dip
 */
?>
<form method="get" id="searchform" class="searchform row collapse" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
  <div class="small-10 columns">
    <input type="search" class="field" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" id="s" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'dip' ); ?>" />
  </div>
  <div class="small-2 columns">
		<input type="submit" class="button prefix" id="searchsubmit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'dip' ); ?>" />
  </div>
</form>
