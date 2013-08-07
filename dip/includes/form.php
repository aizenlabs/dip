<?php
/**
 * The helper for displaying form elements in admin pages
 *
 * @package Dip Framework
 * @subpackage Admin User Interface
 * @since Dip Framework 1.0
 */

class DP_Form
{
  public $namespace;
  public $rows;
  public $html;

  public function __construct($namespace = null)
  {
    $this->namespace = $namespace;
    $this->rows = array();
  }

  /** create a new row and add elements */
  public function row($elements = array())
  {
    /** adjustment so that rows assigned an array */
    if (!is_array($elements))
      $elements = array($elements);

    $this->rows[] = $elements;
  }

  public function text($name, $args = array())
  {
    $elem = $this->_element($name, $args);
    $elem->type = 'text';

    return $elem;
  }

  public function textarea($name, $args = array())
  {
    $elem = $this->_element($name, $args);
    $elem->type = 'textarea';

    return $elem;
  }

  private function _element($name, $args)
  {
    global $post;
    $elem = new stdClass();

    $elem->name = !is_null($this->namespace) ? $this->namespace . '_' . $name : $name;
    $elem->label = isset($args['label']) ? $args['label'] : ucfirst(str_replace('_', ' ', $name));
    $elem->cols = isset($args['cols']) ? $args['cols'] : 'twelve';
    $elem->value = get_post_meta($post->ID, $elem->name, true);
    return $elem;
  }

  public function render()
  {
    /** open tag */
    $this->html = str_get_html('<div class="dp_meta_box"></div>');

    foreach ($this->rows as $row)
    {
      $str_row = str_get_html('<div class="row"></div>');
      foreach ($row as $elem)
      {
        $str_col = str_get_html("<div class=\"{$elem->cols} columns\"></div>");
        $str_col->find('div', 0)->innertext = "<label>{$elem->label}:</label>";

        switch ($elem->type)
        {
          case 'text' :
            $str_col->find('div', 0)->innertext .= "<input type=\"text\" value=\"{$elem->value}\" name=\"{$elem->name}\">";
            break;
          case 'textarea' :
            $str_col->find('div', 0)->innertext .= "<textarea name=\"{$name}\">{$elem->value}</textarea>";
            break;
          default :
            break;
        }
        $str_row->find('div', 0)->innertext .= $str_col;
      }
      $this->html->find('div', 0)->innertext .= $str_row;
    }
    echo $this->html;
  }
}