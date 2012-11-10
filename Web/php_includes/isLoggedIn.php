<?php session_start(); ob_start();

if(!isset($_SESSION['authed']))
{
	setcookie(session_name(), '', time()-42000, '/');
	session_destroy();
	
	$url = $_SERVER['REQUEST_URI'];
	$url = parse_url($url);
	
	header('Location: /index.php?nologin=1&referral=' . $url['path']);
	exit;
}
?>