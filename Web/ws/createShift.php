<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$name = $_POST['name'];
$groups = $_POST['groups'];
$duration = $_POST['duration'];
$start = $_POST['start'];
$end = $_POST['end'];
$days = $_POST['days'];
$color = $_POST['color'];
// check session
if(VerifySession($sessionId,$groupcode,$_SESSION['_id'],true) == true){
		if(($groupcode == "" || $name == "" || $groups == "" || $duration == "" || $start == "" || $end == "" || $days == "" || $color == "")) {
			$message =  "emptyFields";
		} else {
			//convert comma separated into arrays
		if(!isArray($days)){
			$dayArray = explode(",", $days);
		}
		if(!isArray($groups)){
			$groupArray = explode(",",$groups);	
		}
			//check to see if shift name exists
			$where = array('name' => "$name");
			$arg = array('col' => "$groupcode", 'type' => 'shift', 'where' => $where, 'limit' => 1);
			$results = $db->find($arg);
			if($results == null){		
			$obj = array('name' => "$name", 'active' => 1, 'duration' => $duration, 'start' => $start, 'end' => $end, 'color' => $color, 'days' => $dayArray, 'groups' => $groupArray, 'date_created' => new MongoDate());
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