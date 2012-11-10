<?php
require('hash.php');
require_once('' . $_SERVER['DOCUMENT_ROOT'] . '/core/mg_base.class.php');
class Setup{
	

	public $groupcode;
	private $db;
	public $progress;
	
	public function __construct($groupcode){
		$this->db = new MONGORILLA_DB;
		$this->groupcode = $groupcode;
		$this->progress = 'Start';
	}
	
	public function setEvents(){
		//schedule generate event
		$obj = array('day' => '16','active' => 1,'date_created' => new MongoDate(1348683339, 790000),'event' => 'generate','hour' => '01','last_run' => new MongoDate(1348813343, 0),'minute' => '00','month' => 'current','name' => 'Generate Schedule','year' => 'current');
		$arg = array('col' => $this->groupcode, 'type' => 'event', 'obj' => $obj);
		$results = $this->db->upsert($arg);
		//auto publish event
		$obj = array('active' => new MongoInt64(1),'date_created' => new MongoDate(1348683339, 790000),'day' => '01','docType' => 'event','event' => 'autopublish','hour' => '01','last_run' => new MongoDate(1348813343, 0),'minute' => '00','month' => 'current','name' => 'Auto Publish of Schedule','year' => 'current');
		$arg = array('col' => $this->groupcode, 'type' => 'event', 'obj' => $obj);
		$results = $this->db->upsert($arg);
		
		//auto archive event
		$obj = array('active' => new MongoInt64(1),'date_created' => new MongoDate(1348683339, 790000),'day' => '01','event' => 'autoarchive','hour' => '01','last_run' => new MongoDate(1348813343, 0),'minute' => '00','month' => 'current','name' => 'Auto Archive of Schedule','year' => 'current');
		$arg = array('col' => $this->groupcode, 'type' => 'event', 'obj' => $obj);
		$results = $this->db->upsert($arg);
		
		//email reminder event
		$obj = array('active' => 1,'date_created' => new MongoDate(1348683339, 790000),'day' => 'current','event' => 'timeoffemail','hour' => '01','last_run' => new MongoDate(1348813343, 0),'minute' => '00','month' => 'current','name' => 'Reminder Email','year' => 'current');
		$arg = array('col' => $this->groupcode, 'type' => 'event', 'obj' => $obj);
		$results = $this->db->upsert($arg);
		
		//Time off Cut Off event
		$obj = array('day' => '15','active' => 1,'date_created' => new MongoDate(1348683339, 790000),'event' => 'timeoff','hour' => '23','last_run' => new MongoDate(1348802273, 0),'minute' => '00','month' => 'current','name' => 'Time Off Cutoff','year' => 'current');
		$arg = array('col' => $this->groupcode, 'type' => 'event', 'obj' => $obj);
		$results = $this->db->upsert($arg);	
	}
	
	public function setRoles(){
		//User
		$obj = array('active' => new MongoInt64(1),'description' => 'Regular user of the system, they only have access to their own shifts.','name' => 'User');
		$arg = array('col' => $this->groupcode, 'type' => 'role', 'obj' => $obj);
		$results = $this->db->upsert($arg);	
		//Admin
		$obj = array( 'active' => new MongoInt64(1),'description' => 'Administrators of the system. They have complete access.','name' => 'Admin');
		$arg = array('col' => $this->groupcode, 'type' => 'role', 'obj' => $obj);
		$results = $this->db->upsert($arg);	
	}
	
	public function setGroups(){
		//Staff role
		$obj = array('name' => 'Staff','active' => new MongoInt32(1),'max_hours' => '0','min_hours' => '0','description' => 'General staff accounts','date_created' => new MongoDate(1347072146, 941000));
		$arg = array('col' => $this->groupcode, 'type' => 'group', 'obj' => $obj);
		$results = $this->db->upsert($arg);	
		
		//Midlevel role
		$obj = array('name' => 'Midlevel','active' => new MongoInt32(1),'max_hours' => '133','min_hours' => '121','description' => 'This group is for full time midlevels at this hospital.','date_created' => new MongoDate(1346035543, 406000));
		$arg = array('col' => $this->groupcode, 'type' => 'group', 'obj' => $obj);
		$results = $this->db->upsert($arg);	
		
		//Attending role
		$obj = array('active' => new MongoInt32(1),'description' => 'This group is for full time attendings at this hospital.','max_hours' => '121','min_hours' => '109','name' => 'Attending');
		$arg = array('col' => $this->groupcode, 'type' => 'group', 'obj' => $obj);
		$results = $this->db->upsert($arg);	
	}
	
		public function setConfig(){
		//Default configs
		$obj = array('dayOfWeekStart' => 'Sunday','timezone' => '-8.0','emailAutoSend' => 'true','timeoffReminder3' => 'true','timeoffReminder2' => 'false','timeoffReminder' => 'true','emailOptIn' => 'true','autoPublish' => 'false','timeoffDeadline' => '29','scheduleGenerate' => '28','circadian' => 'false','overrideCircadian' => 'true','minHoursBetweenShifts' => '12','maxConsecDay' => '3','maxConsecNight' => '3','maxNightsPerMonth' => '3','maxConsecWorkingDays' => '3','attendingsLowerLevel' => 'true','weekendShifts' => 'false','last_updated' => new MongoDate(1349142386, 939000),'_id' => new MongoId("5066957869e95eb75b8c6981"));
		$arg = array('col' => $this->groupcode, 'type' => 'config', 'obj' => $obj);
		$results = $this->db->upsert($arg);		
		}
		
		public function setUsers(){
		//Default admin user
		$obj = array('user_name' => 'scribe','active' => new MongoInt32(1),'email' => 'staff@hospital.com','first_name' => 'Staff','last_name' => 'Scribe','phone' => '(234) 222-3999','group' => 'Staff','role' => 'Scribe','date_created' => new MongoDate(1347507023, 718000),'password' => '' . create_hash($password) . '');
		$arg = array('col' => $this->groupcode, 'type' => 'user', 'obj' => $obj);
		$results = $this->db->upsert($arg);	
		
		//Default User user
		$obj = array('user_name' => 'admin','active' => new MongoInt32(1),'email' => 'admin@email.com','first_name' => 'Schedule','last_name' => 'Admin','phone' => '','password' => '' . create_hash($password) . '','priority' => new MongoInt32(1),'group' => 'Staff','role' => 'Admin','date_created' => new MongoDate(1348706445, 68000));
		$arg = array('col' => $this->groupcode, 'type' => 'user', 'obj' => $obj);
		$results = $this->db->upsert($arg);	
			
		}
}


?>