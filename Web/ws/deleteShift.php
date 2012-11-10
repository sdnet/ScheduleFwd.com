<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$shiftId = $_POST['id'];
$groupcode = $_POST['grpcode'];
// check session
if(VerifySession($sessionId,$groupcode,$_SESSION['_id'],'Admin') == true){
	if(($shiftId == "" || $groupcode == "")) {
			$message =  "emptyFields";
		} else {
		
			//check to see if shift exists
			$arg = array('id' => $shiftId, 'col' => "$groupcode", 'type' => 'shift');
			$results = $db->count($arg);
			if($results != null){		
				$obj = array('active' => 0);
				$data = $db->upsert(array('id' => $shiftId, 'col' => $groupcode, 'type' => "shift", 'obj' => $obj ));
				$message = "success";	
			}else{
				$message =  "noRecord";	
			}
		}
}else{
//return auth failure
$message = "authFailure";	
}

echo json_encode(array('message' => $message, 'data'=>$data));

?>