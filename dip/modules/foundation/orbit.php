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
  $obj = new DP_Helper_Orbit($_id, $_params);
  $obj->render(); 
}

class DP_Helper_Orbit
{
  private $html;
  private $nodes;
  private $attr;

  public function  __construct ( $_id = null, $_params = array() )
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
    
    if ( empty( $this->nodes ) )
    {
      return;
    }
    else
    {
      foreach ($this->nodes as $slide)
        $this->html->find('div', 0)->innertext .= "<img src=\"{$slide['image']}\" />";
    }

    // print
    echo $this->html;
  }
}




if(!is_admin()) return;

new DP_Panel_Orbit;
class DP_Panel_Orbit extends DP_Panel
{
  public function init() {
        $this->name      = __('Orbit Slider');
        $this->namespace = 'orbit';
        $this->module    = 'foundation';

        $this->tabs      = array( 'slides' => 'Slides',
                                  'settings' => 'Settings' );
    }
    
    public function save_settings()
    {
        $this->settings = get_option( $this->namespace );
        
        switch ( $_GET['tab'] ) {
            case 'slides':
                
                function _filter($var) { return ( !empty($var['image']) || !empty($var['desc']) ); }
                $slides = array_filter($_POST['slide'], '_filter') ;

                $this->settings['slides']      = $slides; //array_merge( $slides );
                
            /*    
                $logo = $_POST['one-logo'];
                $imgsize = getimagesize($_POST['one-logo']['image']);
                
                $logo['image-w'] = $imgsize[0];
                $logo['image-h'] = $imgsize[1];
                
                $this->settings['one-logo'] = $logo;
                $this->settings['one-colors'] = $_POST['one-colors'];
                $this->settings['one-header'] = $_POST['one-header']; */
                break;
            
            default:
                // filter social links
            /*    function _filter_sl($var) { return ( !empty($var['title']) && !empty($var['url']) && !empty($var['network']) ); }
                $sl = array_filter($_POST['one-sl'], '_filter_sl') ;
                
                $this->settings['one-profile'] = $_POST['one-profile'];
                $this->settings['one-sl']      = array_merge( $sl );
                $this->settings['one-footer']  = $this->_filter( $_POST['one-footer'] );
                $this->settings['one-ga']      = $this->_filter( $_POST['one-ga'] ); */
                break;
        }
        
        update_option( $this->namespace, $this->settings );
    }
    
    public function enqueue_scripts()
    {
        wp_enqueue_style('thickbox');
        wp_enqueue_script('media-upload');
        
        wp_enqueue_script( 'dip', get_bloginfo('template_directory') . '/assets/wp-admin.js', array( 'wp-color-picker' ), false, true );
    }
}