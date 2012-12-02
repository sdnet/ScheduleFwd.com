<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$shiftId = $_POST['id'];
$format = $_POST['format'];

if(isset($_POST['userId'])){
	$userId = $_POST['userId'];	
}else{
	$userId = $_SESSION['_id']; 	
}
// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	$where = array();
	if($shiftId == null){
		if($name != null)
		{
			$where['name'] = $name;	
		}
		
	}elseif(isset($shiftId)){
		$shiftIdArray = Array('id' => $shiftId);
	}
	$arg = array('col' => "$groupcode", 'type' => 'shift', 'limit' => 1, 'where' => $where, 'keys' => array("name" => 1, "color" => 1, "start" => 1, "end" => 1,"groups" => 1,"_id" => 1, "days" => 1,"number" => 1, 'location' => 1));
	if(isset($shiftIdArray)){
		$arg = array_merge($shiftIdArray, $arg);
	}
	$results = $db->find($arg);
	if($results != null){
		$data = $results;
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