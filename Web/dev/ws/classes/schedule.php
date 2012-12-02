<?php

require_once('' . $_SERVER['DOCUMENT_ROOT'] . '/core/mg_base.class.php');

class Schedule {

    public $month;
    public $year;
    public $groupcode;
    private $db;
    public $shifts;
    public $schedules;
    public $newId;

    public function __construct($m, $y, $groupcode) {
        $this->db = new MONGORILLA_DB;
        $this->month = sprintf("%02s", $m);
        $this->year = sprintf("%02s", $y);
        $this->groupcode = $groupcode;
    }
    
    public function subval_sort($a,$subkey) {
	foreach($a as $k=>$v) {
		$b[$k] = strtolower($v[$subkey]);
	}
	asort($b);
	foreach($b as $key=>$val) {
		$c[] = $a[$key];
	}
	return $c;
}

    public function getShifts() {
        $arg = array('col' => $this->groupcode, 'type' => 'shift', 'where' => array('active' => 1));
        $temp = $this->db->find($arg);
        $this->shifts = $this->subval_sort($temp,'start');
    }

    public function generate() {
		$args = array('col' => $this->groupcode, 'type' => 'schedule', 'where' => array('month' => ''.$this->month.'', 'year' => ''.$this->year.''));
		$schd = $this->db->find($args);
		$scheduleId = $this->db->_id($schd[0]['_id']);
		$args = array('col' => $this->groupcode, 'type' => 'schedule', 'where' => array(), 'id' => $scheduleId);
		$result = $this->db->delete($args);
		$arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => array('scheduleId' => $scheduleId));
		$results = $this->db->delete($arg);

        $y = $this->month;
        $m = $this->year;
        $days = cal_days_in_month(CAL_GREGORIAN, $m, $y);
        $dates;
        for ($i = 1; $i <= $days; $i++) {
            $day = sprintf("%02s", $i);
            $dates[] = $day;
        }
        $scheduleDays;
        $i = 0;
        foreach ($dates as $date) {
            $tempDate = $this->month . "-" . $this->year . "-" . $date;
            $formDate = "" . $this->year . "/" . $date . "/" . $this->month . " 01:01:01";
            $weekday = date('l', strtotime($formDate));

            foreach ($this->shifts as $shift) {
                if (in_array($weekday, $shift['days'])) {
                    
                    $start = stringReplace(2, ":", $shift['start']);
                    $end = stringReplace(2, ":", $shift['end']);
                    $startdate = "$tempDate $start:00";
                    $enddate = "$tempDate $end:00";
                    if (strtotime($enddate) < strtotime($startdate)) {
                        $endtime = strtotime('+1 day', strtotime($enddate));
                        $enddate = date("Y-m-d H:i:s", $endtime);
                    }
                    $shiftId = $this->db->_id($shift['_id']);
                    $scheduleDays[] = array('id' => $i,
                        'title' => "",
                        'start' => "$tempDate $start:00",
                        'end' => "$tempDate $end:00",
                        'endreal' => "$enddate",
                        'allDay' => false,
                        'groups' => $shift['groups'],
                        'shiftName' => "" . $shift['name'] . "",
                        'users' => "",
                        'color' => $shift['color'],
                        'number' => $shift['number'],
                        'day' => "$weekday",
                        'shiftId' => $shiftId,
                        'location' => $shift['location']
                    );
                    $i++;
                }
            }
        }
        $this->schedules = $scheduleDays;
    }

    public function commit() {
        $obj = array('year' => $this->month, 'month' => $this->year, 'schedule' => $this->schedules);
        $data = $this->db->upsert(array('col' => $this->groupcode, 'type' => 'schedule', 'obj' => $obj));
        $this->newId = $this->db->_id($data);
    }

}

?>