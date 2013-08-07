<?php
/**
 * The helper for displaying Galleries and
 * filter default gallery using jQuery Fancybox
 *
 * @package Dip Framework
 * @subpackage Helpers
 * @since Dip Framework 1.0
*/

/** apply filters */
add_filter( 'post_gallery', '_dip_filter_gallery', 10, 2 );

function _dip_filter_gallery( $output, $attr )
{
  $obj = new Dip_Helper_Gallery($attr);
  $output = $obj->render();

  return $output;
}

// template functions
function dip_gallery( $attr )
{
  $obj = new Dip_Helper_Gallery($attr);
  echo $obj->render();
}

class DP_Helper_Gallery
{
  private $post;

  private $html;
  private $nodes;
  private $attr;

  public function  __construct ($_params)
  {
    $this->attr = new stdClass();

    // set default values
    $this->attr->order       = 'ASC';
    $this->attr->orderby     = 'menu_order ID';
    $this->attr->id          = (int) $post->ID;
    $this->attr->itemtag     = 'dl';
    $this->attr->icontag     = 'dt';
    $this->attr->captiontag  = 'dd';
    $this->attr->columns     =  3;
    $this->attr->size        = 'thumbnail';
    $this->attr->include     = '';
    $this->attr->exclude     = '';

    // We're trusting author input, so let's at least make sure 
    // itlooks like a valid orderby statement
    if ( isset($_params['orderby']) )
    {
      $_params['orderby'] = sanitize_sql_orderby( $_params['orderby'] );

      if ( !$_params['orderby'] )
        unset( $_params['orderby'] );
    }

    // set instance values
    $this->attr->order      = isset($_params['order']) ? $_params['order'] : $this->attr->order;
    $this->attr->orderby    = ($_params['order'] == 'RAND') ? 'none' : isset($_params['orderby']) ? $_params['orderby'] : $this->attr->orderby;
    $this->attr->itemtag    = isset($_params['itemtag']) ? tag_escape($_params['itemtag']) : $this->attr->itemtag;
    $this->attr->icontag    = isset($_params['icontag']) ? tag_escape($_params['icontag']) : $this->attr->icontag;
    $this->attr->captiontag = isset($_params['captiontag']) ? tag_escape($_params['captiontag']) : $this->attr->captiontag;
    $this->attr->columns    = isset($_params['columns']) ? (int) $_params['columns'] : $this->attr->columns;
    $this->attr->size       = isset($_params['size']) ? $_params['size'] : $this->attr->size;
    $this->attr->include    = isset($_params['include']) ? $_params['include'] : $this->attr->include;
    $this->attr->exclude    = isset($_params['exclude']) ? $_params['exclude'] : $this->attr->exclude;

    $this->attr->itemwidth  = $this->attr->columns > 0 ? floor(100/$this->attr->columns) : 100;
    $this->attr->float      = is_rtl() ? 'right' : 'left';
    $this->attr->link       = isset($_params['link']) ? $_params['link'] : null;

    $this->nodes = array();
    $this->html  = null;

    $this->_process();
  }

  private function _process()
  {
    // get attachments
    if( !empty($this->attr->include) )
    {
      $this->attr->include = preg_replace( '/[^0-9,]+/', '', $this->attr->include );
      $attachments = get_posts( array('include' => $this->attr->include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $this->attr->order, 'orderby' => $this->attr->orderby ) );

      foreach ( $attachments as $att )
        $this->nodes[$att->ID] = $att;
    }
    elseif( !empty($this->attr->exclude) )
    {
      $this->attr->exclude = preg_replace( '/[^0-9,]+/', '', $this->attr->exclude );
      $this->nodes = get_children( array('post_parent' => $this->attr->id, 'exclude' => $this->attr->exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $this->attr->order, 'orderby' => $this->attr->orderby) );
    }
    else
    {
      $this->nodes = get_children( array('post_parent' => $this->attr->id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $this->attr->order, 'orderby' => $this->attr->orderby) );    
    }
  }

  public function render()
  {
    if( empty($this->nodes) ) return '';

    // render to feed
    if( is_feed() )
    {
      foreach( $this->nodes as $att )
        $this->html .= wp_get_attachment_link($att->ID, $this->attr->size, true) . "\n";

      return $this->html;
    }

    // default render open tag
    $this->html = str_get_html("<div class='gallery galleryid-{$this->attr->id}'></div>");

    $index = 0;
    foreach ( $this->nodes as $att )
    {
      // open node tag
      $node = str_get_html("<{$this->attr->itemtag} class=\"gallery-item\"></{$this->attr->itemtag}>");

      // add image link
      $link = $this->attr->link == 'file' ? wp_get_attachment_link($att->ID, $this->attr->size, true, false) : wp_get_attachment_link($att->ID, $this->attr->size, false, false);
      $node->find($this->attr->itemtag, 0)->innertext = "<{$this->attr->icontag} class=\"gallery-icon\">{$link}</{$icontag}>";
  
      if( $this->attr->captiontag && trim($att->post_excerpt) )
      {
        $caption = "<{$this->attr->captiontag} class=\"gallery-caption\">
                   " . wptexturize($att->post_excerpt) . "
                   </{$this->attr->captiontag}>";

        $node->find($this->attr->itemtag, 0)->innertext .= $caption;
      }

      // break line
      $sufix = ( $this->attr->columns > 0 && ++$index % $this->attr->columns  == 0 ) ? '<br style="clear: both" />' : '';

      $this->html->find('div.gallery', 0)->innertext .= $node . $sufix;
    }

    // call scripts
    add_action( 'wp_footer', array($this, '_init_script') );

    // print
    $this->html->find('div.gallery', 0)->innertext .= '<br style="clear: both;" />';
    return $this->html;
  }

  public function _init_script()
  { ?>
<style type="text/css">
.galleryid-<?= $this->attr->id ?> { margin: auto; }
.galleryid-<?= $this->attr->id ?> .gallery-item { float: <?= $this->attr->float ?>; text-align: center; margin-top: -1px; width: <?= $this->attr->itemwidth ?>%; }
.galleryid-<?= $this->attr->id ?> .gallery-caption { margin-left: 0; }
</style>
<script type="text/javascript">
jQuery(document).ready(function($)
{
  $(".galleryid-<?= $this->attr->id ?> a")
    .attr("rel", "gallery")
    .fancybox({
      openEffect : 'elastic',
      closeEffect : 'elastic',
      helpers : {
        title : false
      }});
});
</script><?php 
  }
}