<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$groupname = $_POST['name'];
$groupId = $_POST['id'];

// check session
if(VerifySession($sessionId,$groupcode,false,false) == true){
	if($groupcode == "") {
			$message =  "emptyFields";
	} else {
		if(isset($groupId)){
			$where = array('_id'=> new MongoId($groupId));
		}else{
			$where = array('name'=> $groupname);
		}
		
		$arg = array('col' => "$groupcode", 'type' => 'location', 'limit' => 1, 'where' => $where);
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