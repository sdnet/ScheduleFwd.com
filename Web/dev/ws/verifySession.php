<?php 
include('cws.php');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
if (($sessionId == "" || $groupcode )) {
		$message =  "emptyFields";
	} else {
		
		$db = new MONGORILLA_DB;
	$where = array('sessionId' => "$sessionId");
	$arg = array('col' => "$groupcode", 'type' => 'session', 'where' => $where, 'limit' => 1);
		$results = $db->find($arg);
	if($results != null){
		$message = "success";
	}else{
		$message =  "authFailure";	
	}
}

echo json_encode(array('message' => $message, 'data'=>$data));