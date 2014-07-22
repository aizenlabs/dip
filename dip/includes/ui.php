<?php
/**
 * The helper for wp-admin styling
 *
 * @package Dip Framework
 * @subpackage Admin User Interface
 * @version 1.1.0
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

  public function set_menu_icon($id, $icon)
  {
    if(array_key_exists($icon, $this->json->dashicons))
    {
      $this->styles[] = '#adminmenu #'.$id.' div.wp-menu-image:before { content: "'.$this->json->dashicons->$icon.'"; }';
    }
    else
    {
      $this->styles[] = '#adminmenu #'.$id.' div.wp-menu-image:before { content: "\f111"; }';
    }
  }
}
