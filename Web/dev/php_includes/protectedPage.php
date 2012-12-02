<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$user = $_SESSION['userName'];
$email = $_SESSION['email'];
$role = $_SESSION['role'];
$sessionId = session_id();
?>