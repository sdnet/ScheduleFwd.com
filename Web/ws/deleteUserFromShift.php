<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$shiftId = $_POST['shiftId'];
$scheduleId = $_POST['scheduleId'];
$groupcode = $_POST['grpcode'];
if(isset($_POST['username'])){
	$username = $_POST['username'];
}
if(isset($_POST['orgId']) && $_POST['orgId'] != "" && $_POST['orgId'] != null){
	$orgId = $_POST['orgId'];
	$userArray =  getExtUserById($groupcode,$orgId);
	$username = $userArray['org_name'];
}
// check session
if(VerifySession($sessionId,$groupcode,$_SESSION['_id'],'Admin') == true){
	if(($shiftId == "" || $groupcode == "" || $scheduleId == "" || $username == "")) {
			$message =  "emptyFields";
		} else {
			//check to see if shift exists
		$schedule = getScheduleById($groupcode,$scheduleId);
		if($schedule != null){	
			foreach($schedule['schedule'] as $pKey=>$shift){
				if($shift['id'] == $shiftId)
				{	
					foreach($shift['users'] as $key=>$user){
						if($user['user_name'] == $username){
							unset($schedule['schedule'][$pKey]['users'][$key]);
							unset($shift['users'][$key]);
							break;
						}
					}
					
					$temp = array();
					$i = 0;
					foreach($shift['users'] as $key=>$user){
							$temp[$i] = $user;
							$i++;
					}
					$schedule['schedule'][$pKey]['users'] = array();
					$schedule['schedule'][$pKey]['users'] = $temp;
				}
			}
			$obj = array_filter($schedule);
			//$obj = $schedule;
			$data = $db->upsert(array('id' => $scheduleId, 'col' => $groupcode, 'type' => 'schedule', 'obj' => $obj ));
				$message = "success";	
			}else{
				$message =  "noRecord";	
			}
		}
}else{
//return auth failure
$message = "authFailure";	
}

echo json_encode(array('message' => $message, 'data'=>$data));

?>