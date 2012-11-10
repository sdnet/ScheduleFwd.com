<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$format = $_POST['format'];
$messageId = $_POST['messageId'];
if(isset($_POST['id'])){
	$uId = $_POST['id'];	
}else{
	$uId = $_SESSION['_id']; 	
}
$folder = $_POST['folder'];
// check session
if(VerifySession($sessionId,$groupcode,$uId,'User') == true){
	
	$where['active'] = 1;
	$arg = array('id' => $messageId, 'col' => "$groupcode", 'type' => 'message', 'where' => $where);
	$data = $db->find($arg);
	if($data != null){
		$obj = array('folder' => $folder);
		$arg = array('id' => $messageId, 'col' => "$groupcode", 'type' => 'message', 'obj' => $obj);
		$result = $db->upsert($arg);
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