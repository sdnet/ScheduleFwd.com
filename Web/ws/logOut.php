<?php 
include('cws.php');
header('Content-type: application/json');
	$sessionId = $_POST['sessionId'];
	$groupcode = $_POST['grpcode'];
if(VerifySession($sessionId,$groupcode,false,false) == true){
	
	$where = array('sessionId' => "$sessionId", 'groupcode' => $groupcode);
	$arg = array('col' => "sessions", 'type' => 'session', 'where' => $where, 'limit' => 1);
	$results = $db->find($arg);
	if($results != null){
		foreach($results as $result)
		{
			
			$db->delete(array('col' => "sessions", 'id' => $result['_id']));
			$message =  "success";
			$data = array('sessionId' => session_id());
			setcookie(session_name(), '', time()-42000, '/');
			session_destroy();
		}
	}else {
		$message = "notFound";
	}
}else{
	$message =  "authFailure";
}
echo json_encode(array('message' => $message, 'data'=>$data));
?>