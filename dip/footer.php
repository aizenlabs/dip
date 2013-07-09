<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package dip
 */

global $dip; ?>

  <aside id="footer-widgets" class="widgets hide-for-small">
    <div class="row">
      <div class="large-12 columns">
      <?php if ( is_active_sidebar('footer') ) {
        echo '<ul class="large-block-grid-5">';
        dynamic_sidebar('footer');
        echo '</ul>';
      } ?>
      </div>
    </div>
  </aside>

  <footer id="footer">
    <div class="row">
      <div class="large-8 push-4 columns">
       <?php dp_menu('footer', array('attr' => array('class' => 'inline-list')) ); ?>
      </div>
      <div class="large-4 pull-8 columns">
        <p><?php echo $dip->theme->parent()->name; ?> &copy; 2013 <?php echo $dip->theme->parent()->author; ?></p>
      </div>
    </div>
  </footer>

<?php wp_footer(); ?>
</body>
</html>