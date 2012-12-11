<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once('' . $_SERVER['DOCUMENT_ROOT'] . '/core/mg_base.class.php');
require_once('' . $_SERVER['DOCUMENT_ROOT'] . '/ws/functions.php');

class nightPreferencesCheck {
	
	
	private $db;

	public function __construct() {
        $this->db = new MONGORILLA_DB;
    }
    
    public function prefWorkDayAfterNight($shiftToCheck, $user) {
	    $userPref = $user['preferences']['afterNightShift'];
	    
	    $hourDiff = 0;
        if ($userPref == "Wed7am") {
            $hourDiff = 24;
        } else if ($userPref == "Wed12pm") {
            $hourDiff = 31;
        } else if ($userPref == "Wed7pm") {
            $hourDiff = 36;
        } else if ($userPref == "Thurs7am") {
            $hourDiff = 48;
        }
        
        
        
    }
}

?>