<?php

require_once('' .$_SERVER['DOCUMENT_ROOT'] . '/core/mg_base.class.php');

class timeOff
{
	private $db;
	public $sessionId;
	public $groupCode;
	public $timeoff;
	
	public function __construct($groupcode){
		$this->db = new MONGORILLA_DB;	
		$this->groupcode = $groupcode;
	}
	
	public function getTimeOff(){
		$arg = array('col' => $this->groupcode, 'type' => 'currentTimeoff', 'limit' => 1);
		$result = $this->db->find($arg);
		$this->timeoff = $result[0];
	}

	public function update(){
		$sched = $this->timeoff;
		$time = mktime(1, 0, 0, $sched['month'], 15, $sched['year']);
		$monthtime = strtotime(date("Y-m-d", $time) . "+1 month");
		$sched['month'] = date('m', $monthtime);
		$sched['year'] = date('Y', $monthtime);
		$arg = array('col' => $this->groupcode, 'id'=> $this->db->_id($sched['_id']),'obj' => $sched);
		$results = $this->db->upsert($arg);
		return true;
	}
	
}
?>