<?php
/**
 * The helper for wp-admin styling
 *
 * @package Dip Framework
 * @subpackage Admin User Interface
 * @since Dip Framework 1.0
 */

class DP_UserInterface
{
  public $json;
  public $styles;
  
  public function __construct()
  {
    $this->styles = array();
    $this->json = json_decode(file_get_contents("assets/js/admin.json", true));
  }

  public function apply()
  {
    $this->register_styles();
    $this->print_styles();
  }

  public function register_styles()
  {
    $csspath = get_bloginfo('template_url').'/assets/css/';

    wp_register_style('dp-admin', $csspath . 'admin.css');
    wp_enqueue_style('dp-admin');
  }

  public function print_styles()
  {
    echo '<style id="dip-custom-css">';
    foreach($this->styles as $rule)
      echo $rule;
    echo '</style>';
  }

  public function set_icon32($id, $icon)
  {
    global $post_type;

    /** get sprite icon position */
    if(array_key_exists($icon, $this->json->default_icons))
    {
      $icon_position  = $this->json->default_icons->$icon;
      $this->styles[] = '#icon-'.$id.' { background-position: '.$icon_position->full.'; }';
    }
    elseif(array_key_exists($icon, $this->json->custom_icons))
    {
      $icon_position  = $this->json->custom_icons->$icon;
      $icon_url       = get_bloginfo('template_url').'/assets/images/admin/icons.png';
      $this->styles[] = '#icon-'.$id.' { background: transparent url('.$icon_url.') no-repeat '.$icon_position->full.'; }';
    }
  }

  public function set_menu_icon($id, $icon)
  {
    if(array_key_exists($icon, $this->json->default_icons))
    {
      $icon_position  = $this->json->default_icons->$icon;
      $this->styles[] = '#adminmenu #'.$id.' div.wp-menu-image { background-position: '.$icon_position->menu.'; }';
      $this->styles[] = '#adminmenu #'.$id.'.current div.wp-menu-image,';
      $this->styles[] = '#adminmenu #'.$id.'.wp-has-current-submenu div.wp-menu-image,';
      $this->styles[] = '#adminmenu #'.$id.':hover div.wp-menu-image { background-position: '.$icon_position->hover.'; }';
    }
    elseif(array_key_exists($icon, $this->json->custom_icons))
    {
      $icon_position  = $this->json->custom_icons->$icon;
      $icon_url       = get_bloginfo('template_url').'/assets/images/admin/icons.png';
      $this->styles[] = '#adminmenu #'.$id.' div.wp-menu-image { background: transparent url('.$icon_url.') no-repeat scroll '.$icon_position->menu.'; }';
      $this->styles[] = '#adminmenu #'.$id.'.current div.wp-menu-image,';
      $this->styles[] = '#adminmenu #'.$id.'.wp-has-current-submenu div.wp-menu-image,';
      $this->styles[] = '#adminmenu #'.$id.':hover div.wp-menu-image { background: transparent url('.$icon_url.') no-repeat scroll '.$icon_position->hover.'; }';
    }
  }

  /**
   * Show php error mensages like WordPress alerts
   * http://wp.tutsplus.com/tutorials/display-php-errors-as-wordpress-admin-alerts/
   */
  public function admin_alert_errors($errno, $errstr, $errfile, $errline)
  {
    $errorType = array(
      E_ERROR              => 'ERROR',
      E_CORE_ERROR         => 'CORE ERROR',
      E_COMPILE_ERROR      => 'COMPILE ERROR',
      E_USER_ERROR         => 'USER ERROR',
      E_RECOVERABLE_ERROR  => 'RECOVERABLE ERROR',
      E_WARNING            => 'WARNING',
      E_CORE_WARNING       => 'CORE WARNING',
      E_COMPILE_WARNING    => 'COMPILE WARNING',
      E_USER_WARNING       => 'USER WARNING',
      E_USER_NOTICE        => 'USER NOTICE',
      E_DEPRECATED         => 'DEPRECATED',
      E_USER_DEPRECATED    => 'USER_DEPRECATED',
      E_PARSE              => 'PARSING ERROR'
    );

    if (array_key_exists($errno, $errorType)) {
      $errname = $errorType[$errno];
    } else {
      $errname = 'UNKNOWN ERROR';
    }
    ob_start(); ?>
<div class="error">
  <p><strong><?php echo $errname; ?> Error: [<?php echo $errno; ?>] </strong><?php echo $errstr; ?><strong> <?php echo $errfile; ?></strong> on line <strong><?php echo $errline; ?></strong></p>
</div>
<?php
    echo ob_get_clean();
  }
}
