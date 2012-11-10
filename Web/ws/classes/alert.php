<?php


function createAlert($userId,$role,$message,$severity,$groupcode){
	$db = new MONGORILLA_DB;
	$obj = array('role' => $role, 'userId' => $userId, 'message' => $message, 'severity' => $severity, 'active' => 1);
	$data = $db->upsert(array('col' => $groupcode, 'type' => 'alert', 'obj' => $obj ));
	if($data != null)
	{
		return true;
	}else{
		return false;
	}	
}

?>