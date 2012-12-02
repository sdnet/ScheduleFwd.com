<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$scheduleId = $_POST['scheduleId'];
$userId = $_POST['userId'];
$originalId = $_POST['shiftId'];
if(isset($_POST['id'])){
	$uId = $_POST['id'];	
}else{
	$uId = $_SESSION['_id']; 	
}
// check session
if(VerifySession($sessionId,$groupcode,$uId,'User') == true){
	
	$results = getShiftsByUserFromSchedId($groupcode, $scheduleId, $userId);
				if($results != null){
		$shifts = array();		
		foreach($results as $result){
			$canwork = false;
			if(strtotime($result['start']) > strtotime('now')){
				$canwork = getUserCanWork($groupcode,$uId,$result['start'],$result['endreal'],$originalId,$scheduleId);
				if($canwork){	
					$start = date('H:ia',strtotime($result['start']));
					$end = date('H:ia',strtotime($result['endreal']));
					$shifts[] = array('id' => $result['id'], 'name' => '' . date('D, jS', strtotime($result['start'])) . ' - ' . $result['shiftName'] . ' (' . $start . '-' . $end . ')', 'start' => $result['start']);
				}
			}
		}
				$data = $shifts;
				$message = "success";
				}
				else{
					$message = "noRecords";	
				}
				
}else{
	//return auth failure
	$message = "authFailure";	
}

echo json_encode(array('message' => $message, 'data'=>$data));
?>