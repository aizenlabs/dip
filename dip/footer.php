<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package dip
 */

global $dip; ?>

    </div><!-- .row -->
  </section><!-- #main -->

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

  <footer id="footer" role="contentinfo">
    <div class="row">
      <div class="large-8 push-4 columns">
       <?php dp_menu('footer', array('attr' => array('class' => 'inline-list')) ); ?>
      </div>
      <div class="large-4 pull-8 columns">
<?php if(is_child_theme()) : ?>
        <p><?php echo $dip->theme->parent()->name; ?> &copy; 2013 by <?php echo $dip->theme->parent()->author; ?></p>
<?php else : ?>
        <p><?php echo $dip->theme->name; ?> &copy; 2013 by <?php echo $dip->theme->author; ?></p>
<?php endif; ?>
      </div>
    </div>
  </footer>

<?php wp_footer(); ?>
</body>
</html>