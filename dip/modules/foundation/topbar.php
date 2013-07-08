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
  $obj = new DP_Foundation_Topbar($_name, $_params);
  $obj->render();
}

if(!class_exists('DP_Menu')) require('modules/menu.php');

class DP_Foundation_Topbar extends DP_Menu
{
  public function render()
  {
    global $post;

    // open tag
    $classes = isset($this->attr['class']) ? ' ' . $this->attr['class'] : '';
    $this->html = str_get_html("<nav class=\"top-bar{$classes}\"></nav>");

    // define navbar id
    if( !empty($this->attr['id']) )
      $this->html->find('nav', 0)->id = $this->attr['id'];

    // render content
    if ( empty( $this->nodes ) )
    {
      if(!current_user_can('edit_theme_options')) return;
      $this->html->find('nav', 0)->innertext = "<section class=\"top-bar-section\"><ul><li><a href=\"/wp-admin/nav-menus.php\">Hey, this menu is empty! Add something here!</a></li></ul></section>";
    }
    else
    {
      // add toglle buttom
      $name = !empty($this->title) ? "<h1><a href=\"\">{$this->title}</a></h1>" : '';
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
}