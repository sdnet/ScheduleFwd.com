<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require('' .$_SERVER['DOCUMENT_ROOT'] . '/ws/cws.php');

class Event{
	public $db;
	public $month;
	public $year;
	public $day;
	public $groupcode;
	public $time;
	public $events;
	
	public function __construct($grpcode){
		$this->db = new MONGORILLA_DB;
		$this->groupcode = $grpcode;
		$this->time = date("Y-m-d H:i");
	}
	
	
	public function findAll(){
		$where = array('active' => 1);
		$arg = array('col' => $this->groupcode, 'type' => 'event', 'where' => $where);
		$results = $this->db->find($arg);
		if($results != null)
		{	$total = array();
			$month = date('m');
			$year = date('Y');
			$nowtime = date('Y-m-d h:i:s');
			foreach($results as $result){
				$day = $result['day'];
				if($day == 'current'){
				  $day = date('d');	
				}
				$hour = $result['hour'];
				if($hour == 'current'){
					$hour = date('h');	
				}
				$runtime = date('Y-m-d h:i:s', mktime($hour, $result['minute'], 0, $month, $day, $year));
				$lastdate = date('Y-m-d h:i:s', $result['last_run']->sec); 
				
				if(strtotime($runtime) <= strtotime($nowtime) && strtotime($lastdate) <= strtotime($runtime))
				{
					$total[] = $result;
				}
			}
			$this->events = $total;
			return true;	
		}else{
			return false;
		}
	}
	
	public function updateEvent($event){
		$now = date('Y-m-d h:i:s');
		$event['last_run'] =  new MongoDate(strtotime($now));
		$arg = array('id' => $this->db->_id($event['_id']),'col' => $this->groupcode, 'type' => 'event', 'obj' => $event);
		$results = $this->db->upsert($arg);
		return true;
		
	}
	
}

?>