<?php
/**
 * Abstract Class to Create new Post-Types
 *
 * @package Dip Framework
 * @subpackage Module Helpers
 * @since 1.0.0
 */

abstract class DP_PostType
{
  public $post_type;

  public $name;
  public $singular_name;
  public $icon;
  public $slug;

  public $labels = array();
  public $args   = array();

  public $nonce_field = null;
  public $custom_fields;

  public $bp_register = false;

  /**
   * Call init method and create new Post-Type
   * @return DP_PostType self-object
   */
  public function __construct()
  {
    $this->init();
    
    /** set post-type slug */
    if(empty($this->slug)) $this->slug = strtolower($this->name);

    /** populate config arrays */
    $this->set_labels();
    $this->set_args();

    /** call WordPress hooks */
    add_action('init', array($this, 'register_post_type'));

    /** don't call this hooks outside of wp-admin */
    if(!is_admin()) return;
    add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
    add_action('save_post', array($this, 'save_meta_data'));
  }

  /**
   * Abstract method to configure the new Post-Type
   * @return void
   */
  abstract public function init();

  /**
   * Hooked method to register the new Post-Type
   * @return void
   */
  public function register_post_type()
  {
    /** required hook */
    register_post_type($this->post_type, $this->args);

    /** don't call next hooks outside of wp-admin */
    if(!is_admin()) return;
    $is_edit = strpos($_SERVER["REQUEST_URI"], 'post_type='.$this->post_type);

    /** customize post-type admin icon */
    if(!empty($this->icon))
    {
      global $dip;
      $dip->ui->set_menu_icon('menu-posts-'.$this->post_type, $this->icon);
      if($is_edit) $dip->ui->set_icon32('edit', $this->icon);
    }

    /** if is setted, customize title placeholder */
    if(isset($this->args['title_placeholder']) && $is_edit)
      add_filter('enter_title_here', array($this, '_filter_title_placeholder'), 10, 2);

    /** option to remove the slug metabox */
    if(isset($this->args['no_slug_meta_box']) && $is_edit)
      add_action('admin_head', array($this, '_remove_slug_meta_box'));
  }

  /**
   * Generate default labels based in object attributes and marge with custom labels
   * @param array $custom
   * @return void
   */
  public function set_labels()
  {
    /** generate default labels */
    $defaults = array(
      'name'               => $this->name,
      'singular_name'      => $this->singular_name,
      'add_new_item'       => sprintf(__('Add New %1$s', 'dip'), $this->singular_name),
      'edit_item'          => sprintf(__('Edit %1$s', 'dip'), $this->singular_name),
      'new_item'           => sprintf(__('New %1$s', 'dip'), $this->singular_name),
      'all_items'          => sprintf(__('All %1$s', 'dip'), $this->name),
      'view_item'          => sprintf(__('View %1$s', 'dip'), $this->singular_name),
      'search_items'       => sprintf(__('Search %1$s', 'dip'), strtolower($this->name)),
      'not_found'          => sprintf(__('No %1$s found.', 'dip'), strtolower($this->singular_name)),
      'not_found_in_trash' => sprintf(__('No %1$s found in Trash.', 'dip'), strtolower($this->singular_name)),
      'parent_item_colon'  => '',
      'menu_name'          => $this->name
    );

    /** apply custom labels */
    $this->labels = array_replace($defaults, (array)$this->labels);
  }

  /**
   * Generate default args and marge with custom args
   * @param array $custom
   * @return void
   */
  public function set_args()
  {
    /** set default args */
    $defaults = array(
      'labels'             => $this->labels,
      'public'             => true,
      'publicly_queryable' => true,
      'show_ui'            => true, 
      'show_in_menu'       => true, 
      'query_var'          => true,
      'capability_type'    => 'post',
      'has_archive'        => true, 
      'hierarchical'       => false,
      'menu_position'      => 5,
      'supports'           => array('title','editor','thumbnail','author','comments'),
      'taxonomies'         => array(),
      'rewrite'            => array('slug' => $this->slug)
    );

    /** apply custom args */
    $this->args = array_replace($defaults, (array)$this->args);
  }

