<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package dip
 */
 
 /** Get theme info */
 $tm = wp_get_theme();
?>
  <footer class="row" role="contentinfo">
    <p class="large-12 small-12 columns">
      This is version <strong><?php echo $tm->Version; ?></strong> from the <?php echo $tm->Name; ?>.
    </p>
  </footer>

<!-- Included JS Files (Compressed) -->
<?php get_template_part('scripts'); ?>

<?php wp_footer(); ?>
</body>
</html>