<?php
include('cws.php');

//$arg = array('col' => "testy", 'type' => 'schedule', 'where' => array('year' => '2013'));
//	$results = $db->delete($arg);
//print_r($results);
$db = new MONGORILLA_DB;
$groupcode = "testy";
$result = getConfig($groupcode,'emailAutoSend');
print_r($result);

echo "" . date("d-m-Y H:i:s", time()) . "";
?>