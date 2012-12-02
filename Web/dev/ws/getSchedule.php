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
$month = sprintf("%02s", $_POST['month']);
$year = $_POST['year'];
$isAdmin = false;

// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	if($groupcode == "" || $userId == null) {
			$message =  "emptyFields";
	} else {
		if(getRoleById($groupcode,$userId) == 'Admin'){
			$isAdmin = true;
		}
		if($isAdmin){
			$where = array('month'=> $month, 'year' => $year);
		}else{
			$where = array('month'=> $month,'year' => $year, 'published' => 1);
		}
		$arg = array('col' => "$groupcode", 'type' => 'schedule', 'where' => $where);
		$results = $db->find($arg);
		if($results != null){
			$schedule = array();
			$i = 0;
			$scheduleId = $db->_id($results[0]['_id']);
			$where = array('scheduleId' => $scheduleId, 'status' => 'Accepted');
			$arg = array('col' => "$groupcode", 'type' => 'trade', 'where' => $where);
			$traded = $db->find($arg);
			$tradedShifts = array();
			foreach($traded as $trade){
				
				$tradedShifts[] = $trade['original_shift'];
				$tradedShifts[] = $trade['target_shift'];
			}
			array_filter($tradedShifts);
			array_unique($tradedShifts);
			
			$where = array('scheduleId' => $scheduleId);
			$arg = array('col' => "$groupcode", 'type' => 'tempShift', 'where' => $where, 'order' => 'asc', 'order_by' => 'id');
			$shifts = $db->find($arg);
			foreach($shifts as $shift){
				
				$shift['traded'] = 0;
				if($isAdmin == true){
					
					if($shift['users'] == null){
						$shift['color'] = '#FF3D3D; border: 1px solid #666; border-radius: 6px; color: white';
						$shift['mine'] = 0;	
						$shift['traded'] = 0;
					}
					
					$userCount = 0;
					$userCount = count($shift['users']);
					
					if(in_array($userId,$shift['users'])){
						$shift['color'] = '#fffc5c; border: 1px solid #666;border-radius: 6px; color: black';
						$shift['mine'] = 1;
						break;
					}else{
						if(in_array($shift['id'], $tradedShifts)){
							$shift['color'] = '#00CC30; border: 1px solid #666;border-radius: 6px; color: black';
							$shift['traded'] = '1';
						}else{
							$shift['color'] = '#E8E8E8; border: 1px solid #A8A8A8; border-radius: 6px; color: black';
							$shift['mine'] = 0;	
						}
					}
					
					
					$t = 0;
					if($userCount < $shift['number']){
						$shift['color'] = '#FF3D3D; border: 1px solid #666; border-radius: 6px; color: white';
						$shift['mine'] = 0;	
						$t = ($shift['number'] - $userCount);
						for($i = 1;$i <= $t;$i++){
							$shift['users'][] = array('user_name' => 'Open', 'first_name' => "", 'last_name' => "Open", 'id' =>	"NOPE");
						}
						}
						
					}else{
						$shift['traded'] = 0;
						$shift['color'] = '#E8E8E8; border: 1px solid #A8A8A8; border-radius: 6px; color: black';
						$shift['mine'] = 0;	
						if(in_array($userId,$shift['users'])){
							$shift['color'] = '#fffc5c; border: 1px solid #666;border-radius: 6px; color: black';
							$shift['mine'] = 1;
							break;
						}
					}
					
				$shift['year'] = $year;
				$shift['month'] = $month;
				$shift['scheduleId'] = $scheduleId;
					$shift['title'] = "$userCount";
				$shift['start'] = date('Y-m-d H:i:s',$shift['start']->sec);
				$shift['endreal'] = date('Y-m-d H:i:s',$shift['endreal']->sec);
				unset($shift['timeoffs']);
					$schedule[] = $shift;
					$i++;
				}	
			$data = $schedule;
			$message = "success";
		
		}else{
			$message = "noRecords";	
		}
	}				
}else{
	//return auth failure
	$message = "authFailure";	
}

echo json_encode(array('message' => $message, 'data'=>$data));
?>