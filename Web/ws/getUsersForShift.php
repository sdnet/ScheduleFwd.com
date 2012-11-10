<?php

include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$shiftId = $_POST['shiftId'];
$scheduleId = $_POST['scheduleId'];
$groupcode = $_POST['grpcode'];
// check session
if(VerifySession($sessionId,$groupcode,$_SESSION['_id'],'Admin') == true){
	$where = array();
	$where['active'] = 1;
	$where['group'] = array('$ne' => 'Staff'); 
	$ss = getShiftFromSchedule($groupcode,$shiftId,$scheduleId);
	if($ss['users'] != null){
		foreach($ss['users'] as $u){
			$currUsers[] = $u['user_name'];
		}	
		$where['user_name'] = array('$nin' => $currUsers);
	}
	$arg = array('col' => "$groupcode", 'type' => 'user', 'order' => 'asc', 'order_by' => 'last_name', 'where' => $where, 'keys' => array("first_name" => 1, "last_name" => 1, "user_name"=> 1, "max_hours" => 1, "min_hours" => 1,"_id" => 1, "group" => 1));
	$results = $db->find($arg);
				if($results != null){
		$userArray = array();
		$avail = array();
		$other = array();
				$shift = getShiftFromSchedule($groupcode,$shiftId,$scheduleId);	
				$t1 = new DateTime($shift['start']);
		$t2 = new DateTime($shift['endreal']);
				$t3 = date_diff($t1,$t2);
				$duration = $hours + $t3->h;
		foreach($results as $user){
			$maxhours = getUserMaxHours($groupcode,$db->_id($user['_id']));
			$userobj = null;
			$hours = 0;
			$hours = getHoursByUserId($groupcode,$db->_id($user['_id']),$scheduleId);
			$fname = substr($user['first_name'],0,1);
			$difference = ($maxhours - $hours);
			$difference2 = ($difference - $duration);
			if($difference2 < 0){
				$difference2 = "<span style=\"color: red;\">" . $difference2 . "</span>";	
			}
			$display = "" . $user['last_name'] . " " . $fname . ". : " . $difference  . " (" . $difference2 . ")";
			$userobj = array('first_name' => $user['first_name'], 'id' => $db->_id($user['_id']), 'last_name' => $user['last_name'], 'user_name' => $user['user_name'], 'display' => "$display", 'group' => $user['group']);
			$canwork = getUserCanWork($groupcode,$db->_id($user['_id']),$shift['start'],$shift['endreal']);
			if($canwork == true){
				if(in_array($user['group'],$shift['groups']) && ($difference2 <= $maxhours) && ($difference2 > 0)){
					$avail[] = $userobj;	
				}else{
					if(in_array('Attending',$shift['groups'])){
						if($user['group'] == 'Attending'){
							$other[] = $userobj;
						}
					}else{
						$other[] = $userobj;
					}	
				}
			}
		}	
		$arg = array('col' => "$groupcode", 'type' => 'extUser', 'order' => 'asc', 'order_by' => 'org_name', 'where' => $where, 'keys' => array("_id" => 1, "org_name" => 1));
		$extTemp = $db->find($arg);
		$external = array();
		foreach($extTemp as $ext){
			$external[] = array('display' => $ext['org_name'], 'id' => $db->_id($ext['_id']));
		}
		
		$othergroup = array();
		foreach($other as $prov){
			$grp = $prov['group'];
			$othergroup[$grp][] = $prov;
		}
		
		$data = array('available' => $avail, 'allusers' => $othergroup, 'external' => $external);
				$message = "success";
				}
				else{
					$message = "noRecords";	
				}
				
}else{
	//return auth failure
	$message = "authFailure";	
}


echo json_encode(array('message' => $message, 'data'=>$data));
?>