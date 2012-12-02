<?php
include('cws.php');
require_once('classes/schedule.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
if(isset($_POST['id'])){
	$userId = $_POST['id'];
}else{
	$userId = $_SESSION['_id'];
}

$isAdmin = false;

// check session
if(VerifySession($sessionId,$groupcode,$userId,'Admin') == true){
	if($groupcode == "" || $userId == null) {
		$message =  "emptyFields";
	} else {
		
		$today = time();
		$year = date('Y',strtotime("+2 months", $today));
		$month =  date('m',strtotime("+2 months", $today));
		$where = array('year' => $year, 'month' => $month);
		$args = array('col'=>$groupcode,'type' => 'timeoff', 'where' => $where);
		$result = $db->find($args);
		
	
		$arg = array('col' => "$groupcode", 'type' => 'shift');
		$results = $db->find($arg);
		if($results != null){
				//make the schedule for this month
				$today = time();
				
				$year = date('Y',strtotime("+2 months", $today));
				$month =  date('m',strtotime("+2 months", $today));
				
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
							$statusArray[] = array('date' => $key, 'id' => $value, 'status' => $time['status']);
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
						$heatcount = 0;
						if(in_array($db->_id($shift['_id']),$timeOffArray)){
							$timeOffValue = 1;	
						}
						$currId = $db->_id($shift['_id']);
					foreach($result as $timeoff){
						foreach($timeoff['time_off'] as $key=>$value){
							if($key == $tempDate && $value == $currId) 
							{
								$heatcount++;			
							}
						}
					}
					$color = "#FFFFFF"; 
					if($heatcount > 20){
						$color = "#003366; color: #FFF"; 
						}
					if($heatcount <= 20 && $heatcount > 15){
						$color = "#004D99; color: #FFF"; 
						} 
					if($heatcount <= 15 && $heatcount > 10){
						$color = "#0073E6; color: #FFF"; 
						}
					if($heatcount <= 10 && $heatcount > 5){
						$color = "#1A8CFF; color: #FFF"; 
						}
					if($heatcount <= 5 && $heatcount >= 1){ 
						$color = "#4DA6FF; color: #FFF"; 
						}
					if($heatcount == 0 || $heatcount == null){ 
						$color = "#FFFFFF"; 
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
								'color' => $color,
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