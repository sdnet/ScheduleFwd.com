<?php

include('cws.php');
require('reports/reportFunctions.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$type = $_POST['type'];

if(isset($_POST['id'])){
	$uId = $_POST['id'];	
}else{
	$uId = $_SESSION['_id']; 	
}

$year = $_POST['year'];
$month = $_POST['month'];
// check session
if(VerifySession($sessionId,$groupcode,$uId,'Admin') == true){
include('reports/' . $type . '.php');	

if(isset($pie)){
	$chartType = 'pie';
	$chart = $pie;
}
if(isset($bar)){
	$chartType = 'bar';
	$chart = $bar;
}	
}else{
//return auth failure
$message = "authFailure";		
}
				
echo json_encode(array('message' => $message, 'data'=> array('keys' => $titleKeys, 'dataKeys' => $dataKeys, 'data' => $data, '' . $chartType . '' => $chart)));
?>