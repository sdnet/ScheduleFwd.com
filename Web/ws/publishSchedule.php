<?php
include('cws.php');
include('classes/archive.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
if(isset($_POST['id'])){
	$userId = $_POST['id'];
}else{
	$userId = $_SESSION['_id'];
}
$scheduleId = $_POST['scheduleId'];

// check session
if(VerifySession($sessionId,$groupcode,$userId,'Admin') == true){
	if($groupcode == "" || $userId == null || $scheduleId == "") {
			$message =  "emptyFields";
	} else {
		
		$arg = array('id' => $scheduleId,'col' => "$groupcode", 'type' => 'schedule');
		$results = $db->find($arg);
		if($results != null){
			$obj = array('published' => 1);
			$arg = array('id' => $scheduleId,'col' => "$groupcode", 'type' => 'schedule', 'obj' => $obj);
			$result = $db->upsert($arg);
			$arch = new Archive($results[0]['month'],$results[0]['year'],'start',$groupcode,$scheduleId);
			$arch->getSchedule();
			$arch->removeArchive();
			$arch->setArchive();
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