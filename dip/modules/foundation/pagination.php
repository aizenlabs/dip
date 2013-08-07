<?php
/**
 * The helper for displaying pagination links
 *
 * @package Dip Framework
 * @subpackage Foundation Module
 * @version 1.0.0
 * @since Dip Framework 1.0
 */

function dp_pagination($args = null)
{
  $obj = new DP_Foundation_Pagination($args);
  $obj->render();
}

class DP_Foundation_Pagination
{
  private $base;
  private $format;
  private $search;
  private $total;
  private $current;
  private $show_all;
  private $end_size;
  private $mid_size;
  private $prev_next;
  private $prev_text;
  private $next_text;

  private $centered;

  private $nodes;
  private $html;

  public function  __construct($args)
  {
    global $dip, $wp_query;

    $protocol           = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $this->base         = $protocol . preg_replace('/(page.*|\?.*)/', '', $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );

    $this->format       = $dip->site->permalink ? 'page/%#%/' : '?page=%#%';
    $this->search       = get_query_var('s') ? get_query_var('s') : false;

    $this->total        = $wp_query->max_num_pages;
    $this->current      = get_query_var('paged') ? (int)get_query_var('paged') : 1;

    $this->end_size     = isset($args['end_size']) ? $args['end_size'] : 1;
    $this->mid_size     = isset($args['mid_size']) ? $args['mid_size'] : 2;

    $this->prev_next    = isset($args['prev_next']) ? $args['prev_next'] : true;
    $this->prev_text    = isset($args['prev_text']) ? $args['prev_text'] : __('« Previous');
    $this->next_text    = isset($args['next_text']) ? $args['next_text'] : __('Next »');

    $this->centered     = isset($args['centered']) ? $args['centered'] : true;
    
    $this->_process();
  }

  protected function _process()
  {
    /* add search query into base format */
    if($this->search)
    {
      $this->format = str_replace('?', "?s={$this->search}&", $this->format);
    }
    
    /** add mid nodes */
    for($i = $this->current - $this->mid_size; $i <= $this->current + $this->mid_size; $i++)
    {
      if($i > 0 && $i != $this->current && $i <= $this->total)
        $this->nodes[$i] = $this->_get_node($i);
    }

    /** add start and end nodes */
    for($i = 1; $i <= $this->end_size; $i++)
    {
      $this->nodes[$i] = $this->_get_node($i);

      if($i == $this->end_size && !isset($this->nodes[$this->end_size + 1]))
        $this->nodes[$this->end_size + 1] = $this->_get_node($this->end_size + 1, 'dots');
    }

    for($i = $this->total; $i > $this->total - $this->end_size; $i--)
    {
      $this->nodes[$i] = $this->_get_node($i);

      if($i == ($this->total - $this->end_size + 1) && !isset($this->nodes[$this->total - $this->end_size]))
        $this->nodes[$this->total - $this->end_size] = $this->_get_node($this->total - $this->end_size, 'dots');
    }
    
    /** add prev and next nodes */
    if($this->prev_next == true)
    {
      $this->nodes[0] = $this->_get_node($this->current - 1, 'arrow');
      $this->nodes[$this->total + 1] = $this->_get_node($this->current + 1, 'arrow');
    }
    
    /** add current page */
    $this->nodes[$this->current] = $this->_get_node($this->current, 'current');

    /* order the array */
    ksort($this->nodes);    
  }
  
  public function _get_node($page, $type = 'page')
  {
    $url = $this->base . str_replace('%#%', $page, $this->format);
    
    switch ($type)
    {
      case 'current':
        $text  = $page;
        $class = 'current';
        break;

      case 'dots' :
        $text  = '&hellip;';
        $class = 'unavailable';
        $url   = '';
        break;

      case 'arrow' :
        $text  = $page == $this->current-1 ? $this->prev_text : $this->next_text;

        if($page == 0 || $page == $this->total+1)
        {
          $class = 'arrow unavailable';
          $url = '';
        }
        else
        {
          $class = 'arrow';
        }
        break;

      default:
        $text  = $page;
        $class = null;
        break;
    }
    
    return array('text'  => $text,
                 'class' => $class,
                 'url'   => $url);
  }

  public function render()
  {
    if(!empty($this->nodes))
    {
      $this->html = str_get_html('<ul class="pagination"></ul>'); 

      foreach ($this->nodes as $node)
      {
        $class = !is_null($node['class']) ? " class=\"{$node['class']}\"" : '';
        $item = "<li{$class}><a href=\"{$node['url']}\">{$node['text']}</a></li>";

        $this->html->find('ul', 0)->innertext .= $item;
      }
      
      /** set as centered */
      if($this->centered == true)
      {
        $this->html = '<div class="pagination-centered">' . $this->html->outertext . '</div>';
      }
    }

    echo $this->html;
  }
}