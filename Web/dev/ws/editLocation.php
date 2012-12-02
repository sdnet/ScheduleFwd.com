<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$groupId = $_POST['id'];
$gname = $_POST['name'];
$gdesc = $_POST['description'];
$gshort = $_POST['short'];

// check session
if(VerifySession($sessionId,$groupcode,false,false) == true){

	if(($groupId == "" || $groupcode == "" || $gname == "" || $gshort == "")) {
			$message =  "emptyFields";
	} else {
		
		$where = array('short' => "$gshort", '_id' => array('$ne' => new MongoId($groupId)));
		$arg = array('col' => "$groupcode", 'type' => 'location', 'where' => $where, 'limit' => 1);
			$results = $db->find($arg);
			if($results == null){
				
				
			$obj = array('name' => "$gname", 'short' => $gshort, 'description' => $gdesc, 'date_created' => new MongoDate());
			$data = $db->upsert(array('id' => $groupId, 'col' => $groupcode, 'type' => "location", 'obj' => $obj ));
				$message = "success";
			}else{
				$message =  "locationExists";	
			}
		
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