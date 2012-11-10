<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$orgname = $_POST['orgname'];
$groupcode = $_POST['grpcode'];
$orgId = $_POST['id'];
$format = $_POST['format'];

// check session
if(VerifySession($sessionId,$groupcode,false,false) == true){
	$where = array();
	if($orgId == null){
		
		if($email != null)
		{ 
			$where['email'] = $email;
		}
		if($orgname != null)
		{
			$where['org_name'] = $orgname;	
		}
		if($lastName != null)
		{
			$where['last_name'] = $lastName;	
		}
	}elseif(isset($orgId)){
		$orgIdArray = Array('id' => $orgId);
	}
				//$where = array('email' => "$email", 'org_name' => "$orgname", 'first_name' => "$firstName", 'last_name' => "$lastName", 'group' => "$group", 'role' => "$role");
	$arg = array('col' => "$groupcode", 'type' => 'extUser', 'limit' => 1, 'where' => $where);
	if(isset($orgIdArray)){
		$arg = array_merge($orgIdArray, $arg);
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