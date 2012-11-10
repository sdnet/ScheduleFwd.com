<?php

require_once('' .$_SERVER['DOCUMENT_ROOT'] . '/core/mg_base.class.php');

class timeOffReminder
{
	private $db;
	public $sessionId;
	public $groupCode;
	public $timeoffCurrent;
	public $timeoffReminder;
	public $timeoff;
	public $daydiff;
	public $users;
	public $usersRequested;
	
	public function __construct($groupcode){
		$this->db = new MONGORILLA_DB;	
		$this->groupcode = $groupcode;
	}
	
	public function getTimeOff(){
		$arg = array('col' => $this->groupcode, 'type' => 'currentTimeoff', 'limit' => 1);
		$result = $this->db->find($arg);
		$this->timeoffCurrent = $result[0];
		
		$arg = array('col' => $this->groupcode, 'type' => 'event', 'limit' => 1, 'where' => array('event' => 'timeoffemail'));
		$result = $this->db->find($arg);
		$this->timeoffReminder = $result[0];
		
		$arg = array('col' => $this->groupcode, 'type' => 'event', 'limit' => 1, 'where' => array('event' => 'timeoff'));
		$result = $this->db->find($arg);
		$this->timeoff = $result[0];
	}

	public function shouldRemind(){
		$remind = $this->timeoffReminder;
		$sched = $this->timeoffCurrent;
		$timeo = $this->timeoff;
		$time = mktime(1, 0, 0, date('m'), $timeo['day'], date('Y'));
		$daybefore = date('Y-m-d', strtotime(date("Y-m-d", $time) . "+3 days"));
		$dayof = date("Y-m-d", $time);
		$now = date('Y-m-d');
		$daydiff = strtotime($dayof) - time();
		$this->daydiff = floor($daydiff/(60*60*24));
		if($daybefore <= strtotime($now) && strtotime($now) <= strtotime($dayof)){
			return true;
		}else{
			return false;
			}
	}
	
	private function sendReminder($to, $subject, $message){
		
		$headers = "MIME-Version: 1.0\r\n"
			."Content-Type: text/plain; charset=utf-8\r\n"
			."Content-Transfer-Encoding: 8bit\r\n"
			. "From: Schedule Forward <support@scheduleforward.com>\r\nReply-To: Schedule Forward <support@scheduleforward.com>\r\n"
			."X-Mailer: PHP/". phpversion();
		$head = "********* This is an automated message from Schedule Forward, please do not reply **********\r\n
		
";
		$foot = "

--------
Thank you,
The Schedule Forward Team";
		$message = $head . $message . $foot;
		$success = mail($to, $subject, $message, $headers);
		if($success)
		{
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getUsers(){
		$arg = array('col' => $this->groupcode, 'type' => 'user', 'where' => array('active' => 1, 'group' => array('$ne' => 'Staff')));
		$results = $this->db->find($arg);
		$this->users = $results;
	}
	
	public function getUserRequested(){
		$sched = $this->timeoffCurrent;
		$month = $sched['month'];
		$arg = array('col' => $this->groupcode, 'type' => 'timeoff', 'distinct' => 'userId', 'where' => array('month' => $month, 'year' => $sched['year']));
		$results = $this->db->find($arg);
		$this->usersRequested = $results;
	}
	
	public function remind(){
		if($this->shouldRemind()){
			$this->getUsers();
			$this->getUserRequested();
			$timeo = $this->timeoff;
			$sched = $this->timeoffCurrent;
			$time = mktime(1, 0, 0, $sched['month'], $timeo['day'], $sched['year']);
			$timeoff1 = getConfig($groupcode, 'timeoffReminder');
			$timeoff2 = getConfig($groupcode, 'timeoffReminder2');
			$timeoff3 = getConfig($groupcode, 'timeoffReminder3');
			$emailList = array();
			if($this->daydiff == 0){
				$subject = "Time Off Requests DUE TODAY";
				$message = "You must get all of your time off requests in today to get them in for the next schedule in " . date('F',$time) . ".

Please either go to http://www.schedulefwd.com or request a time off sheet from your admin.";
				foreach($this->users as $user){	
					$emailList[] = $user['email'];	
				}
			}
			if($this->daydiff == 1 && $timeoff1 == 1){
				$subject = "Time Off Request Reminder - 1 day left!";
				$message = "You have one day remaining to get your time off requests in for " . date('F',$time) . ".

Please either go to http://www.schedulefwd.com or request a time off sheet from your admin.";
				foreach($this->users as $user){	
					$emailList[] = $user['email'];	
				}
			}
			if($this->daydiff == 2 && $timeoff2 == 1){
				$subject = "Time Off Request Reminder - 2 days left!";
$message = "You have two days (48 hours) remaining to get your time off requests in for " . date('F',$time) . ". 
				
Please either go to http://www.schedulefwd.com or request a time off sheet from your admin.";
				foreach($this->users as $user){
					$id = $this->db->_id($user['_id']);
					if(!in_array($id, $this->usersRequested)){
						$emailList[] = $user['email'];	
					}	
				}
			}
			if($this->daydiff == 3 && $timeoff3 == 1){
				$subject = "Time Off Request Reminder - 3 days left!";
				$message = "You have three days (72 hours) remaining to get your time off requests in for " . date('F',$time) . ". 
				
Please either go to http://www.schedulefwd.com or request a time off sheet from your admin.";
				foreach($this->users as $user){
					$id = $this->db->_id($user['_id']);
					if(!in_array($id, $this->usersRequested)){
						$emailList[] = $user['email'];	
					}	
				}
			}
			foreach($emailList as $email){
				$success = $this->sendReminder($email, $subject, $message);	
				if($success){
				 echo "mail sent to $email </br>";	
				}else{
				echo "mail failed to send to $email<br />";	
				}
			}
			return true;
		}else{
			return false;
		}
	}
}
?>