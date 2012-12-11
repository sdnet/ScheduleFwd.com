<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//require('rules.php');
//require('rulesEngine.php');
require_once('' . $_SERVER['DOCUMENT_ROOT'] . '/core/mg_base.class.php');
require_once('' . $_SERVER['DOCUMENT_ROOT'] . '/ws/functions.php');
ini_set("max_execution_time", "9000");

class Staff {

    public $groupcode;
    private $db;
    public $scheduleId;
    public $schedule;
    public $users;
    public $groups;
    public $year;
    public $month;
    public $scheduleDays;
    public $openShifts = 0;
    public $badIds = array();
    public $finish = 0;

    public function __construct($grp, $id) {
        $this->db = new MONGORILLA_DB;
        $this->scheduleId = $id;
        $this->groupcode = $grp;
        $this->users = array();
    }

    public function getSchedule() {
        $final = array();
        $this->prevShifts = array();
        $arg = array('col' => $this->groupcode, 'type' => 'schedule', 'id' => $this->scheduleId);
        $result = $this->db->find($arg);
        $schedule = $result[0]['schedule'];
        $this->year = $result[0]['year'];
        $this->month = $result[0]['month'];
        $lastday = date("t", strtotime($this->month . "/01/" . $this->year . " -1 month"));
        $lastmonth = date("m", strtotime($this->month . "/01/" . $this->year . " -1 month"));
        $lastyear = date("m", strtotime($this->month . "/01/" . $this->year . " -1 month"));

        $where = array('month' => $lastmonth, 'year' => $lastyear);
        $arg = array('col' => $this->groupcode, 'type' => 'schedule', 'where' => $where);
        $lresult = $this->db->find($arg);
        if ($lresult != null) {
            foreach ($lresult[0]['schedule'] as $shift) {
                if ($lastday == date("d", strtotime($shift['start']))) {
                    $shift['day'] = '00';
                    $final[$key] = $shift;
                    $shift['start'] = new MongoDate(strtotime($shift['start']));
                    $shift['endreal'] = new MongoDate(strtotime($shift['endreal']));
                    $shift['number'] = (int) $shift['number'];
                    $shift['scheduleId'] = $this->scheduleId;
                    $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'obj' => $shift);
                    $results = $this->db->upsert($arg);
                }
            }
        }
        $key = 0;
        foreach ($schedule as $t => $shift) {
            $key = $shift['id'];
//            $t1 = new DateTime($shift['start']);
//            $t2 = new DateTime($shift['endreal']);
//            $t3 = $t1->diff($t2);
//            $duration = $t3->h;
            $t1 = strtotime($shift['start']);
            $t2 = strtotime($shift['endreal']);
            $t3 = $t2 - $t1;
            $duration = floor($t3 / 60);
            $shift['duration'] = $duration;
            $shift['day'] = date('d', strtotime($shift['start']));
            $final[$key] = $shift;
            $start = $shift['start'];
            $end = $shift['end'];

            $shift['dayName'] = date('l', strtotime($shift['start']));
            if (isWeekend($start)) {
                $weekend = 1;
            } else {
                $weekend = 0;
            }
            $shift['weekend'] = $weekend;
            if (isNight($start, $end)) {
                $night = 1;
            } else {
                $night = 0;
            } 
            $shift['night'] = $night;
            $shift['start'] = new MongoDate(strtotime($shift['start']));
            $shift['endreal'] = new MongoDate(strtotime($shift['endreal']));
            $shift['timeoffs'] = array();
            $shift['number'] = (int) $shift['number'];
            $shift['scheduleId'] = $this->scheduleId;
            $where = array('time_off' => array('' . date('Y-m-d', strtotime($start)) . '' => $shift['shiftId']), 'year' => '' . $this->year . '', 'month' => '' . $this->month . '', 'status' => array('$ne' => 'Disapproved'));
            $arg = array('col' => $this->groupcode, 'type' => 'timeoff', 'where' => $where);
            $tresults = $this->db->find($arg);
            if ($tresults != null) {
                foreach ($tresults as $timeoff) {
                    if (isset($timeoff['mustwork']) && ($timeoff['mustwork'] == true)) {
                        $shift['mustwork'] = array();

                        $shift['mustwork'][] = $timeoff['userId'];
                    } else {
                        $shift['timeoffs'][] = $timeoff['userId'];
                    }
                }
            }
            $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'obj' => $shift);
            $results = $this->db->upsert($arg);
        }
        $this->openShifts = $key;
        $this->schedule = $final;
        $this->year = $result[0]['year'];
        $this->month = $result[0]['month'];
        //$args = array('col' => $this->groupcode, 'key' => array('id' => 1));
        //$this->db->createIndex($args);
    }

    public function getGroups() {
        $arg = array('col' => $this->groupcode, 'type' => 'group');
        $this->groups = $this->db->find($arg);
    }

    public function getUsers() {
        $nightReq = getConfig($this->groupcode, 'maxNightsPerMonth');
        $arg = array('col' => $this->groupcode, 'type' => 'user', 'where' => array('scheduleProvider' => array('$ne' => 'No'), 'active' => 1, 'group' => array('$ne' => 'Staff')));
        $results = $this->db->find($arg);
        $userList = array();
        foreach ($results as $result) {
            $userId = $this->db->_id($result['_id']);
            $this->badIds[$userId] = array();
            $username = $result['user_name'];
            $timeoffrequests = getTimeOffByUserId($this->groupcode, $userId, $this->month, $this->year);
            $tokens = 0;
            $timeoffs = array();
            $timeCount = 0;
            if ($timeoffrequests != null) {

                foreach ($timeoffrequests as $key => $timeoff) {
                    $timeCount++;
                    foreach ($timeoff['time_off'] as $k => $v) {
                        $timeoffs[] = array('granted' => 0, 'date' => $k, 'shiftId' => $v);
                    }
                    if (isset($timeoff['priority'])) {
                        if ($timeoff['priority'] == '1') {
                            $tokens++;
                        }
                    }
                    $tokens++;
                }
            }
//$nightcount = getNightCountForUser($this->groupcode, $userId, $this->month, $this->year);
//$weekendcount = getWeekendCountForUser($this->groupcode, $userId, $this->month, $this->year);
            $nightcount = 2;
            $weekendcount = 2;
            $min = null;
            $max = null;
            if (isset($result['min_hours'])) {
                $min = $result['min_hours'];
            }
            if (isset($result['max_hours'])) {
                $max = $result['max_hours'];
            }
            if ($min == null || $max == null) {
                foreach ($this->groups as $group) {
                    if ($group['name'] == $result['group']) {
                        $min = $group['min_hours'];
                        $max = $group['max_hours'];
                    }
                }
            }
            $requestedShifts = getRequestedShiftsByUserId($this->groupcode, $userId, $this->month, $this->year);
            if($requestedShifts == null){
                $requestedShifts = $min/12;
            }
            $arr = array_merge(array('min_hours' => $min * 60, 'requested_shifts' => $requestedShifts, 'max_hours' => $max * 60, 'current' => 0, 'available' => ($this->openShifts - $timeCount), 'diff' => 0, 'hours' => 0, 'bad' => array(), 'night_hours' => (((int) $nightReq) * 60), 'tokens' => $tokens, 'timeoffs' => $timeoffs, 'nightcount' => $nightcount, 'weekendcount' => $weekendcount), $result);
            $userList[] = $arr;
        }
        $this->users = $userList;
    }

    /*
      Pseudo code for new functionized staffing capability

      For each shift
      Get shift properties
      If user is not currently in shift

     */

    private function lessThanMaxGroupHours($group, $user) {
        if ($group >= $user['hours']) {
            
        }
    }

    private function getDateFromFullDateString($fullDateString) {
        $retDate = explode(" ", $fullDateString);
        if (count($retDate) > 0) {
            $retDate = $retDate[0];
        } else {
            $retDate = $fullDateString;
        }
        return $retDate;
    }

    private function timeOffExistsForUser($user, $scheduleShift) {
        $ret = false;
        $timeOffs = $user['timeoffs'];
        $shiftId = $scheduleShift['shiftId'];
        $date = date('Y-m-d', strtotime($scheduleShift['start']));
        if ($timeOffs) {
            foreach ($timeOffs as $timeoff) {
                if (($date == $timeoff['date']) && ($timeoff['shiftId'] == $shiftId)) {
                    $ret = true;
                    break;
                }
            }
        }

        return $ret;
    }

    private function isCircadianMet($user, $shift) {
        if ($user['preferences']['circadian'] == '1') {

            $start = new MongoId(strtotime($shift['start'] . ' - 1 day'));
            $end = new MongoId(strtotime($shift['endreal'] . ' + 1 day'));
            $where = array('users.id' => array('$in' => array($this->db->_id($user))), '$or' => array('start' => array('$gt' => $start, '$lt' => $end), 'endreal' => array('$lt' => $start, '$gt' => $end)));
            $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where);
            $results = $this->db->find($arg);

            if (is_array($results)) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    private function isShiftDayOrNight($scheduleShift) {
        $retDayOrNight = "Day";
        $start = $scheduleShift['start'];
        $end = $scheduleShift['endreal'];

// Only change the value of the $retDayOrNight variable if a night shift (from functions.php)
        if (isNight($start, $end) == true) {
            $retDayOrNight = "Night";
        }

        return $retDayOrNight;
    }

    private function isShiftWeekendOrWeekday($scheduleShift) {
        $retWeekdayOrWeekend = "Weekday";
        $start = $scheduleShift['start'];

// Only change the value of the $retDayOrNight variable if a night shift (from functions.php)
        if (isWeekend($start) == true) {
            $retWeekdayOrWeekend = "Weekend";
        }

        return $retWeekdayOrWeekend;
    }

    private function isUserUnderMin($user) {
        $ret = false;
        $userId = $this->db->_id($user['_id']);
        $hours = $user['hours'];
        $userMinHours = $user['min_hours'];
        //$userMinHours = getUserMinHours($this->groupcode, $userId);

        if ($hours < $userMinHours) {
            $ret = true;
        }

        return $ret;
    }

    private function getUserHours($userId) {
        foreach ($this->users as $user) {
            if ($this->db->_id($user['_id']) == $userId) {
                return $user['hours'];
            }
        }
        return false;
    }

    private function isUserOverMax($user, $shift) {
        $ret = false;
        $userId = $this->db->_id($user['_id']);
        $us = $this->getUserHours($userId);
        $hours = $us['hours'] + $shift['duration'];
        // $userMaxHours = getUserMaxHours($this->groupcode, $userId);

        if ($hours > $user['max_hours']) {
            $ret = true;
        }

        return $ret;
    }

    private function getNextShiftByDay($shift) {
        return getNextDayShift($this->groupcode, $shift['start'], $shift['shiftId'], $this->schedule);
    }

    private function getNextAvailableShift($shift) {
        $shiftId = $shift['id'];
        return getNextAvailableShift($this->schedule, $shiftId);
    }

    private function getFirstAvailableShift($user, $shiftId, $offset = 0, $weekend = false) {
        return getFirstAvailableShift($user, $this->schedule, $shiftId, $offset, $weekend);
    }

    private function isPreferredDay($user, $date) {
        $dayarray = $user['preferences']['days'];
        $day = date('l', strtotime($date));
        return in_array($day, $dayarray);
    }

    /*
      Name: isPreferredShiftAfterNight
      Author: Steve A

      Description: Calculates the difference between the end of the night shift
      and the start of the would-be next shift and validates the
      calculated number against the user's preference

      @params	$user			The user object
      @params	$nightShift		The night shift (end time)
      @params	$nextShift		The would-be next shift (start time)
     */

    private function isPreferredShiftAfterNight($user, $nightShift, $nextShift) {
        $ret = false;

// Get user's current preferences (values: Wed7am, Wed12pm, Wed7pm, Thurs7am)
        $userPref = $user['preferences']['afterNightShift'];

// Get the night shift's ending time
        $nightEndTime = new DateTime($nightShift['endreal']);

// Get the would-be next shift's start time
        $nextStartTime = new DateTime($nextShift['start']);

// Calculate the hour difference of the user's preference
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

// Find the difference between the two dates and calculate the difference
        $hoursBetween = date_diff($nightEndTime, $nextStartTime);
        if ($hourDiff >= $hoursBetween) {
            $ret = true;
        }

        return $ret;
    }

    private function isShiftBlockable($user, $shift, $night = false) {
        $blockable = false;
        if (isWeekend($shift['start']) && $night == false) {
            if ($user['preferences']['block_weekend'] == 1) {
                if (date('w', strtotime($shift['start'])) == '0') {
                    $blockable = 1;
                } else {
                    $blockable = 2;
                }
            } else {
                $blockable = 1;
            }
        } else {
            if ($user['preferences']['block_days'] == '1') {
                if (isNight($shift['start'], $shift['endreal'])) {
                    $blockable = $user['preferences']['desired_nights'];
                } else {
                    $blockable = $user['preferences']['desired_days'];
                }
            }
        }
        return $blockable;
    }

    private function getUsersByShiftPreference($shift, $offset = 0) {
        $users = array();
        $shiftId = $shift['shiftId'];
        foreach ($this->users as $user) {
            $key = array_search($shiftId, $user['preferences']['shifts']);
            $users[$this->db->_id($user['id'])] = $key;
        }
        sort($users);
        return $users;
    }

    private function getShiftsByUser($user) {
        $userId = $this->db->_id($user['_id']);
        return getShiftsByUserId($this->groupcode, $userId);
    }

    private function adjustTokensForUser($user, $tokens) {
        foreach ($this->users as $key => $vUser) {
            if ($vUser['user_name'] == $user['user_name']) {
                $tokensum = $tokens + $vUser['tokens'];
                $this->users[$key]['tokens'] = $tokensum;
                return true;
            }
        }
        return false;
    }

    /*
      Name: getUsersPreferredShift
      Author: Steve A

      Description: Attemps to retrieve the user's preferred shift based
      on a key offset (0 being most preferred)

      @params	$user			The user object
      @params	$offset			The key offset for the shift preference doc
     */

    private function getUsersPreferredShift($user, $offset = 0) {
        $ret = null;

// Get the shift preference from the user document and return it, if exists
        $preferredShiftName = $user['preferences']['shifts'][$offset];
        if ($preferredShiftName != null) {
            $ret = $preferredShiftName;
        } else {
            $ret = getFirstAvailable($this->schedule);
        }
        return $ret;
    }

    private function getNumOfSlotsOpenForShift($shift) {
        $count = 0;
        $userCount = 0;
        $number = $currShift['number'];
        $shiftId = $shift['id'];
        $currShift = $schedule[$shiftId];
        foreach ($currShift['users'] as $key => $user) {
            $userCount++;
        }
        $count = (int) $number - (int) $userCount;
        return $count;
    }

    private function countOpenShifts() {
        $count = 0;
        foreach ($this->schedule as $shift) {
            $userCount = $shift['number'];
            $presentCount = 0;
            foreach ($shift['users'] as $user) {
                $presentCount++;
            }
            $difference = $userCount - $presentCount;
            $count = $count + $difference;
        }
        return $count;
    }

    private function setBadIds($user, $shift) {
        $userId = $this->db->_id($user['_id']);
        $hours = getConfig($this->groupcode, 'minHoursBetweenShifts');
//echo "<br /><br />" . $shift['id'] .": ";
// echo date('m/d/Y H:i:s', $shift['start']->sec);
// echo "<br>";
        $beforeCheck = new MongoDate(strtotime(date('m/d/Y H:i:s', $shift['start']->sec) . ' -' . $hours . ' hours'));
        $afterCheck = new MongoDate(strtotime(date('m/d/Y H:i:s', $shift['endreal']->sec) . ' +' . $hours . ' hours'));
        if ($user['preferences']['circadian'] == '1') {
            $afterCheck = new MongoDate(strtotime(date('m/d/Y H:i:s', $shift['start']->sec) . ' +24 hours'));
//print_r(date('m/d/Y H:i:s',strtotime(date('m/d/Y H:i:s',$shift['start']->sec) .' +24 hours')));
        }
        //$where = array('$or' => array(array('endreal' => array('$gt' => $beforeCheck), 'endreal' => array('$lt' => $afterCheck)), array('start' => array('$gt' => $beforeCheck), 'start' => array('$lt' => $afterCheck))));
        $where = array('$or' => array(array('endreal' => array('$gt' => $beforeCheck), 'start' => array('$lt' => $afterCheck)), array('endreal' => array('$gt' => $beforeCheck), 'start' => array('$lt' => $afterCheck))));
        $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where, 'order' => 'id', 'order_by' => 'asc');
        $results = $this->db->find($arg);
//$userKey = $this->getUserKey($userId);

        foreach ($results as $result) {

            $tempId = $result['id'];

            $this->badIds[$userId]['' . $tempId . ''] = $tempId;
        }

//        $where = array('endreal' => array('$gt' => $beforeCheck), 'endreal' => array('$lt' => $afterCheck));
//        $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where);
//        $results = $this->db->find($arg);
//        foreach ($results as $result) {
//            $tempId = $result['id'];
//            $this->badIds[$userId]['' . $tempId . ''] = $tempId;
//        }
    }

    private function getUserKey($userId) {
        $returnKey = false;
        foreach ($this->users as $key => $user) {
            if ($this->db->_id($user['_id']) == $userId) {
                $returnKey = $key;
            }
        }
        return $returnKey;
    }

    private function canWork($user, $shift = false) {
        $results = false;
        $hours = getConfig($this->groupcode, 'minHoursBetweenShifts');

        if ($shift) {

            $start = new MongoDate(strtotime($shift['start'] . ' -' . $hours . ' hours'));
            $end = new MongoDate(strtotime($shift['endreal'] . ' +' . $hours . ' hours'));
            $where = array('users.id' => array('$in' => array($this->db->_id($user))), '$nor' => array('start' => array('$gt' => $start, '$lt' => $end), 'endreal' => array('$lt' => $start, '$gt' => $end)));
            if ($user['preferences']['circadian'] == '1') {
                $start = new MongoDate(strtotime($shift['start'] . ' - 24 hours'));
                $where['$nor'][] = array('start' => array('$gte' => $start));
            }

            $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where);
            $results = $this->db->find($arg);
        } else {

            $where = array('users.id' => array('$in' => array($this->db->_id($user))));
            $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where);
            $firstresults = $this->db->find($arg);
            $badarray = array();
            $orList = array();
            if (is_array($firstresults)) {
                foreach ($firstresults as $result) {
                    $badarray[] = $result['id'];
                    $start = new MongoDate(strtotime($result['start'] . ' -' . $hours . ' hours'));
                    $end = new MongoDate(strtotime($result['endreal'] . ' +' . $hours . ' hours'));
                    $orList[] = array('$and' => array('$or' => array(array('start' => array('$gt' => $start, '$lt' => $end), array('endreal' => array('$lt' => $start, '$gt' => $end))))));
                }
                $where = array('groups' => array('$in' => array($user['group'])), '$or' => $orList);
                if ($badarray != null) {
                    $where['id'] = array('$nin' => $badarray);
                }
                $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where);
                $results = $this->db->find($arg);
            }
        }
        return $results;
    }

    private function queryShift($user, $shift = false, $weekend = 0, $night = false) {

        $where = array();

        $daymax = getconfig($this->groupcode, 'maxConsecWorkingDays');
        $userId = $this->db->_id($user['_id']);
        $userKey = $this->getUserKey($userId);
        $badarray = $this->badIds[$userId];
        $pass = 0;
        if ($user['preferences']['shifts'] != null) {
            foreach ($user['preferences']['shifts'] as $shiftId) {
                $pass++;
                $where = array('id' => array('$nin' => $badarray), 'timeoffs' => array('$nin' => array($this->db->_id($user['_id']))), 'day' => array('$ne' => '00'), 'number' => array('$gt' => 0), 'groups' => array('$in' => array($user['group'])));
                if (isset($weekend) && $weekend != false && $weekend != 'no') {
                    $where['weekend'] = $weekend;
                }
                if (isset($user['location'])) {
                    if ($user['location'] == 'All' || $user['location'] == null) {
                        
                    } else {
                        $where['location'] = $user['location'];
                    
                    }
                }
                if ($night != false) {
                    $where['night'] = 1;
                }
                if ($shift != null) {
                    $where['shiftId'] = $shiftId;
                }
                $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where, 'order_by' => 'id', 'order' => 'asc', 'limit' => 1);
                $results = $this->db->find($arg);
                if (!empty($results[0])) {
                    if ($daymax) {
                        $breakCount = 1;
                        $day = (int) ($results[0]['day']);
                        for ($i = 1; $i < $daymax; $i++) {

                            $day++;
                            $where = array('day' => sprintf('%02s', $day), 'users.id' => array('$in' => array($userId)));
                            
                            $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where, 'order_by' => 'id', 'order' => 'asc', 'limit' => 1);
                            $days = $this->db->find($arg);
                            if (!empty($days)) {
                                $breakCount++;
                            } else {
                                break;
                            }
                        }

                        for ($i = 1; $i < $daymax; $i++) {

                            $day--;
                            $where = array('day' => sprintf('%02s', $day), 'users.id' => array('$in' => array($userId)));
                            
                            $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where, 'order_by' => 'id', 'order' => 'asc', 'limit' => 1);
                            $days = $this->db->find($arg);
                            if (!empty($days)) {
                                $breakCount++;
                            } else {
                                break;
                            }
                        }

                        if ($breakCount <= $daymax) {
                            break;
                        }
                    }
                }
            }
        } else {
            $where = array('timeoffs' => array('$nin' => array($this->db->_id($user['_id']))), 'day' => array('$ne' => '00'), 'number' => array('$gt' => 0));
            if (isset($weekend) && $weekend != false && $weekend != 'no') {
                $where['weekend'] = $weekend;
            }

            if ($night != false) {
                $where['night'] = 1;
            }
            if (isset($user['location'])) {
                if ($user['location'] == 'All' || $user['location'] == null) {
                    
                } else {
                    $where['location'] = $user['location'];
                }
            }
            $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where, 'order_by' => 'id', 'order' => 'asc', 'limit' => 1);
            $results = $this->db->find($arg);
        }

        return $results[0];
    }

    private function queryShifts($user, $shift = false, $weekend = 0, $night = false) {

        $where = array();


        $userId = $this->db->_id($user['_id']);
        $userKey = $this->getUserKey($userId);
        $badarray = $this->badIds[$userId];

        if ($user['preferences']['shifts'] != null) {
            foreach ($user['preferences']['shifts'] as $shiftId) {
                $where = array('id' => array('$nin' => $badarray), 'timeoffs' => array('$nin' => array($this->db->_id($user['_id']))), 'day' => array('$ne' => '00'), 'number' => array('$gt' => 0), 'shiftId' => $shiftId);
                if (isset($weekend) && $weekend != 'no') {
                    $where['weekend'] = $weekend;
                }

                if (isset($night) && $night != false) {
                    $where['night'] = $night;
                }
                $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where, 'order_by' => 'id', 'order' => 'asc');
                $results = $this->db->find($arg);
                if ($results != null) {
                    break;
                }
            }
        } else {
            $where = array('timeoffs' => array('$nin' => array($this->db->_id($user['_id']))), 'day' => array('$ne' => '00'), 'number' => array('$gt' => 0));
            if (isset($weekend) && $weekend != 'no') {
                $where['weekend'] = $weekend;
            }

            if (isset($night) && $night != false) {
                $where['night'] = $night;
            }
            $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where, 'order_by' => 'id', 'order' => 'asc');
            $results = $this->db->find($arg);
        }
        return $results[0];
    }

    private function getNextShiftAfterShift($user, $shift, $weekend = false) {

        $day = (int) $shift['day'];

        $day++;
        $day = sprintf("%02d", $day);
        $shiftId = $shift['shiftId'];
        $userId = $this->db->_id($user['_id']);
        $userKey = $this->getUserKey($userId);
        $badarray = $this->badIds[$userId];
        $daymax = getconfig($this->groupcode, 'maxConsecWorkingDays');


        $where = array('id' => array('$nin' => $badarray), 'timeoff' => array('$nin' => array('' . $userId . '')), 'number' => array('$gt' => 0), 'day' => '' . $day . '', 'groups' => array('$in' => array($user['group'])));
        if (isset($weekend) && $weekend != false) {
            $where['weekend'] = $weekend;
        }
        if (isset($user['location'])) {
            if ($user['location'] == 'All' || $user['location'] == null) {
                
            } else {
                $where['location'] = $user['location'];
            }
        }

        $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where, 'order_by' => 'id', 'order' => 'asc', 'limit' => 1);
        $results = $this->db->find($arg);
        $prefs = $user['preferences']['shifts'];
