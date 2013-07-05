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
function dp_topbar($_name, $_params = array())
{
  $obj = new DP_Fondation_Topbar($_name, $_params);
  $obj->render(); 
}

class DP_Fondation_Topbar
{
  private $title;
  private $menu_name;
  private $depth;
  private $root;
  private $dropdown;

  private $self_url;
  private $parent_url;

  private $html;
  private $nodes;
  private $attr;
    
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
    $this->depth       = isset( $_params['parent'] ) ? DP_Helper_Menu::get_item_id( $_params['parent'], $this->menu_name ) : 0;
    $this->root        = isset( $_params['root'] ) ? $_params['root'] : false;
    $this->dropdown    = isset( $_params['dropdown'] ) ? $_params['dropdown'] : false;

    $this->self_url    = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $this->parent_url  = $post->post_parent ? get_permalink( $post->post_parent ) : null;

    $this->html        = null;
    $this->nodes       = array();
    $this->attr        = isset( $_params['attr'] ) ? $_params['attr'] : null;

    $this->_process();
  }

  private function _process ()
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
    $this->html = str_get_html('<nav class="top-bar"></nav>');

    // define navbar id
    if( !empty($this->attr['id']) )
      $this->html->find('nav', 0)->id = $this->attr['id'];

    // render content
    if ( empty( $this->nodes ) )
    {
      if(!current_user_can('edit_theme_options')) return;
      $this->html->find('nav', 0)->innertext = "<section class=\"top-bar-section\"><ul><li><a href=\"/wp-admin/nav-menus.php\">Hey, the menu is empty! Add something here!</a></li></ul></section>";
    }
    else
    {
      // add toglle buttom
      $name = !empty($this->title) ? "<h1><a href=\"#\">{$this->title}</a></h1>" : '';
      $str = "<ul class=\"title-area\"><li class=\"name\">{$name}</li> <li class=\"toggle-topbar menu-icon\"><a href=\"\"><span>Menu</span></a></li></ul>";
      $this->html->find('nav', 0)->innertext = $str;

      // open section 
      $str  = "<section class=\"top-bar-section\"><ul></ul></section>";
      $this->html->find('nav', 0)->innertext .= $str;

      // reload parser
      $this->html = str_get_html($this->html);        
      $this->html->find('section ul', 0)->class = 'nth-'.$this->depth;

      // recursive function
      $this->_render_node($this->depth);
    }

    // print
    echo $this->html;
  }

  private function _render_node($_parent)
  {
    if( !is_array($this->nodes[$_parent]) ) return;

    foreach( $this->nodes[$_parent] as $node )
    {
      $node_html = str_get_html("<li><a>{$node->title}</a></li>");
      $node_html->find('li a', 0)->href = $node->url;

      // init dropdown
      if( is_array($this->nodes[$node->id]) )
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

  private function _add_node_classes($_node)
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