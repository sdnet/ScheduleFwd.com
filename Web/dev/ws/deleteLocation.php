<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupId = $_POST['id'];
$groupcode = $_POST['grpcode'];

// check session
if(VerifySession($sessionId,$groupcode,$_SESSION['_id'],'Admin') == true){
	if(($groupId == "" || $groupcode == "")) {
			$message =  "emptyFields";
		} else {
			
		$arg = array('col' => $groupcode, 'type' => 'location', 'id' => "$groupId", 'limit' => 1);
				$result = $db->find($arg);
		if($result != null){
					
			$obj = array('active' => 0);
			$data = $db->upsert(array('id' => "$groupId", 'type' => 'location', 'col' => $groupcode, 'obj' => $obj ));
					$message = "success";
				}
				else{
					$message = "LocationNotExist";	
				}
				
		}
}else{
//return auth failure
$message = "authFailure";	
}

echo json_encode(array('message' => $message, 'data'=>$data));

?>