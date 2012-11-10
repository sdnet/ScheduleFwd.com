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
$month = $_POST['month'];
$year = $_POST['year'];

// Check to see if months add up and get 6 months out
$monthArray = array();
$monthArray2 = array();
$combinedArray = array();
$multiYears = 0;

// Move the current month, and the month previous, into the first month array
array_push($monthArray,$month-1);
array_push($monthArray,$month);

for ($i = 1; $i <= 4; $i++) {
	if ($month + $i > 12) {
		$tmpMonth = ($month + $i) - 12;
		$multiYears = 1;
		array_push($monthArray2, $tmpMonth);	
	} else {
		array_push($monthArray, $month + $i);
	}
}

$combinedArray[$year] = $monthArray;
if ($multiYears > 0) { $combinedArray[$year+1] = $monthArray2; }

// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	if($groupcode == "" || $userId == null) {
			$message =  "emptyFields";
	} else {
		foreach($combinedArray as $key=>$value){
			$months = array();
			foreach($value as $val){
				if(strlen((string)$val) == 1){
					$months[] = "0" . (string)$val;	
				}else{
					$months[] = (string)$val;	
				}
			}
			foreach($months as $mm){
				$where = array('active' => 1, 'month' => $mm, 'year' => "" . $key . "");
				$arg = array('col' => "$groupcode", 'type' => 'schedule', 'where' => $where);
				$result = $db->find($arg);
				if($result != null){
					$temp[] = $result;	
				}
			}
		}
		if($temp != null){
		foreach($temp as $results){
			$combined = array();
				foreach($results as $result){
					$mysched = array();
					$othersched = array();
					foreach($result['schedule'] as $sched){
						if($sched['users']['id'] == $userId){
							$mysched[] = $sched;
							$othersched[] = $sched;	
						}else{
							$othersched[] = $sched;
						}
					}
					unset($result['schedule']);
					$result['mySchedule'] = $mysched;
					$result['deptSchedule'] = $othersched;	
					$combined[] = $result;
				}
				foreach($combined as $key=>$value){
				$data[] = $value;
				}
				}
				$message = "success";
				//$data = $combined;	
			}else{
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