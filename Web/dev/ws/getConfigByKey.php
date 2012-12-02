<?php
include('cws.php');
header('Content-type: application/json');
$groupcode = $_POST['grpcode'];
$sessionId = $_POST['sessionId'];
$key = $_POST['key'];
if(isset($_POST['id'])){
	$userId = $_POST['id'];
}else{
	$userId = $_SESSION['_id'];
}
// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	if(($groupcode == "" || $key == "")) {
		$message =  "emptyFields";
	} else {
		//check to see if user exists
		$arg = array('col' => "$groupcode", 'type' => 'config', 'limit' => 1);
		$results = $db->find($arg);
		if($results != null){	
			$return = array();
			foreach($results as $result){
				$return = array($key => $result[$key]);
				}		
			$data = $return;
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