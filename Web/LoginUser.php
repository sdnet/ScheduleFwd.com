<?php 
session_start();
require_once('../core/mg_base.class.php');
require('../protected/hash.php');


if ($_POST['submit'] == "true") {
	$return = "false";
	
	$username = $_POST['username'];
	$password = $_POST['password'];
	$groupcode = $_POST['grpcode'];
	
	
	if (($username == "") || ($password == "")) {
		echo "emptyFields";
	} else {
		
		        $db = new MONGORILLA_DB;
		$where = array('username' => "$username", 'password' => "$password");
		$arg = array('col' => "$groupcode", 'type' => 'user', 'where' => $where, 'limit' => 1);
		$results = $db->find($arg);
		if($results != null){
			foreach($results as $result)
				{
				if($result['active'] == 1) {
				// the user id and password match,
				// set the session
				
					
						$_SESSION['_id'] = $record['_id'];
						$_SESSION['firstName'] = $record['first_name'];
						$_SESSION['lastName'] = $record['last_name']; 
						$_SESSION['email'] = $record['email'];
						$_SESSION['phone'] = $record['phone'];
						$_SESSION['authed'] = '1';
						$_SESSION['active'] = $record['active'];
						$_SESSION['userName'] = $record['username'];
					$db->upsert(array('col' => "$groupcode", 'type' => "session", 'obj' => array('sessionId' => session_id(), 'userId' => $record['_id'])));
						echo "Success";
						}
					
					else{
						echo "NotActive";
					}
					}
				}
		else {
			echo "AuthFailure";
	}
	}
} else {
	echo "NoSubmission";	
}
?>