<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = "";
$userId = "50331011c48128131b000000";
$groupcode = "testy";
$token = "8C73346B6FDF22CE7ED64480012BF04D2608F100E3391A13ADCEA95AB7F5F93C";


$where = array(('device_tokens') => array('$in' => $token), '_id' => array( '$ne' => $userId));
	$arg = array('col' => $groupcode, 'type' => 'user', 'where' => $where, 'limit' => 1);
	$result = $db->find($arg);
	
print_r($arg);
	
	print "Print: " . $result;
	
	if($result == null){
		
		if(in_array($token, $result['device_tokens'])){
			$currTokens = $result['device_tokens'];
			$currTokens[] = $token;
			$obj = array('device_tokens' => $currTokens);		
			$data = $db->upsert(array('id' => $userId, 'col' => $groupcode, 'type' => "user", 'obj' => $obj ));
			$message = "success";
		}else{
			$message = "deviceDuplicate";
		}
	}else{
		$message = "deviceExists";
	}
	
echo json_encode(array('message' => $message, 'data'=>$data));