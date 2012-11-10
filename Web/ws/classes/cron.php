<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require('' .$_SERVER['DOCUMENT_ROOT'] . '/ws/cws.php');
require('event.php');
require('archive.php');
require('schedule.php');
require('timeOff.php');
require('timeOffReminder.php');
$db = new MONGORILLA_DB;
$collectionnames = $db->list_collections_names();
$collections = array();
foreach($collectionnames as $name){
	if($name == 'mongorilla' || $name == 'notifications' || $name == 'sessions'){
		
	}else{
		$collections[] = $name;
	}	
}
foreach($collections as $groupcode){
	$event = new Event("testy");

	$event->findAll();

	foreach($event->events as $result){
		if($result['event'] == 'generate')
		{
			$month = date("m", strtotime("+2 month"));
			$year = date("Y", strtotime("+2 month"));
			$schedule = new Schedule($year,$month,$groupcode);
			$schedule->getShifts();
			$schedule->generate();
			$schedId = $schedule->commit();
			if($schedId != ""){
			$staff = new Staff($groupcode,$schedId);
			$staff->getSchedule();
			$staff->getGroups();
			$staff->getUsers();
			$staff->staffSchedule();
			$res = $staff->updateSchedule();
			if($res != ""){
				$event->updateEvent($result);		
				}
			}	
		}
		if($result['event'] == 'timeoffemail')
		{
			$timeoff = new timeOffReminder($groupcode);
			$timeoff->getTimeOff();
			
			if($timeoff->remind()){
				$event->updateEvent($result);
			}	
		}
		if($result['event'] == 'timeoff'){
			$timeoff = new timeOff($groupcode);
			$timeoff->getTimeOff();
			if($timeoff->update()){
				$event->updateEvent($result);
			}	
		}
		if($result['event'] == 'autoarchive'){
			$lastmonth = date("m", strtotime("-1 month"));
			$lastyear = date("Y", strtotime("-1 month"));
			$where = array('active' => 1, 'month' => $lastmonth, 'year' => $lastyear);
			$arg = array('col' => $groupcode, 'type' => 'schedule', 'where' => $where);
			$results = $db->find($arg);
			if($results != null){
				$obj = array('schedule' => $results[0], 'active' => 1, 'month' => $lastmonth, 'year' => $lastyear, 'time' => 'end');
				$arg = array('col' => $groupcode, 'type' => 'archive', 'obj' => $obj);
				$results = $db->upsert($arg);
				$event->updateEvent($result);	
			}
		}
		if($result['event'] == 'autopublish'){
			$autopub = getConfig($groupcode,'autoPublish');
			if($autopub == 1){
				$month = date("m", strtotime("+1 month"));
				$year = date("Y", strtotime("+1 month"));
				$where = array('published' => 0, 'month' => $month, 'year' => $year);
				$arg = array('col' => "$groupcode", 'type' => 'schedule');
				$results = $db->find($arg);
				if($results != null){
					$obj = array('published' => 1);
					$arg = array('id' => $db->_id($results[0]['_id']),'col' => "$groupcode", 'type' => 'schedule', 'obj' => $obj);
					$result = $db->upsert($arg);
					$arch = new Archive($results[0]['month'],$results[0]['year'],'start',$groupcode,$db->_id($results[0]['_id']));
					$arch->getSchedule();
					$arch->removeArchive();
					$arch->setArchive();
					$event->updateEvent($result);	
				}
			}
		}
		if($result['event'] == 'dailypdf'){
			
		}
		echo "<br />";
	}
}

?>
