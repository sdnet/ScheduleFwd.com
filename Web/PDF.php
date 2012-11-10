<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include('ws/cws.php');
require_once("ws/classes/dompdf/dompdf_config.inc.php");
$sessionId = $_SESSION['sessionId'];
$groupcode = $_GET['group'];
if(isset($_GET['id'])){
	$userId = $_GET['id'];
}else{
	$userId = $_SESSION['_id'];
}
$type = $_GET['type'];

