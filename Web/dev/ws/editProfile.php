<?php
include('cws.php');
require('classes/hash.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$userId = new MongoId($_POST['id']);
$username = $_POST['username'];
$groupcode = $_POST['grpcode'];
$firstName = $_POST['firstname'];
$lastName = $_POST['lastname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$originalPassword = $_POST['original'];
if(!isset($_POST['password']) || $_POST['password'] != ''){
$password = $_POST['password'];
}

if(!isset($_POST['password2']) || $_POST['password2'] != '' ){
	$password2 = $_POST['password2'];
}

// check session
if(VerifySession($sessionId,$groupcode,$_SESSION['_id'],'User') == true){
	if(ValidEmail($email)){
		if(($userId == "" || $username == "" || $groupcode == "" || $firstName == "" || $lastName == "" || $email == "" || $phone == "")) {
			$message =  "emptyFields";
		}else{
				$arg = array('id' => $_POST['id'], 'col' => "$groupcode", 'type' => 'user', 'where' => array(), 'limit' => 1);
				$result1 = $db->find($arg);
		
				if(validate_password($originalPassword,$result1[0]['password'])){
					//check to see if user exists
					$where = array('user_name' => "$username", '_id' => array( '$ne' => $userId));
					$arg = array('col' => "$groupcode", 'type' => 'user', 'where' => $where, 'limit' => 1);
					$results = $db->find($arg);
					if($results == null){
						$where = array('email' => "$email", '_id' => array( '$ne' => $userId));
						$arg = array('col' => $groupcode, 'type' => 'user', 'where' => $where, 'limit' => 1);
						$result = $db->find($arg);
						if($result == null){
							$obj = array('user_name' => "$username", 'active' => 1, 'email' => $email, 'first_name' => $firstName, 'last_name' => $lastName, 'phone' => $phone);	
								if((isset($password) && isset($password2)) && ($password2 == $password)){
								$passArray = Array('password' => create_hash($password));
								$obj = array_merge($obj, $passArray);	
							}	
						$id = $db->upsert(array('id' => $_POST['id'], 'col' => $groupcode, 'type' => "user", 'obj' => $obj ));
							$data = array('userId' => $id, 'user_name' => "$username");
							$message = "success";
							
						}
						else{
							$message = "emailExists";
						}	
					}else{
						$message =  "userExists";
					}
				}else{
				$message = "passwordInvalid";
			}
		}
	}else{
	$message = "emailInvalid";
	}
	}else{
		//return auth failure
		$message = "authFailure";	
	}

echo json_encode(array('message' => $message, 'data'=>$data));

?>