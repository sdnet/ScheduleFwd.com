<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
if(isset($_POST['userId'])){
	$userId = $_POST['userId'];	
}else{
	$userId = $_SESSION['_id']; 	
}
// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	
	$scheduleId = getCurrentSchedule($groupcode);
	$totalhours = getTotalHoursByUserId($groupcode,$userId);
	$hours = getHoursByUserId($groupcode,$userId,$db->_id($scheduleId[0]['_id']));
	$maxhours = getUserMaxHours($groupcode,$userId);
	$totalshifts = getTotalShiftsByUserId($groupcode,$userId);
	$shifts = getMonthShiftsByUserId($groupcode,$userId);
	$remain = $maxhours - $hours;
	if($hours != null){
		$data = array('hours' => $hours, 'remaining' => $remain, 'total' => $totalhours, 'totalshifts' => $totalshifts, 'shifts' => $shifts, 'traded' => 0);
		$message = "success";
	}
	else{
		$message = "noRecords";	
	}
	
}else{
	//return auth failure
	$message = "authFailure";	
}


echo json_encode(array('message' => $message, 'data'=>$data));
?>