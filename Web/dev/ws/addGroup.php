<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$groupname = $_POST['name'];
$groupdesc = $_POST['description'];
$max = $_POST['max'];
$min = $_POST['min'];
$priority = $_POST['priority'];

// check session
if(VerifySession($sessionId,$groupcode,false,false) == true){

	if(($groupname == "" || $groupcode == "" || !isset($max) || !isset($min))) {
			$message =  "emptyFields";
	} else {
		$hourCheck = hourCheck($min,$max);	
		if($hourCheck){
			$where = array('name' => "$groupname");
			$arg = array('col' => "$groupcode", 'type' => 'group', 'where' => $where, 'limit' => 1);
			$results = $db->find($arg);
			if($results == null){
				if(!isset($priority)){
					$priority = 1;	
				}
				
				
				$obj = array('name' => "$groupname", 'active' => 1, 'max_hours' => $max, 'min_hours' => $min, 'description' => $groupdesc, 'priority' => $priority, 'date_created' => new MongoDate());
				$data = $db->upsert(array('col' => $groupcode, 'type' => "group", 'obj' => $obj ));
				$message = "success";
			}else{
				$message =  "groupExists";	
			}
		}else{
			$message = "overrideInvalid";
		}
	}				
}else{
	//return auth failure
	$message = "authFailure";	
}

if($format == 'dt'){
	$data = dtFormat($data);
}

echo json_encode(array('message' => $message, 'data'=>$data));
?>