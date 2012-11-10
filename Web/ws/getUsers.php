<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$username = $_POST['username'];
$groupcode = $_POST['grpcode'];
$firstName = $_POST['firstname'];
$lastName = $_POST['lastname'];
$email = $_POST['email'];
$group = $_POST['group'];
$role = $_POST['role'];
$format = $_POST['format'];
$exclude = $_POST['exclude'];
if(isset($_POST['id'])){
	$userId = $_POST['id'];
}else{
	$userId = $_SESSION['_id'];
}
// check session
if(VerifySession($sessionId,$groupcode,false, false) == true){
	
	$where = array();
	$where['active'] = 1;
	if($email != null)
	{ 
	$where['email'] = $email;
	}
	if($username != null)
	{
		$where['user_name'] = $username;	
	}
	if($firstName != null)
	{
		$where['first_name'] = $firstName;
	}
	if($lastName != null)
	{
		$where['last_name'] = $lastName;	
	}
	if($group != null)
	{
		$where['group'] = $group;	
	}
	if($role != null)
	{
		$where['role'] = $role;	
	}
	if($exclude != null)
	{
		$where['_id'] = array('$ne' => new MongoId($userId));	
	}
			//	$where = array('email' => "$email", 'user_name' => "$username", 'first_name' => "$firstName", 'last_name' => "$lastName", 'group' => "$group", 'role' => "$role");
	$arg = array('col' => "$groupcode", 'type' => 'user', 'where' => $where, 'keys' => array("first_name" => 1, "last_name" => 1, "user_name"=> 1, "email" => 1,"phone" => 1,"role" => 1,"_id" => 1, "group" => 1));
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