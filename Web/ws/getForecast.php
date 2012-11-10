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
$number = $_POST['number'];

// check session
if(VerifySession($sessionId,$groupcode,$userId,'Admin') == true){
	if($groupcode == "" || $userId == null) {
		$message =  "emptyFields";
	} else {
		
		$days = date('Y-m-d');
		
		$assembled = array();
		for($m = 1;$m <= $number;$m++){
			$tempday = strtotime ( '+1 day' , strtotime ( $days) );
			$days = date('Y-m-d', $tempday);
			//$days = sprintf("%02s", $days);
			$assembled[$days] =  array();	
		}
		$results = getForecastSchedule($groupcode,2,'next');
		if($results != null){
			$i = 0;
			foreach($results as $result){
				foreach($result['schedule'] as $sched){
						if(strtotime($sched['start']) > strtotime('now') && strtotime($sched['start']) < strtotime('+' . $number+1 . ' days')){
							$time = "99999999999999999999";
							$time = substr($sched['start'],0,10);
							if(array_key_exists($time,$assembled)){
								$users = null;
								foreach($sched['users'] as $user){
									$users[] = $user['first_name'] . " " . $user['last_name'];	
								}
								$temparray = null;
								$temparray = array('time' => $sched['start'], 'name' => $sched['shiftName'], 'users' => implode(',',$users));
								$assembled[$time][] = $temparray;
						}
					}
				}
			}
			$new = array();
			foreach($assembled as $key=>$value){
				$format = 'Y-m-d';
				$date = DateTime::createFromFormat($format, $key);
				$tempdate = $date->format('F jS');
				$new[$tempdate] = $value;
			}
			$data = $new;
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