<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
if(isset($_POST['id'])){
	$userId = $_POST['id'];
}else{
	$userId = $_SESSION['_id'];
}
$groupcode = $_POST['grpcode'];
$shifts = $_POST['shifts'];
$days = $_POST['days'];
$blockWeekend = $_POST['blockweekend'];
$blockDays = $_POST['blockdays'];
$maxNights = $_POST['maxnights'];
$desiredNights = $_POST['desirednights'];
$maxDays = $_POST['maxdays'];
$desiredDays = $_POST['desireddays'];
$circadian = $_POST['circadian'];
$afterNightShift = $_POST['afterNightShift'];

// check session
if(VerifySession($sessionId,$groupcode,$_SESSION['_id'],'User') == true){
		if(($userId == "")) {
			$message =  "emptyFields";
		}else{
					//check to see if user exists
					$arg = array('id' => $_POST['id'], 'col' => "$groupcode", 'type' => 'user', 'limit' => 1);
					$results = $db->find($arg);
					if($results != null){
						$shiftArray = array();
						if(!is_array($days)){
						$dayArray = explode(",", $days);
						}
						if(isset($shifts)){
							$i = 0;
						$sArray = explode(",", $shifts);
						foreach($sArray as $shift){
							$shiftArray[$i] = $shift;	
							$i++;
						}
						}
			foreach($shiftArray as $shift){
				$shiftArr[] = trim(preg_replace("/\([^)]+\)/","",$shift));
			}
			$obj = array('preferences' => array('days' => $dayArray, 'shifts' => $shiftArr, 'block_days' => $blockDays, 'block_weekend' => $blockWeekend, 'max_days' => $maxDays, 'max_nights' => $maxNights, 'desired_nights' => $desiredNights, 'desired_days' => $desiredDays, 'circadian' => $circadian, 'afterNightShift' => $afterNightShift));			
						$data = $db->upsert(array('id' => $_POST['id'], 'col' => $groupcode, 'type' => "user", 'obj' => $obj ));
							$message = "success";
							
						}else{
						$message =  "userNotExists";
					}
				}
	}else{
		//return auth failure
		$message = "authFailure";	
	}

echo json_encode(array('message' => $message, 'data'=>$data));

?>