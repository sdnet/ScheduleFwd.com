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
$status = $_POST['status'];
$tradeId = $_POST['tradeId'];

// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	if($groupcode == "" || $userId == null || $tradeId == "" || $status == "") {
			$message =  "emptyFields";
	} else {
		// check to see if its an active or old request
		$arg = array('col' => "$groupcode", 'id' => $tradeId, 'where' => array('active' => 1));
		$traderecord = $db->find($arg);
		if($traderecord != null){
			if(checkAccept($groupcode,$tradeId) == true){
				if(getRoleById($groupcode,$userId) == 'Admin'){
					$obj = array('status' => $status, 'admin_approved' => 1, 'date_completed' => new MongoDate());
				}else{
					if(getConfig($groupcode,'tradeApproval') == 'false'){
						$obj = array('status' => $status, 'date_completed' => new MongoDate());
					}else{
						if($status == 'Accepted'){
							$obj = array('status' => 'Admin Approval', 'date_completed' => new MongoDate());
						}else{
							$obj = array('status' => $status, 'date_completed' => new MongoDate());
						}
					}
				}
			$arg = array('col' => "$groupcode", 'type' => 'trade', 'id' => $tradeId, 'obj' => $obj);
			$results = $db->upsert($arg);
			$arg = array('col' => "$groupcode", 'id' => $tradeId);
			$results = $db->find($arg);
			if($results != null){
					if((($status == 'Accepted') && (getConfig($groupcode,'tradeApproval') == 'false')) || (($status == 'Accepted') && (getConfig($groupcode,'tradeApproval') == 'true') && ($results[0]['admin_approved'] == 1))){
					if($results[0]['admin_approved'] == 1)
						{
						$admintext = " by the admin. Your trade is now in effect.";		
						}
						$targetUser = getUserById($groupcode,$results[0]['target_user']);
						$targetUserArray['first_name'] = $targetUser['first_name'];
						$targetUserArray['last_name'] = $targetUser['last_name'];
						$targetUserArray['user_name'] = $targetUser['user_name'];
						$targetUserArray['id'] = $db->_id($targetUser['_id']);
						$shift = getShiftFromSchedule($groupcode,$results[0]['original_shift'],$results[0]['scheduleId']);
						foreach($shift['users'] as $key=>$user){
							if($user['id'] == $results[0]['original_user']){
								$shift['users'][$key] = $targetUserArray;	
							}	
						}

						$schedule = getScheduleById($groupcode,$results[0]['scheduleId']);
						foreach($schedule['schedule'] as $key=>$sh){
							if($sh['id'] == $shift['id']){
								$schedule['schedule'][$key] = $shift;
							}
						}
						
						if($results[0]['target_shift'] != null){
							$originalUser = getUserById($groupcode,$results[0]['original_user']);
							$originalUserArray['first_name'] = $originalUser['first_name'];
							$originalUserArray['last_name'] = $originalUser['last_name'];
							$originalUserArray['user_name'] = $originalUser['user_name'];
							$originalUserArray['id'] = $db->_id($originalUser['_id']);
							$shift = getShiftFromSchedule($groupcode,$results[0]['target_shift'],$results[0]['scheduleId']);
							foreach($shift['users'] as $key=>$user){
								if($user['id'] == $results[0]['target_user']){
									$shift['users'][$key] = $originalUserArray;	
								}	
							}
							foreach($schedule['schedule'] as $key=>$sh){
								if($sh['id'] == $shift['id']){
									$schedule['schedule'][$key] = $shift;	
								}
							}
						}

						$obj = array('schedule' => $schedule['schedule']);	
						$arg = array('id' => $results[0]['scheduleId'],'col' => "$groupcode", 'type' => 'schedule', 'obj' => $obj);
						$resulted = $db->upsert($arg);
						$severity = 'Notification';
					}else{
						$severity = 'Notification';	
					}
					$shift = getShiftFromSchedule($groupcode,$results[0]['original_shift'],$results[0]['scheduleId']);
					createAlert($results[0]['original_user'],'User','Your shift trade request for: ' . $shift['start'] . ' ' . $shift['shiftName'] . ' has been ' . $status . '' . $admintext . '.',$severity,$groupcode);
					if($results[0]['admin_approved'] == 1){
						createAlert($results[0]['target_user'],'User','The shift trade request for: ' . $shift['start'] . ' ' . $shift['shiftName'] . ' has been ' . $status . '' . $admintext . '',$severity,$groupcode);
					}
					$message = "success";
			}
			
			else{
				$message = "noRecords";	
			}	
			}else{
				$obj = array('status' => 'Cancelled');
				$arg = array('col' => "$groupcode", 'type' => 'trade', 'id' => $tradeId, 'obj' => $obj);
				$results = $db->upsert($arg);
					$message = "cancelled";	
				}	
		}else{
		$message = "notActive";	
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