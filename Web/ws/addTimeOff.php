<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
if(isset($_POST['id']) && $_POST['id'] != ""){
	$userId = $_POST['id'];
}else{
	$userId = $_SESSION['_id'];
}
$shiftId = trim($_POST['shiftId']);
$date = trim($_POST['date']);
$priority = trim($_POST['priority']);
$update = $_POST['update'];
// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	if($groupcode == "" || $userId == null) {
			$message =  "emptyFields";
	} else {
		if($year == null){
			$year = date('Y',strtotime($date));
		}
		if($month == null){
			$month = date('m',strtotime($date));
		}
		if($priority == null){
		 $priority = 0;	
		}
                $date = date('Y-m-d',strtotime($date));
		if($update == true){
			$where = array('year' => $year, 'month' => $month, 'userId' => $userId, 'time_off' => array('$in' => array(array($date => $shiftId))));
			$arg = array('col' => "$groupcode", 'type' => 'timeoff',  'where' => $where);
			$result = $db->find($arg);
			$timeOffId = $db->_id($result[0]['_id']);
			$arg = array('id' => $timeOffId, 'col' => "$groupcode", 'type' => 'timeoff',  'obj' => array('priority' => $priority));
			$results = $db->upsert($arg);	
		}else{
		$timeOffArray = array($date => $shiftId);
		$arg = array('id' => "$userId",'col' => "$groupcode", 'type' => 'user');
		$result = $db->find($arg);
		$role = getRoleById($groupcode, $userId);
		$obj = array('first_name' => $result[0]['first_name'], 'last_name' => $result[0]['last_name'], 'user_name' => $result[0]['user_name'], 'status' => 'Pending', 'active' => 1, 'userId' => $userId, 'role' => $role, 'month' => $month, 'year' => $year,'time_off' => $timeOffArray, 'status' => 'Pending', 'date_created' => new MongoDate());
		$arg = array('col' => "$groupcode", 'type' => 'timeoff',  'obj' => $obj);
		$results = $db->upsert($arg);
		}
		if($results != null){
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