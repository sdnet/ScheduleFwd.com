<html>
<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set("max_execution_time", "500");

include('cws.php');
require('classes/staff.php');
require('classes/schedule.php');
//header('Content-type: application/json');

$year = 2013;
$month = 2;
$groupcode = "testy";
$args = array('col' => "$groupcode", 'type' => 'schedule', 'where' => array('month' => ''.$month.'', 'year' => ''.$year.''));
$result = $db->delete($args);

$schedule = new Schedule($year,$month,$groupcode);

$schedule->getShifts();
$schedule->generate();
$schedule->commit();



$year = "2013";
$month = "02";
$groupcode = "testy";



$where = array('month' => $month, 'year' => $year);
$arg = array('col' => $groupcode, 'type' => 'schedule', 'where' => $where);
$results = $db->find($arg);

$id = $db->_id($results[0]['_id']);
$staff = new Staff($groupcode,$id);
$staff->getSchedule();
$staff->getGroups();
$staff->getUsers();
$staff->staffSchedule();

print_r($staff->users);

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