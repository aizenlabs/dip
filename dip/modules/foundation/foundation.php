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

new DP_Foundation;

class DP_Foundation
{
  public function __construct()
  {
    $this->_includes();
    
    if(is_admin() || is_login_page()) return;
    add_action('init', array($this, '_load_assets'));
    add_action('wp_footer', array($this, '_load_jslib'));
  }

  protected function _includes()
  {
    require('breadcrumbs.php');
    require('orbit.php');
    require('pagination.php');
    require('topbar.php');
  }
  
  public function _load_assets()
  {
    global $dip;

    wp_register_style('normalize', $dip->theme->template_directory_uri.'/assets/css/normalize.css', false, '2.1.1');
    wp_register_style('foundation', $dip->theme->template_directory_uri.'/assets/css/foundation.css', array('normalize'), '4.2.3');

    wp_register_script('modernizr', $dip->theme->template_directory_uri.'/assets/js/vendor/custom.modernizr.js', false, '2.6.2');
    wp_register_script('foundation', $dip->theme->template_directory_uri.'/assets/js/foundation/foundation.min.js', false, '2.1.1', true);

    wp_enqueue_style('foundation');
    wp_enqueue_script('foundation');
  }
  
  public function _load_jslib()
  {
    global $dip; ?>
    <script>
      <!-- Check for Zepto support, load jQuery if necessary -->
      document.write('<script src=<? echo $dip->theme->template_directory_uri ?>/assets/js/vendor/'
        + ('__proto__' in {} ? 'zepto' : 'jquery')
        + '.js><\/script>');
    </script>
<?php
  }
}