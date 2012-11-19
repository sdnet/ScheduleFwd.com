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
$location = $_POST['location'];
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
		if($location == null || $location == ""){
			$location = 'All';	
		}
			//check to see if shift name exists
			$where = array('name' => "$name", 'active' => 1);
			$arg = array('col' => "$groupcode", 'type' => 'shift', 'where' => $where, 'limit' => 1);
			$results = $db->find($arg);
			if($results == null){		
			$obj = array('name' => "$name", 'active' => 1, 'duration' => $duration, 'start' => $start, 'end' => $end, 'color' => $color, 'days' => $dayArray, 'groups' => $groupArray, 'number' => $number, 'location' => $location, 'date_created' => new MongoDate());
			$data = $db->upsert(array('col' => $groupcode, 'type' => "shift", 'obj' => $obj ));
					$message = "success";
				
					// add the shift to user preference documents
					$where = array('group' => $groups, 'active' => 1);
					$arg = array('col' => "$groupcode", 'type' => 'user', 'where' => $where);
					$results = $db->find($arg);
					foreach ($results as $result) {
						$countOfShiftPrefs = count($result['preferences']['shifts']);
						if (($countOfShiftPrefs == "") || ($countOfShiftPrefs == null)) {
							$countOfShiftPrefs = 0;
						}
						$result['preferences']['shifts'][$countOfShiftPrefs] = $db->_id($data);	
						// $result is ready to be upserted
						$data2 = $db->upsert(array('id' => $result['_id'], 'col' => $groupcode, 'type' => "user", 'obj' => $result ));
					}
					
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