  /**
   * Automatic list and register defined meta boxes
   * To save the fields, is required register in the custom_fields attribute
   * @return void
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
        add_meta_box($id, $title, array($this, $method), $this->post_type, 'normal', 'high');
      }
    }

    /** add nonce after registered meta box */
    global $nonce;
    $nonce = $this->nonce_field = wp_nonce_field($this->slug, 'nonce_'.$this->post_type, true, false);

    /** hook function to print nonce field */
    add_action('edit_form_advanced', create_function('$post', 'global $nonce; echo $nonce;'));
  }

  /**
   * Save registered custom fields
   * @param int $post_id
   * @return void
   */
  public function save_meta_data($post_id)
  {
    if($_REQUEST != 'POST' || !$this->_validate($post_id, $_POST['nonce_'.$this->post_type]))
      return;

    foreach($this->custom_fields as $field)
      update_post_meta($post_id, $field, $_POST[$field], get_post_meta($post_id, $field, true));
    
    /** BUDDYPRESS ONLY: register activity */
    $this->_buddypress_register_activity( $post_id );
  }

  /**
   * Validate post request and if user can edit the page
   * @param int $post_id
   * @param string $nonce
   * @return bool
   */
  public function _validate($post_id, $nonce)
  {
    /** don't validate on wp autosave */
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return false;

    /** Check permissions */
    if ('page' == $_POST['post_type'])
      if(!current_user_can('edit_page', $post_id)) return false;
    else
      if(!current_user_can('edit_post', $post_id)) return false;

    /** Check nonce field */
    if(!wp_verify_nonce($nonce, $this->slug)) return false;

    return true;
  }

  /**
   * Method hooked to replace the title placeholder in new Post-Type screen
   * @return string
   */
  public function _filter_title_placeholder()
  {
    return $this->args['title_placeholder'];
  }

  /**
   * Method used to remove slug meta box and print css rules to hidden it
   * @return void
   */
  public function _remove_slug_meta_box()
  {
    global $post, $pagenow;

    if(is_admin() && get_post_type() == $this->post_type && ($pagenow=='post-new.php' OR $pagenow=='post.php'))
    {
      remove_meta_box('slugdiv', $this->post_type, 'advanced');

      /** print script and style to hide the meta box */
      echo "<style>#edit-slug-box{display:none;}</style>";
      echo "<script type='text/javascript'>
              jQuery(document).ready(function($) {
                jQuery('#edit-slug-box').remove();
              });
            </script>";
    }
  }

  /**
   * Method to register buddypress activity
   * @param int $post_id
   * @return void
   */
  public function _buddypress_register_activity($post_id)
  {
    if(!class_exists('BP_Core_User') || !$this->bp_register)
      return false;

    global $bp, $wpdb;

    $is_revision = wp_is_post_revision($post_id);
    $post_id = $is_revision ? $is_revision : $post_id;
    $post = get_post($post_id);

    $post_id = (int)$post_id;
    $user_id = (int)$post->post_author;

    if('publish' == $post->post_status && '' == $post->post_password)
    {
      /** record this in activity streams */
      $post_permalink = get_permalink($post_id);

      $activity_action = sprintf(__('%s published a new %s: %s'),
                                 bp_core_get_userlink($user_id), $this->singular_name, "<a href=\"{$post_permalink}\">{$post->post_title}</a>");

      $activity_content = $post->post_content;

      bp_blogs_record_activity( array(
        'user_id'           => $user_id,
        'action'            => apply_filters('bp_blogs_activity_new_post_action', $activity_action, $post, $post_permalink),
        'content'           => apply_filters('bp_blogs_activity_new_post_content', $activity_content, $post, $post_permalink),
        'primary_link'      => apply_filters('bp_blogs_activity_new_post_primary_link', $post_permalink, $post_id),
        'type'              => 'new_'.$this->post_type,
        'item_id'           => $blog_id,
        'secondary_item_id' => $post_id,
        'recorded_time'     => $post->post_date_gmt
      ));
    }
  }
}