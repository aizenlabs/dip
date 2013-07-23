<?php
/**
 * The helper for displaying Google Maps
 *
 * @package Dip Framework
 * @subpackage Gmaps Module
 * @version 1.0.0
 * @since Dip Framework 1.0
*/

// create shortcodes
add_shortcode("gmap", "dp_gmaps");
add_shortcode("map", "dp_static_gmaps");

// template functions
function dp_gmaps($_params)
{
  $obj = new DP_Helper_Gmaps($_params);
  $obj->render(); 
}

function dp_static_gmaps($_params)
{
  if( is_string($_params) )
    $_params = array('center' => $_params);

  // force type to static map
  $_params['type'] = 'static';

  $obj = new DP_Helper_Gmaps($_params);
  $obj->render(); 
}

class DP_Helper_Gmaps
{
  private $address;
  private $center;
  private $width;
  private $height;
  private $zoom;

  private $type;
  private $default_src;
  private $static_src;

  private $html;
  private $attr;

  public function  __construct ($_params)
  {
    // set default values
    $this->type    = 'default';
    $this->width   = 600;
    $this->height  = 400;
    $this->zoom    = 15;
    
    if( is_string($_params) )
      $_params = array('center' => $_params); /* ex: -25.495249,-49.288399 or 'Sao Paulo, SP' */

    $this->address  = isset($_params['address']) ? $_params['address'] : '';
    $this->center   = isset($_params['center']) ? $_params['center'] : $this->address;
    $this->type     = isset($_params['type']) ? $_params['type'] : $this->type;
    $this->width    = isset($_params['w']) ? $_params['w'] : $this->width;
    $this->height   = isset($_params['h']) ? $_params['h'] : $this->height;
    $this->zoom     = isset($_params['z']) ? $_params['z'] : $this->zoom;

    $this->default_src = 'http://maps.google.com/maps';
    $this->static_src  = 'http://maps.googleapis.com/maps/api/staticmap';

    $this->html        = null;
    $this->attr        = isset( $_params['attr'] ) ? $_params['attr'] : array();

    // register Google Maps JS API v3
    wp_register_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?v=3&sensor=false', '', false, true);

    $this->_process();
  }

  private function _process()
  {
    if( $this->type == 'static' )
      $this->_process_static();
    else
      $this->_process_default();
  }

  private function _process_default()
  {
    // open tag and define map size
    $this->html = str_get_html("<div style=\"width:{$this->width}px; height:{$this->height}px;\"></div>");

    // define id
    $this->html->find('div', 0)->id = 'map_canvas';

    // instance API script
    add_action( 'wp_footer', create_function($args, "wp_enqueue_script('google-maps');") );
    add_action( 'wp_footer', array($this, '_init_script') );
  }

  private function _process_static()
  {
    // open tag
    $this->html = str_get_html('<img />');

    // define id
    if( !empty($this->attr['id']) )
      $this->html->find('img', 0)->id = $this->attr['id'];

    // build img src
    $request = "?center={$this->center}&zoom={$this->zoom}&size={$this->width}x{$this->height}&markers=color:red|{$this->center}&sensor=false";
    $this->html->find('img', 0)->src = $this->static_src . $request;

    // set alt text
    $this->html->find('img', 0)->alt = __("Map from {$this->address}");
  }

  public function render()
  {
    // print
    echo $this->html;
  }

  public function _init_script()
  { ?>
<script type="text/javascript">
jQuery(document).ready(function($)
{
  var geocoder = new google.maps.Geocoder();

  geocoder.geocode( { 'address': '<?php echo $this->center ?>'}, function(results, status)
  {
    if (status == google.maps.GeocoderStatus.OK)
    {
      var lat = results[0].geometry.location.lat();
      var lng = results[0].geometry.location.lng();

      mapOptions = {
        center: new google.maps.LatLng(lat, lng),
        zoom: <?php echo $this->zoom ?>,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      };

      var map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

      // add maker
      var marker = new google.maps.Marker({
        position: results[0].geometry.location,
        map: map
      });
    }
  });
});
</script><?php 
  }
}