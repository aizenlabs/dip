<?php
/**
 * The helper for displaying Breadcrumbs
 *
 * @package Dip Framework
 * @subpackage Foundation Module
 * @version 1.0.0
 * @since Dip Framework 1.0
 */

/**
 * @param string $menu menu name or registered location
 */
function dp_breadcrumbs($menu)
{
  $obj = new DP_Foundation_Breadcrumbs($menu);
  $obj->render();
};

class DP_Foundation_Breadcrumbs
{
  private $menu_name;
  private $menu_items;

  private $self_url;

  private $html;
  private $nodes;

  public function  __construct($_menu)
  {
    /** check if parameter is a menu location */
    if(has_nav_menu($_menu)) {
      $theme_locations = get_nav_menu_locations();
      $menu_obj = get_term( $theme_locations[$_menu], 'nav_menu' );
      $this->menu_name = $menu_obj->name;
    } else {
      $this->menu_name = $_menu;
    }

    $protocol         = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $this->self_url   = $protocol . preg_replace('/\?.*/', '', $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );
    $this->menu_items = wp_get_nav_menu_items($this->menu_name);    
    $this->nodes      = array();

    $this->_process();
  }

  private function _process()
  {
    if(!is_array($this->menu_items)) return;

    /** init process by current page */
    $current = $this->_get_current_item();

    if($current)
    {
      $this->nodes[] = $current;

      /** get first parent id */
      $current = $this->nodes[0]->parent;

      while( $current != 0 ) {
        $node = $this->_get_parent_item($current);

        $this->nodes[] = $node;
        $current = $node->parent;
      }
    }

    /** add home */
    $node = new stdClass();

    $node->title = 'Home';
    $node->url = home_url('/');
    $node->alt = __('Go to home page');

    $this->nodes[] = $node;
      
    /** reorder nodes array */
    $this->nodes = array_reverse($this->nodes);
  }

  protected function _get_current_item()
  {
    if(!is_array($this->menu_items)) return;

    /** set default return */
    $node = false;

    foreach ($this->menu_items as $item)
    {
      if( $item->url == $this->self_url )
      {
        $node = new stdClass();

        $node->title  = $item->title;
        $node->url    = $item->url;
        $node->parent = $item->menu_item_parent;
        $node->alt    = __('Reload current page');

        return $node;
      }
    }

    return $node;
  }

  protected function _get_parent_item($_parent)
  {
    // set default return
    $node = false;

    foreach ($this->menu_items as $item)
    {
      if( $item->ID == $_parent )
      {
        $node = new stdClass();

        $node->title  = $item->title;
        $node->url    = $item->url;
        $node->parent = $item->menu_item_parent;
        $node->alt    = sprintf(__('Go to %s'), $item->title);

        return $node;
      }
    }
    return $node;
  }

  public function render()
  {
    /** open tag */
    $this->html = str_get_html('<ul class="breadcrumbs"></ul>');

    /** define breadcrumb id */
    if(!empty($this->attr['id']))
      $this->html->find('ul', 0)->id = $this->attr['id'];
    
    if(empty($this->nodes) && current_user_can('edit_theme_options')) {
      $str = str_get_html("<li><a href=\"#\" alt=\"\">".__('Hey, the indicated menu is empty or does not exists!')."</a></li>");
      $this->html->find('ul', 0)->innertext .= $str;
    }

    foreach ($this->nodes as $node)
    {
      $str = str_get_html("<li><a href=\"{$node->url}\" alt=\"{$node->alt}\">{$node->title}</a></li>");

      if( $str->find('a[href=#]') )
        $str->find('a[href=#]', 0)->parent()->class = 'unavailable';

      $this->html->find('ul', 0)->innertext .= $str;
    }

    /** reload parser */
    $this->html = str_get_html($this->html);
    $this->html->find('li', -1)->class = 'current';

    /** print */
    echo $this->html;
  }
}