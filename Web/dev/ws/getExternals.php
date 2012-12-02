<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$orgName = $_POST['orgname'];
$groupcode = $_POST['grpcode'];
$firstName = $_POST['firstname'];
$lastName = $_POST['lastname'];
$email = $_POST['email'];

$format = $_POST['format'];
// check session
if(VerifySession($sessionId,$groupcode,false, false) == true){
	
	$where = array();
	$where['active'] = 1;
	if($email != null)
	{ 
	$where['email'] = $email;
	}
	if($orgName != null)
	{
		$where['org_name'] = $orgName;	
	}
	if($firstName != null)
	{
		$where['first_name'] = $firstName;
	}
	if($lastName != null)
	{
		$where['last_name'] = $lastName;	
	}
	
			//	$where = array('email' => "$email", 'user_name' => "$orgName", 'first_name' => "$firstName", 'last_name' => "$lastName", 'group' => "$group", 'role' => "$role");
	$arg = array('col' => "$groupcode", 'type' => 'extUser', 'where' => $where);
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