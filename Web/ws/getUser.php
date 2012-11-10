<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$username = $_POST['username'];
$groupcode = $_POST['grpcode'];
$userId = $_POST['id'];
$format = $_POST['format'];

// check session
if(VerifySession($sessionId,$groupcode,false,false) == true){
	$where = array();
	if($userId == null){
		
		if($email != null)
		{ 
			$where['email'] = $email;
		}
		if($username != null)
		{
			$where['user_name'] = $username;	
		}
		if($lastName != null)
		{
			$where['last_name'] = $lastName;	
		}
	}elseif(isset($userId)){
		$userIdArray = Array('id' => $userId);
	}
				//$where = array('email' => "$email", 'user_name' => "$username", 'first_name' => "$firstName", 'last_name' => "$lastName", 'group' => "$group", 'role' => "$role");
	$arg = array('col' => "$groupcode", 'type' => 'user', 'limit' => 1, 'where' => $where, 'keys' => array("first_name" => 1, "user_name" => 1, "last_name" => 1, "email" => 1,"phone" => 1,"role" => 1,"_id" => 1, "priority" => 1, "group" => 1, 'max_hours' => 1, 'min_hours' => 1, 'picture' => 1));
	if(isset($userIdArray)){
		$arg = array_merge($userIdArray, $arg);
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