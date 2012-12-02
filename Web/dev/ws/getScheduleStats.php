<?php
include('cws.php');
header('Content-type: application/json');
require_once('' . $_SERVER['DOCUMENT_ROOT'] . '/ws/functions.php');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
if(isset($_POST['id'])){
	$userId = $_POST['id'];
}else{
	$userId = $_SESSION['_id'];
}
$month = sprintf("%02s", $_POST['month']);
$year = $_POST['year'];
$isAdmin = false;

// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true) {
	if($groupcode == "" || $userId == null) {
			$message =  "emptyFields";
	} else {
		if(getRoleById($groupcode,$userId) == 'Admin') {
			$isAdmin = true;
		}
		
		$where = array('month'=> $month, 'year' => $year);
		$arg = array('col' => "$groupcode", 'type' => 'schedule', 'where' => $where);
		$results = $db->find($arg);
		$userHours = array();
                if($results){
                 $results = $results[0]['schedule'];
                }else{
                $where = array('scheduleId' => $db->_id($results[0]['_id']));
		$arg = array('col' => "$groupcode", 'type' => 'tempShift', 'where' => $where);
		$results = $db->find($arg);
                $dateswap = 1;
                }

		foreach ($results as $shift) {
			$date = $shift['start'];
			$dateEnd = $shift['endreal'];
                        if($dateswap == 1){
                            $date = date('m/d/Y H:i:s', $shift['start']->sec);
                           $dateEnd = date('m/d/Y H:i:s', $shift['endreal']->sec);
                        }
			$users = $shift['users'];
			$users = $users;
		
			for ($i=0; $i<count($users); $i++) {
				if (is_array($users)) {
					$user = $users[$i]['user_name'];
					$fname = $users[$i]['first_name'];
					$lname = $users[$i]['last_name'];
					$userId = $users[$i]['id'];
					$userTimeOffs = getMonthTimeOffByUserId($groupcode, $userId, $month, $year);
					if (($userTimeOffs == "") || ($userTimeOffs == null)) {
						$userTimeOffs = 0;	
					}
					if (array_key_exists($user, $userHours)) {
						$userHours[$user]['userName'] = $lname . ", " . $fname;
						$userHours[$user]['totalhours'] = $userHours[$user]['totalhours'] + floor($shift['duration']/60);
						$userHours[$user]['numshifts'] = $userHours[$user]['numshifts'] + 1;
						$userHours[$user]['averageHoursPerShift'] = round($userHours[$user]['totalhours'] / $userHours[$user]['numshifts'],1);
						$userHours[$user]['totalTimeOff'] =  $userTimeOffs;
						
						if (isWeekend($date) == true) {
							//if ($userHours[$user]['numWeekendShifts']) {
								$userHours[$user]['numWeekendShifts'] = $userHours[$user]['numWeekendShifts'] + 1;
							//} else {
							//	$userHours[$user]['numWeekendShifts'] = 0;
							//}
						}
						
						if (isNight($date, $dateEnd) == true) {
							$userHours[$user]['numNightShifts'] = $userHours[$user]['numNightShifts'] + 1;	
						}
						
					} else {
						$userHours[$user]['userName'] = $lname . ", " . $fname;
						$userHours[$user]['totalhours'] = floor($shift['duration'] / 60);
						$userHours[$user]['numshifts'] = 1;
						$userHours[$user]['averageHoursPerShift'] = floor( $shift['duration'] / 60);
						$userHours[$user]['numWeekendShifts'] = 0;
						$userHours[$user]['numNightShifts'] = 0;
						$userHours[$user]['totalTimeOff'] = $userTimeOffs;
						
						if (isWeekend($date) == true) {
							$userHours[$user]['numWeekendShifts'] = 1;	
						}
						
						if (isNight($date, $dateEnd) == true) {
							$userHours[$user]['numNightShifts'] = 1;	
						}
					}
				}
			}
		}
		
		$message = "success";
		$data = $userHours;
	}
}else{
	//return auth failure
	$message = "authFailure";	
}

echo json_encode(array('message' => $message, 'data'=>$data));
?>