//        if (is_array($prefs) && is_array($results)) {
//            foreach ($prefs as $wanted) {
//                foreach ($results as $result) {
//                    if ($wanted == $result['shiftId']) {
//                        return $result;
//                    }
//                }
//            }
//        } else {


        if (!empty($results[0])) {
            if ($daymax) {
                $breakCount = 1;
                $day = (int) ($results[0]['day']);
                for ($i = 1; $i < $daymax; $i++) {

                    $day++;
                    $where = array('day' => sprintf('%02s', $day), 'users.id' => array('$in' => array($userId)));
                    
                    $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where, 'order_by' => 'id', 'order' => 'asc', 'limit' => 1);
                    $days = $this->db->find($arg);
                    if (!empty($days)) {
                        $breakCount++;
                    } else {
                        break;
                    }
                }

                for ($i = 1; $i < $daymax; $i++) {

                    $day--;
                    $where = array('day' => sprintf('%02s', $day), 'users.id' => array('$in' => array($userId)));
                    
                    
                    $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where, 'order_by' => 'id', 'order' => 'asc', 'limit' => 1);
                    $days = $this->db->find($arg);
                    if (!empty($days)) {
                        $breakCount++;
                    } else {
                        break;
                    }
                }
            }
            if ($breakCount > $daymax) {
                return false;
            }
        }
        return $results[0];
