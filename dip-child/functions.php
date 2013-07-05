<?php
global $loader;

$loader['config'] = array(
  'foundation'   => '4',
  'menus'        => array('menu' => 'Main menu', 'top' => 'Top menu')
);

$loader['modules'] = array(
  'article'    => true,
  'single'     => true,
  'foundation' => true,
  'gmaps'      => true,
);