<?php
/**
 * Foundation Helpers
 * Include the files from module
 *
 * @package Dip Framework
 * @subpackage Foundation Module
 * @version 1.0.0
 * @since Dip Framework 1.0
 */
 
require('breadcrumbs.php');
require('orbit.php');
require('navbar.php');

wp_register_style('normalize', get_bloginfo('template_url').'/assets/css/normalize.css', false, '2.1.1');
wp_register_style('foundation', get_bloginfo('template_url').'/assets/css/foundation.css', array('normalize'), '4.2.3');

wp_register_script('modernizr', get_bloginfo('template_url').'/assets/js/vendor/custom.modernizr.js', false, '2.6.2');
wp_register_script('foundation', get_bloginfo('template_url').'/assets/js/foundation/foundation.js', false, '2.1.1', true);

wp_enqueue_style('foundation');

add_action('wp_footer', 'dp_load_scripts');
wp_enqueue_script('foundation');

function dp_load_scripts() { ?>
<script>
  document.write('<script src=<? echo get_bloginfo('template_url') ?>/assets/js/vendor/'
    + ('__proto__' in {} ? 'zepto' : 'jquery')
    + '.js><\/script>');
</script>
<?php }