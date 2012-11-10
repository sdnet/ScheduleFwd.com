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
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	if($groupcode == "" || $userId == null) {
		$message =  "emptyFields";
	} else {
		
		$results = getUserSchedule($userId,$groupcode,3,'next');
		if($results != null){
			$i = 0;
			$assembled = array();
			foreach($results as $result){
				foreach($result['schedule'] as $sched){
					foreach($sched['users'] as $k=>$v){
					
						if($v['id'] == $userId){
							if(strtotime($sched['start']) > strtotime('now')){
								if($i < 5){
									$assembled[] = $sched;
									$i++;
								}else{
									break;
								}
							}
						}
					}
				}
			}
			$data = $assembled;
			$message = "success";
		}
		else{
			$message = "noRecords";	
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