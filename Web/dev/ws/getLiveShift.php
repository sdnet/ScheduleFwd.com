<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$shiftId = $_POST['shiftId'];
$format = $_POST['format'];
$scheduleId = $_POST['scheduleId'];

if(isset($_POST['userId'])){
	$userId = $_POST['userId'];	
}else{
	$userId = $_SESSION['_id']; 	
}
// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	/*$where = array();
	$arg = array('col' => "$groupcode", 'type' => 'schedule', 'limit' => 1, 'id' => $scheduleId);
	$results = $db->find($arg);*/
	$shift = getShiftFromSchedule($groupcode, $shiftId, $scheduleId);
	if($shift != null){
		
				$data = $shift;
				$message = "success";	
	}
	else{
		$message = "noRecords";	
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