<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include('cws.php');
require('classes/schedule.php');
header('Content-type: application/json');
$year = 2012;
$month = 12;
$groupcode = "testy";
$schedule = new Schedule($year,$month,$groupcode);

$schedule->getShifts();
$schedule->generate();
$schedule->commit();

print_r($schedule->schedules);

echo json_encode(array('message' => $message, 'data'=>$data));

?>