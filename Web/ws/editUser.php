<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$userId = new MongoId($_POST['id']);
$username = $_POST['username'];
$groupcode = $_POST['grpcode'];
$firstName = $_POST['firstname'];
$lastName = $_POST['lastname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$group = $_POST['group'];
$role = $_POST['role'];
$picture = $_POST['picture'];
if(isset($_POST['min']) && $_POST['min'] != ""){
	$min = $_POST['min'];
}
if(isset($_POST['max']) && $_POST['max'] != ""){
	$max = $_POST['max'];
}
$priority = $_POST['priority'];
$hourCheck = true;
// check session
if(VerifySession($sessionId,$groupcode,$_SESSION['_id'],'Admin') == true){
	if(isset($min) || isset($max)){
		$hourCheck = hourCheck($min,$max);
	}	
	if($hourCheck){
		if(ValidEmail($email)){
			if(($userId == "" || $username == "" || $groupcode == "" || $firstName == "" || $lastName == "" || $group == "" || $role == "")) {
				$message =  "emptyFields";
			} else {
				//check to see if user exists
				$where = array('user_name' => "$username", '_id' => array( '$ne' => $userId));
				$arg = array('col' => "$groupcode", 'type' => 'user', 'where' => $where, 'limit' => 1);
				$results = $db->find($arg);
				print_r($results);
				if($results == null){
					
					$where = array('email' => "$email", '_id' => array( '$ne' => $userId));
					$arg = array('col' => $groupcode, 'type' => 'user', 'where' => $where, 'limit' => 1);
					$result = $db->find($arg);
					if($result == null){
						if(!isset($priority)){
							$priority = 1;	
						}
						
						$obj = array('user_name' => "$username", 'active' => 1, 'email' => $email, 'first_name' => $firstName, 'last_name' => $lastName, 'phone' => $phone, 'group' => $group, 'priority' => $priority, 'role' => $role, 'picture' => $picture, 'date_created' => new MongoDate());		
						if(isset($min) && isset($max) && is_numeric($max) && is_numeric($min)){
							$hourArray = array('min_hours' => $min, 'max_hours' => $max);
							$obj = array_merge($obj, $hourArray);
						}
						$id = $db->upsert(array('id' => $userId, 'col' => $groupcode, 'type' => "user", 'obj' => $obj ));
						$data = array('userId' => $id, 'user_name' => "$username");
						$message = "success";
					}
					else{
						$message = "emailExists";	
					}
					
				}else{
					$message =  "userExists";	
				}
			}
		}else{
			$message = "emailInvalid";
		}
	}else{
		$message = "overrideInvalid";
	}
}else{
	//return auth failure
	$message = "authFailure";	
}

echo json_encode(array('message' => $message, 'data'=>$data));

?>