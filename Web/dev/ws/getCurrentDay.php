<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];

if(isset($_POST['userId'])){
	$userId = $_POST['userId'];	
}else{
	$userId = $_SESSION['_id']; 	
}
// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	$today = date('Y-m-d');
	$year = date('Y');
	$month = date('m');	
	$where = array('year' => $year, 'month' => $month);
	$arg = array('col' => "$groupcode", 'type' => 'schedule', 'limit' => 1, 'where' => $where);
	$results = $db->find($arg);
	if($results != null){
		$onToday = array();
		foreach($results[0]['schedule'] as $shift){
			$day = substr($shift['start'], 0, 10);
			if($day == $today){
				$onToday[] = $shift;
			}
		}
		$data = $onToday;
		$message = "success";
	}
	else{
		$message = "noRecords";	
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