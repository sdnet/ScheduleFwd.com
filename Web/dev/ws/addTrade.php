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
$target = $_POST['target'];
$original = $_POST['original'];
$targetUser = $_POST['targetUser'];
$scheduleId = $_POST['scheduleId'];
$comments = $_POST['comments'];


// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	if($groupcode == "" || $userId == null || $scheduleId == "") {
			$message =  "emptyFields";
	} else {
		
		$error = "";
		//Check for swapping original shift	
		$shift = getShiftFromSchedule($groupcode,$original,$scheduleId);	
		$t1 = new DateTime($shift['start']);
		$t2 = new DateTime($shift['endreal']);
		$t3 = date_diff($t1,$t2);
		$original_duration = $hours + $t3->h;
		//check for people that have too many hours or too few hours
		$omaxhours = getUserMaxHours($groupcode,$userId);
		$ominhours = getUserMinHours($groupcode,$userId);
		$hours = 0;
		$hours = getHoursByUserId($groupcode,$userId,$scheduleId);
		$difference = ($omaxhours - $hours);
		$difference2 = ($difference - $original_duration);	
		$original_mindiff = ($hours - $original_duration);
		//check if target as well
		if($target != null && $target != ""){
			$shift = getShiftFromSchedule($groupcode,$target,$scheduleId);	
			$t1 = new DateTime($shift['start']);
			$t2 = new DateTime($shift['endreal']);
			$t3 = date_diff($t1,$t2);
			$duration = $hours + $t3->h;
			//check for people that have too many hours or too few hours
			$maxhours = getUserMaxHours($groupcode,$targetUser);
			$minhours = getUserMinHours($groupcode,$targetUser);
			$hours = 0;
			$hours = getHoursByUserId($groupcode,$targetUser,$scheduleId);
			$difference = ($maxhours - $hours);
			$difference2 = ($difference - $duration);	
			$original_mindiff = $original_mindiff + $duration;
			$mindiff = ($hours - $duration);
			$mindiff = $mindiff + $original_duration;
			if($mindiff < $minhours){
				$userdiff = getUserById($groupcode,$targetUser);
				$error[] = $userdiff['first_name'] . " " . $userdiff['last_name'] ." will end up with too few hours.";
			}	
		}
		if($original_mindiff < $ominhours){
			$userdiff = getUserById($groupcode,$userId);
			$error[] = $userdiff['first_name'] . " " . $userdiff['last_name'] ." will end up with too few hours.";
		}
		if($error == null || $_POST['override'] == 1){
			$obj = array('target_user' => $targetUser, 'target_shift' => $target, 'original_shift' => $original, 'original_user' => $userId, 'status' => 'Pending', 'active' => 1, 'scheduleId' => $scheduleId, 'comments' => $comments, 'date_created' => new MongoDate());
			$arg = array('col' => "$groupcode", 'type' => 'trade', 'obj' => $obj);
			$results = $db->upsert($arg);
			if($results != null){
				$shift = getShiftFromSchedule($groupcode,$original,$scheduleId);
				$severity = 'Alert';
				createAlert($userId,'User','Your shift trade request has been sent for ' . $shift['start'] . ' ' . $shift['shiftName'] . '','Notification',$groupcode);
				if(isset($targetUser)){
					createAlert($targetUser,'User','A trade request has been issued for ' . $shift['start'] . ' ' . $shift['shiftName'] . '!',$severity,$groupcode);
				}
				$message = "success";	
				}else{
					$message = "noRecords";	
				}
			}else{
				$data = $error;
				$message = "error";
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