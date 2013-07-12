<?php
/**
 * The helper for print arrays
 *
 * @package Dip Framework
 * @subpackage Helpers
 * @version 1.0.0
 * @since 1.0.0
 */

if(!function_exists('_p')) {
  /**
   * Check if array key isset and print the value
   * @param mixed $mixed
   * @param string $key
   * @return void
   */
  function _p($mixed, $key = null)
  {
    if(!is_array($mixed))
      echo $mixed;
    elseif(isset($mixed[$key]))
      echo $mixed[$key];
  }
}