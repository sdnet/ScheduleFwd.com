<?php
$where = array();
$where['active'] = 1;	
$arg = array('col' => "$groupcode", 'type' => 'user','where' => $where ,'keys' => array("first_name" => 1, "last_name" => 1, "group" => 1, "_id" => 1));
	$results = $db->find($arg);
	if($results != null){
		$count = array();
		foreach($results as $result)
		{
		if($month == null){
			$count = getTotalHoursByUserId($groupcode,$db->_id($result['_id']));
		}else{
			$count = getMonthHoursByUserId($groupcode,$db->_id($result['_id']),$month,$year);
		}
		unset($result['_id']);
			$result['count'] = $count;
			$data[] = $result;
		
		}
		
	$chartData = array();
	foreach($data as $d){
		$chartData[$d['last_name']] = $d['count'];
	}
	$titleKeys = array('First Name', 'Last Name', 'Group', 'Hours');
	$dataKeys = array('first_name','last_name','group','count');
	$bar = barIt($chartData);
	//$data = $results;
	$message = "success";
}
else{
	$message = "noRecords";	
}
		
?>