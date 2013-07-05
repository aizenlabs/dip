<?php
if(!is_admin()) return;
new DP_Panel_Single;

class DP_Panel_Single extends DP_Panel
{
  public function init()
  {
    $this->name = __('Single page');
    $this->namespace = 'single';
    $this->parent = false;
    $this->position = 21;
    
    $this->icon = 'pencil';
    //$this->tabs = array('single' => 'Single', 'blababsl' => 'OKOSKOKS', 'dfijf'=> 'AAAA');
  }
    
    public function save_settings()
    {
        $this->settings = get_option( $this->namespace );
        update_option( $this->namespace, $this->settings );
    }
    
    public function enqueue_scripts()
    {
        wp_enqueue_style('thickbox');
        wp_enqueue_script('media-upload');
        
        wp_enqueue_script( 'dip', get_bloginfo('template_directory') . '/assets/js/admin.js', array( 'wp-color-picker' ), false, true );
    }
}