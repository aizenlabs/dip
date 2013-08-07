<?php
/**
 * Module to help send email forms
 *
 * @package Dip Framework
 * @subpackage Sendmail Module
 * @version 1.0.0
 * @since Dip Framework 1.0
*/

if(is_admin() || $_SERVER['REQUEST_METHOD'] != "POST" || !wp_verify_nonce($_POST['_wpnonce'], 'sendmail')) return;

global $dip;
$config = $dip->modules['sendmail'];

$to = $config['to'];
$headers[] = "From: {$_POST['name']} <{$_POST['email']}>";
$subject = $_POST['subject'];
$message = '';

$fields = $_POST;
unset($fields['_wpnonce']);
unset($fields['_wp_http_referer']);
unset($fields['subject']);

foreach($fields as $field => $value)
{
  $message .= ucfirst($field) . ': ' . $value . "<br />"; 
}

wp_mail($to, $subject, $message, $headers);

wp_redirect($_SERVER["HTTP_REFERER"].'?success');
exit;