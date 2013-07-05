<?php
/**
 * The helper for displaying Twitter widget
 *
 * @package Dip Framework
 * @subpackage Helper
 * @since Dip Framework 1.0
*/

// template functions
function dp_twitter($_user, $_params = array())
{
  $obj = new DP_Helper_Twitter($_user, $_params);
  $obj->render(); 
}

class DP_Helper_Twitter
{
  private $user;
  private $source;
  private $count;

  private $html;
  private $nodes;
  private $attr;

  public function  __construct ( $_user, $_params = array() )
  {
    $this->user   = $_user;
    $this->count  = isset( $_params['count'] ) ? $_params['count'] : 5;

    $this->source = "http://api.twitter.com/1/statuses/user_timeline.json?screen_name={$this->user}&count={$this->count}";

    $this->html   = null;
    $this->nodes  = array();
    $this->attr   = isset( $_params['attr'] ) ? $_params['attr'] : array();

    $this->_process();
  }

  private function _process()
  {
    $twits = json_decode( file_get_contents( $this->source ) );

    $pattern = array('/(http\:\/{2}[-a-zA-Z0-9_.\/]+)[[:space:]]?/', '/\@([a-zA-Z0-9]+)[[:space:]]?/', '/\#([a-zA-Z0-9]+)[[:space:]]?/');
    $replace = array('<a href="$1" target="_blank">$1</a> ', '<a href="http://twitter.com/#!/$1" target="_blank">@$1</a> ', '<a href="http://twitter.com/#!/search?q=%23$1" target="_blank">#$1</a> ');

    foreach ( $twits as $twit )
    {
      $node = new stdClass();

      $node->text = preg_replace($pattern, $replace, $twit->text);
      $node->date = date(' d M', strtotime($twit->created_at));
      $node->user = $twit->user->screen_name;

      $this->nodes[] = $node;
    }
  }

  public function render()
  {
      // open tag
    $this->html = str_get_html('<ul class="twitter"></ul>');

    // define orbit id
    if( !empty($this->attr['id']) )
      $this->html->find('ul', 0)->id = $this->attr['id'];

    if ( empty( $this->nodes ) )
    {
      return;
    }
    else
    {
      foreach ($this->nodes as $node)
      {
        $html  = "<li class=\"twit\">{$node->text}<br />";
        $html .= "<span>{$node->date} via @{$node->user}</span></li>";

        $this->html->find('ul', 0)->innertext .= $html;
      }
    }

    // print
    echo $this->html;
  }
}