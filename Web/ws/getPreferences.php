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

// check session
if(VerifySession($sessionId,$groupcode,$_SESSION['_id'],'User') == true){
	if(($userId == "")) {
			$message =  "emptyFields";
		}else{
					//check to see if user exists
					$arg = array('id' => $_POST['id'], 'col' => "$groupcode", 'type' => 'user', 'limit' => 1);
					$results = $db->find($arg);
					if($results != null){
			if(!isset($results[0]['preferences']['shifts'])){
				$shifts = getShiftsByUserId($groupcode, $userId);
				foreach($shifts as $shift){				
					$results[0]['preferences']['shifts'][] = array($db->_id($shift['_id']),"" . $shift['name'] . "  (" . DATE("g:i a", STRTOTIME("" . $shift['start'] . "")) . " - " . DATE("g:i a", STRTOTIME("" . $shift['end'] . "")) . ")");	
					
				}
				$results[0]['preferences']['days'] = array();
			}else{
				$userArray = $results[0]['preferences']['shifts'];
				$tempArray = getShiftsByUserId($groupcode,$userId);
				foreach($tempArray as $tempShift){
					$name = $tempShift['name'];
					$id = $db->_id($tempShift['_id']);
					if(!in_array($id,$userArray)){
						$results[0]['preferences']['shifts'][] = $id;
						//$results[0]['preferences']['shifts'][] = array($db->_id($tempShift['_id']),"" . $tempShift['name'] . "  (" . DATE("g:i a", STRTOTIME("" . $tempShift['start'] . "")) . " - " . DATE("g:i a", STRTOTIME("" . $tempShift['end'] . "")) . ")");
						}	
				}
		
				foreach($tempArray as $nameArray){
					$ids[] = $db->_id($nameArray['_id']);	
				}
				if($userArray != null){
					foreach($userArray as $key=>$value){
						$userIdList[] = $value;
					}
					$diff =  array_diff($userIdList, $ids);
				}else{
					$diff = $ids;	
				}

				foreach($diff as $key=>$value){
					foreach($results[0]['preferences']['shifts'] as $k=>$t){
						if(in_array($t,$diff)){
							unset($results[0]['preferences']['shifts'][$k]);
						}
					}
				}			
			
				//$results[0]['preferences']['shifts'] = array_unique($results[0]['preferences']['shifts']);
				$i = 0;
				foreach($results[0]['preferences']['shifts'] as $key=>$value){
					$tempShift = getShiftById($groupcode, $value);
					unset($results[0]['preferences']['shifts'][$key]);
					$results[0]['preferences']['shifts'][$i] = array($db->_id($tempShift['_id']),"" . $tempShift['name'] . " (" . DATE("g:i a", STRTOTIME("" . $tempShift['start'] . "")) . " - " . DATE("g:i a", STRTOTIME("" . $tempShift['end'] . "")) . ")");
					$i++;
				}
			}
						$message = "success";
						$data = $results[0]['preferences'];	
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