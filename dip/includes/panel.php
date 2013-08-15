<?php
/**
 * Abstract class to help create new Admin Panels
 *
 * @package Dip Framework
 * @subpackage Module Helpers
 * @since 1.0.0
 */

abstract class DP_Panel
{
  public $name;
  public $menu_title;
  public $namespace;
  public $module;
  public $settings;  

  public $parent       = 'themes.php';
  public $capability   = 'edit_theme_options';
  public $icon         = 'options';
  public $position     = '100';

  public $tabs         = false;
  public $current_tab;

  public $shortcut     = null;

  public $screen_id;
  public $metaboxes    = false;

  /**
   * Call init method and create new Panel
   * @return DP_Panel self-object
   */
  public function __construct()
  {
    /** don't run outside of wp-admin */
    if(!is_admin()) return;

    /** call extend class init */
    $this->init();
    
    /** fill necessary attributes if is null */
    if(is_null($this->menu_title)) $this->menu_title = $this->name;
    if(is_null($this->module)) $this->module = $this->namespace;
    if($this->tabs && is_null($this->current_tab)) $this->current_tab = isset($_GET['tab']) ? $_GET['tab'] : current(array_keys($this->tabs));
    
    /** customize wp-admin icons for the new panel */
    global $dip;
    $dip->ui->set_icon32($this->namespace, $this->icon);
    if($this->parent==false) $dip->ui->set_menu_icon('toplevel_page_'.$this->namespace, $this->icon);

    /** only if is a form submit */
    if (isset($_POST['option_page']) && $_POST['option_page'] == $this->namespace)
    {
      /** call abstract method to save */
      $this->save_settings();

      /* redirect to original tab, if was setted */
      wp_redirect($this->get_panel_uri($this->current_tab, '&updated=true'));
      exit;
    }

    /** load saved settings */
    $this->settings = get_option($this->namespace);

    /** call WordPress hooks */
    add_action('admin_menu', array($this, 'register_panel'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
  }

  /**
   * Abstract method to configure the new Panel
   * @return void
   */
  abstract public function init();

  /**
   * Hooked method to register the new Panel
   * @return void
   */
  public function register_panel()
  {
    /** register panel in admin menu */
    if($this->parent == false) {
      $this->screen_id = add_menu_page($this->name, $this->menu_title, $this->capability, $this->namespace, array($this, 'panel_view'), '', $this->position);
    } else {
      $this->screen_id = add_submenu_page($this->parent, $this->name, $this->menu_title, $this->capability, $this->namespace, array($this, 'panel_view'));
    }

    add_action('load-'.$this->screen_id, array($this, 'register_setting'));
    add_action('load-'.$this->screen_id, array($this, 'do_request'));

    /* Add callbacks for this screen only. */
    if($this->metaboxes)
    {
      add_action('load-'.$this->screen_id, array($this, 'allow_meta_boxes'));
      add_action('admin_footer-'.$this->screen_id, array($this, 'print_meta_boxes_scripts'));

      $this->add_meta_boxes();
    }
    
    /* Init help tabs */
    $this->add_screen_options();
    $this->add_help_tabs();
  }

  /**
   * Hooked method to register the Panel settings
   * @return void
   */
  public function register_setting()
  {
    register_setting($this->namespace, $this->settings);
  }

  /**
   * Empty methods to save registered settings and register scripts
   * @return void
   */
  public function save_settings() {}
  public function enqueue_scripts() {}

  /**
   * Hooked method to render the panel view
   * @param string $tab
   * @return string
   */
  public function get_panel_uri($tab = null, $sufix = null)
  {
    $separator = strpos($this->parent, '?') ? '&' : '?';
    $tab_query = !is_null($tab) ? "&tab={$tab}" : '';
    $page_base = !empty($this->parent) ? $this->parent : 'admin.php';
    return admin_url("{$page_base}{$separator}page={$this->namespace}{$tab_query}{$sufix}");
  }
  
  /**
   * Hooked method to render the panel view
   * @return void
   */
  public function panel_view()
  {
    /** open container */
    echo '<div class="wrap">';

    /** has tabs or sigle page */
    $this->panel_header();

    /** include file view or show the error */
    $filename = $this->get_panel_view_filename();
    if(empty($filename) || (include $filename) === false)
    {
      printf('<p>'.__('You need create the panel view!'.'</p>', 'dip'));
      return;
    }
    
    /** close container */
    echo '</div>';
  }

  /**
   * Get a valid filname view
   * @return string $filename
   */
  public function get_panel_view_filename()
  {
    /** set template view order */
    if($this->tabs)
    {
      $paths = array(
        "panels/{$this->module}/{$this->current_tab}.phtml",
        "panels/{$this->module}/{$this->namespace}/{$this->current_tab}.phtml",
        "modules/{$this->module}/{$this->current_tab}.phtml",
        "modules/{$this->module}/panels/{$this->current_tab}.phtml",
        "modules/{$this->module}/panels/{$this->namespace}/{$this->current_tab}.phtml"
      );
    }
    else
    {
      $paths = array(
        "panels/{$this->namespace}.phtml",
        "panels/{$this->module}/{$this->namespace}.phtml",
        "modules/{$this->module}/{$this->namespace}.phtml",
        "modules/{$this->module}/panels/{$this->namespace}.phtml"        
      );
    }
    
    /** search a valid panel view */
    foreach($paths as $filename) {
      if(stream_resolve_include_path($filename))
        return $filename;
    }
  }

  /**
   * Print panel header: tabs or single title
   * @return void
   * @uses screen_icon() http://codex.wordpress.org/Function_Reference/screen_icon
   */
  public function panel_header()
  {
    screen_icon($this->namespace);

    if($this->tabs)
    {
      echo '<h2 class="nav-tab-wrapper">';
      foreach ($this->tabs as $key => $label)
      {
        $class = ($key == $this->current_tab) ? ' nav-tab-active' : '';
        printf('<a class="nav-tab%s" href="%s">%s</a>', $class, $this->get_panel_uri($key), $label);
      }
      echo '</h2>';
    }
    elseif(!empty($this->shortcut))
    {
      $label = isset($this->shortcut['label']) ? $this->shortcut['label'] : __('Add New');
      $uri   = isset($this->shortcut['uri']) ? $this->shortcut['uri'] : $this->shortcut;

      printf(
        '<h2>%s <a href="%s" class="add-new-h2">%s</a></h2>',
        esc_html($this->name),
        esc_url(admin_url($uri)),
        esc_html($label)
      );
    }
    else
    {
      printf('<h2>%s</h2>', esc_html__($this->name));
    }
  }

  /**
   * Actions to be taken prior to page loading. This is after headers have been set.
   * @uses load-$hook
   * @since 1.1.0
   */
  public function allow_meta_boxes()
  {
    /* Trigger the add_meta_boxes hooks to allow meta boxes to be added */
    do_action('add_meta_boxes_'.$this->screen_id, null);
    do_action('add_meta_boxes', $this->screen_id, null);

    /* Enqueue WordPress' script for handling the meta boxes */
    wp_enqueue_script('postbox');

    if(method_exists($this, 'get_screen_options')) return;
    /* Add screen option: user can choose between 1 or 2 columns (default 2) */
    add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );
  }

  /**
   * Prints script in footer. This 'initialises' the meta boxes
   * @since 1.1.0
   */
  function print_meta_boxes_scripts()
  {
    echo '<script>jQuery(document).ready(function(){ postboxes.add_postbox_toggles(pagenow); });</script>';
  }
  
  /**
   * Automatic list and register defined meta boxes
   * @return void
   * @since 1.1.0
   */
  public function add_meta_boxes()
  {
    /** list all methods */
    $methods = get_class_methods($this);

    /** search defined meta boxes */
    foreach($methods as $i=>$method)
    {
      /** select meta box config method by pattern */
      if(preg_match('/^_meta_.+$/', $method))
      {
        $id = preg_replace('/_meta_/', '', $method, 1);
        $title = __(ucfirst(str_replace('_', ' ', $id)));
        
        /** register the meta box */
        add_meta_box($id, $title, array($this, $method), $this->screen_id, 'normal', 'high');
      }
    }
  }

   /**
   * Automatic add screen options
   * @return void
   * @since 1.1.0
   */
  public function add_screen_options()
  {
    if(!method_exists($this, 'get_screen_options')) return;
    
    /** 
     * Create the WP_Screen object against your admin page handle
     * This ensures we're working with the right admin page
     */
    $admin_screen = WP_Screen::get($this->screen_id);
    $options = $this->get_screen_options();

    foreach($options as $key=>$values)
      $admin_screen->add_option($key, $values);
  }

  /**
   * Automatic list and register help tabs
   * @return void
   * @since 1.1.0
   */
  public function add_help_tabs()
  {
    if(!method_exists($this, 'get_help_tabs')) return;
    
    /** 
     * Create the WP_Screen object against your admin page handle
     * This ensures we're working with the right admin page
     */
    $admin_screen = WP_Screen::get($this->screen_id);
    $help = $this->get_help_tabs();

    foreach($help['tabs'] as $tab)
      $admin_screen->add_help_tab($tab);

    if(isset($help['sidebar']))
      $admin_screen->set_help_sidebar($help['sidebar']);
  }
  
   /**
   * Automatic call the related method after a form submit
   * @return void
   * @since 1.1.0
   */
  public function do_request()
  {
    if($_SERVER['REQUEST_METHOD'] != "POST") return;

    /** list all methods */
    $methods = get_class_methods($this);

    /** search defined meta boxes */
    foreach($methods as $i=>$method)
    {
      /** select meta box config method by pattern */
      if(preg_match('/^_action_.+$/', $method))
      {
        $action = str_replace('-', '_', preg_replace('/_action_/', '', $method, 1)) ;
        if($_POST['action'] == $action)
        {
          $this->$method();
          exit;
        }
      }
    }
  }
}