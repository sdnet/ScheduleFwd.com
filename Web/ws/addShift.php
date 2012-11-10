<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$name = $_POST['name'];
$groups = $_POST['groups'];
$start = $_POST['start'];
$end = $_POST['end'];
$days = $_POST['days'];
$color = $_POST['color'];
$number = $_POST['number'];
// check session
if(VerifySession($sessionId,$groupcode,$_SESSION['_id'],'Admin') == true){
	if(($groupcode == "" || $groups == "" || $start == "" || $end == "" || $days == "")) {
			$message =  "emptyFields";
		} else {
			//convert comma separated into arrays
		if(!is_array($days)){
			$dayArray = explode(",", $days);
		}
		if(!is_array($groups)){
			$groupArray = explode(",",$groups);	
		}
		
		if($number == ""){
			$number = 1;	
		}
			//check to see if shift name exists
			$where = array('name' => "$name");
			$arg = array('col' => "$groupcode", 'type' => 'shift', 'where' => $where, 'limit' => 1);
			$results = $db->find($arg);
			if($results == null){		
			$obj = array('name' => "$name", 'active' => 1, 'duration' => $duration, 'start' => $start, 'end' => $end, 'color' => $color, 'days' => $dayArray, 'groups' => $groupArray, 'number' => $number, 'date_created' => new MongoDate());
			$data = $db->upsert(array('col' => $groupcode, 'type' => "shift", 'obj' => $obj ));
					$message = "success";	
			}else{
				$message =  "nameExists";	
			}
		}
}else{
//return auth failure
$message = "authFailure";	
}

echo json_encode(array('message' => $message, 'data'=>$data));

?>