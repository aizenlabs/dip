<?php
/**
 * The helper for displaying Google Maps
 *
 * @package Dip Framework
 * @subpackage Gmaps Module
 * @version 1.0.0
 * @since Dip Framework 1.0
*/

if(is_admin() || $_SERVER['REQUEST_METHOD'] != "POST" || !wp_verify_nonce($_POST['_wpnonce'], 'sendmail')) return;

global $dip;
$config = $dip->modules['sendmail'];

$headers[] = 'From: Me Myself <me@example.net>';

wp_mail($config['to'], '$subject', $_POST['name'], $headers);

wp_redirect($_SERVER["HTTP_REFERER"].'?messagesend');
exit;