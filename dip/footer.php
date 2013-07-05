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
        <div class="large-6 columns">
          <p>&copy; This is version <strong><?php echo $dip->theme->version; ?></strong> from the <?php echo $dip->theme->name; ?></p>
        </div>
        <div class="large-6 columns">
          <ul class="inline-list right">
            <li><a href="#">Link 1</a></li>
            <li><a href="#">Link 2</a></li>
            <li><a href="#">Link 3</a></li>
            <li><a href="#">Link 4</a></li>
          </ul>
        </div>
      </div>
    </div>
  </footer>

<!-- Included JS Files (Compressed) -->
<?php get_template_part('scripts'); ?>

<?php wp_footer(); ?>
</body>
</html>