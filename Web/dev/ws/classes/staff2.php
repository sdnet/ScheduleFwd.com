<?php
require('rules.php');
require('rulesEngine.php');
require_once('' .$_SERVER['DOCUMENT_ROOT'] . '/core/mg_base.class.php');

class Staff
{	
	public $groupcode;
	private $db;
	private $scheduleId;
	public $schedule;
	public $users;
	public $groups;
	
	public function __construct($grp,$id){
		$this->db = new MONGORILLA_DB;
		$this->scheduleId = $id;
		$this->groupcode = $grp;
	}
	
	public function getSchedule(){
		$arg = array('col' => $this->groupcode, 'type' => 'schedule', 'id' => $this->scheduleId);
		$this->schedule = $this->db->find($arg);
	}
	
	public function getGroups(){
		$arg = array('col' => $this->groupcode, 'type' => 'group');
		$this->groups = $this->db->find($arg);
	}
	
	public function getUsers(){
		$arg = array('col' => $this->groupcode, 'type' => 'user', 'where' => array('active' => 1));
		$results = $this->db->find($arg);
		foreach($results as $result){
			$arr = array_merge(array('weight' => 1), $result);
			//if($result['min_hours'] == null && $result['max_hours'] == null){
			if(isset($result['min_hours']) && isset($result['max_hours'])) {
				foreach($this->groups as $group){
					if($group['name'] == $result['group'])
					{
						$min = $group['min_hours'];
						$max = $group['max_hours'];
					}
				}
				$arr = array_merge(array('min_hours' => $min , 'max_hours' => $max, 'hours' => 0),$arr);
			}
			$userArray[] = $arr;
		}
			
		$this->users = $userArray;
		}
	
	public function staffSchedule(){
	
	
		$newScheduleArray = array();
	
		$db = new MONGORILLA_DB;
		
		
		foreach($this->schedule as $sched)
		{
						
		foreach($sched['schedule'] as $shift) {
		
				
			$schedId = $sched['_id'];
			$gCode = $this->groupcode;
			
			$numberOfUsersIn = intVal($shift['number']);
		
			$start = $shift['start'];
			$timestamp = strtotime($start);
			$dayNum = date('N', $timestamp);
			$month = date('m', $timestamp);
			$year = date('Y', $timestamp);

		
			$dayArray = array(
							'1' => 'Monday',
							'2' => 'Tuesday',
							'3' => 'Wednesday',
							'4' => 'Thursday',
							'5' => 'Friday',
							'6' => 'Saturday',
							'7' => 'Sunday',
							);
			
			$userPreferedArray = array();
			$userNotPreferedArray = array();
				
			$theUsers = $this->users;
			foreach($theUsers as $user) {
			
				$newScheduleArray = array();
				
				$newScheduleArray = $this->schedule;
			
				$userId = $db->_id($user['_id']);
			
				//$usersShifts = getShiftsByUserFromSchedId($gCode, $schedId, $userId);
				
				
				//print_r($usersShifts);
				

				if(isset($user['preferences'])) {
					$doesPrefer = False;
					$preferenceArray = $user['preferences']['days'];
					
					$theDay = strtolower($dayArray[$dayNum]);
					
					foreach($preferenceArray as $preferedDay) {
						
						if(strtolower($preferedDay) == $theDay) {
							$doesPrefer = True;
							break;
						}
					}
					
					if($doesPrefer) {
						$userPreferedArray[] = $user;
					}
					else {
						$userNotPreferedArray[] = $user;					}
					
				}
				else {
					$userPreferedArray[] = $user;
				}
			}
						
			
					
			for($i = 0; $i < $numberOfUsersIn; $i++) {
			
				$userChecked = False;
				
				$theId = "";
			
				while ((count($userPreferedArray) > 0 || count($userNotPreferedArray)) && !$userChecked) {
					if(count($userPreferedArray) > 0) {
						$g = rand(0, count($userPreferedArray)-1);
				
						$user = $userPreferedArray[$g];
						
						unset($userPreferedArray[$g]);
						$userPreferedArray = array_values($userPreferedArray);
					}
					else if(count($userNotPreferedArray) > 0) {
							$g = rand(0, count($userNotPreferedArray)-1);
				
							$user = $userNotPreferedArray[$g];
							
							unset($userNotPreferedArray[$g]);
							$userNotPreferedArray = array_values($userNotPreferedArray);
					}
					else {
						// do nothing
					}
					
					print_r("+++++++++Begin++++++++++\n");
					
					print_r($user['user_name']);
					print_r($user);
					print_r("++++++++++End++++++++++\n");
						
					if(checkPreviousShiftLessThan($gCode, $user['user_name'], $shift['start'], $shift['end'])) {
							$userChecked = True;
					}
				}
			
				
			
				if(!$userChecked) {
					$user = array('first_name' => 'Open', 'last_name' => 'Open', 'user_name' => 'Open', 'id' => 'Open');
					
					$theId = 'Open';
				}
				else {
					$theId = $this->db->_id($user['_id']);
				}

				$shift['users'][] = array('first_name' => $user['first_name'], 'last_name' => $user['last_name'], 'user_name' => $user['user_name'], 'id' => $theId);
			}
			$newScheduleArray[] = $shift;
			
				
			$this->schedule = $newScheduleArray;
			
			$this -> updateSchedule();
		}
		}
				
	}

	public function updateSchedule(){
		$obj = array('schedule' => $this->schedule, 'active' => 1);
		$arg = array('col' => $this->groupcode, 'type' => 'schedule', 'obj' => $obj, 'id' => $this->scheduleId);
		$results = $this->db->upsert($arg);
		return $this->db->_id($results);
	}
}
?>