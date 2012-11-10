<?php
include('cws.php');
header('Content-type: application/json');
// check session

	$where = array();
	$where['active'] = 1;

			//	$where = array('email' => "$email", 'user_name' => "$username", 'first_name' => "$firstName", 'last_name' => "$lastName", 'group' => "$group", 'role' => "$role");
$arg = array('type' => 'user', 'order' => 'desc', 'order_by' => 'natural', 'keys' => array("groupcode" => 1, "name" => 1, "_id" => 0));
	$results = $db->find($arg);
				if($results != null){
				$data = $results;
				$message = "success";
				}
				else{
					$message = "noRecords";	
				}
				

if($format == 'dt'){
	$data = dtFormat($data);
}
else{

}
echo json_encode(array('message' => $message, 'data'=>$data));
?>