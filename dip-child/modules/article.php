<?php
/**
 * TODO http://shibashake.com/wordpress-theme/add-custom-post-type-columns
 * TODO http://wp.tutsplus.com/tutorials/plugins/a-guide-to-wordpress-custom-post-types-taxonomies-admin-columns-filters-and-archives/
 */
new DP_PostType_Article;

class DP_PostType_Article extends DP_PostType
{
  public $post_type     = 'article';

  /** init function is called before constructor */
  public function init()
  {
    $this->name             = __('Articles');
    $this->singular_name    = __('Article');
    $this->icon             = 'tools';

    $this->args['supports'] = array('title', 'author', 'excerpt', 'thumbnail');
    $this->args['title_placeholder'] = __('Aaaaaabrubru');

    /** register custom fields to save meta, only if you use the meta boxes */
    $this->custom_fields    = array('_source_title', '_source_url', '_source_url_2');
    
    /** register taxonomies */
    $a = new DP_Taxonomy((object) array(
      'taxonomy'      => 'taxonomy',
      'name'          => 'Taxonomies',
      'singular_name' => 'Taxonomy',
      'object_type'   => $this->post_type
    ));
  }

  /** setting the meta boxes */
  public function _meta_source_of_doom()
  {
      $form = new DP_Form('_source');

      $title = $form->text('title', array('cols'=>'seven'));
      $url = $form->text('url', array('cols'=>'five'));

      $form->row(array($title, $url));
      $form->render();
  }

  public function _meta_novo_meta_box()
  {
    $form = new DP_Form('_source');

    $title = $form->text('title_2', array('cols'=>'seven'));
    $url = $form->text('url_2', array('cols'=>'five'));

    $form->row(array($title, $url));
    $form->render();
  }
}