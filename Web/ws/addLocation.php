<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$gname = $_POST['name'];
$gdesc = $_POST['description'];
$gshort = $_POST['short'];

// check session
if(VerifySession($sessionId,$groupcode,false,false) == true){

	if(($gname == "" || $groupcode == "" || $gname == "" || $gshort == "")) {
			$message =  "emptyFields";
	} else {
		
		$where = array('short' => "$gshort");
		$arg = array('col' => "$groupcode", 'type' => 'location', 'where' => $where, 'limit' => 1);
			$results = $db->find($arg);
			if($results == null){

			$obj = array('active' => 1, 'name' => "$gname", 'short' => $gshort, 'description' => $gdesc, 'date_created' => new MongoDate());
			$data = $db->upsert(array('col' => $groupcode, 'type' => "location", 'obj' => $obj ));
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

echo json_encode(array('message' => $message, 'data'=>$data));
?>