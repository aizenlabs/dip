<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package dip
 */
 
 /** Get theme info */
 global $dip; ?>
  <footer class="row"  role="contentinfo">
    <div class="large-12 columns">
      <hr />
      <div class="row">
        <div class="large-6 small 12 columns columns">
          <p>&copy; This is version <strong><?php echo $dip->theme->version; ?></strong> from the <?php echo $dip->theme->name; ?></p>
        </div>
        <div class="large-6 small-12 columns">
          <?php dp_menu('footer', array('attr' => array('class' => 'inline-list right')) ); ?>
        </div>
      </div>
    </div>
  </footer>

<?php wp_footer(); ?>
</body>
</html>