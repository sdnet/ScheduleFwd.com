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
$timeOffId = $_POST['timeOffId'];

// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	if($groupcode == "" || $userId == null || $timeOffId == "" || $status == "") {
			$message =  "emptyFields";
	} else {
		// check to see if its an active or old request
		$arg = array('col' => "$groupcode", 'id' => $timeOffId, 'where' => array('active' => 1));
		$timerecord = $db->find($arg);
		if($timerecord != null){
			$obj = array('status' => $status, 'active' => 0);
			$arg = array('col' => "$groupcode", 'type' => 'timeoff', 'id' => $timeOffId, 'obj' => $obj);
			$results = $db->upsert($arg);
			$arg = array('col' => "$groupcode", 'id' => $db->_id($results));
			$results = $db->find($arg);
			if($results != null){
				if($status == 'Approved'){
					$arg = array('id' => $results[0]['userId'], 'col' => "$groupcode", 'type' => 'user');
					$result = $db->find($arg);
					
					$temp = $result[0]['time_off'];
					if($temp == null)
					{
						$temp = array();
					}
					$tempArray = array_merge($results[0]['time_off'],$temp);
				
					$obj = array('time_off' => array_unique($tempArray));	
					$arg = array('id' => $results[0]['userId'],'col' => "$groupcode", 'type' => 'user', 'obj' => $obj);
					$resulted = $db->upsert($arg);
					$severity = 'Notification';
				}else{
				$severity = 'Alert';	
				}
				foreach($results[0]['time_off'] as $key=>$value)
				{
					$daysoff .= $value . ", ";	
				}
				createAlert($results[0]['userId'],'User','Your time off request for: ' . $daysoff . ' has been ' . $status . '.',$severity,$groupcode);
				$message = "success";
			}
			else{
				$message = "noRecords";	
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