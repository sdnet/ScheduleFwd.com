<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../functions.php');

require_once('' .$_SERVER['DOCUMENT_ROOT'] . '/core/mg_base.class.php');

class rulesEngine {

	public static $instance = NULL;
	
	public $userName;
	public $groupCode;
	public $userId;

	function __construct($gCode, $uName) {
		$this->userName = $uName;
		$this->groupCode = $gCode;
		$this->userId = getUserId($this->userName, $this->groupCode);
	}
	
	public static function getInstance($gCode, $uName) {
		if(!isset(self::$instance)) {
			self::$instance = new rulesEngine($gCode, $uName);
		}
		else {
			self::$instance->userName = $uName;
			self::$instance->groupCode = $gCode;
			self::$instance->userId = getUserId($this->userName, $this->groupCode);
		}
		
		
		return self::$instance;
	}
	
	public function basicRule($rule) {
	
		$ruleArray = null;
		
		foreach($rule as $key=>$value) {
			if($value['rule']) {
				$ruleArray = $value['rule'];
			}
		}
		
		$userData = getUserById($this->groupCode, $this->userId);
		
		$val1Type = $ruleArray[0]['type'];
		$oppType = $ruleArray[1]['type'];
		$val2Type = $ruleArray[2]['type'];
		
		$val1Name = $ruleArray[0]['name'];
		$oppName = $ruleArray[1] ['name'];
		$val2Name = $ruleArray[2]['value'];
		
		$vt1 = $userData[$val1Name];
		$oppT = $oppName;
		$vt2 = $userData[$val2Name];
		
		eval("\$bool = $vt1 $oppT $vt2;");
		
		if ($bool) {
			return 'true';
		}
		else {
			return 'false';
		}
	}
	
	public function hasWorkedShiftRightBefore($startTime) {
				
		if (true) {
			return 'true';
		}
		else {
			return 'false';
		}
	}
}

?>