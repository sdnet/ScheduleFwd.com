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
$shiftId = trim($_POST['shiftId']);
$date = trim($_POST['date']);

// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	if($groupcode == "" || $userId == null || $shiftId == "") {
			$message =  "emptyFields";
	} else {
		$where = array('userId' => $userId, 'time_off' => array($date => $shiftId));
		$arg = array('col' => "$groupcode", 'type' => 'timeoff', 'where' => $where);
		$results = $db->delete($arg);
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