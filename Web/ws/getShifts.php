<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$name = $_POST['name'];
$duration = $_POST['duration'];
$start = $_POST['start'];
$end = $_POST['end'];
$days = $_POST['days'];
$group = $_POST['group'];
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
	if($start != null)
	{ 
	$where['start'] = $start;
	}
	if($name != null)
	{
		$where['name'] = $name;	
	}
	if($end != null)
	{
		$where['end'] = $end;
	}
	if($duration != null)
	{
		$where['duration'] = $duration;	
	}
	if($group != null)
	{
		$where['groups'] = array('$in' => array($group));	
	}
	if($days != null)
	{
		$where['day'] = $day;	
	}
			
	$arg = array('col' => "$groupcode", 'type' => 'shift', 'where' => $where);
	$results = $db->find($arg);
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

if($format == 'dt'){
	$data = dtFormat($data);
}
else{

}
echo json_encode(array('message' => $message, 'data'=>$data));
?>