<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
if(isset($_POST['id'])){
	$userId = $_POST['id'];
}else{
	$userId = $_SESSION['_id'];
}

// check session
if(VerifySession($sessionId,$groupcode,$_SESSION['_id'],'User') == true){
	$where = array();
	$results = array();
	$user = getUserById($groupcode,$userId);
	$usergroup = $user['group'];
	$role = getRoleById($groupcode,$userId);
	$where = array('active' => 1);
	$arg = array('col' => "$groupcode", 'type' => 'user','where' => $where, 'keys' => array("first_name" => 1, "user_name" => 1, "last_name" => 1, "group" => 1));
	$userresults = $db->find($arg);
	$arg = array('col' => "$groupcode", 'type' => 'group','where' => $where, 'keys' => array('name' => 1));
	$groupresults = $db->find($arg);
	$arg = array('col' => "$groupcode", 'type' => 'role','where' => $where, 'keys' => array("name" => 1));
	$roleresults = $db->find($arg);
	foreach($userresults as $user){
		$results[] = array('value' => $user['user_name'], 'name' => '' . $user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['group'] . ')');	
	}
	
	foreach($groupresults as $user){
		if($role == 'Admin'){
			$results[] = array('value' => 'group:' . $user['name'], 'name' => 'All '.  $user['name'] . 's');	
		}else{
			if($usergroup == $user['name']){
				$results[] = array('value' => 'group:' . $user['name'], 'name' => 'All '.  $user['name'] . 's');	
			}
			}
	}
	
	foreach($roleresults as $user){
		if($role == 'Admin'){
			$results[] = array('value' => 'role:' . $user['name'], 'name' => 'All '.  $user['name'] . 's');	
		}else{
			if($user['name'] == 'User'){
				$results[] = array('value' => 'role:' . $user['name'], 'name' => 'All '.  $user['name'] . 's');	
			}
		}
	}
	
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

echo json_encode(array('message' => $message, 'data'=>$data));
?>