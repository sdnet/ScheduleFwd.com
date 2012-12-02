<?php 
	session_start(); 
	$page = "";
	echo $_SESSION['role'];
	if ($_SESSION['role'] == "Admin") {
		header('Location: /home');
	} elseif ($_SESSION['role'] == "Scribe") {
		header('Location: /shome');
	} else {
		header('Location: /uhome');
	}
?>