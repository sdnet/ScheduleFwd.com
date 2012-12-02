<?php
$where = array();
$where['active'] = 1;	
$arg = array('col' => "$groupcode", 'type' => 'group', 'where' => $where, 'keys' => array("name" => 1, "_id" => 0));
	$results = $db->find($arg);
	if($results != null){
		$count = array();
		foreach($results as $result)
		{
		$args = array('col' => "$groupcode", 'type' => 'user', 'where' => array('active' => 1, 'group' => $result['name']));
			$count = $db->count($args);
			$result['count'] = $count;
			$data[] = $result;
		}
		
	$chartData = array();
	foreach($data as $d){
		$chartData[$d['name']] = $d['count'];
	}
	$titleKeys = array('Group Name', '# of People');
	$dataKeys = array('name', 'count');
	$pie = pieIt($chartData);
	//$data = $results;
	$message = "success";
}
else{
	$message = "noRecords";	
}
		
?>