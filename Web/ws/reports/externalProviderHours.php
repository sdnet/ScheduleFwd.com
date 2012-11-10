<?php
$where = array();
$where['active'] = 1;	
$arg = array('col' => "$groupcode", 'type' => 'extUser','where' => $where ,'keys' => array("org_name" => 1, "_id" => 1));
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
		$chartData[$d['org_name']] = $d['count'];
	}
	$titleKeys = array('Organization', 'Hours');
	$dataKeys = array('org_name','count');
	$bar = barIt($chartData);
	//$data = $results;
	$message = "success";
}
else{
	$message = "noRecords";	
}
		
?>