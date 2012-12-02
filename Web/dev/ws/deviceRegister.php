<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$userId = $_POST['id'];
$groupcode = $_POST['grpcode'];
$token = $_POST['token'];

if(VerifySession($sessionId,$groupcode,false, false) == true){
	
	$where = array('device_tokens' => array('$in' => array($token)), '_id' => array( '$ne' => new MongoId($userId)));
					$arg = array('col' => $groupcode, 'type' => 'user', 'where' => $where, 'limit' => 1);
					$result = $db->find($arg);
	if($result == null){
		
		$arg = array('id' => $userId, 'col' => $groupcode, 'type' => 'user', 'limit' => 1);
		$results = $db->find($arg);
		if(!in_array($token, $results['device_tokens'])){
		$currTokens = $results['device_tokens'];
		$currTokens[] = $token;
		$obj = array('device_tokens' => $currTokens);		
		$data = $db->upsert(array('id' => $userId, 'col' => $groupcode, 'type' => "user", 'obj' => $obj ));
		$message = "success";
		}else{
			$message = "deviceDuplicate";
			}
	}else{
		$obj = array_diff($result['device_tokens'], array("" . $token . ""));
		$data = $db->upsert(array('id' => $userId, 'col' => $groupcode, 'type' => "user", 'obj' => $obj ));
		$message = "success";
	}
}else{
	//return auth failure
	$message = "authFailure";	
}
echo json_encode(array('message' => $message, 'data'=>$data));