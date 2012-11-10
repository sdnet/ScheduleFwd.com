<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
if(!isset($_SESSION['_id']))
{
	setcookie(session_name(), '', time()-42000, '/');
	session_destroy();
	header('Location: index.php');
	exit;
}elseif(isset($_SESSION['_id'])){

	$url = "http://www.schedulefwd.com/ws/logOut.php";
$ch = curl_init();
//curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_POST, 1); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POSTFIELDS, array(
		'sessionId' => "" . session_id() . "",
		'grpcode' => "" . $_SESSION['grpcode'] . ""
  )); 
	
	$result = curl_exec($ch); 
curl_close($ch); 
	$content = json_decode($result,true);
	if($content['message'] == 'success'){
		setcookie(session_name(), '', time()-42000, '/');
		session_destroy();
		header('Location: index.php?logout=1');
		exit;
	}else{
		setcookie(session_name(), '', time()-42000, '/');
		session_destroy();
		header('Location: index.php?logout=1');
	}
}

?>