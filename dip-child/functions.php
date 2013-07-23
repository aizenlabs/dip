<?php
global $loader;

$loader['config'] = array(
  'foundation'   => '4',
  'adminbar'     => false,
  'avatars'      => array('size' => 42),
  'menus'        => array('menu' => 'Main menu', 'top' => 'Top menu'),
  'sidebars'     => array('home', 'sidebar', 'footer')
);

$loader['modules'] = array(
  'article'    => true,
  'single'     => true,
  'foundation' => true,
  'gmaps'      => true,
);