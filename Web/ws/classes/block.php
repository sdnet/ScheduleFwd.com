<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once('' . $_SERVER['DOCUMENT_ROOT'] . '/core/mg_base.class.php');
require_once('' . $_SERVER['DOCUMENT_ROOT'] . '/ws/functions.php');


class block {
	
    private $db;

	public function __construct() {
        $this->db = new MONGORILLA_DB;
    }
    
    public function numberOfDaysInBlock($day, $month, $year, $userId, $grpCode) {
	    $numDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	    //starts at 1 because that is the day that could be inserted
	    $numDaysInBlock = 1;
	    
	    $dayCheck = $day;
	    	    
	    //check backwards first	    
	    
	    $scheduledForTheDay = True;
	    while($scheduledForTheDay) {
	    
	    
	    	//get the previous day to check
		    $dayCheck = $dayCheck - 1;
		    
		    if ($dayCheck < 10) {
			    $dayCheck = '0'.$dayCheck;
		    }
		    else {
			    $dayCheck = $dayCheck.'';
		    }
	    
			$where = array('day' => $dayCheck);
			$arg = array('col' => $grpCode, 'type' => 'tempShift', 'where' => $where);
			$daysSchedule = $this->db->find($arg);
			
			$scheduledForTheDay = False;
			
			foreach ($daysSchedule as $shift) {
				$userArray = $shift['users'];
				if (!empty($userArray)) {
					foreach($userArray as $user) {
						if($user['id'] == $userId) {
							$scheduledForTheDay = True;
							$numDaysInBlock++;
						}
					}
				}
				
			}
			
			//hack remove when we allow neg numbers

			if($dayCheck == 1) {
				$scheduledForTheDay = False;
			}
	    }
	    
	    //check  forwards
	    $dayCheck = $day;   
	    
	    $scheduledForTheDay = True;
	    while($scheduledForTheDay) {
	    
	    	//get the previous day to check
		    $dayCheck = $dayCheck + 1;
		    
	    	//break at last day of month
			if ($dayCheck > $numDaysInMonth) {
				break;
			}
		    
		    if ($dayCheck < 10) {
			    $dayCheck = '0'.$dayCheck;
		    }
		    else {
			    $dayCheck = $dayCheck.'';
		    }
		    
		    $where = array('day' => $dayCheck);
			$arg = array('col' => $grpCode, 'type' => 'tempShift', 'where' => $where);
			$daysSchedule = $this->db->find($arg);
			
			
			$scheduledForTheDay = False;
			
			foreach ($daysSchedule as $shift) {
				$userArray = $shift['users'];
				if (!empty($userArray)) {
					foreach($userArray as $user) {
						if($user['id'] == $userId) {
							$scheduledForTheDay = True;
							$numDaysInBlock++;
						}
					}
				}
				
			}
			
	    }
	    
	    
	    return $numDaysInBlock;
	    
	    
    }
}

?>