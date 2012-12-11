<html>
<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set("max_execution_time", "500");

include('cws.php');
require('classes/staff.php');
require('classes/schedule.php');
//header('Content-type: application/json');
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$time_start = microtime_float();
$year = "2013";
$month = "01";
//$groupcode = "tomtest1";
$groupcode = "testy";
$args = array('col' => "$groupcode", 'type' => 'schedule', 'where' => array('month' => ''.$month.'', 'year' => ''.$year.''));
$result = $db->delete($args);


$year = 2013;
$month = 1;
$schedule = new Schedule($year,$month,$groupcode);

$schedule->getShifts();
$schedule->generate();
$schedule->commit();



$year = "2013";
$month = "01";



$where = array('month' => $month, 'year' => $year);
$arg = array('col' => $groupcode, 'type' => 'schedule', 'where' => $where);
$results = $db->find($arg);

$id = $db->_id($results[0]['_id']);
$staff = new Staff($groupcode,$id);
$staff->getSchedule();
$staff->getGroups();
$staff->getUsers();
$staff->staffSchedule();
$staff->updateSchedule();
$time_end = microtime_float();
$time = $time_end - $time_start;

echo "Took $time seconds\n";

foreach($staff->users as $user){
	echo "<br>" . $user['user_name'] . " is scheduled for " . floor($user['hours']/60);
	
}
//foreach($staff->badIds as $key=>$value){
//echo "<br /><br/>";
//echo $key;
//echo "<br /><br />";
//foreach($value as $k=>$v){
//	echo $v;
//	echo "<br />";
//}	
	
//}
//echo json_encode(array('message' => $message, 'data'=>$data));

/*
$bool = False;
$i = 0;

$hello = array();

$hello[] = "Hello";
$hello[] = "Big";
$hello[] = "World";

unset($hello[1]);
$hello = array_values($hello);

while(!$bool) {
	print_r($hello[$i]."<br>");
	if($i == 1) {
		$bool = True;
	}
	$i++;
}

print_r(count($hello));
*/
?>