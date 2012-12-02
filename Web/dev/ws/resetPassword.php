<?php
require('cws.php');
require('classes/hash.php');

if ($_POST['submit'] == "true") {


	$password2 = $_POST['password2'];
	$password = $_POST['password'];
	$guid = $_POST['guid'];
	$groupcode = $_POST['grpcode'];
		
	if (($password2 == "") || ($password == "")) {
		echo "emptyFields";
	} else {
		
		if($password2 == $password){
			
			$where = array('guid' => "$guid");
			$arg = array('col' => "$groupcode", 'type' => 'reset', 'where' => $where, 'limit' => 1);
			$result = $db->find($arg);
			$results = $result[0];
			if($results != null){
				// the user id and password match,
				// set the session
				$db->delete(array('col' => "$groupcode", 'id' => $results['_id']));
				//clear the rest
				$db->delete(array('col' => "$groupcode", 'userId' => $results['userId']));
				
				$obj = array('password' => "" . create_hash($password) ."");
				$id = $db->upsert(array('id' => $results['userId'], 'col' => $groupcode, 'type' => "user", 'obj' => $obj ));
				echo "success";
			}
			else
			{
			 echo "invalidGUID";	
			}
		}
		else
		{
		 echo 'notMatch';	
		}
			
	}
	
} else {
	echo "NoSubmission";	
}
?>