<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and <header>
 *
 * @package dip
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <!-- Set the viewport width to device width for mobile -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?php wp_title('|', true, 'right'); ?></title>
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

  <!-- IE Fix for HTML5 Tags -->
  <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  
  <header id="header">
    <hgroup class="row">
      <h1 class="large-5 small-12 columns"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
      <h2 class="subheader large-7 columns hide-for-small"><?php bloginfo( 'description' ); ?></h2>
    </hgroup>
    
    <?php dp_topbar('menu', array('dropdown' => true, 'attr' => array('class' => 'row')) ); ?>
  </header>