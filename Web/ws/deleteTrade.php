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
$tradeId = $_POST['tradeId'];


// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	if($groupcode == "" || $userId == null || $tradeId == "") {
			$message =  "emptyFields";
	} else {
		$obj = array('status' => 'Cancelled', 'active' => 1);
		$arg = array('id' => $tradeId, 'col' => "$groupcode", 'type' => 'trade', 'obj' => $obj);
		$results = $db->upsert($arg);
		if($results != null){
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