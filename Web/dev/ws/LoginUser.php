<?php
include('cws.php');
require('../protected/hash.php');
header('Content-type: application/json');
if ($_POST['submit'] == "true") {
	$return = "false";
	
	$username = $_POST['username'];
	$password = $_POST['password'];
	$groupcode = $_POST['grpcode'];
	
	
	if (($username == "") || ($password == "") || ($groupcode == "")) {
		$message =  "emptyFields";
	} else {
		$uregex = new MongoRegex("/^" . $username . "$/i");
		$where = array('user_name' => $uregex);
		$arg = array('col' => "$groupcode", 'type' => 'user', 'where' => $where, 'limit' => 1);
		$results = $db->find($arg);
		if($results != null){
				if(validate_password($password,$results[0]['password'])){
			foreach($results as $result)
				{
				if($result['active'] == 1) {
				// the user id and password match,
				// set the session
			
					$_SESSION['_id'] = $db->_id($result['_id']);
						$_SESSION['firstName'] = $result['first_name'];
						$_SESSION['lastName'] = $result['last_name']; 
						$_SESSION['group'] = $result['group'];
						$_SESSION['email'] = $result['email'];
						$_SESSION['grpcode'] = $groupcode;
						$_SESSION['authed'] = '1';
						$_SESSION['role'] = $result['role'];
						$_SESSION['active'] = $result['active'];
						$_SESSION['userName'] = $result['user_name'];
						$where = array('userId' => $result['_id'], 'user_name' => $result['user_name']);
						$db->upsert(array('col' => 'sessions', 'type' => "session", 'obj' => array('groupcode' => $groupcode, 'user_name' => $result['user_name'], 'sessionId' => session_id(), 'userId' => $result['_id'])));
						$message =  "success";
						$data = array('sessionId' => session_id(), 'groupCode' => $groupcode, 'userName' => $result['username'], 'userId' => $result['_id']);
						
						
						}
					
					else{
						$message =  "notActive";
					}
					}
			}else {
				$message =  "authFailure";
			}
		}else{
			$message = "authFailure";
			}
	}
} else {
	$message =  "noSubmission";	
}

echo json_encode(array('message' => $message, 'data'=>$data));
?>