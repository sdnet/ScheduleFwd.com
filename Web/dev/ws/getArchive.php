<?php
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
if(isset($_POST['id'])){
	$userId = $_POST['id'];
}
else {
	$userId = $_SESSION['_id'];
}

if(isset($_POST['startOrEnd'])) {
	$startOrEnd = $_POST['startOrEnd'];
}

$month = sprintf("%02s", $_POST['month']);
$year = $_POST['year'];
$isAdmin = false;

// check session
if(VerifySession($sessionId,$groupcode,$userId,'Admin') == true){
	if($groupcode == "" || $userId == null){
			$message =  "emptyFields";
	}else{	
		$where = array('month'=> $month, 'year' => $year, 'time' => $startOrEnd);
		$arg = array('col' => "$groupcode", 'type' => 'archive', 'where' => $where);
		$results = $db->find($arg);
		if($results != null){
			$complete = array();
			$i = 0;
			$rez = $results[0]['schedule'];
			foreach($rez as $result){
					$complete[] = array('published' => $result['published']);
					foreach($result['schedule'] as $key=>$sched){
						/*if($sched['users'] == null){
							$sched['color'] = '#FF3D3D; border: 1px solid #666; border-radius: 6px; color: white';
							$sched['mine'] = 0;	
						}
						
						foreach($sched['users'] as $k=>$user){
							if($user['id'] == $userId){
								$sched['color'] = '#fffc5c; border: 1px solid #666;border-radius: 6px; color: black';
								$sched['mine'] = 1;
								break;
							}
							else {
								if(getShiftTraded($groupcode,$sched['id'],$db->_id($archive[0]['_id']))) {
									$sched['color'] = '#00CC30; border: 1px solid #666;border-radius: 6px; color: black';
									
								}
								else {
									$sched['color'] = '#E8E8E8; border: 1px solid #A8A8A8; border-radius: 6px; color: black';
									$sched['mine'] = 0;	
								}	
							}
						}*/
						$userCount = 0;
						$userCount = count($sched['users']);
						$t = 0;
						if($userCount < $sched['number']){
							$sched['color'] = '#FF3D3D; border: 1px solid #666; border-radius: 6px; color: white';
							$sched['mine'] = 0;	
							$t = ($sched['number'] - $userCount);
							for($i = 1;$i <= $t;$i++) {
								$sched['users'][] = array('user_name' => 'Open', 'first_name' => "", 'last_name' => "Open", 'id' =>	"NOPE");
							}
						}
						
						$sched['title'] = "$userCount";
						$sched['year'] = $result['year'];
						$sched['month'] = $result['month'];
						$sched['scheduleId'] = $db->_id($result['_id']);
						$result['schedule'][$i] = $sched;
						$complete[] = $result['schedule'][$i];
						$i++;
					}	
				}
				foreach($complete as $key=>$value) {
					$data[] = $value;
				}
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