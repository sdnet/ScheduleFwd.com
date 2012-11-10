<?php
include('cws.php');
require('classes/schedule.php');
require('classes/staff.php');
header('Content-type: application/json');
ini_set("max_execution_time", "500");
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
if(isset($_POST['id'])){
	$userId = $_POST['id'];
}else{
	$userId = $_SESSION['_id'];
}
$scheduleId = $_POST['scheduleId'];
$year = (int)$_POST['year'];
$month = (int)$_POST['month'];

// check session
if(VerifySession($sessionId,$groupcode,$userId,'Admin') == true){
	if($groupcode == "" || $userId == null || $scheduleId == null) {
			$message =  "emptyFields";
	} else {
		if($year != null && $month != null){
			$args = array('col' => "$groupcode", 'type' => 'schedule', 'where' => array('month' => ''.$month.'', 'year' => ''.$year.''));
		}else{
			$args = array('col' => "$groupcode", 'id' => $scheduleId, 'type' => 'schedule');
		}
			$result = $db->delete($args);
			// time to invoke the new schedule generation	
			$schedule = new Schedule($year,$month,$groupcode);
			$schedule->getShifts();
			$schedule->generate();
			$schedule->commit();
			$newScheduleId = $schedule->newId;
		$staff = new Staff($groupcode,$newScheduleId);
		$staff->getSchedule();
		$staff->getGroups();
		$staff->getUsers();
		$staff->staffSchedule();
		
			$message = "success";
	}				
}else{
	//return auth failure
	$message = "authFailure";	
}

echo json_encode(array('message' => $message, 'data'=> $newScheduleId));
?>