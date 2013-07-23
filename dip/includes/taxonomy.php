<?php
/**
 * Class to help create new Taxonomies
 * Used by Modules or inside new Post-Types
 *
 * @package Dip Framework
 * @subpackage Module Helpers
 * @since 1.0.0
 */

class DP_Taxonomy
{
  public $taxonomy;

  public $name;
  public $singular_name;
  public $slug;

  /**
   * object_type
   * @var string|array
   */
  public $object_type;

  public $labels = array();
  public $args   = array();

  /**
   * Populate object and call WordPress hook to register the Taxonomy
   * @param object $prop attributes like DP_Taxonomy Class
   * @return DP_Taxonomy self-object
   */
  public function  __construct(stdClass $prop)
  {
    /** fill required attributes */
    $this->taxonomy       = $prop->taxonomy;
    $this->name           = $prop->name;
    $this->singular_name  = $prop->singular_name;

    /** set the slug */
    $this->slug           = !empty($prop->slug) ? $prop->slug : strtolower($this->name);

    /** link the Taxonomy to Post-Types */
    $this->object_type    = !empty($prop->object_type) ? $prop->object_type : '';

    /** populate config arrays, if labels and args isn't defined the method will ignore it */
    $this->set_labels($prop->labels);
    $this->set_args($prop->args);

    /** call WordPress hooks to register the new Taxonomy */
    add_action('init', array($this, 'register_taxonomy'));
  }

  /**
   * Method called by init hook to register the new Taxonomy
   * @return void
   */
  public function register_taxonomy()
  {
    register_taxonomy($this->taxonomy, $this->object_type, $this->args);
  }

  /**
   * Generate default labels based in object attributes and marge with custom labels
   * @param array $custom
   * @return void
   */
  public function set_labels($custom)
  {
    /** generate default labels */
    $defaults = array(
      'name'            => $this->name,
      'singular_name'   => $this->singular_name,
      'search_items'    => sprintf(__('Search %1$s', 'dip'), strtolower($this->name)),
      'all_items'       => sprintf(__('Todos os %1$s', 'dip'), $this->name),
      'parent_item'     => sprintf(__('Parent %1$s', 'dip'), $this->singular_name),
      'edit_item'       => sprintf(__('Edit %1$s', 'dip'), $this->singular_name),
      'update_item'     => sprintf(__('Update %1$s', 'dip'), $this->singular_name),
      'add_new_item'    => sprintf(__('Add New %1$s', 'dip'), $this->singular_name),
      'new_item_name'   => sprintf(__('New %1$s Name', 'dip'), $this->singular_name),
      'menu_name'       => $this->name,
    );

    /** apply custom labels */
    $this->labels = array_replace($defaults, (array)$custom);
  }

  /**
   * Generate default args and marge with custom args
   * @param array $custom
   * @return void
   */
  public function set_args($custom)
  {
    /** set default args */
    $defaults = array(
      'hierarchical'  => true,
      'labels'        => $this->labels,
      'show_ui'       => true,
      'query_var'     => true,
      'rewrite'       => array('slug' => $this->slug),
    );

    /** apply custom args */
    $this->args = array_replace($defaults, (array)$custom);
  }
}