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
  public $theme;
  public $config;
  public $modules;
  public $routes;
  public $ui;

  function __construct()
  {
    global $loader;

    $this->config  = isset($loader['config']) ? $loader['config'] : array();
    $this->routes  = isset($loader['routes']) ? $loader['routes'] : false; 
    $this->modules = array_replace(array('foundation'=>true), (array)$loader['modules']);

    /** run the router to redirect pages */
    $this->_run_router();

    /** get all theme info */
    $this->theme = wp_get_theme();
    $this->theme->stylesheet_uri           = get_stylesheet_uri();
    $this->theme->stylesheet_directory_uri = get_stylesheet_directory_uri();
    $this->theme->template_directory_uri   = get_template_directory_uri();

    $this->_include_libs();

    if(!is_admin()) return;
    $this->ui = new DP_UserInterface();
  }
  
  public function start()
  {
    $this->_init_supports();
    $this->_load_modules();
    $this->_call_hooks();
  }
  
  protected function _run_router()
  {
    if($this->routes)
      add_action('template_redirect', array(&$this, '_set_router'));
    else
      return false;
  }

  public function _set_router()
  {
    global $wp_query, $post; 

    foreach($this->routes as $key=>$value)
    {
      if(is_page($key)) {
        wp_redirect( site_url($value) );
        exit;
      }
    }
  }

  protected function _include_libs()
  {
    // Libraries
    require_once('vendor/simplehtmldom.php');
    require_once('helpers/array.php');

    // Module classes
    require_once('post-type.php');
    require_once('taxonomy.php');

    // 
    if(is_admin()) {
      require_once('form.php');
      require_once('panel.php');
      require_once('ui.php');
    } else {
      require_once('helpers/template-tags.php');
    } 
  }

  protected function _call_hooks()
  {
    if(is_admin()) {
      /** wp-admin hooks */
      add_action('admin_print_styles', array(&$this->ui, 'apply'));
    } else {
      /** theme hooks */
      add_action('init', array(&$this, '_load_scripts'));
    }
  }

  protected function _init_supports()
  {
    // Remove admin bar
    if(isset($this->config['adminbar']) && $this->config['adminbar'] == false)
      add_filter('show_admin_bar', '__return_false');  
    
    // Register menus
    if(isset($this->config['menus']) && is_array($this->config['menus']))
      register_nav_menus($this->config['menus']);
    
    // Register sidebars
    if(isset($this->config['sidebars']) && is_array($this->config['sidebars'])) {
      foreach ($this->config['sidebars'] as $id=>$args) {
        if(is_int($id)) $id = $args;
        $defaults = array(
          'name'          => ucwords(str_replace('-', ' ', $id)),
          'id'            => $id
        );
        
        $args = array_merge($defaults, (array)$args);
        register_sidebar($args);
      }  
    }  
  }

  protected function _load_modules()
  {
    if(!is_array($this->modules)) return;
    foreach($this->modules as $module=>$args)
    {
      if($args === true)
      {
        $filename = stream_resolve_include_path("modules/{$module}.php") ? "modules/{$module}.php" : "modules/{$module}/{$module}.php";
        include_once($filename);
      }
    }
  }

  public function _load_scripts()
  {
    wp_register_style('dip', $this->theme->stylesheet_uri, false, $this->theme->version);

    if(is_child_theme() && file_exists($this->theme->stylesheet_directory_uri.'/script.js'))
      wp_register_script('dip', $this->theme->stylesheet_directory_uri.'/script.js', false, $this->theme->version, true);
    elseif(is_child_theme())
      wp_register_script('dip', $this->theme->template_directory_uri.'/script.js', false, $this->theme->parent()->version, true);
    else
      wp_register_script('dip', $this->theme->template_directory_uri.'/script.js', false, $this->theme->version, true);

    wp_enqueue_style('dip');
    wp_enqueue_script('dip');
  }
}