//        }
    }

    private function getShift($shiftId) {
        $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => array('id' => $shiftId, 'day' => array('$ne' => '0')));
        $results = $this->db->find($arg);
        return $results[0];
    }

    private function setShift($shift) {
        $number = (int) $shift['number'];
        $number--;
        $shift['number'] = (int) $number;
        $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'id' => $this->db->_id($shift['_id']), 'obj' => $shift);

        $results = $this->db->upsert($arg);
        return $results;
    }

    private function placeUserInShift($user, $shift) {
        $shiftId = $shift['id'];
        $tokens = 0;
//$newShift = $this->getShift($shift);
        $shift['users'][] = array('first_name' => $user['first_name'], 'last_name' => $user['last_name'], 'user_name' => $user['user_name'], 'id' => $this->db->_id($user['_id']));
        $update = $this->setShift($shift);
        $this->setBadIds($user, $shift);
        foreach ($this->users as $key => $us) {
            if ($this->db->_id($user['_id']) == $this->db->_id($us['_id'])) {
                $hours = $us['hours'];
                $newhours = $hours + $shift['duration'];
                $us['hours'] = $newhours;
                if ($shift['night'] == 1) {
                    $us['night_hours'] = (int) $us['night_hours'] - (int) $shift['duration'];
                }
                /*  echo "<br>";
                  echo $us['user_name'];
                  echo ":";
                  echo $us['hours'] . " / " . $us['min_hours'] . " out of " . $us['max_hours']; */
                $this->users[$key] = $us;

                break;
            }
        }
        if ($this->isPreferredDay($user, $shift['start'])) {
            $tokens++;
        }
        if (isWeekend($shift['start'])) {
            $tokens--;
        }
        $prefArray = $user['preferences']['shifts'];
        $value = array_search($shift['shiftId'], $prefArray);
        $prefCount = count($prefArray);
        $prefEnd = $prefCount - $value;
        $tokens = $tokens + $prefEnd;
        $this->adjustTokensForUser($user, $tokens);
        $openshifts = $this->openShifts;
        $openshifts--;
        $this->openShifts = $openshifts;
    }

    private function placeUsersInPreferredShifts() {
        foreach ($this->users as $user) {
            $canUserTakeShift = false;
            $pass = 0;
            $prefCount = 0;
            foreach ($user['preferences']['shifts'] as $prefshift) {
                $prefCount++;
            }
// Continue looping through shifts until a shift is found that matches a 
// user preference and the user can work it
// while ($canUserTakeShift == false) {
// Gets the user's preferred shift

            $nextShift = $this->queryShift($user, false, 0, false);
            if ($nextShift) {
// If user has worked their max monthly hours, break
                if (!$this->isUserOverMax($user, $nextShift)) {
// If the user has requested the system to block shifts, loop 
// through the block at once and attempt to place the user 
// into the complete block
//get count of block
                    $block = $this->isShiftBlockable($user, $nextShift);

                    if ($block) {
// Place the user into the first day of the block series
// if circadian is met

                        $this->placeUserInShift($user, $nextShift);
                        $canUserTakeShift = true;


// Loop through the next available shifts based on the number 
// of blockable shifts preferred by the user

                        for ($i = 1; $i < $block; $i++) {

                            $nextShift = $this->getNextShiftAfterShift($user, $nextShift);

                            if (is_array($nextShift) && !$this->isUserOverMax($user, $nextShift)) {

                                if (date('w', strtotime($nextShift['start'])) != '6') {
                                    $this->placeUserInShift($user, $nextShift);
                                    $canUserTakeShift = true;
                                } else {
                                    break;
                                }
                            } else {
                                break;
                            }
                        }
                    } else {
// The user does not want their shifts blocked, so simply process 
// the single instance of the shift and user

                        $this->placeUserInShift($user, $nextShift);
                        $canUserTakeShift = true;
                    } //end if ($this->isShiftBlockable($shift)
                }// end if (!$this->isUserOverMax($user)
//   $canUserTakeShift = true;
            } // end of second internal while
//$pass++; //increment the pass counter to see how many passes have taken place
//if ($pass >= $prefCount) {
//    break;
//}
//   } // end while
        } // end foreach	
    }

    private function isUserOverMin($user) {
        if ($user['hours'] >= $user['min_hours']) {
            return true;
        } else {
            return false;
        }
    }

    private function getUserMin() {

        $low = array();
        foreach ($this->users as $key => $user) {
            if (!$this->isUserOverMin($this->users[$key])) {
                $low[$key] = $user['hours'];
            }
        }

        $return = array_keys($low, min($low));
        return $return[0];
    }

    private function getUserMax($group) {

        $low = array();
        foreach ($this->users as $key => $user) {
            if ($this->isUserOverMin($this->users[$key])) {
                if ($user['group'] == $group) {
                    $low[$key] = $user['hours'];
                }
            }
        }

        $return = array_keys($low, max($low));
        return $return[0];
    }

    private function placeUsersFirstShifts() {
        //  usort($this->users, function($a, $b) {
        //            return $a['available'] - $b['available'];
        //      });

        $notplaced = 0;
        while ($notplaced == 0) {

            $shiftTaken = 0;
            //foreach ($this->users as $userKey => $user) {
            $userKey = $this->getUserMin();
            $user = $this->users[$userKey];

// Continue looping through shifts until a shift is found that matches a 
// user preference and the user can work it
// while ($canUserTakeShift == false) {
// Gets the user's preferred shift
            if (!$this->isUserOverMin($this->users[$userKey])) {

                $nextShift = $this->queryShift($user, false, 'no', false);
                if ($nextShift) {

// If user has worked their max monthly hours, break
// If the user has requested the system to block shifts, loop 
// through the block at once and attempt to place the user 
// into the complete block
//get count of block
                    $block = $this->isShiftBlockable($user, $nextShift);

                    if ($block) {
// Place the user into the first day of the block series
// if circadian is met

                        $this->placeUserInShift($user, $nextShift);
                        $this->users[$userKey]['current']++;
                        $shiftTaken++;


// Loop through the next available shifts based on the number 
// of blockable shifts preferred by the user

                        for ($i = 1; $i < $block; $i++) {

                            $nextShift = $this->getNextShiftAfterShift($user, $nextShift);

                            if (is_array($nextShift) && !$this->isUserOverMin($this->users[$userKey], $nextShift)) {
                                $this->placeUserInShift($user, $nextShift);
                                $this->users[$userKey]['current']++;
                            } else {
                                break;
                            }
                        }
                    } else {
// The user does not want their shifts blocked, so simply process 
// the single instance of the shift and user

                        $this->placeUserInShift($user, $nextShift);
                        $this->users[$userKey]['current']++;
                        $shiftTaken++;
                    } //end if ($this->isShiftBlockable($shift)
                    // end if (!$this->isUserOverMax($user)
                }
            }
//   $canUserTakeShift = true;
            // end of second internal while
//$pass++; //increment the pass counter to see how many passes have taken place
            //} // end foreach

            if ($shiftTaken == 0) {
                break;
            }
        }  // end while
    }

    private function placeUsersInWeekends() {


        foreach ($this->users as $user) {
            $takenShifts = 0;
            $pass = 0;
            $prefCount = 0;
            foreach ($user['preferences']['shifts'] as $prefshift) {
                $prefCount++;
            }
// Continue looping through shifts until a shift is found that matches a 
// user preference and the user can work it
// while ($canUserTakeShift == false) {
// Gets the user's preferred shift
            while ($takenShifts < $user['weekendcount']) {
                $nextShift = $this->queryShift($user, false, 1, 0);
                if ($nextShift) {
// If user has worked their max monthly hours, break
                    if (!$this->isUserOverMax($user, $nextShift)) {

// If the user has requested the system to block shifts, loop 
// through the block at once and attempt to place the user 
// into the complete block
//get count of block
                        $block = $this->isShiftBlockable($user, $nextShift);
                        if ($block) {
// Place the user into the first day of the block series
// if circadian is met

                            $this->placeUserInShift($user, $nextShift);
                            $takenShifts++;

                            if (date('w', $nextShift['start']->sec) == '6') {

// Loop through the next available shifts based on the number 
// of blockable shifts preferred by the user


                                $nextShift = $this->getNextShiftAfterShift($user, $nextShift, 1);

                                if (is_array($nextShift) && !$this->isUserOverMax($user, $nextShift)) {


                                    $this->placeUserInShift($user, $nextShift);
                                    $takenShifts++;
                                } else {
                                    break;
                                }
                            } else {
                                break;
                            }
                        } else {
// The user does not want their shifts blocked, so simply process 
// the single instance of the shift and user

                            $this->placeUserInShift($user, $nextShift);
                            $takenShifts++;
                        } //end if ($this->isShiftBlockable($shift)
                    }// end if (!$this->isUserOverMax($user)
//   $canUserTakeShift = true;
                } // end of second internal while
                $pass++; //increment the pass counter to see how many passes have taken place
                if ($pass >= $prefCount) {
                    break;
                }
            } // end while
        } // end foreach	
    }

    private function placeUsersInNights() {


        foreach ($this->users as $user) {
            $takenShifts = 0;
            $pass = 0;
            $prefCount = 0;
            $userId = $this->db->_id($user['_id']);
            foreach ($user['preferences']['shifts'] as $prefshift) {
                $prefCount++;
            }
// Continue looping through shifts until a shift is found that matches a 
// user preference and the user can work it
// while ($canUserTakeShift == false) {
// Gets the user's preferred shift

            while ($user['night_hours'] > 0) {

                $nextShift = $this->queryShift($user, false, 'no', 1);
                if ($nextShift) {
// If user has worked their max monthly hours, break
                    if (!$this->isUserOverMax($user, $nextShift)) {

// If the user has requested the system to block shifts, loop 
// through the block at once and attempt to place the user 
// into the complete block
//get count of block
                        $block = $this->isShiftBlockable($user, $nextShift);
                        if ($block) {
// Place the user into the first day of the block series
// if circadian is met

                            $this->placeUserInShift($user, $nextShift);
                            $user['night_hours'] = $user['night_hours'] - $nextShift['duration'];
                            $takenShifts++;



// Loop through the next available shifts based on the number 
// of blockable shifts preferred by the user


                            $nextShift = $this->getNextShiftAfterShift($user, $nextShift, false);

                            if (is_array($nextShift) && !$this->isUserOverMax($user, $nextShift)) {


                                $this->placeUserInShift($user, $nextShift);
                                $user['night_hours'] = $user['night_hours'] - $nextShift['duration'];
                                $takenShifts++;
                            } else {
                                break;
                            }
                        } else {
// The user does not want their shifts blocked, so simply process 
// the single instance of the shift and user

                            $this->placeUserInShift($user, $nextShift);
                            $user['night_hours'] = $user['night_hours'] - $nextShift['duration'];
                            $takenShifts++;
                        } //end if ($this->isShiftBlockable($shift)
                    }// end if (!$this->isUserOverMax($user)
//   $canUserTakeShift = true;
                } // end of second internal while
                $pass++; //increment the pass counter to see how many passes have taken place
                if ($pass >= $prefCount) {
                    break;
                }
            } // end while
        } // end foreach	
    }

    private function placeMinUsers() {
        foreach ($this->users as $key => $user) {
            $user['diff'] = $user['min_hours'] - $user['hours'];
            $this->users[$key] = $user;
        }
        $done = 0;
        while ($done == 0) {

            usort($this->users, function($a, $b) {
                        return $b['diff'] - $a['diff'];
                    });

            $takenShifts = 0;
            foreach ($this->users as $userkey => $user) {

                if ($user['diff'] >= 0) {
                    $userId = $this->db->_id($user['_id']);


                    $pass = 0;
                    $pass++;
                    $prefCount = 0;

                    $nextShift = $this->queryShift($user, false, 'no', false);
                    if ($nextShift) {
// If user has worked their max monthly hours, break
                        if (!$this->isUserOverMax($user, $nextShift)) {

// If the user has requested the system to block shifts, loop 
// through the block at once and attempt to place the user 
// into the complete block
//get count of block
                            $block = $this->isShiftBlockable($user, $nextShift);
                            if ($block) {
// Place the user into the first day of the block series
// if circadian is met

                                $this->placeUserInShift($user, $nextShift);
                                $takenShifts++;
                                $this->users[$userkey]['diff'] = $this->users[$userkey]['diff'] - $nextShift['duration'];



// Loop through the next available shifts based on the number 
// of blockable shifts preferred by the user

                                for ($i = 1; $i < $block; $i++) {
                                    $nextShift = $this->getNextShiftAfterShift($user, $nextShift, false);

                                    if (is_array($nextShift) && !$this->isUserOverMax($user, $nextShift)) {

                                        if ($this->isUserUnderMin($this->users[$userkey])) {
                                            $this->placeUserInShift($user, $nextShift);
                                            $takenShifts++;
                                            $this->users[$userkey]['diff'] = $this->users[$userkey]['diff'] - $nextShift['duration'];
                                        }
                                    } else {
                                        break;
                                    }
                                }
                            } else {
// The user does not want their shifts blocked, so simply process 
// the single instance of the shift and user

                                $this->placeUserInShift($user, $nextShift);
                                $takenShifts++;
                                $this->users[$userkey]['diff'] = $this->users[$userkey]['diff'] - $nextShift['duration'];
                            } //end if ($this->isShiftBlockable($shift)
                        }// end if (!$this->isUserOverMax($user)
//   $canUserTakeShift = true;
                    } // end of second internal while
                }  //echo "<br>";echo $takenShifts;  
            }
            if ($takenShifts == 0) {
                break;
            }
        }
    }

    private function placeUsersInRemainingShifts() {
// -- Now that users have their preferred shifts, start rules-based checks and schedule for real
        foreach ($this->users as $key => $user) {
            $user['diff'] = $user['max_hours'] - $user['hours'];
            $this->users[$key] = $user;
        }
        $done = 0;
        while ($done == 0) {
            $takenShifts = 0;
            uasort($this->users, function($a, $b) {
                        return $b['diff'] - $a['diff'];
                    });
            foreach ($this->users as $userkey => $user) {

                if ($user['diff'] >= 0) {
                    $pass = 0;
                    $prefCount = 0;
                    $userId = $this->db->_id($user['_id']);

                    $nextShift = $this->queryShift($user, false, 'no', false);
                    if ($nextShift) {
// If user has worked their max monthly hours, break
                        if (!$this->isUserOverMax($user, $nextShift)) {

// If the user has requested the system to block shifts, loop 
// through the block at once and attempt to place the user 
// into the complete block
//get count of block
                            $block = $this->isShiftBlockable($user, $nextShift);
                            if ($block) {
// Place the user into the first day of the block series
// if circadian is met

                                $this->placeUserInShift($user, $nextShift);
                                $takenShifts++;
                                $this->users[$userkey]['diff'] = $this->users[$userkey]['diff'] - $nextShift['duration'];



// Loop through the next available shifts based on the number 
// of blockable shifts preferred by the user

                                for ($i = 1; $i < $block; $i++) {
                                    $nextShift = $this->getNextShiftAfterShift($user, $nextShift, false, false);

                                    if (is_array($nextShift) && !$this->isUserOverMax($user, $nextShift)) {


                                        $this->placeUserInShift($user, $nextShift);
                                        $takenShifts++;
                                        $this->users[$userkey]['diff'] = $this->users[$userkey]['diff'] - $nextShift['duration'];
                                    } else {
                                        break;
                                    }
                                }
                            } else {
// The user does not want their shifts blocked, so simply process 
// the single instance of the shift and user

                                $this->placeUserInShift($user, $nextShift);
                                $takenShifts++;
                                $this->users[$userkey]['diff'] = $this->users[$userkey]['diff'] - $nextShift['duration'];
                            } //end if ($this->isShiftBlockable($shift)
                        }// end if (!$this->isUserOverMax($user)
//   $canUserTakeShift = true;
                    } // end of second internal while
                    //echo "<br>";echo $takenShifts;  
                }
            }

            if ($takenShifts == 0) {
                $done = 1;
            }
        }
    }

    private function replaceShifts() {
        foreach ($this->users as $key => $user) {
            $user['diff'] = $user['min_hours'] - $user['hours'];
            $this->users[$key] = $user;
        }
        uasort($this->users, function($a, $b) {
                    return $b['diff'] - $a['diff'];
                });

// Continue looping through shifts until a shift is found that matches a 
// user preference and the user can work it
// while ($canUserTakeShift == false) {
// Gets the user's preferred shift
        $shiftTaken = 0;
        foreach ($this->users as $userKey => $user) {
            if (!$this->isUserOverMin($this->users[$userKey])) {

                $nextShift = $this->queryShift($user, false, 'no', false);
                if ($nextShift) {

                    // If user has worked their max monthly hours, break
                    // If the user has requested the system to block shifts, loop 
                    // through the block at once and attempt to place the user 
                    // into the complete block
                    //get count of block
                    $block = $this->isShiftBlockable($user, $nextShift);

                    if ($block) {
                        // Place the user into the first day of the block series
                        // if circadian is met

                        $this->placeUserInShift($user, $nextShift);
                        $this->users[$userKey]['current']++;
                        $shiftTaken++;


                        // Loop through the next available shifts based on the number 
                        // of blockable shifts preferred by the user

                        for ($i = 1; $i < $block; $i++) {

                            $nextShift = $this->getNextShiftAfterShift($user, $nextShift);

                            if (is_array($nextShift) && !$this->isUserOverMin($this->users[$userKey], $nextShift)) {
                                $this->placeUserInShift($user, $nextShift);
                                $this->users[$userKey]['current']++;
                            } else {
                                break;
                            }
                        }
                    } else {
                        // The user does not want their shifts blocked, so simply process 
                        // the single instance of the shift and user

                        $this->placeUserInShift($user, $nextShift);
                        $this->users[$userKey]['current']++;
                        $shiftTaken++;
                    } //end if ($this->isShiftBlockable($shift)
                    // end if (!$this->isUserOverMax($user)
                }
            }
        }

        if ($shiftTaken == 0) {
            return 1;
        } else {
            return 0;
        }
    }

    private function tryBlock($user, $shift, $count) {
        
    }

    private function fillShifts() {
        $where = array('day' => array('$ne' => '00'), 'number' => array('$gt' => 0));
        $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where, 'order_by' => 'id', 'order' => 'asc');
        $results = $this->db->find($arg);
        if ($results != null) {
            foreach ($results as $shift) {
                $group = $shift['groups'][0];
                $this->sortUsers('hours', 'asc');
                foreach ($this->users as $key => $user) {
                    if ($group == $user['group']) {

                        $userId = $this->db->_id($user['_id']);
                        $badarray = $this->badIds[$userId];


                        $where = array('id' => array('$nin' => $badarray), 'id' => $shift['id'], 'timeoffs' => array('$nin' => array($this->db->_id($user['_id']))), 'day' => array('$ne' => '00'), 'number' => array('$gt' => 0), 'groups' => array('$in' => array($user['group'])));
                        if (isset($user['location'])) {
                            if ($user['location'] == 'All' || $user['location'] == null) {
                                
                            } else {
                                $where['location'] = trim($user['location']);
                            }
                        }
                        $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where, 'order_by' => 'id', 'order' => 'asc', 'limit' => 1);
                        $results = $this->db->find($arg);
                        if ($results != null) {
                            if (!$this->isUserOverMax($user, $shift)) {
                                $this->placeUserInShift($user, $shift);
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    private function mustWorks() {
        $timeoffs = 0;
        $shiftTaken = 0;
        while ($timeoffs == 0) {
            $shiftTaken = 0;
            uasort($this->users, function($a, $b) {
                        return $a['hours'] - $b['hours'];
                    });
            foreach ($this->users as $key => $user) {
                $userId = $this->db->_id($user['_id']);
                $userKey = $this->getUserKey($userId);
                $badarray = $this->badIds[$userId];
                $where = array('mustwork' => array('$in' => array($userId)), 'status' => array('$ne' => 'Disapproved'), 'day' => array('$ne' => '00'), 'number' => array('$gt' => 0), 'id' => array('$nin' => $badarray));
                if (isset($user['location'])) {
                    if ($user['location'] == 'All' || $user['location'] == null) {
                        
                    } else {
                        $where['location'] = trim($user['location']);
                    }
                }
                $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where, 'limit' => 1);
                $results = $this->db->find($arg);
                if (is_array($results[0])) {
                    $this->placeUserInShift($this->users[$key], $results[0]);
                    $shiftTaken++;
                }
            }
            if ($shiftTaken == 0) {
                break;
            }
        }
    }

    public function staffSchedule() {

        $this->sortUsers('both', 'desc');
        $this->mustWorks();

        $this->placeUsersInNights();
        if (getConfig($this->groupcode, 'weekendShifts') == 'true') {
            $this->placeUsersInWeekends();
        }
       // $this->placeUsersFirstShifts();

        while ($this->finish == 0) {
            $this->finish = $this->replaceShifts();
        }
        $this->placeUsersInRemainingShifts();
       // $this->fillShifts();
    }

    public function isUserContainedInShift($userArray, $theUser) {
        if ($userArray == null || $userArray < 1) {
            return False;
        }

        $userId = $this->db->_id($theUser['_id']);

        foreach ($userArray['users'] as $aUser) {
            $aUserId = $this->db->_id($aUser['_id']);
            if ($userId == $aUserId) {
                return True;
            }
        }
        return False;
    }

    public function updateSchedule() {
        $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => array('day' => array('$ne' => '0')));
        $results = $this->db->find($arg);
        foreach ($results as $shift) {
//$shift['users'][] = array('first_name' => $pickeduser['first_name'], 'last_name' => $pickeduser['last_name'], 'user_name' => $pickeduser['user_name'], 'id' => $this->db->_id($pickeduser['_id']));
            $this->schedule[$shift['id']]['users'] = $shift['users'];
        }


        $obj = array('schedule' => $this->schedule, 'active' => 1, 'published' => 0);
        $arg = array('col' => $this->groupcode, 'type' => 'schedule', 'obj' => $obj, 'id' => $this->scheduleId);
        $results = $this->db->upsert($arg);
        return $this->db->_id($results);
    }

    private function getUserForShift($shiftId) {
        
    }

    public function getUserTimeOffByShift($user, $shiftId) {
        $timeoffs = $user['timeoffs'];
        $shift = false;
        foreach ($timeoffs as $timeoff) {
            foreach ($timeoff['time_off'] as $key => $value) {
                if ($shiftId == $value) {
                    $shift = $timeoff;
                    break;
                }
            }
        }
        return $shift;
    }

    /*
      Name: sortUsers
      Description: Takes the users object and randomizes the
      collection, then sorts them based on $sortType

      @params	$sortType	The type of sort, either 'both' or 'tokens'
     */

    public function sortUsers($sortType = 'tokens', $order = 'asc') {
        rsort($this->users);
        if ($sortType == "both") {
            $this->sortUsersByPriorityThenTokens();
        } else {
            $this->sortUsersByTokens();
        }
    }

    /*
      Name: sortUsersByPriorityThenTokens
      Description: Takes the users object and sorts them
      based on priority, then by number of tokens ASC
     */

    private function sortUsersByPriorityThenTokens() {
        $priority = array();
        foreach ($this->users as $key => $row) {
            $priority[$key] = $row['priority'];
        }

        array_multisort($priority, SORT_DESC, $this->users);

        $array5 = array();
        $array4 = array();
        $array3 = array();
        $array2 = array();
        $array1 = array();

        foreach ($this->users as $user) {
            $priorityNum = $user['priority'];

            switch ($priorityNum) {
                case "5":
                    array_push($array5, $user);
                    break;
                case "4":
                    array_push($array4, $user);
                    break;
                case "3":
                    array_push($array3, $user);
                    break;
                case "2":
                    array_push($array2, $user);
                    break;
                case "1":
                    array_push($array1, $user);
                    break;
            }
        }

        foreach ($array5 as $user) {
            $priority = array();
            foreach ($array5 as $key => $row) {
                $priority[$key] = $row['tokens'];
            }
            array_multisort($priority, SORT_ASC, $array5);
        }

        foreach ($array4 as $user) {
            $priority = array();
            foreach ($array4 as $key => $row) {
                $priority[$key] = $row['tokens'];
            }
            array_multisort($priority, SORT_ASC, $array4);
        }

        foreach ($array3 as $user) {
            $priority = array();
            foreach ($array3 as $key => $row) {
                $priority[$key] = $row['tokens'];
            }
            array_multisort($priority, SORT_ASC, $array3);
        }

        foreach ($array2 as $user) {
            $priority = array();
            foreach ($array2 as $key => $row) {
                $priority[$key] = $row['tokens'];
            }
            array_multisort($priority, SORT_ASC, $array2);
        }

        foreach ($array1 as $user) {
            $priority = array();
            foreach ($array1 as $key => $row) {
                $priority[$key] = $row['tokens'];
            }
            array_multisort($priority, SORT_ASC, $array1);
        }


        $newArray = array();

        foreach ($array5 as $user) {
            array_push($newArray, $user);
        }

        foreach ($array4 as $user) {
            array_push($newArray, $user);
        }

        foreach ($array3 as $user) {
            array_push($newArray, $user);
        }

        foreach ($array2 as $user) {
            array_push($newArray, $user);
        }

        foreach ($array1 as $user) {
            array_push($newArray, $user);
        }

        $this->users = $newArray;

//        foreach ($this->users as $user) {
//            echo $user['priority'] . " - " . $user['tokens'] . "<br />";
//        }
    }

    /*
      Name: sortUsersByTokens
      Description: Takes the users object and sorts them
      based on number of tokens ASC
     */

    private function sortUsersByTokens($order_by = 'tokens', $order = 'asc') {
        $tokens = array();
        foreach ($this->users as $key => $row) {
            $tokens[$key] = $row[$order_by];
        }
        if ($order == 'asc') {
            array_multisort($tokens, SORT_ASC, $this->users);
        } else {
            array_multisort($tokens, SORT_DESC, $this->users);
        }

//        foreach ($this->users as $user) {
//            echo $user['priority'] . " - " . $user['tokens'] . "-" . $user['hours'] . "<br />";
//        }
    }

}

?>