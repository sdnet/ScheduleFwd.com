<?php

include('cws.php');
require_once('classes/schedule.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$group = $_POST['group'];
if (isset($_POST['id'])) {
    $userId = $_POST['id'];
} else {
    $userId = $_SESSION['_id'];
}
if (isset($_POST['month'])) {
    $tmonth = $_POST['month'];
    $tyear = $_POST['year'];
}

$isAdmin = false;

// check session
if (VerifySession($sessionId, $groupcode, $userId, 'User') == true) {
    if ($groupcode == "" || $userId == null) {
        $message = "emptyFields";
    } else {
        $month = date('m', strtotime($tmonth . "/01/" . $tyear));
        $year = date('Y', strtotime($tmonth . "/01/" . $tyear));
        $timeOff = getTimeOffByUserId($groupcode, $userId, $month, $year);
        if (getRoleById($groupcode, $userId) == 'Admin') {
            $isAdmin = true;
        }

        $arg = array('col' => "$groupcode", 'type' => 'user', 'id' => $userId);
        $result = $db->find($arg);
        $userGroup = $result[0]['group'];
        if (isset($group)) {
            $userGroup = $group;
        }
        $where = array('active' => 1, 'groups' => array('$in' => array($userGroup)));
        $arg = array('col' => "$groupcode", 'type' => 'shift', 'where' => $where);


        $results = $db->find($arg);
        if ($results != null) {
            //make the schedule for this month
            $today = time();

            if ($tmonth == null) {

                $year = date('Y', strtotime("+2 months", $today));
                $month = date('m', strtotime("+2 months", $today));
            }
			
			$where = array('year' => $year, 'month' => $month, 'userId' => $userId);
			$arg = array('col' => "$groupcode", 'type' => 'expectedShifts',  'where' => $where);
			$result = $db->find($arg);
			if($result != null){
				$expectedShifts = $result[0]['shifts'];		
			}else{
				$year1 = date('Y', strtotime("+1 month", $today));
				$month1 = date('m', strtotime("+1 month", $today));
				$where = array('year' => $year1, 'month' => $month1, 'userId' => $userId);
				$arg = array('col' => "$groupcode", 'type' => 'expectedShifts',  'where' => $where);
				$result = $db->find($arg);
				if($result != null){
				$expectedShifts = $result[0]['shifts'];	
				}else{
				$expectedShifts = 12;	
				}
			}
            $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $dates;
            for ($i = 1; $i <= $days; $i++) {
                $day = sprintf("%02s", $i);
                $dates[] = $day;
            }
            $scheduleDays;
            $i = 0;

            foreach ($dates as $date) {
                $tempDate = $year . "-" . $month . "-" . $date;
                $formDate = "" . $month . "/" . $date . "/" . $year . " 01:01:01";
                $weekday = date('l', strtotime($formDate));
                $timeOffArray = array();
				
                foreach ($timeOff as $time) {
					
                    foreach ($time['time_off'] as $key => $value) {
                        $statusArray[] = array('date' => $key, 'id' => $value, 'status' => $time['status'], 'priority' => $time['priority'], 'mustwork' => $time['mustwork']);
                    }
                    foreach ($time['time_off'] as $key => $value) {
                        if ($key == $tempDate) {
                            $timeOffArray[] = $value;
                        }
                    }
                }
                foreach ($results as $shift) {
                    $timeOffValue = 0;
                    $status = 0;
                    $priority = 0;
					$mustwork = "";

                    if (in_array($db->_id($shift['_id']), $timeOffArray)) {
                        $timeOffValue = 1;
                    }
                    $currId = $db->_id($shift['_id']);

                    foreach ($statusArray as $arr) {
                        if ($arr['id'] == $currId && $arr['date'] == trim($tempDate)) {
                            if ($arr['status'] == 'Approved') {
                                $status = 1;
                            } elseif ($arr['status'] == 'Disapproved') {
                                $status = -1;
                            } else {
                                $status = 0;
                            }
                            $priority = $arr['priority'];
							$mustwork = $arr['mustwork'];
                        }
                    }

                    if (in_array($weekday, $shift['days'])) {
                        $i++;
                        $start = stringReplace(2, ":", $shift['start']);
                        $end = stringReplace(2, ":", $shift['end']);
                        $scheduleDays[] = array('id' => $i,
                            'title' => "",
                            'start' => "$start:00 - $end:00",
                            'date' => "$tempDate ",
                            'allDay' => false,
                            'groups' => $shift['groups'],
                            'shiftName' => "" . $shift['name'] . "",
                            'users' => "",
                            'priority' => $priority,
							'mustwork' => $mustwork,
                            'status' => $status,
                            'timeoff' => $timeOffValue,
                            'day' => "$weekday",
                            'shiftId' => $db->_id($shift['_id'])
                        );
                    }
                }
            }


            $schedule = $scheduleDays;

            $data = $schedule;
            $message = "success";
        } else {
            $message = "noRecords";
        }
    }
} else {
    //return auth failure
    $message = "authFailure";
}

if ($format == 'dt') {
    $data = dtFormat($data);
} else {
    
}
//if Supported the front end uncomment this
//echo json_encode(array('message' => $message, 'data' => array('data' => $data, 'expectedShifts' => $expectedShifts)));
echo json_encode(array('message' => $message, 'data' => $data));
?>