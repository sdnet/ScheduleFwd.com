<?php
include('cws.php');
header('Content-type: application/json');
$groupcode = $_POST['grpcode'];
$sessionId = $_POST['sessionId'];
$dayOfWeekStart = $_POST['dayOfWeekStart'];
$timezone = $_POST['timezone'];
$emailAutoSend = $_POST['emailAutoSend'];
$timeoffReminder3 = $_POST['timeoffReminder3'];
$timeoffReminder2 = $_POST['timeoffReminder2'];
$timeoffReminder = $_POST['timeoffReminder'];
$emailOptIn = $_POST['emailOptIn'];
$autoPublish = $_POST['autoPublish'];
$timeoffDeadline = $_POST['timeoffDeadline'];
$sortBy = $_POST['sortBy'];
$scheduleGenerate = $_POST['scheduleGenerate'];
$circadian = $_POST['circadian'];
$overrideCircadian = $_POST['overrideCircadian'];
$minHoursBetweenShifts = $_POST['minHoursBetweenShifts'];
$maxConsecDay = $_POST['maxConsecDay'];
$maxConsecNight = $_POST['maxConsecNight'];
$maxNightsPerMonth = $_POST['maxNightsPerMonth'];
$maxConsecWorkingDays = $_POST['maxConsecWorkingDays'];
$attendingsLowerLevel = $_POST['attendingsLowerLevel'];
$weekendShifts = $_POST['weekendShifts'];
$tradeApproval = $_POST['tradeApproval'];
$displayTimes = $_POST['displayTimes'];
$shiftsOrHours = $_POST['shiftsOrHours'];

// check session
if(VerifySession($sessionId,$groupcode,$_SESSION['_id'],'Admin') == true){
	if(($groupcode == "")) {
		$message =  "emptyFields";
	} else {
		//check to see if user exists
		$arg = array('col' => "$groupcode", 'type' => 'config', 'key' => array('_id' => 1), 'limit' => 1);
		$results = $db->find($arg);
		if($results != null){
			$timeoffSet = getEvent($groupcode,'timeoff');
			$scheduleSet = getEvent($groupcode,'generate');
			
			
			$timeoffVal = sprintf("%02s", $timeoffDeadline);
			$schedVal = sprintf("%02s", $scheduleGenerate);
			$db->upsert(array('id' => $db->_id($timeoffSet['_id']), 'col' => $groupcode, 'type' => "event", 'obj' => array('day' => $schedVal)));
			$db->upsert(array('id' => $db->_id($scheduleSet['_id']), 'col' => $groupcode, 'type' => "event", 'obj' => array('day' => $timeoffVal)));
			
			$obj = array('dayOfWeekStart' => $dayOfWeekStart, 'timezone' => $timezone, 'emailAutoSend' => $emailAutoSend, 'timeoffReminder3' => $timeoffReminder3, 'timeoffReminder2' => $timeoffReminder2, 'timeoffReminder' => $timeoffReminder, 'emailOptIn' => $emailOptIn, 'autoPublish' => $autoPublish, 'timeoffDeadline' => $timeoffDeadline, 'sortBy' => $sortBy, 'scheduleGenerate' => $scheduleGenerate, 'circadian' => $circadian, 'overrideCircadian' => $overrideCircadian, 'minHoursBetweenShifts' => $minHoursBetweenShifts, 'maxConsecDay' => $maxConsecDay, 'maxConsecNight' => $maxConsecNight, 'maxNightsPerMonth' => $maxNightsPerMonth, 'maxConsecWorkingDays' => $maxConsecWorkingDays, 'attendingsLowerLevel' => $attendingsLowerLevel, 'weekendShifts' => $weekendShifts, 'tradeApproval' => $tradeApproval, 'displayTimes' => $displayTimes, 'shiftsOrHours' => $shiftsOrHours, 'last_updated' => new MongoDate());
			$id = $db->upsert(array('id' => $db->_id($results[0]['_id']), 'col' => $groupcode, 'type' => "config", 'obj' => $obj ));
			$data = $id;
			$message = "success";
			
			
		}else{
			$message =  "Failure";	
		}
	}

}else{
	//return auth failure
	$message = "authFailure";	
}

echo json_encode(array('message' => $message, 'data'=>$data));

?>