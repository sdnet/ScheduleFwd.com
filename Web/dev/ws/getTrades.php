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

if(isset($_POST['status'])){
	$status = $_POST['status'];	
}
// check session
if(VerifySession($sessionId,$groupcode,$userId, 'User') == true){
	
	$where = array();
	$where['active'] = 1;
	if($status != ""){
		$where['status'] = $status;	
	}
	if(getRoleById($groupcode,$userId) == 'Admin'){
		$where['status'] = array('$ne' => 'Pending');
	}else{
		$where['$or'] = array(array('original_user' => $userId), array('target_user' => $userId));
	}
	$arg = array('col' => "$groupcode", 'type' => 'trade', 'where' => $where);
	$results = $db->find($arg);
		if($results != null){
		$trades = array();
		$ctrades = array();
		foreach($results as $result){
			$oUser = getUserById($groupcode,$result['original_user']);
			$tUser = getUserById($groupcode,$result['target_user']);
			$oShift =  getShiftFromSchedule($groupcode,$result['original_shift'],$result['scheduleId']);
			$tShift =  getShiftFromSchedule($groupcode,$result['target_shift'],$result['scheduleId']);
			if($result['status'] == 'Pending' || $result['status'] == 'Admin Approval'){
				$trades[] = array('id' => $db->_id($result['_id']), 'original_user' => $oUser, 'target_user' => $tUser, 'original_shift' => $oShift, 'target_shift' => $tShift, 'date_created' => date('m-d-Y h:i', $result['date_created']->sec), 'status' => $result['status'], 'scheduleId' => $result['scheduleId'], 'comments' => $result['comments']);
			}else{
				$ctrades[] = array('id' => $db->_id($result['_id']), 'original_user' => $oUser, 'target_user' => $tUser, 'original_shift' => $oShift, 'target_shift' => $tShift, 'date_created' => date('m-d-Y h:i', $result['date_created']->sec), 'status' => $result['status'], 'scheduleId' => $result['scheduleId'], 'comments' => $result['comments']);
			}
		}		
				$data = array('Pending' => $trades, 'Completed' => $ctrades);
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