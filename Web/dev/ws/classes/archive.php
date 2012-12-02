<?php
class Archive{
	
	public $month;
	public $year;	
	public $groupcode;
	private $db;
	public $time;
	public $schedule;
	public $scheduleId;
	
	public function __construct($m,$y,$tme,$groupcode,$id){
		$this->db = new MONGORILLA_DB;
		$this->month = sprintf("%02s", $m);
		$this->year = sprintf("%02s", $y);
		$this->groupcode = $groupcode;
		$this->time = $tme;
		$this->scheduleId = $id;
	}
	
	public function getSchedule(){
		$arg = array('col' => $this->groupcode, 'type' => 'schedule', 'id' => $this->scheduleId);
		$this->schedule = $this->db->find($arg);
	}
	
	public function setArchive(){
		$obj = array('schedule' => $this->schedule, 'active' => 1, 'month' => $this->month, 'year' => $this->year, 'time' => $this->time);
		$arg = array('col' => $this->groupcode, 'type' => 'archive', 'obj' => $obj);
		$results = $this->db->upsert($arg);
		return $results;
	}
	
	public function removeArchive(){
		$where = array('active' => 1, 'month' => $this->month, 'year' => $this->year, 'time' => $this->time);
		$arg = array('col' => $this->groupcode, 'type' => 'archive', 'where' => $where);
		$results = $this->db->delete($arg);
		if($results != null)
		{
		return true;	
		}else{
			return false;
		}
	}
	
}


?>