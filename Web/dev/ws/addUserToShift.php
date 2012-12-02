<?php

include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$shiftId = $_POST['shiftId'];
$scheduleId = $_POST['scheduleId'];
$groupcode = $_POST['grpcode'];
if (isset($_POST['username'])) {
    $username = $_POST['username'];
}
if (isset($_POST['orgId'])) {
    $orgId = $_POST['orgId'];
}
// check session
if (VerifySession($sessionId, $groupcode, $_SESSION['_id'], 'Admin') == true) {
    if (($shiftId == "" || $groupcode == "" || $scheduleId == "" || $username == "")) {
        $message = "emptyFields";
    } else {
        //check to see if shift exists
//		$schedule = getScheduleById($groupcode,$scheduleId);
//		if($schedule != null){	
//			foreach($schedule['schedule'] as $pKey=>$shift){
//				if($shift['id'] == $shiftId)
//				{
//					$userCount = count($shift['users']);
//					$maxcount = $shift['number'];
//					if($userCount <= $maxcount){
//						if(isset($username) && $username != "" && $username != null){
//						$userId = getUserId($username,$groupcode);
//						$userArray =  getUserById($groupcode,$userId);
//						$userobj = array('user_name' => $userArray['user_name'],'first_name' => $userArray['first_name'], 'last_name' => $userArray['last_name'], 'id' => $db->_id($userArray['_id']));
//						}	
//						if(isset($orgId) && $orgId != "" && $orgId != null){
//							$userArray =  getExtUserById($groupcode,$orgId);
//							$userobj = array('user_name' => $userArray['org_name'],'first_name' => null,'last_name' => $userArray['org_name'], 'id' => $orgId);	
//						}	
//						$schedule['schedule'][(int)$pKey]['users'][] = $userobj;
//						$obj = $schedule;
//						
//						$data = $db->upsert(array('id' => $scheduleId, 'col' => $groupcode, 'type' => 'schedule', 'obj' => $obj ));
//						$message = "success";	
//						break;
//					}else{
//						
//						if(isset($username) && $username != "" && $username != null){
//						$userId = getUserId($username,$groupcode);
//							$userArray =  getUserById($groupcode,$userId);
//							$userobj = array('user_name' => $userArray['user_name'],'first_name' => $userArray['first_name'], 'last_name' => $userArray['last_name'], 'id' => $db->_id($userArray['_id']));
//						}	
//						if(isset($orgId) && $orgId != "" && $orgId != null){
//							$userArray =  getExtUserById($groupcode,$orgId);	
//							$userobj = array('user_name' => $userArray['org_name'],'first_name' => null,'last_name' => $userArray['org_name'], 'id' => $orgId);	
//						}		
//						$schedule['schedule'][(int)$pKey]['users'][] = $userobj;
//						$obj = $schedule;
//						$data = $db->upsert(array('id' => $scheduleId, 'col' => $groupcode, 'type' => 'schedule', 'obj' => $obj ));
//						$message = "warning";
//						$data = 'There are more providers than the shift is set to have.';
//						break;
//					}
//				}
//			}

        $shift = getShiftFromSchedule($groupcode, $shiftId, $scheduleId);
        if ($shift != null) {
            $userCount = count($shift['users']);
            $maxcount = $shift['number'];
            if (isset($username) && $username != "" && $username != null) {
                $userId = getUserId($username, $groupcode);
                $userArray = getUserById($groupcode, $userId);
                $userobj = array('user_name' => $userArray['user_name'], 'first_name' => $userArray['first_name'], 'last_name' => $userArray['last_name'], 'id' => $db->_id($userArray['_id']));
            }
            if (isset($orgId) && $orgId != "" && $orgId != null) {
                $userArray = getExtUserById($groupcode, $orgId);
                $userobj = array('user_name' => $userArray['org_name'], 'first_name' => null, 'last_name' => $userArray['org_name'], 'id' => $orgId);
            }
    
            //$obj = $schedule;
            $shift['users'][] = $userobj;
            $args = array('id' => $db->_id($shift['_id']), 'col' => $groupcode, 'type' => 'tempShift', 'obj' => array('users' => $shift['users']));
            $data = $db->upsert($args);
         //   if ($userCount <= $maxcount) {
                $message = "success";
           // } else {
            //    $message = "warning";
          //  }
        } else {
            $message = "noRecord";
        }
    }
} else {
//return auth failure
    $message = "authFailure";
}

echo json_encode(array('message' => $message, 'data' => $data));
?>