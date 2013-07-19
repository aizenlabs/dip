<?php
/**
 * The helper for displaying Menus and Navbars
 *
 * @package Dip Framework
 * @subpackage Foundation Module
 * @version 1.0.0
 * @since Dip Framework 1.0
 */

// template functions
function dp_menu($_name, $_params = array())
{
  $obj = new DP_Menu($_name, $_params);
  $obj->render(); 
}

class DP_Menu
{
  protected $title;
  protected $menu_name;
  protected $depth;
  protected $root;
  protected $dropdown;

  protected $self_url;
  protected $parent_url;

  protected $html;
  protected $nodes;
  protected $attr;
    
  /* @params( parent, root, dropdown, attr:array('name'=>'value') ) */
  public function  __construct ($_menu, $_params)
  {
    global $post;

    /** check if parameter is a menu location */
    if(has_nav_menu($_menu)) {
      $theme_locations = get_nav_menu_locations();
      $menu_obj = get_term($theme_locations[$_menu], 'nav_menu');
      $this->menu_name = $menu_obj->name;
    } else {
      $this->menu_name = $_menu;
    }

    $this->title       = isset( $_params['title'] ) ? $_params['title'] : '';
    $this->depth       = isset( $_params['parent'] ) ? DP_Menu::get_item_id( $_params['parent'], $this->menu_name ) : 0;
    $this->root        = isset( $_params['root'] ) ? $_params['root'] : false;
    $this->dropdown    = isset( $_params['dropdown'] ) ? $_params['dropdown'] : false;

    $protocol          = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $this->self_url    = $protocol . preg_replace('/(page.*|\?.*)/', '', $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );
    $this->parent_url  = $post->post_parent ? get_permalink( $post->post_parent ) : null;

    $this->html        = null;
    $this->nodes       = array();
    $this->attr        = isset( $_params['attr'] ) ? $_params['attr'] : null;

    $this->_process();
  }

  protected function _process ()
  {
    $menu_items = wp_get_nav_menu_items($this->menu_name);
    if(!is_array($menu_items)) return;

    foreach( $menu_items as $item )
    {
      // simplify the object
      $node = new stdClass();

      $node->id      = $item->ID;
      $node->parent  = $item->menu_item_parent;
      $node->title   = $item->title;
      $node->url     = $item->url;
      $node->classes = !empty($item->classes[0]) ? $item->classes : array();

      // organize node
      $this->nodes[$node->parent][$node->id] = $node;
    }
  }
  
  static function get_item_id ($_node_title, $_menu)
  {
    if(has_nav_menu($_menu)) {
      $theme_locations = get_nav_menu_locations();
      $menu_obj = get_term($theme_locations[$_menu], 'nav_menu');
      $menu_name = $menu_obj->name;
    } else {
      $menu_name = $_menu;
    }

    $menu_items = wp_get_nav_menu_items( $menu_name );
    if( !is_array( $menu_items ) ) return;

    foreach( $menu_items as $item )
    {
      if( $item->title == $_node_title )
        return $item->ID;
    }
    return false;
  }

  public function render()
  {
    global $post;

    // open tag
    $classes = isset($this->attr['class']) ? ' ' . $this->attr['class'] : '';
    $this->html = str_get_html("<ul class=\"nth-{$this->depth}{$classes}\"></ul>");

    // add others attributes to main tag
    $this->_add_html_attr();

    // render content
    if ( empty( $this->nodes ) )
    {
      if(!current_user_can('edit_theme_options')) return;
      $this->html->find('ul', 0)->innertext = "<li><a href=\"/wp-admin/nav-menus.php\">Hey, this menu (<em>{$this->menu_name}</em>) is empty! Add something here!</a></li>";
    }
    else
    {
      // recursive function
      $this->_render_node($this->depth);
    }

    // print
    echo $this->html;
  }

  protected function _add_html_attr()
  {
    $attrs = $this->attr;
    unset($attrs['class']);

    if(empty($attrs)) return;

    foreach ($attrs as $attr => $value) {
      $this->html->find('nav', 0)->$attr = $value;
    }
  }

  protected function _render_node($_parent)
  {
    if( !isset($this->nodes[$_parent]) || !is_array($this->nodes[$_parent]) ) return;

    foreach( $this->nodes[$_parent] as $node )
    {
      $node_html = str_get_html("<li><a>{$node->title}</a></li>");
      $node_html->find('li a', 0)->href = $node->url;

      // init dropdown
      if( isset($this->nodes[$node->id]) && is_array($this->nodes[$node->id]) && $this->dropdown )
      {
        $node->classes[] = 'has-dropdown';
        $node_html->find('li', 0)->innertext .= "<ul class=\"dropdown nth-{$node->id}\"></ul>";
      }

      // add classes
      $node = $this->_add_node_classes($node);
      $node_html->find('li', 0)->class = implode(' ', array_reverse($node->classes));

      // append node to html
      $this->html->find('.nth-'.$_parent, 0)->innertext .= $node_html;
      $this->html = str_get_html($this->html); // reload parse

      $this->_render_node($node->id);
    }
  }

  protected function _add_node_classes($_node)
  {
    // check if the link of the current page or parent page is equal to this
    switch ( $_node->url )
    {
      case $this->self_url : 
        $_node->classes[] = 'active';
        break;
      case $this->parent_url :
        array_push($_node->classes, 'active', 'parent');
        break;
    }

    return $_node;
  }
}