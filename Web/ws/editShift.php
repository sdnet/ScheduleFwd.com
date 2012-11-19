<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$shiftId = $_POST['id'];
$groupcode = $_POST['grpcode'];
$name = $_POST['name'];
$groups = $_POST['groups'];
$start = $_POST['start'];
$end = $_POST['end'];
$days = $_POST['days'];
$color = $_POST['color'];
$number = $_POST['number'];
$location = $_POST['location'];
// check session
if(VerifySession($sessionId,$groupcode,$_SESSION['_id'],'Admin') == true){
	if(($shiftId == "" || $groupcode == "" || $groups == "" || $start == "" || $end == "" || $days == "")) {
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
		if($location == null || $location == ""){
		$location = 'All';	
		}
			//check to see if shift name exists
		$where = array('name' => "$name",'_id' => array( '$ne' => new MongoId($shiftId)));
			$arg = array('col' => "$groupcode", 'type' => 'shift', 'where' => $where, 'limit' => 1);
			$results = $db->find($arg);
			if($results == null){		
			$obj = array('name' => "$name", 'active' => 1, 'duration' => $duration, 'start' => $start, 'end' => $end, 'color' => $color, 'days' => $dayArray, 'groups' => $groupArray, 'number' => $number,'location' => $location,);
			$data = $db->upsert(array('id' => $shiftId, 'col' => $groupcode, 'type' => 'shift', 'obj' => $obj ));
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