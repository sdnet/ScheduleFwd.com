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

// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	if($groupcode == "" || $userId == null) {
			$message =  "emptyFields";
	} else {
		if(getRoleById($groupcode,$userId) == 'Admin'){
			$where = array('role' => 'Admin','active' => 1);
		}else{
			$where = array('role' => 'User', 'userId' => $userId,'active' => 1);
		}
		$arg = array('col' => "$groupcode", 'type' => 'alert', 'where' => $where);
		$results = $db->find($arg);
		if($results != null){
			if(getRoleById($groupcode,$userId) == 'Admin'){
				$data = array("count" => count($results), "alerts" => $results);	
			}
			else{
				$data = $results;
			}
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