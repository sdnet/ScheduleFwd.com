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
$limit = $_POST['limit'];
if(isset($_POST['old'])){
	$old = 0;
}else{
   $old = 1;	
}

if(isset($_POST['status'])){
	$status = $_POST['status'];
	}
	
if($status == 'Approved'){
	$old = 0;	
}
if($status == 'Pending'){
	$old = 1;	
}
if($status == 'Disapproved'){
	$old = 0;	
}
// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	if($groupcode == "" || $userId == null) {
			$message =  "emptyFields";
	} else {
		
		if(!isset($limit)){
			$limit = 60;	
		}
		if(getRoleById($groupcode,$userId) == 'Admin'){
			$where = array('role' => array('$in' => array('Admin','User')),'active' => $old, 'status' => $status);
		}else{
			$where = array('role' => 'User', 'userId' => $userId);
		}
		$arg = array('col' => "$groupcode", 'type' => 'timeoff', 'where' => $where, 'keys' => array('user_name' => 1, 'date_created' => 1, 'first_name' => 1, 'last_name' => 1, 'time_off' => 1, 'status' => 1, 'priority' => 1), 'limit' => $limit);
		$results = $db->find($arg);
		if($results != null){
			$data = $results;
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