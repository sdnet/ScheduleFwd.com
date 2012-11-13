<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
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

    public function __construct($grp, $id) {
        $this->db = new MONGORILLA_DB;
        $this->scheduleId = $id;
        $this->groupcode = $grp;
    }

    public function getSchedule() {
        $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => array());
        $results = $this->db->delete($arg);
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
                    $shift['day'] = '0';
                    $final[$key] = $shift;
                    $shift['start'] = new MongoDate(strtotime($shift['start']));
                    $shift['endreal'] = new MongoDate(strtotime($shift['endreal']));
                    $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'obj' => $shift);
                    $results = $this->db->upsert($arg);
                }
            }
        }
        $key = 0;
        foreach ($schedule as $t => $shift) {
            $key = $shift['id'];
            $t1 = new DateTime($shift['start']);
            $t2 = new DateTime($shift['endreal']);
            $t3 = date_diff($t1, $t2);
            $duration = $t3->m;
            $shift['duration'] = $duration;
            $shift['day'] = date('d', strtotime($shift['start']));
            $final[$key] = $shift;
            $start = $shift['start'];
            $end = $shift['end'];
            $shift['dayName'] = date('l',strtotime($shift['start']));
            $shift['weekend'] = isWeekend($start);
            $shift['night'] = isNight($start,$end);
            $shift['start'] = new MongoDate(strtotime($shift['start']));
            $shift['endreal'] = new MongoDate(strtotime($shift['endreal']));
            $shift['timeoffs'] = array();
            $where = array('time_off' => array('' . date('Y-m-d', strtotime($start)) . '' => $shift['shiftId']), 'year' => '' . $this->year . '', 'month' => '' . $this->month . '', 'status' => array('$ne' => 'Disapproved'));
            $arg = array('col' => $this->groupcode, 'type' => 'timeoff', 'where' => $where);
            $tresults = $this->db->find($arg);
            if ($tresults != null) {
                foreach ($tresults as $timeoff) {
                    $shift['timeoffs'][] = $timeoff['userId'];
                }
            }
            $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'obj' => $shift);
            $results = $this->db->upsert($arg);
        }
        $this->openShifts = $key;
        $this->schedule = $final;
        $this->year = $result[0]['year'];
        $this->month = $result[0]['month'];
    }

    public function getGroups() {
        $arg = array('col' => $this->groupcode, 'type' => 'group');
        $this->groups = $this->db->find($arg);
    }

    public function getUsers() {
        $nightReq = getConfig($this->groupcode, 'maxNightsPerMonth');
        $arg = array('col' => $this->groupcode, 'type' => 'user', 'where' => array('active' => 1));
        $results = $this->db->find($arg);

        foreach ($results as $result) {
            $userId = $this->db->_id($result['_id']);
            $timeoffrequests = getTimeOffByUserId($this->groupcode, $userId, $this->month, $this->year);
            $tokens = 0;
            $timeoffs = array();
            if ($timeoffrequests != null) {
                foreach ($timeoffrequests as $key => $timeoff) {
                    foreach ($timeoff['time_off'] as $k => $v) {
                        $timeoffs[] = array('granted' => 0, 'date' => $k, 'shiftId' => $v);
                    }
                    if ($timeoff['priority'] == '1') {
                        $tokens++;
                    }
                    $tokens++;
                }
            }
            $nightcount = getNightCountForUser($this->groupcode, $userId, $this->month, $this->year);
            $weekendcount = getWeekendCountForUser($this->groupcode, $userId, $this->month, $this->year);
            


            $min = $result['min_hours'];
            $max = $result['max_hours'];
            if ($minhour == null || $maxhour == null) {
                foreach ($this->groups as $group) {
                    if ($group['name'] == $result['group']) {
                        $min = $group['min_hours'];
                        $max = $group['max_hours'];
                    }
                }
            }
            $arr = array_merge(array('min_hours' => $min*60, 'max_hours' => $max*60, 'hours' => 0, 'night_hours' => (((int)$nightReq)/60), 'tokens' => $tokens, 'timeoffs' => $timeoffs, 'nightcount' => $nightcount, 'weekendcount' => $weekendcount), $result);
            $userArray[] = $arr;
        }

        $this->users = $userArray;
    }

    /*
      Pseudo code for new functionized staffing capability

      For each shift
      Get shift properties
      If user is not currently in shift

     */

    private function setShiftProperties($shift) {
        $propJSON;
        $propJSON['start'] = $shift['start'];
        $propJSON['end'] = $shift['endreal'];
        $propJSON['t1'] = new DateTime($shift['start']);
        $propJSON['t2'] = new DateTime($shift['endreal']);
        $propJSON['t3'] = date_diff($propJSON['t1'], $propJSON['t2']);
        $propJSON['duration'] = $propJSON['t3']->h;
        $propJSON['timestamp'] = strtotime($propJSON['start']);
        $propJSON['endstamp'] = strtotime($shift['endreal']);
        $propJSON['day'] = date('l', $propJSON['timestamp']);
        $propJSON['month'] = date('m', $propJSON['timestamp']);
        $propJSON['year'] = date('Y', $propJSON['timestamp']);
        $propJSON['group'] = $shift['groups'][0];
        $propJSON['weekend'] = isWeekend($start);
        $propJSON['nighttime'] = strtotime('' . $propJSON['month'] . '-' . $propJSON['day'] . '-' . $propJSON['year'] . ' 3:00am');

        if ($propJSON['nighttime'] > $propJSON['timestamp'] && $propJSON['nighttime'] < $propJSON['endstamp']) {
            $propJSON['dayornight'] = 'night';
        } else {
            $propJSON['dayornight'] = 'day';
        }

        return $propJSON;
    }

    private function setUserPreferences($user) {
        $prefJSON;
        $prefJSON['dayPref'] = $user['preferences']['days'];
        $prefJSON['shiftPref'] = $user['preferences']['shifts'];
        $prefJSON['blockdays'] = $user['preferences']['block_days'];
        $prefJSON['blockweekend'] = $user['preferences']['block_weekend'];
        $prefJSON['maxdays'] = $user['preferences']['max_days'];
        $prefJSON['maxnights'] = $user['preferences']['max_nights'];
        $prefJSON['desirednights'] = $user['preferences']['desired_nights'];
        $prefJSON['desireddays'] = $user['preferences']['desired_days'];

        return $prefJSON;
    }

    private function lessThanMaxGroupHours($group, $user) {
        if ($group >= $user['hours']) {
            
        }
    }

    private function processUserForShift($properties, $shift, $user, $maxgrouphours, $maxpreferences, $maxconsecdays, $shiftPref) {
        if (getUserCanWorkSchedule($this->groupcode, $this->db->_id($user['_id']), $properties['start'], $properties['end'], $shift['id'], $this->schedule)) {

            if ($this->lessThanMaxGroupHours($maxgrouphours, $user['hours'])) {

                if ($maxpreferences >= $user['weight']) {

                    $userPrefs = $this->setUserPreferences($user);

                    if ($userPrefs['desireddays'] == null) {
                        $userPrefs['desireddays'] = 0;
                    }

                    /*
                      echo " user: ";
                      echo $user['user_name'];
                      echo " - ";
                      print_r(getNumberOfDaysConsec($this->groupcode, $shift['id'], $this->db->_id($user['_id']), $this->schedule, $maxconsecdays));
                     */

                    if (getNumberOfDaysConsec($this->groupcode, $shift['id'], $this->db->_id($user['_id']), $this->schedule, $maxconsecdays) < $maxconsecdays) {
                        $prefweight = 0;
                        $daymatch = 0;
                        $weightdiff = 0;
                        $daycount = count($userPrefs['dayPref']);
                        if (in_array($day, $userPrefs['dayPref'])) {
                            $daymatch = 1;
                            $weightdiff = $weightdiff + ((1 - (1 / $daycount)) / 7);
                        }

                        if (count($userPrefs['shiftPref']) > 0) {
                            $prefvalue = array_search($shift['shiftName'], $userPrefs['shiftPref']);
                            $prefcount = count($userPrefs['shiftPref']);
                            $prefweight = $prefvalue / $prefcount;
                            $prefweight = 1 - $prefweight;
                            $prefweight = $prefweight;
                            $weightdiff = $weightdiff + $prefweight;
                        }
                        //if(getShiftsRow($this->groupcode,$db->_id($user['_id']),$shift['start'],$shift['endreal'],$this->schedule)){
                        if ($weightdiff <= $user['weight']) {
                            $this->users[$userKey]['weight'] = ($user['weight']) - ($weightdiff / 100);
                            $this->users[$userKey]['hours'] = $user['hours'] + $properties['duration'];
                            $pickeduser = $user;
                            for ($e = 1; $e <= $userPrefs['desireddays']; $e++) {
                                $daytemp = date('Y-m-d', strtotime(date("Y-m-d", strtotime($date)) . " +" . $e . " day"));
                                $blockid = getNextDayShiftId($this->groupcode, $daytemp, $shift['shiftName'], $this->schedule);
                                if (getNumberOfDaysConsec($this->groupcode, $blockid, $this->db->_id($user['_id']), $this->schedule, $maxconsecdays) < $maxconsecdays) {

                                    $this->schedule[$blockid]['users'][] = array('first_name' => $pickeduser['first_name'], 'last_name' => $pickeduser['last_name'], 'user_name' => $pickeduser['user_name'], 'id' => $this->db->_id($pickeduser['_id']));
                                }
                            }

                            // echo " <span style=\"color: green;\">PICKED!</span>";
                            return $user;
                        } elseif ($user['hours'] < $averagehours) {
                            //$this->users[$userKey]['weight'] = ($user['weight']) - ($weightdiff/100);
                            $this->users[$userKey]['hours'] = $user['hours'] + $properties['duration'];
                            $pickeduser = $user;
                            for ($e = 1; $e <= $userPrefs['desireddays']; $e++) {
                                $daytemp = date('Y-m-d', strtotime(date("Y-m-d", strtotime($date)) . " +" . $e . " day"));
                                $blockid = getNextDayShiftId($this->groupcode, $daytemp, $shift['shiftName'], $this->schedule);
                                if (getNumberOfDaysConsec($this->groupcode, $blockid, $db->_id($user['_id']), $this->schedule, $maxconsecdays) < $maxconsecdays) {

                                    $this->schedule[$blockid]['users'][] = array('first_name' => $pickeduser['first_name'], 'last_name' => $pickeduser['last_name'], 'user_name' => $pickeduser['user_name'], 'id' => $this->db->_id($pickeduser['_id']));
                                }
                            }
                            // echo " <span style=\"color: green;\">PICKED!</span>";
                            return $user;
                        }

                        //	}
                        if ($properties['weekend']) {
                            if ($properties['dayornight'] == 'night') {
                                
                            }
                            if ($properties['dayornight'] == 'day') {
                                
                            }
                        }
                        if (!$properties['weekend']) {
                            if ($properties['dayornight'] == 'night') {
                                
                            }
                            if ($properties['dayornight'] == 'day') {
                                
                            }
                        }
                    }
                }
            }
        }

        return false;
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
            $prevShift = getPreviousShiftWorked($this->schedule, $this->db->_id($user['_id']), $shift['id']);
            $nextShift = getNextShiftWorked($this->schedule, $this->db->_id($user['_id']), $shift['id']);
            if ($prevShift && $nextShift) {
                $start = strtotime($shift['start']);
                $end = strtotime($shift['endreal']);
                $prevEnd = strtotime($prevShift['endreal'] . '+ 24 hours');
                $nextStart = strtotime($nextShift['start'] . '- 24 hours');
                if ($nextShift == null) {
                    $nextStart = strtotime($this->year . ' + 1 year');
                }
                if ($start < $prevEnd && $end > $nextStart) {
                    return true;
                } else {
                    return false;
                }
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
        $userMinHours = getUserMinHours($this->groupcode, $userId);

        if ($hours < $userMinHours) {
            $ret = true;
        }

        return $ret;
    }

    private function isUserOverMax($user, $shift) {
        $ret = false;
        $userId = $this->db->_id($user['_id']);
        $hours = $user['hours'] + (int) $shift['duration'];
        $userMaxHours = getUserMinHours($this->groupcode, $userId);

        if ($hours > $userMaxHours) {
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

    private function isShiftBlockable($user, $shift) {
        $blockable = false;
        if (isWeekend($shift['start'])) {
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
    
    private function canWork($user,$shift){
        
        
    }
    
    private function queryShift($user, $weekend = false, $night = false){
        
        $where = array('shiftId' => $shiftId, 'timeoffs' => array('$nin' => $this->db->_id($user['_id'])),'number' => array('$gt' => 0), 'day' => array('$ne' => '0'), 'groups' => array('$in' => array($user['group'])));
        if($weekend){
            $where['weekend'] = true;
        }
        if($night){
            $where['night'] = true;
        }
        $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => $where);
        $results = $this->db->find($arg);
        return $results[0];
        
    }

    private function getShift($shiftId) {
        $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'where' => array('id' => $shiftId, 'day' => array('$ne' => '0')));
        $results = $this->db->find($arg);
        return $results[0];
    }

    private function setShift($shift) {
        $number = $shift['number'];
        $number--;
        $shift['number'] = $number;
        $arg = array('col' => $this->groupcode, 'type' => 'tempShift', 'id' => $this->db->_id($shift['_id']), 'obj' => $shift);
        $results = $this->db->upsert($arg);
        return $results;
    }

    private function placeUserInShift($user, $shift) {
        $shiftId = $shift['id'];
        $tokens = 0;
        $newShift = $this->getShift($shift['id']);
        $newShift['users'][] = array('first_name' => $user['first_name'], 'last_name' => $user['last_name'], 'user_name' => $user['user_name'], 'id' => $this->db->_id($user['_id']));
        $update = $this->setShift($newShift);
        foreach ($this->users as $key => $us) {
            if ($this->db->_id($user['_id']) == $this->db->_id($us['_id'])) {
                $hours = $us['hours'];
                $newhours = $hours + $shift['duration'];
                $us['hours'] = $newhours;
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
            while ($canUserTakeShift == false) {
                // Gets the user's preferred shift
                $shiftId = $this->getUsersPreferredShift($user, $pass);
                $goodshift = false;
                $shiftpass = 0;
                while ($goodshift == false) {
                    // Gets the next instance of the user-preferred shift
                    $nextShift = $this->getFirstAvailableShift($user, $shiftId, $shiftpass, 5);

                    if (!$nextShift) {
                        break;
                    }
                    // Unless the user has requested this shift off, process
                    if (!$this->timeOffExistsForUser($user, $nextShift)) {
                        $goodshift = true;
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
                                if ($this->isCircadianMet($user, $nextShift)) {
                                    $this->placeUserInShift($user, $nextShift);
                                    $canUserTakeShift = true;
                                }

                                // Loop through the next available shifts based on the number 
                                // of blockable shifts preferred by the user

                                for ($i = 1; $i < $block; $i++) {
                                    $ucount = 0;
                                    $nextShift = $this->getNextShiftByDay($nextShift);
                                    if ($nextShift != null || $nextShift != "") {

                                        foreach ($nextShift['users'] as $u) {
                                            $ucount++;
                                        }
                                        if ($nextShift['number'] > $ucount) {
                                            if ($this->isCircadianMet($user, $nextShift)) {
                                                if (date('w', strtotime($nextShift['start'])) != '6') {
                                                    $this->placeUserInShift($user, $nextShift);
                                                    $canUserTakeShift = true;
                                                } else {
                                                    break;
                                                }
                                            }
                                        }
                                    } else {
                                        break;
                                    }
                                }
                            } else {
                                // The user does not want their shifts blocked, so simply process 
                                // the single instance of the shift and user
                                if ($this->isCircadianMet($user, $nextShift)) {
                                    $this->placeUserInShift($user, $nextShift);
                                    $canUserTakeShift = true;
                                }
                            } //end if ($this->isShiftBlockable($shift)
                        }// end if (!$this->isUserOverMax($user)
                    } // end if (!$this->timeOffExistsForUser($user,$shift))
                    //   $canUserTakeShift = true;
                    $shiftpass++;
                    if ($shiftpass >= $scheduleCount) {
                        break;
                    }
                } // end of second internal while
                $pass++; //increment the pass counter to see how many passes have taken place
                if ($pass >= $prefCount) {
                    break;
                }
            } // end while
        } // end foreach	
    }

    private function placeUsersInWeekends() {
        $scheduleCount = count($this->schedule);
        $scheduleCount--;
        $this->openShifts = $this->countOpenShifts();
        while ($this->openShifts > 0) {
            $this->sortUsers("tokens");
            foreach ($this->users as $user) {
                $maxgrouphours = maxGroupHours($this->users, $user['group']);
                if ($user['hours'] <= $maxgrouphours) {
                    $canUserTakeShift = false;
                    $pass = 0;
                    $prefCount = 0;
                    foreach ($user['preferences']['shifts'] as $prefshift) {
                        $prefCount++;
                    }
                    // Continue looping through shifts until a shift is found that matches a 
                    // user preference and the user can work it 

                    while ($canUserTakeShift == false) {
                        // Gets the user's preferred shift
                        $shiftId = $this->getUsersPreferredShift($user, $pass);
                        $goodshift = false;
                        $shiftpass = 0;
                        while ($goodshift == false) {
                            // Gets the next instance of the user-preferred shift
                            $nextShift = $this->getFirstAvailableShift($user, $shiftId, $shiftpass, '8');
                            if (!$this->isUserOverMax($user, $nextShift)) {
                                if (!$nextShift) {
                                    break;
                                }
                                // Unless the user has requested this shift off, process
                                if (!$this->timeOffExistsForUser($user, $nextShift)) {
                                    $goodshift = true;
                                    // If user has worked their max monthly hours, break
                                    // If the user has requested the system to block shifts, loop 
                                    // through the block at once and attempt to place the user 
                                    // into the complete block
                                    //get count of block
                                    $block = $this->isShiftBlockable($user, $nextShift);
                                    if ($block) {
                                        // Place the user into the first day of the block series
                                        // if circadian is met
                                        if ($this->isCircadianMet($user, $nextShift)) {
                                            $this->placeUserInShift($user, $nextShift);
                                            $canUserTakeShift = true;
                                        }

                                        // Loop through the next available shifts based on the number 
                                        // of blockable shifts preferred by the user

                                        for ($i = 1; $i < $block; $i++) {
                                            $ucount = 0;
                                            $nextShift = $this->getNextShiftByDay($nextShift);
                                            if ($nextShift != null || $nextShift != "") {

                                                foreach ($nextShift['users'] as $u) {
                                                    $ucount++;
                                                }
                                                if ($nextShift['number'] > $ucount) {
                                                    if ($this->isCircadianMet($user, $nextShift)) {
                                                        if (date('w', strtotime($nextShift['start'])) != '6') {
                                                            $this->placeUserInShift($user, $nextShift);
                                                            $canUserTakeShift = true;
                                                        } else {
                                                            break;
                                                        }
                                                    }
                                                }
                                            } else {
                                                break;
                                            }
                                        }
                                    } else {
                                        // The user does not want their shifts blocked, so simply process 
                                        // the single instance of the shift and user
                                        if ($this->isCircadianMet($user, $nextShift)) {
                                            $this->placeUserInShift($user, $nextShift);
                                            $canUserTakeShift = true;
                                        }
                                    } //end if ($this->isShiftBlockable($shift)
                                } // end if (!$this->timeOffExistsForUser($user,$shift))
                            }
                            $shiftpass++;
                            if ($shiftpass >= $scheduleCount) {
                                break;
                            }
                        } // end of second internal while   

                        $pass++; //increment the pass counter to see how many passes have taken place
                        if ($pass >= $prefCount) {
                            break;
                        }
                    } // end while 
                }
            } // end foreach	
            if ($canUserTakeShift == false) {
                break;
            }
        }
    }

    private function placeUsersInRemainingShifts() {
        // -- Now that users have their preferred shifts, start rules-based checks and schedule for real
        $maxUsers = count($users);
        // Loop through shifts within schedule
        foreach ($this->schedule as $shift) {

            // While there are open slots in the current shift
            $numSlotsOpen = $this->getNumOfSlotsOpenForShift($shift);
            for ($i = 0; $i <= $numSlotsOpen; $i++) {
                $canUserTakeShift = false;
                $placementCount = 0;
                while ($canUserTakeShift == false) {
                    $user = $this->getUsersByShiftPreference($shift, $placementCount);

                    // Unless the user has requested this shift off, process
                    if (!$this->timeOffExistsForUser($user, $shift)) {
                        if (!$this->isUserOverMax($user)) {
                            $previous = $this->getUsersPreferredShift($user);

                            // Is circadian met?
                            if ($this->isCircadianMet($previous)) {

                                // Is the shift a day or night shift
                                $dayOrNight = $this->isShiftDayOrNight($previous);
                                if ($dayOrNight == "Day") {

                                    // If the previous shift was a night shift, make sure they can work this day shift
                                    if ($this->getPreviousShiftByDay($day, $shift) == "Night") {
                                        if (($shift['endreal'] - $previous) >= $user['preferences']['shiftAfterNight']) {
                                            $this->placeUserInShift($user, $shift);
                                        } else {
                                            break; // break from getPreviousShiftByDay check
                                        }
                                    } // end if ($this->getPreviousShiftByDay($day,$shift) == "Night")

                                    if ($this->isShiftWeekendOrWeekday($shift)) {
                                        // Run some kind of weekend normalization checks here
                                        // If normalization code checks out, schedule the user within this shift
                                        //$this->placeUserInShift($user, $shift);
                                    } // end if ($this->isShiftWeekendOrWeekday($shift)
                                } // end if ($dayOrNight == "Day")
                            } // end if ($this->isCircadianMet($previous))
                        } // end if (!$this->isUserOverMax($user))
                    } // end if (!$this->timeOffExistsForUser($user, $shift))
                    $placementCount++; //increment the pass counter to see how many passes have taken place
                    if ($placementCount > $maxUsers) {
                        break;
                    }
                }
            } // end while ($numSlotsOpen > 0)
        } // foreach ($shifts as $shift)
    }

    public function staffSchedule() {
        $this->sortUsers("both");
        $this->placeUsersInPreferredShifts();

        $this->placeUsersInWeekends();
        //$this->placeUsersInRemainingShifts();










        /*
          $newScheduleArray = array();
          $db = new MONGORILLA_DB;
          $maxconsecdays = getConfig($this->groupcode, "maxConsecWorkingDays");
          foreach ($this->schedule as $skey => $shift) {
          //Collect stats for each shift
          $FTE = (int) $shift['number'];
          if ($FTE == null) {
          $FTE = 1;
          }

          if ($FTE >= count($shift['users'])) {
          $properties = $this->setShiftProperties($shift);
          $allusers = $this->users;
          shuffle($allusers);

          for ($i = 1; $i <= $FTE; $i++) {
          $pickeduser = null;
          $maxgrouphours = maxGroupHours($this->users, $properties['group']);
          $maxpreferences = maxPreference($this->users, $properties['group']);
          $averagehours = avgHours($this->users, $properties['group']);

          foreach ($allusers as $userKey => $user) {
          if ($properties['group'] == $user['group']) {
          // echo "<br />-------------";
          // echo "<br /> SHIFT: " . $shift['shiftName'] . " - " . $shift['id'];
          // echo "<br /> group: " . $user['group'];
          // echo "<br /> user: " . $user['user_name'];
          // echo "<br /> totalhrs: " . $user['hours'];
          // echo "<br /> grphours: " . $maxgrouphours;
          // echo "<br /> pref: " . $maxpreferences;
          // echo "<br /> weight: " . $user['weight'];
          // echo "<br /> averagehours: " . $averagehours;
          // echo "<br /> was picked:";

          if ($user['hours'] <= $maxgrouphours) {
          $current = $user['hours'];
          $diff = $current + $properties['duration'];

          if ($diff <= $user['max_hours']) {
          $block = 1;

          // Loop through the total block number for the user and attempt to fill the block of days
          for ($i = 0; $i < $block; $i++) {
          $pickeduser = $this->processUserForShift($properties, $shift, $user, $maxgrouphours, $maxpreferences, $maxconsecdays);
          unset($allusers[$userKey]);
          }

          }
          }
          }
          }

          if ($pickeduser == null) {
          //$pickeduser = array('first_name' => '', 'last_name' => '', 'user_name' => 'Open', 'id' => '');
          }

          if ($pickeduser != null) {
          $shift['users'][] = array('first_name' => $pickeduser['first_name'], 'last_name' => $pickeduser['last_name'], 'user_name' => $pickeduser['user_name'], 'id' => $this->db->_id($pickeduser['_id']));
          }
          } // for ($i = 1; $i <= $FTE; $i++)

          $this->schedule[$skey] = $shift;
          } //if ($FTE > count($shift['users']))

          $this->updateSchedule();
          } // foreach

         */
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


        $obj = array('schedule' => $this->schedule, 'active' => 1);
        $arg = array('col' => $this->groupcode, 'type' => 'schedule', 'obj' => $obj, 'id' => $this->scheduleId);
        $results = $this->db->upsert($arg);
        print_r($results);
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

    public function sortUsers($sortType) {
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

        foreach ($this->users as $user) {
            echo $user['priority'] . " - " . $user['tokens'] . "<br />";
        }
    }

    /*
      Name: sortUsersByTokens
      Description: Takes the users object and sorts them
      based on number of tokens ASC
     */

    private function sortUsersByTokens() {
        $tokens = array();
        foreach ($this->users as $key => $row) {
            $tokens[$key] = $row['tokens'];
        }

        array_multisort($tokens, SORT_ASC, $this->users);

        foreach ($this->users as $user) {
            echo $user['priority'] . " - " . $user['tokens'] . "<br />";
        }
    }

}

?>