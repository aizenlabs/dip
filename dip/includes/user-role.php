<?php
/**
 * Abstract Class to Create new User Role
 *
 * @package Dip Framework
 * @subpackage Module Helpers
 * @since 1.1.0
 */

abstract class DP_UserRole
{
  public $role;
  public $name;
  public $capabilities = array();

  /**
   * Call init method and create new Post-Type
   * @return DP_PostType self-object
   */
  public function __construct()
  {
    $this->init();

    /** call WordPress hooks */
    add_action('after_switch_theme', array($this, 'add_user_role'));
    add_action('switch_theme', array($this, 'remove_user_role'));
  }

  /**
   * Abstract method to configure the user role
   * @return void
   */
  abstract public function init();

  /**
   * Hooked method to register the new User Role
   * @return void
   */
  public function add_user_role()
  {
    add_role($this->role, $this->name, $this->capabilities);
  }

  /**
   * Hooked method to remove the user role
   * @return void
   */
  public function remove_user_role()
  {
    remove_role($this->role);
  }
}