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

if(isset($_POST['scheduleId'])){
	$scheduleId = $_POST['scheduleId'];	
}

if(isset($_POST['shiftId'])){
	$shiftId = $_POST['shiftId'];	
}

// check session
if(VerifySession($sessionId,$groupcode,$userId, 'User') == true){
	$trades = array();
	
	$where = array();
	$where['active'] = 1;
	$where['status'] = 'Accepted';
	//$where['$or'] = array(array('target_shift' => $shiftId), array('original_shift' => $shiftId));
	$where['original_shift'] = $shiftId;
	$arg = array('col' => "$groupcode", 'type' => 'trade', 'where' => $where, 'order_by' => 'date_completed', 'order' => 'DESC');
	$results = $db->find($arg);
		if($results != null){
		
		foreach($results as $result){
			$oUser = getUserById($groupcode,$result['original_user']);
			$tUser = getUserById($groupcode,$result['target_user']);
			
			$trades[] = array('id' => $db->_id($result['_id']), 'original_user' => $oUser, 'target_user' => $tUser, 'date_created' => date('m-d-Y h:i', $result['date_completed']->sec), 'status' => $result['status'], 'scheduleId' => $result['scheduleId'], 'comments' => $result['comments']);
			
		}
		}
		
	$where = array();
	$where['active'] = 1;
	$where['status'] = 'Accepted';
	//$where['$or'] = array(array('target_shift' => $shiftId), array('original_shift' => $shiftId));
	$where['target_shift'] = $shiftId;
	$arg = array('col' => "$groupcode", 'type' => 'trade', 'where' => $where, 'order_by' => 'date_completed', 'order' => 'DESC');
	$results = $db->find($arg);
	if($results != null){
		
		foreach($results as $result){
			$oUser = getUserById($groupcode,$result['original_user']);
			$tUser = getUserById($groupcode,$result['target_user']);
			
			$trades[] = array('id' => $db->_id($result['_id']), 'original_user' => $tUser, 'target_user' => $oUser, 'date_created' => date('m-d-Y h:i', $result['date_completed']->sec), 'status' => $result['status'], 'scheduleId' => $result['scheduleId'], 'comments' => $result['comments']);			
		}			
				}

				if($trades != null){
				$data = $trades;
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