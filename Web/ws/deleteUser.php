<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$userId = $_POST['userId'];
$groupcode = $_POST['grpcode'];
// check session
if(VerifySession($sessionId,$groupcode,$_SESSION['_id'],'Admin') == true){
	if(($userId == "" || $groupcode == "")) {
			$message =  "emptyFields";
		} else {
			
		$arg = array('col' => $groupcode, 'type' => 'user', 'id' => "$userId", 'limit' => 1);
				$result = $db->find($arg);
		if($result != null){
					
			$obj = array('active' => 0);
			$data = $db->upsert(array('id' => "$userId", 'col' => $groupcode, 'obj' => $obj ));
					$message = "success";
				}
				else{
					$message = "UserNotExist";	
				}
				
		}
}else{
//return auth failure
$message = "authFailure";	
}

echo json_encode(array('message' => $message, 'data'=>$data));

?>