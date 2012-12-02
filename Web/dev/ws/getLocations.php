<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$format = $_POST['format'];

if(isset($_POST['id'])){
	$uId = $_POST['id'];	
}else{
	$uId = $_SESSION['_id']; 	
}
// check session
if(VerifySession($sessionId,$groupcode,$uId,'User') == true){
	
	$where = array();
	$where['active'] = 1;
		
	$arg = array('col' => "$groupcode", 'type' => 'location', 'where' => $where);
	$results = $db->find($arg);
	if($results != null){
		foreach($results as $result)
		{
			$data[] = $result;
		}
		//$data = $results;
		$message = "success";
	}
	else{
		$message = "noRecords";	
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