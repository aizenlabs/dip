<?php
/**
 * The helper for displaying the Orbit Slider
 * Sliders are added by Panel Admin
 *
 * @package Dip Framework
 * @subpackage Foundation Module
 * @version 1.0.0
 * @since Dip Framework 1.0
 */

// template functions
function dp_orbit($_id = null, $_params = array())
{
  $obj = new DP_Foundation_Orbit($_id, $_params);
  $obj->render(); 
}

class DP_Foundation_Orbit
{
  private $html;
  private $nodes;
  private $attr;

  public function  __construct ($_id = null, $_params = array())
  {
    $option = get_option('orbit');

    $this->html        = null;
    $this->nodes       = $option['slides'];
    $this->attr        = isset( $_params['attr'] ) ? $_params['attr'] : array();
    $this->attr['id']  = $_id;
  }

  public function render()
  {
    // open tag
    $this->html = str_get_html('<div class="orbit"></div>');

    // define orbit id
    if( !empty($this->attr['id']) )
      $this->html->find('div', 0)->id = $this->attr['id'];
    
    // add orbit preloader
    $this->html->find('div', 0)->innertext .= '<div class="preloader"></div>';
    
    // add slides wrapper and reload the parser
    $this->html->find('div', 0)->innertext .= '<ul data-orbit></ul>';
    $this->html = str_get_html($this->html);
    
    if ( empty( $this->nodes ) )
    {
      if(!current_user_can('edit_theme_options')) return;
      $this->html->find('ul', 0)->innertext .= '<li><span style="display: block; font-size: 3em; line-height: 7em; text-align: center;">Hey, you need add some slides here!</span></li>"';
    }
    else
    {
      foreach ($this->nodes as $slide)
        $this->html->find('ul', 0)->innertext .= "<li><img src=\"{$slide['image']}\" /></li>";
    }

    // print
    echo $this->html;
  }
}

/** load panel only in wp-admin */
if(!is_admin()) return;
new DP_Panel_Orbit;

class DP_Panel_Orbit extends DP_Panel
{
  public function init() {
    $this->name      = __('Orbit Slider');
    $this->namespace = 'orbit';
    $this->module    = 'foundation';

    $this->parent    = false;
    $this->position  = 23.05;
    $this->icon      = 'star';
  }
    
  public function save_settings()
  {
    $this->settings = get_option( $this->namespace );

    function _filter($var) { return ( !empty($var['image']) || !empty($var['desc']) ); }
    $slides = array_filter($_POST['slide'], '_filter') ;

    $this->settings['slides']      = $slides; //array_merge( $slides );
    update_option( $this->namespace, $this->settings );
  }

  public function enqueue_scripts()
  {
    global $dip;
    wp_enqueue_style('thickbox');
    wp_enqueue_script('media-upload');

    wp_enqueue_script( 'dip', $dip->theme->template_directory_uri . '/assets/wp-admin.js', array( 'wp-color-picker' ), false, true );
  }
}