<?php
include('cws.php');
header('Content-type: application/json');
$groupcode = $_POST['grpcode'];
$sessionId = $_POST['sessionId'];

// check session
if(VerifySession($sessionId,$groupcode,$_SESSION['_id'],'Admin') == true){
	if(($groupcode == "")) {
		$message =  "emptyFields";
	} else {
		//check to see if user exists
		$arg = array('col' => "$groupcode", 'type' => 'config', 'limit' => 1);
		$results = $db->find($arg);
		if($results != null){			
			$data = $results;
			$message = "success";
		}else{
			$message =  "fail";	
		}
	}

}else{
	//return auth failure
	$message = "authFailure";	
}

echo json_encode(array('message' => $message, 'data'=>$data));

?>