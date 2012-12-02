<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$shiftId = $_POST['id'];
$groupcode = $_POST['grpcode'];
// check session
if(VerifySession($sessionId,$groupcode,$_SESSION['_id'],'Admin') == true){
	if(($shiftId == "" || $groupcode == "")) {
			$message =  "emptyFields";
		} else {
		
			//check to see if shift exists
			$arg = array('id' => $shiftId, 'col' => "$groupcode", 'type' => 'shift');
			$results = $db->count($arg);
			if($results != null){		
				$obj = array('active' => 0);
				$data = $db->upsert(array('id' => $shiftId, 'col' => $groupcode, 'type' => "shift", 'obj' => $obj ));
				$message = "success";	
			}else{
				$message =  "noRecord";	
			}
			
			// remove the shift from user preference documents
			$arg = array('col' => "$groupcode", 'type' => 'user');
			$results = $db->find($arg);
			foreach ($results as $result) {
				$shiftPrefs = $result['preferences']['shifts'];
				foreach ($shiftPrefs as $key => $shift) {
					if ($shift == $shiftId) {
						unset($result['preferences']['shifts'][$key]);
						$tmpShiftOrder = $result['preferences']['shifts'];
						unset($result['preferences']['shifts']);
						$i=0;
						foreach ($tmpShiftOrder as $key => $shift2) {
							$result['preferences']['shifts'][$i] = $shift2;
							$i++;
						}
					}
				}
				
				if ($result['preferences']['shifts'] == "") {
					$result['preferences']['shifts'] = array();	
				}
				
				// $result is ready to be upserted
				$data = $db->upsert(array('id' => $result['_id'], 'col' => $groupcode, 'type' => "user", 'obj' => $result ));
			}
		}
}else{
//return auth failure
$message = "authFailure";	
}

echo json_encode(array('message' => $message, 'data'=>$data));

?>