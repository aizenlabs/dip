<?php
/**
 * Class to load the framework and modules
 *
 * @package Dip Framework
 * @subpackage Core
 * @since Dip Framework 1.0
 */

class DP_Bootstrap
{
  public $config;
  public $modules;
  public $ui;
  
	function __construct()
	{
	  global $loader;

    $this->config  = $loader['config'];
    $this->modules = $loader['modules']; 

    $this->_include_libs();

    if(!is_admin()) return;
		$this->ui = new DP_UserInterface();
    set_error_handler(array(&$this->ui, 'admin_alert_errors'), E_ERROR ^ E_CORE_ERROR ^ E_COMPILE_ERROR ^ E_USER_ERROR ^ E_RECOVERABLE_ERROR ^  E_WARNING ^  E_CORE_WARNING ^ E_COMPILE_WARNING ^ E_USER_WARNING ^ E_USER_NOTICE ^ E_DEPRECATED ^ E_USER_DEPRECATED ^ E_PARSE);
  }
  
  public function start()
  {
    $this->_init_supports();
    $this->_load_modules();
    $this->_call_hooks();
    $this->_load_scripts();
  }

  protected function _load_scripts()
  {
    wp_register_style('dip', get_stylesheet_uri(), false, '1.0.0');
    wp_register_script('dip', get_bloginfo('template_url').'/script.js', false, '1.0.0', true);

    wp_enqueue_style('dip');
    wp_enqueue_script('dip');
  }

  protected function _include_libs()
  {
    // Libraries
    require_once('vendor/simplehtmldom.php');

    // Module classes
    require_once('post-type.php');
    require_once('taxonomy.php');

    // 
    if(!is_admin()) return;
    require_once('form.php');
    require_once('panel.php');
    require_once('ui.php');
  }

  protected function _init_supports()
  {
    // Register menus
    if(is_array($this->config['menus']))
      register_nav_menus($this->config['menus']);
  }

  protected function _call_hooks()
  {
    /** wp-admin hooks */
    if(!is_admin()) return;
    add_action('admin_print_styles', array(&$this->ui, 'apply'));
  }

  protected function _load_modules()
  {
    foreach($this->modules as $module=>$args)
    {
      if($args === true)
      {
        $filename = stream_resolve_include_path("modules/{$module}.php") ? "modules/{$module}.php" : "modules/{$module}/{$module}.php";
        include_once($filename);
      }
    }
  }
}