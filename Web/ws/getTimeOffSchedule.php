<?php
include('cws.php');
require_once('classes/schedule.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$group = $_POST['group'];
if(isset($_POST['id'])){
	$userId = $_POST['id'];
}else{
	$userId = $_SESSION['_id'];
}
if(isset($_POST['month']))
{ 
	$tmonth = $_POST['month'];
	$tyear = $_POST['year'];
}

$isAdmin = false;

// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	if($groupcode == "" || $userId == null) {
			$message =  "emptyFields";
	} else {
		$timeOff = getTimeOffByUserId($groupcode,$userId,$tmonth,$tyear);
		if(getRoleById($groupcode,$userId) == 'Admin'){
			$isAdmin = true;
		}
		
			$arg = array('col' => "$groupcode", 'type' => 'user', 'id' => $userId);
			$result = $db->find($arg);
			$userGroup = $result[0]['group'];
			if(isset($group)) {
				$userGroup = $group;
			}
			$where = array('groups' => array('$in' => array($userGroup)));
			$arg = array('col' => "$groupcode", 'type' => 'shift', 'where' => $where);

	
		$results = $db->find($arg);
		if($results != null){
			//make the schedule for this month
			$today = time();
			if($tmonth != null){
				$month = $tmonth;
				$year = $tyear;
			}else{
				
				$year = date('Y',strtotime("+2 months", $today));
				$month =  date('n',strtotime("+2 months", $today));
			}
			
			$days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
			$dates;
			for($i = 1;$i <= $days;$i++)
			{
				$day = sprintf("%02s", $i);
				$dates[] = $day;
			}
			$scheduleDays;
			$i = 0;
			foreach($dates as $date)
			{
				$tempDate = $year . "-" . $month ."-" . $date;
				$formDate = "" . $month . "/" . $date . "/". $year . " 01:01:01";	
				$weekday = date('l', strtotime($formDate));
				$timeOffArray = array();
				foreach($timeOff as $time){
					foreach($time['time_off'] as $key=>$value){
						$statusArray[] = array('date' => $key, 'id' => $value, 'status' => $time['status'], 'priority' => $time['priority']);
					}
					foreach($time['time_off'] as $key=>$value){
						if($key == $tempDate){
							$timeOffArray[] = $value;
						}	
					}
			}
				foreach($results as $shift){
					$timeOffValue = 0;
					$status = 0;
					$priority = 0;
					
					if(in_array($db->_id($shift['_id']),$timeOffArray)){
						$timeOffValue = 1;	
					}
					$currId = $db->_id($shift['_id']);
					
					foreach($statusArray as $arr){
						if($arr['id'] == $currId && $arr['date'] == trim($tempDate)){
							if($arr['status'] == 'Approved'){
								$status = 1;
							}elseif($arr['status'] == 'Disapproved'){
								$status = -1;
							}else{
								$status = 0;	
							}
							$priority = $arr['priority'];
						}
					}
					
					if(in_array($weekday, $shift['days'])){
						$i++;
						$start = stringReplace(2,":",$shift['start']);
						$end = stringReplace(2,":",$shift['end']);
						$scheduleDays[] = array('id' => $i,
							'title' => "",
							'start' => "$start:00 - $end:00",
							'date' => "$tempDate ",
							'allDay' => false,
							'groups' => $shift['groups'],
							'shiftName' => "" . $shift['name'] . "",
							'users' => "",
							'priority' => $priority,
							'status' => $status,
							'timeoff'=> $timeOffValue,
							'day' => "$weekday",
							'shiftId' => $db->_id($shift['_id'])
							);
					}
				}
			}
			
			
			$schedule = $scheduleDays;
			
			$data = $schedule;
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