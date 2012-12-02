<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
if(isset($_POST['id'])){
	$userId = $_POST['id'];
}else{
	$userId = $_SESSION['_id'];
}
$alertId = $_POST['alertId'];

// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	if($groupcode == "" || $userId == null || $alertId == "") {
			$message =  "emptyFields";
	} else {
		$obj = array('active' => 0);
		$arg = array('col' => "$groupcode", 'type' => 'alert', 'id' => $alertId, 'obj' => $obj);
		$results = $db->upsert($arg);
		if($results != null){
			$data = $results;
			$message = "success";
		}
		else{
			$message = "noRecords";	
		}
	}				
}else{
	//return auth failure
	$message = "authFailure";	
}

if($format == 'dt'){
	$data = dtFormat($data);
}
else{

}
echo json_encode(array('message' => $message, 'data'=>$data));
?>