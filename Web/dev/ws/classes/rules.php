<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once('' .$_SERVER['DOCUMENT_ROOT'] . '/core/mg_base.class.php');



class rules {

	private $db;
	public $groupCode;
	public static $instance = NULL;
	public $resultsArray;

	
	function __construct($gCode) {
		$initBool = $this->initializeConnection();
		
		$this->groupCode = $gCode;
		
		if($initBool) {
			$this->resultsArray = $this->pullRules();
		}
	}
	
	public static function getInstance($gCode) {
		if(!isset(self::$instance)) {
			self::$instance = new rules($gCode);
		}
		return self::$instance;
	}
	
	private function initializeConnection() {
		$this->db = new MONGORILLA_DB;
		
		if($this->db) {
			return true;
		}
		
		return false;
	}
	
	public function pullRules(){
		$arg = array('col' => $this->groupCode,'type' => 'rule', 'limit' => 1);
		$results = $this->db->find($arg);
		
		return $results;
	}
 
}

?>