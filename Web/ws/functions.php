<?php

require_once('' . $_SERVER['DOCUMENT_ROOT'] . '/core/mg_base.class.php');

function VerifySession($sessionId, $groupcode, $userId = false, $role = false) {
    if ($sessionId == null || $groupcode == null) {
        return false;
    } else {
        $db = new MONGORILLA_DB;
        $where = array('sessionId' => $sessionId, 'groupcode' => $groupcode);
        $arg = array('col' => 'sessions', 'type' => 'session', 'where' => $where, 'limit' => 1);
        $results = $db->find($arg);
        if ($results != null) {
            if ($role == false) {
                return true;
            } elseif ($role == 'Admin') {
                $args = array('col' => $groupcode, 'id' => $userId, 'type' => 'user');
                $uresult = $db->find($args);
                foreach ($uresult as $result) {
                    if ($result['role'] == 'Admin') {
                        return true;
                    } else {
                        return false;
                    }
                }
            } elseif ($role == 'User') {
                $args = array('col' => $groupcode, 'id' => $userId, 'type' => 'user');
                $uresult = $db->find($args);
                foreach ($uresult as $result) {
                    if ($result['role'] == 'User' || $result['role'] == 'Admin' || $result['role'] == 'Scribe') {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        } else {
            return false;
        }
    }
}

/**
  Validate an email address.
  Provide email address (raw input)
  Returns true if the email address has the email
  address format and the domain exists.
 */
function ValidEmail($email) {
    $isValid = true;
    $atIndex = strrpos($email, "@");
    if (is_bool($atIndex) && !$atIndex) {
        $isValid = false;
    } else {
        $domain = substr($email, $atIndex + 1);
        $local = substr($email, 0, $atIndex);
        $localLen = strlen($local);
        $domainLen = strlen($domain);
        if ($localLen < 1 || $localLen > 64) {
            // local part length exceeded
            $isValid = false;
        } else if ($domainLen < 1 || $domainLen > 255) {
            // domain part length exceeded
            $isValid = false;
        } else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
            // local part starts or ends with '.'
            $isValid = false;
        } else if (preg_match('/\\.\\./', $local)) {
            // local part has two consecutive dots
            $isValid = false;
        } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
            // character not valid in domain part
            $isValid = false;
        } else if (preg_match('/\\.\\./', $domain)) {
            // domain part has two consecutive dots
            $isValid = false;
        } else if
        (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
            // character not valid in local part unless 
            // local part is quoted
            if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
                $isValid = false;
            }
        }
        if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
            // domain not found in DNS
            $isValid = false;
        }
    }
    return $isValid;
}

function flattenByRemoveingIntKeys($array) {
    foreach ($array as $key => $val) {
        if (is_array($val))
            $array[$key] = flattenByRemoveingIntKeys($val);
        else if (is_numeric($key)) {
            $array .= $val[$key];
            unset($array[$key]);
        }
    }
}

function mobileFormat($data) {
    return json_encode($data);
}

function dtFormat($data) {

    $final = array();
    foreach ($data as $key => $value) {
        $final[] = $value;
    }

    $dtarray = $final;


    return $dtarray;
}

function hourCheck($min, $max) {
    if ($min >= 0 && $max > 0) {
        if (is_int((int) $min) && is_int((int) $max)) {
            if ($min <= $max) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function getDeviceTokens($groupcode, $userId) {
    $db = new MONGORILLA_DB;
    $args = array('col' => $groupcode, 'id' => $userId, 'type' => 'user', 'limit' => 1);
    $uresult = $db->find($args);
    if ($uresult != null) {
        foreach ($uresult as $result) {
            return $result['device_tokens'];
        }
    } else {
        return false;
    }
}

function getRoleById($groupcode, $userId) {
    $db = new MONGORILLA_DB;
    $args = array('col' => $groupcode, 'id' => $userId, 'type' => 'user', 'limit' => 1);
    $uresult = $db->find($args);
    if ($uresult != null) {
        foreach ($uresult as $result) {
            return $result['role'];
        }
    } else {
        return false;
    }
}

function getConfig($groupcode, $optional = null) {
    $db = new MONGORILLA_DB;
    $args = array('col' => $groupcode, 'type' => 'config', 'limit' => 1);
    $result = $db->find($args);
    if ($result != null) {
        if ($optional != null) {
            // return $result;
            return $result[0][$optional];
        } else {
            return $result[0];
        }
    } else {
        return false;
    }
}

function stringReplace($x, $r, $str) {
    $out = "";
    $temp = substr($str, $x);
    $out = substr_replace($str, "$r", $x);
    $out .= $temp;
    return $out;
}

function mailIt($to, $subject, $message) {

    $headers = "MIME-Version: 1.0\r\n"
            . "Content-Type: text/plain; charset=utf-8\r\n"
            . "Content-Transfer-Encoding: 8bit\r\n"
            . "From: Schedule Forward <support@scheduleforward.com>\r\nReply-To: Schedule Forward <support@scheduleforward.com>\r\n"
            . "X-Mailer: PHP/" . phpversion();
    $head = "********* This is an automated message from Schedule Forward, please do not reply **********\r\n";
    $foot = "

--------
Thank you,
The Schedule Forward Team";
    $message = $head . $message . $foot;
    if (mail($to, $subject, $message, $headers)) {
        return true;
    } else {
        return false;
    }
}

function startsWith($haystack, $needle, $case=true) {
    if ($case) {
        return (strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
    }
    return (strcasecmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
}

function getUserId($username, $groupcode) {
    $db = new MONGORILLA_DB;
    $username = new MongoRegex("/^" . $username . "$/i");
    $args = array('col' => $groupcode, 'type' => 'user', 'where' => array('user_name' => $username, 'active' => 1));
    $result = $db->find($args);
    if ($result != null) {
        return $db->_id($result[0]['_id']);
    } else {
        return false;
    }
}

function getUserById($groupcode, $userId) {
    $db = new MONGORILLA_DB;
    $args = array('col' => $groupcode, 'type' => 'user', 'id' => $userId, 'limit' => 1);
    $uresult = $db->find($args);
    if ($uresult != null) {
        return $uresult[0];
    } else {
        return false;
    }
}

function getPreferredShiftsByUser($groupcode, $userId) {
    $db = new MONGORILLA_DB;
    $args = array('col' => $groupcode, 'type' => 'user', 'id' => $userId, 'limit' => 1);
    $uresult = $db->find($args);
    if ($uresult != null) {
        return $uresult[0]['preferences']['shifts'];
    } else {
        return false;
    }
}

function getExtUserById($groupcode, $userId) {
    $db = new MONGORILLA_DB;
    $args = array('col' => $groupcode, 'type' => 'extUser', 'id' => $userId, 'limit' => 1);
    $uresult = $db->find($args);
    if ($uresult != null) {
        return $uresult[0];
    } else {
        return false;
    }
}

function getRoleIds($role, $groupcode) {
    $db = new MONGORILLA_DB;
    $args = array('col' => $groupcode, 'type' => 'user', 'where' => array('role' => $role));
    $uresult = $db->find($args);
    if ($uresult != null) {
        foreach ($uresult as $result) {
            $list[] = $db->_id($result['_id']);
        }
        return $list;
    } else {
        return false;
    }
}

function getEvent($groupcode, $event) {
    $db = new MONGORILLA_DB;
    $args = array('col' => $groupcode, 'type' => 'event', 'where' => array('event' => $event));
    $result = $db->find($args);
    if ($result != null) {
        return $result[0];
    } else {
        return false;
    }
}

function getGroupIds($group, $groupcode) {
    $db = new MONGORILLA_DB;
    $args = array('col' => $groupcode, 'type' => 'user', 'where' => array('group' => $group));
    $uresult = $db->find($args);
    if ($uresult != null) {
        foreach ($uresult as $result) {
            $list[] = $db->_id($result['_id']);
        }
        return $list;
    } else {
        return false;
    }
}

function createAlert($userId, $role, $message, $severity, $groupcode) {
    $db = new MONGORILLA_DB;
    $obj = array('role' => $role, 'userId' => $userId, 'message' => $message, 'severity' => $severity, 'active' => 1);
    $data = $db->upsert(array('col' => $groupcode, 'type' => 'alert', 'obj' => $obj));
    if ($data != null) {
        return true;
    } else {
        return false;
    }
}

function getTime() {
    return date("Y-m-d H:i:s");
}

function getUserSchedule($userId, $groupcode, $limit = 6, $next = 'next', $time = null) {
    $db = new MONGORILLA_DB;
    if ($next == 'next') {
        $comp = '$gt';
    } else {
        $comp = '$lt';
    }
    if ($time != null) {
        
    } else {
        $time = getTime();
    }
    $args = array('col' => $groupcode, 'type' => 'schedule', 'where' => array('schedule.start' => array("$comp" => $time), 'schedule.users.id' => $userId), 'limit' => $limit, 'order_by' => 'natural', 'order' => 'DESC');
    $result = $db->find($args);
    return $result;
}

function getForecastSchedule($groupcode, $limit = 2, $next = 'next') {
    $db = new MONGORILLA_DB;
    if ($next == 'next') {
        $comp = '$gt';
    } else {
        $comp = '$lt';
    }
    $args = array('col' => $groupcode, 'type' => 'schedule', 'where' => array('schedule.start' => array("$comp" => getTime())), 'limit' => $limit, 'order_by' => 'natural', 'order' => 'DESC');
    $result = $db->find($args);
    return $result;
}

function getShiftFromSchedule($groupcode, $shiftId, $scheduleId) {
    $db = new MONGORILLA_DB;
    /* $args = array('col' => $groupcode, 'type' => 'schedule', 'id' => $scheduleId);
      $result = $db->find($args);
      foreach ($result[0]['schedule'] as $shift) {
      if ($shift['id'] == $shiftId) {
      return $shift;
      break;
      }
      } */

    $args = array('col' => $groupcode, 'type' => 'tempShift', 'where' => array('id' => (int) $shiftId, 'scheduleId' => $scheduleId), 'limit' => 1);
    $result = $db->find($args);
    return convertTimes($result[0]);
}

function convertTimes($shift) {
    $shift['start'] = date('Y-m-d H:i:s', $shift['start']->sec);
    $shift['endreal'] = date('Y-m-d H:i:s', $shift['endreal']->sec);

    return $shift;
}

function getMonthYearFromScheduleId($groupcode, $scheduleId) {
    $db = new MONGORILLA_DB;
    $monthAndYear = array();
    $args = array('col' => $groupcode, 'type' => 'schedule', 'id' => $scheduleId, $where = array());
    $results = $db->find($args);
    if ($results) {
        foreach ($results as $result) {
            $monthAndYear['month'] = $result['month'];
            $monthAndYear['year'] = $result['year'];
        }
    }

    return $monthAndYear;
}

function getShiftTraded($groupcode, $shiftId, $scheduleId) {
    $db = new MONGORILLA_DB;
    $where = array('scheduleId' => $scheduleId, 'status' => 'Accepted', '$or' => array(array('original_shift' => "$shiftId"), array('target_shift' => "$shiftId")));
    $args = array('col' => $groupcode, 'type' => 'trade', 'where' => $where);
    $result = $db->find($args);
    if ($result == null) {
        return false;
    } else {
        return true;
    }
}

function getScheduleById($groupcode, $scheduleId) {
    $db = new MONGORILLA_DB;
    $args = array('col' => $groupcode, 'type' => 'schedule', 'id' => $scheduleId);
    $result = $db->find($args);
    if ($result != null) {
        return $result[0];
    } else {
        return false;
    }
}

function checkAccept($groupcode, $tradeID) {
    $db = new MONGORILLA_DB;
    $args = array('col' => $groupcode, 'type' => 'trade', 'id' => $tradeID);
    $result = $db->find($args);
    if ($result != null) {
        $scheduleId = $result[0]['scheduleId'];
        if ($result[0]['original_shift'] != null) {
            $oShift = getShiftFromSchedule($groupcode, $result[0]['original_shift'], $scheduleId);
            foreach ($oShift['users'] as $user) {
                if (!in_array($result[0]['original_user'], $user)) {
                    return false;
                }
            }
        }
        if ($result[0]['target_shift'] != null) {
            $tShift = getShiftFromSchedule($groupcode, $result[0]['target_shift'], $scheduleId);
            foreach ($tShift['users'] as $user) {
                if (!in_array($result[0]['target_user'], $user)) {
                    return false;
                }
            }
        }
        return true;
        //$oShift = $result[0]['original_shift'];
        //$tShift = $result[0]['target_shift'];
        //$scheduleId = $result[0]['scheduleId'];
        //$date = strtotime(date('m-d-Y h:i', $result[0]['date_created']->sec));
        //$where = array('active' => 1, 'status' => 'Accepted', 'scheduleId' => $result[0]['scheduleId'], '$or' => array(array('target_shift' => $tShift), array('original_shift' => $oShift)));
        //$args = array('col'=>$groupcode,'type' => 'trade', 'where' => $where);
        //$results = $db->find($args);
        //if($results == null){
        //	return true;
        //}else{
        //	$state = true;
        //	foreach($results as $rez){
        //		if(strtotime(date('m-d-Y h:i', $rez['date_created']->sec)) > $date){
        //			$state = false;	
        //		}
        //	}
        //	return $state;
        //}
    } else {
        return false;
    }
}

function getShiftById($groupcode, $shiftId) {
    $db = new MONGORILLA_DB;
    $args = array('col' => $groupcode, 'type' => 'shift', 'id' => $shiftId);
    $result = $db->find($args);
    if ($result != null) {
        return $result[0];
    } else {
        return false;
    }
}

function getTimeOffByUserId($groupcode, $userId, $month = null, $year = null) {
    $db = new MONGORILLA_DB;
    if ($year == null) {
        $year = '' . date('Y') . '';
    }
    if ($month == null) {
        $month = '' . date('m') . '';
    }
    $where = array('year' => "$year", 'month' => "$month", 'userId' => $userId, 'mustwork' => array('$ne' => true));
    $args = array('col' => $groupcode, 'type' => 'timeoff', 'where' => $where);
    $result = $db->find($args);
    if ($result != null) {
        return $result;
    } else {
        return false;
    }
}

function getRequestedShiftsByUserId($groupcode, $userId, $month = null, $year = null) {
    $db = new MONGORILLA_DB;
    if ($year == null) {
        $year = '' . date('Y') . '';
    }
    if ($month == null) {
        $month = '' . date('m') . '';
    }
    $where = array('year' => "$year", 'month' => "$month", 'userId' => $userId);
    $args = array('col' => $groupcode, 'type' => 'expectedShifts', 'where' => $where);
    $result = $db->find($args);
    if ($result != null) {
        return $result[0]['shifts'];
    } else {
        return false;
    }
}

function isTimeoffRequestedByUserIdAndShiftId($groupcode, $userId, $date, $month, $year, $shiftId) {
    $ret = false;
    $db = new MONGORILLA_DB;
    $where = array('year' => $year, 'month' => $month, 'time_off' => array('$in' => array($date => $shiftId)), 'userId' => $userId, 'active' => 'Approved', 'mustwork' => array('$ne' => true));
    $args = array('col' => $groupcode, 'type' => 'timeoff', 'where' => $where);
    $result = $db->find($args);
    if ($result != null) {
        $ret = true;
    }

    return $ret;
}

function getShiftsByUserId($groupcode, $userId) {
    $db = new MONGORILLA_DB;
    $args = array('col' => $groupcode, 'type' => 'user', 'id' => $userId);
    $result = $db->find($args);
    $group = $result[0]['group'];
    $where = array('active' => 1, 'groups' => array('$in' => array($group)));
    $args = array('col' => $groupcode, 'type' => 'shift', 'where' => $where);
    $results = $db->find($args);
    if ($results != null) {
        return $results;
    } else {
        return false;
    }
}

function getCurrentSchedule($groupcode) {
    $db = new MONGORILLA_DB;
    $year = date('Y');
    $month = date('m');
    $args = array('col' => $groupcode, 'type' => 'schedule', 'where' => array('month' => $month, 'year' => $year), 'limit' => 1, 'order_by' => 'natural', 'order' => 'DESC', 'keys' => array('_id' => 1));
    $result = $db->find($args);
    return $result;
    if ($result != null) {
        return $result;
    } else {
        return false;
    }
}

function getHoursByUserId($groupcode, $userId, $scheduleId) {
    $db = new MONGORILLA_DB;
    $hours = 0;
    // $results = getScheduleById($groupcode, $scheduleId);
    $where = array('users.id' => array('$in' => array($userId)), 'scheduleId' => $scheduleId);
    $args = array('col' => $groupcode, 'type' => 'tempShift', 'where' => $where);
    $shifts = $db->find($args);
    foreach ($shifts as $shift) {
        $hours = $hours + $shift['duration'] / 60;
    }
    if ($shifts != null) {
        return $hours;
    } else {
        return false;
    }
}

function getUserMaxHours($groupcode, $userId) {
    $db = new MONGORILLA_DB;
    $args = array('col' => $groupcode, 'type' => 'user', 'id' => $userId, 'limit' => 1);
    $uresult = $db->find($args);
    $result = $uresult[0];
    if ($result != null) {
        if (!isset($result['max_hours']) || $result['max_hours'] == null) {
            $where = array('name' => $result['group'], 'active' => 1);
            $args = array('col' => $groupcode, 'type' => 'group', 'where' => $where, 'limit' => 1);
            $uresult = $db->find($args);
            $maxhours = $uresult[0]['max_hours'];
        } else {
            $maxhours = $result['max_hours'];
        }
        return $maxhours;
    } else {
        return false;
    }
}

function getUserMinHours($groupcode, $userId) {
    $db = new MONGORILLA_DB;
    $args = array('col' => $groupcode, 'type' => 'user', 'id' => $userId, 'limit' => 1);
    $uresult = $db->find($args);
    $result = $uresult[0];
    if ($result != null) {
        if (!isset($result['min_hours']) || $result['min_hours'] == null) {
            $where = array('name' => $result['group'], 'active' => 1);
            $args = array('col' => $groupcode, 'type' => 'group', 'where' => $where, 'limit' => 1);
            $uresult = $db->find($args);
            $maxhours = $uresult[0]['min_hours'];
        } else {
            $maxhours = $result['min_hours'];
        }
        return $maxhours;
    } else {
        return false;
    }
}

function getTotalHoursByUserId($groupcode, $userId, $year = null) {
    $db = new MONGORILLA_DB;
    if ($year == null) {
        $year = date('Y');
    }
    $hours = 0;
    $where = array('year' => $year);
    $args = array('col' => $groupcode, 'type' => 'schedule', 'where' => $where);
    $results = $db->find($args);
    foreach ($results as $result) {
        $shifts = $result['schedule'];
        foreach ($shifts as $shift) {
            foreach ($shift['users'] as $user) {
                if ($user['id'] == $userId) {

                    $t1 = strtotime($shift['start']);
                    $t2 = strtotime($shift['endreal']);
                    $t3 = $t2 - $t1;
                    $hours = floor($t3 / 60 / 60);
                }
            }
        }
    }
    if ($results != null) {
        return $hours;
    } else {
        return false;
    }
}

function getMonthHoursByUserId($groupcode, $userId, $month = null, $year = null) {
    $db = new MONGORILLA_DB;
    if ($year == null) {
        $year = date('Y');
    }
    if ($month == null) {
        $month = date('m');
    }
    $hours = 0;
    $where = array('year' => $year, 'month' => $month);
    $args = array('col' => $groupcode, 'type' => 'schedule', 'where' => $where);
    $results = $db->find($args);
    foreach ($results as $result) {
        $shifts = $result['schedule'];
        foreach ($shifts as $shift) {
            foreach ($shift['users'] as $user) {
                if ($user['id'] == $userId) {

                    $t1 = strtotime($shift['start']);
                    $t2 = strtotime($shift['endreal']);
                    $t3 = $t2 - $t1;
                    $hours = floor($t3 / 60 / 60);
                }
            }
        }
    }
    if ($results != null) {
        return $hours;
    } else {
        return false;
    }
}

function getMonthShiftsByUserId($groupcode, $userId, $month = null, $year = null) {
    $db = new MONGORILLA_DB;
    if ($year == null) {
        $year = date('Y');
    }
    if ($month == null) {
        $month = date('m');
    }
    $shiftcount = 0;
    $where = array('year' => '' . $year . '', 'month' => '' . $month . '');
    $args = array('col' => $groupcode, 'type' => 'schedule', 'where' => $where);
    $results = $db->find($args);
    foreach ($results as $result) {
        $shifts = $result['schedule'];
        foreach ($shifts as $shift) {
            foreach ($shift['users'] as $user) {
                if ($user['id'] == $userId) {

                    $shiftcount++;
                }
            }
        }
    }
    if ($results != null) {
        return $shiftcount;
    } else {
        return false;
    }
}

function getTotalShiftsByUserId($groupcode, $userId, $year = null) {
    $db = new MONGORILLA_DB;
    if ($year == null) {
        $year = date('Y');
    }

    $shiftcount = 0;
    $where = array('year' => $year);
    $args = array('col' => $groupcode, 'type' => 'schedule', 'where' => $where);
    $results = $db->find($args);
    foreach ($results as $result) {
        $shifts = $result['schedule'];
        foreach ($shifts as $shift) {
            foreach ($shift['users'] as $user) {
                if ($user['id'] == $userId) {
                    $shiftcount++;
                }
            }
        }
    }
    if ($results != null) {
        return $shiftcount;
    } else {
        return false;
    }
}

function getTotalTimeOffByUserId($groupcode, $userId, $year = null) {
    $db = new MONGORILLA_DB;
    if ($year == null) {
        $year = date('Y');
    }

    $timecount = 0;
    $where = array('time_off' => array('$in' => array($year)), 'userId' => $userId, 'status' => array('$ne' => 'Disapproved'), 'mustwork' => array('$ne' => true));
    $args = array('col' => $groupcode, 'type' => 'timeoff', 'where' => $where);
    $results = $db->find($args);
    foreach ($results as $result) {
        $timecount++;
    }
    return $timecount;
}

function getMonthTimeOffByUserId($groupcode, $userId, $month = null, $year = null) {
    $db = new MONGORILLA_DB;
    if ($year == null) {
        $year = date('Y');
    }
    if ($month == null) {
        $month = date('m');
    }

    $timecount = 0;
    $where = array('year' => $year, 'month' => $month, 'userId' => $userId, 'status' => array('$ne' => 'Disapproved'), 'mustwork' => array('$ne' => true));
    $args = array('col' => $groupcode, 'type' => 'timeoff', 'where' => $where);
    $results = $db->find($args);
    foreach ($results as $result) {
        $timecount++;
    }
    return $timecount;
}

function getUserCanWork($groupcode, $user, $shift) {
    $db = new MONGORILLA_DB;
    $hours = getConfig($groupcode, 'minHoursBetweenShifts');

//    $resultsi = getUserSchedule($userId, $groupcode, 2, 'previous', $starttime);
//    if ($resultsi != null) {
//        $i = 0;
//        $past = false;
//        $future = false;
//        foreach ($resultsi as $resulti) {
//            foreach ($resulti['schedule'] as $sched) {
//                if (($shiftId != null) && ($sched['id'] == $shiftId) && ($scheduleId == $db->_id($resulti['_id']))) {
//                    
//                } else {
//                    foreach ($sched['users'] as $k => $v) {
//                        if ($v['id'] == $userId) {
//                            if ((strtotime($sched['endreal']) > strtotime('' . $starttime . ' -' . $hours . ' hours')) && (strtotime($sched['endreal']) < strtotime('' . $endtime . ' +' . $hours . ' hours'))) {
//                                //print_r("past: " . date($sched['endreal']) . " > " . date('Y-m-d H:i:s', strtotime('' . $starttime . ' -' . $hours . ' hours')) . " || ");
//                                $past = true;
//                                break 3;
//                            }
//                            if ((strtotime($sched['start']) > strtotime('' . $starttime . ' -' . $hours . ' hours')) && (strtotime($sched['start']) < strtotime('' . $endtime . ' +' . $hours . ' hours'))) {
//                                //echo "future: " . date($sched['endreal']) . " > " . date('Y-m-d H:i:s', strtotime('' . $starttime . ' -' . $hours . ' hours')) . " || ";
//                                $future = true;
//                                break 3;
//                            }
//                        }
//                    }
//                }
//            }
//        }
//    }


    $results = false;
    //  $shift = getShiftFromSchedule($groupcode, $shiftId, $scheduleId);
    $start = new MongoDate(strtotime($shift['start'] . ' -' . $hours . ' hours'));
    $end = new MongoDate(strtotime($shift['endreal'] . ' +' . $hours . ' hours'));
    //if ($user['preferences']['circadian'] == '1') {
    //    $start = new MongoDate(strtotime($shift['start'] . ' - 24 hours'));
   // }
    $where = array('users.id' => array('$in' => array($db->_id($user['_id']))), '$nor' => array(
            array('start' => array('$gt' => $start, '$lt' => $end)),
            array('endreal' => array('$lt' => $start, '$gt' => $end))));
    $arg = array('col' => $groupcode, 'type' => 'tempShift', 'where' => $where);
    $results = $db->find($arg);
    if ($results == 0) {
        return true;
    } else {
        return false;
    }
}

function getUsersCanWork($groupcode, $shift) {
    $userList = array();
    $db = new MONGORILLA_DB;
    $hours = getConfig($groupcode, 'minHoursBetweenShifts');
    //$shift = convertTimes($shift);
    $start = new MongoDate(strtotime($shift['start'] . ' -' . $hours . ' hours'));
    $end = new MongoDate(strtotime($shift['endreal'] . ' +' . $hours . ' hours'));
    $where = array('$or' => array(
            array('start' => array('$gt' => $start, '$lt' => $end)),
            array('endreal' => array('$lt' => $start, '$gt' => $end))));
    $arg = array('col' => $groupcode, 'type' => 'tempShift', 'where' => $where, 'keys' => array('users' => 1));
    $results = $db->find($arg);

    foreach ($results as $result) {
        foreach ($result['users'] as $users) {
            $userList[] = $users['user_name'];
        }
    }
    array_unique($userList);
    return $userList;
}

function getUserCanWorkSchedule($groupcode, $userId, $starttime, $endtime, $shiftId, $schedule) {
    $db = new MONGORILLA_DB;
    $hours = getConfig($groupcode, 'minHoursBetweenShifts');
    //$results = getUserSchedule($userId,$groupcode,2,'next');
    //if($results != null){
    //	$i = 0;
    //	$future = false;
    //	foreach($results as $result){
    //		foreach($result['schedule'] as $sched){
    //			foreach($sched['users'] as $k=>$v){
    //				if($v['id'] == $userId){
    //					if(strtotime($sched['start']) > strtotime('' . $endtime . ' +' . $hours . ' hours') && )
    //						{
    //						echo "" . date($sched['start']) . " > " . date('Y-m-d H:i:s', strtotime('' . $endtime . ' +' . $hours . ' hours')) . "";
    //						$future = true;
    //						break 3;
    //					}
    //				}
    //			}
    //		}
    //	}
    //}
    if ($schedule != null) {
        $i = 0;
        $past = false;
        $future = false;
        foreach ($schedule as $sched) {
            if (($shiftId != null) && ($sched['id'] == $shiftId)) {
                
            } else {
                foreach ($sched['users'] as $k => $v) {
                    if ($v['id'] == $userId) {
                        if ((strtotime($sched['endreal']) > strtotime('' . $starttime . ' -' . $hours . ' hours')) && (strtotime($sched['endreal']) < strtotime('' . $endtime . ' +' . $hours . ' hours'))) {
                            //print_r("past: " . date($sched['endreal']) . " > " . date('Y-m-d H:i:s', strtotime('' . $starttime . ' -' . $hours . ' hours')) . " || ");
                            $past = true;
                            break 2;
                        }
                        if ((strtotime($sched['start']) > strtotime('' . $starttime . ' -' . $hours . ' hours')) && (strtotime($sched['start']) < strtotime('' . $endtime . ' +' . $hours . ' hours'))) {
                            //echo "future: " . date($sched['endreal']) . " > " . date('Y-m-d H:i:s', strtotime('' . $starttime . ' -' . $hours . ' hours')) . " || ";
                            $future = true;
                            break 2;
                        }
                    }
                }
            }
        }
    }

    if ($past == true || $future == true) {
        return false;
    } else {
        return true;
    }
}

function getLastFiveShifts($groupcode, $scheduleId) {
    $db = new MONGORILLA_DB;
    $args = array('col' => $groupcode, 'id' => $scheduleId, 'type' => 'schedule', 'limit' => 1);
    $uresult = $db->find($args);

    print_r($args);
    $result = $uresult[0];
    if ($result != null) {
        print_r($result);
    }
}

function getLastShifts($groupcode, $scheduleId, $date, $numShifts, $user) {
    $db = new MONGORILLA_DB;
    $args = array('col' => $groupcode, 'id' => $scheduleId, 'type' => 'schedule', 'limit' => 1);
    $uresult = $db->find($args);

    $result = $uresult[0];
    if ($result != null) {
        print_r($result['schedule']);
    }

    $old_date_timestamp = strtotime($date);
    $new_date = date('d', $old_date_timestamp);
}

function checkPreviousShiftLessThan($groupCode, $user, $startDate, $endDate) {

    $startDate1 = $startDate;
    $endDate1 = $endDate;

    $old_date_timestamp = strtotime($startDate);
    $month = date('m', $old_date_timestamp);
    $year = date('Y', $old_date_timestamp);

    $schdId = getScheduleId($groupCode, $month, $year);

    $uId = getUserId($user, $groupCode);

    $shiftArrayCurrent = getShiftsByUserFromSchedId($groupCode, $schdId, $uId);

    print_r("*******Begin*******\n");

    print_r($shiftArrayCurrent);

    print_r("********End*******\n");

    // get previous month's schedule

    $month = (int) $month;
    $year = (int) $year;

    if ($month == 1) {

        $month = '12';
        $year = $year - 1;
    } else {
        $month = $month - 1;
    }
    $month = (string) $month;
    $year = (string) $year;

    $schdId = getScheduleId($groupCode, $month, $year);

    $uId = getUserId($user, $groupCode);

    $shiftArrayPrevious = getShiftsByUserFromSchedId($groupCode, $schdId, $uId);

    $shiftArray = array();


    foreach ($shiftArrayPrevious as $shift) {
        $shiftArray[] = $shift;
    }
    foreach ($shiftArrayCurrent as $shift) {
        $shiftArray[] = $shift;
    }


    if (count($shiftArray) < 1) {
        return false;
    }

    foreach ($shiftArray as $shift) {
        print_r($shift['start'] . ' - ' . $shift['end'] . '<br>');
    }


    if (shiftFallsOnAShift($shiftArray, $startDate1, $endDate1)) {
        return true;
    } else {
        //check first if before first shift
        $compareShift = null;
        for ($i = 0; $i <= count($shiftArray); $i++) {
            if ($i < count($shiftArray) - 1) {
                $shift = $shiftArray[$i];

                if ((strtotime($shift['end']) <= strtotime($startDate)) && (strtotime($shift['end'] . ' + 12 hours') >= strtotime($startDate))) {
                    return true;
                }
            }
        }
        return false;
    }
}

function getScheduleId($groupcode, $month, $year) {

    $db = new MONGORILLA_DB;

    if (strlen($month) < 2) {
        $month = '0' . $month;
    }

    $args = array('col' => $groupcode, 'type' => 'schedule', 'where' => array('month' => $month, 'year' => $year), 'limit' => 1, 'order_by' => 'natural', 'order' => 'DESC');
    $result = $db->find($args);

    $id = $db->_id($result[0]['_id']);

    return $id;
}

function getShiftsByUserFromSchedId($groupcode, $scheduleId, $user) {
    $db = new MONGORILLA_DB;

    $args = array('col' => $groupcode, 'type' => 'schedule', 'id' => $scheduleId, 'limit' => 1);

    $result = $db->find($args);

    $shiftArray = array();

    if ($result != null) {

        foreach ($result as $schedules) {
            foreach ($schedules['schedule'] as $shift) {
                if (isset($shift['users']) && $shift['users'] != null) {
                    foreach ($shift['users'] as $aUser) {
                        if ($aUser['id'] == $user) {
                            $shiftArray[] = $shift;
                        }
                    }
                } else {
                    
                }
            }
        }
    }

    return $shiftArray;
}

function weekendNormalization($month, $year) {
    $db = new MONGORILLA_DB;
    $inMonth = array();
    $inYear = array();

    // First month and year combination
    array_push($inMonth, $month);
    array_push($inYear, $year);
    $month--;

    // Second month and year combination
    if ($month < 1) {
        $month = 12;
        $year--;
    }
    array_push($inMonth, $month);
    array_push($inYear, $year);
    $month--;

    // Third month and year combination
    if ($month < 1) {
        $month = 12;
        $year--;
    }
    array_push($inMonth, $month);
    array_push($inYear, $year);

    $userHours = array();
    $db = new MONGORILLA_DB;

    for ($i = 0; $i < count($inMonth); $i++) {
        $where = array('month' => '' . $inMonth[$i] . '', 'year' => '' . $inYear[$i] . '');
        $arg = array('col' => "$groupcode", 'type' => 'schedule', 'where' => $where);
        $results = $db->find($arg);

        foreach ($results[0]['schedule'] as $shift) {
            $date = $shift['start'];
            $users = $shift['users'];
            if ($users) {
                foreach ($users as $user) {
                    $username = $user['user_name'];
                    $fname = $user['first_name'];
                    $lname = $user['last_name'];
                    $userHours[$username]['id'] = "";
                    if (isWeekend($date) == true) {
                        if (isset($userHours[$username]['numWeekendShifts'])) {
                            $userHours[$username]['numWeekendShifts'] = $userHours[$username]['numWeekendShifts'] + 1;
                            $userHours[$username]['weekendPercentage'] = (int) ($userHours[$username]['numWeekendShifts'] / 3);
                        } else {
                            $userHours[$username]['numWeekendShifts'] = 0;
                            $userHours[$username]['weekendPercentage'] = 0;
                        }
                    }
                }
            }
        }
    }

    return $userHours;
}

function shiftFallsOnAShift($shiftArray, $startTime, $endTime) {
    foreach ($shiftArray as $shift) {
        $startTime1 = strtotime($startTime);
        $endTime1 = strtotime($endTime);
        $theStart = strtotime($shift['start']);
        $theEnd = strtotime($shift['end']);

        if (($theStart <= $startTime1) && ($startTime1 <= $theEnd)) {
            return true;
        }

        if (($theStart <= $endTime1) && ($endTime1 <= $theEnd)) {
            return true;
        }
    }
    return false;
}

//***************
//Checks to see if string date is a weekend
function isWeekend($date) {
    $weekend = false;
    if (date('N', strtotime($date)) >= 6) {
        $weekend = true;
    }
    return $weekend;
}

function isNight($startdate, $enddate) {
    $timestamp = strtotime($startdate);
    $day = date('d', $timestamp);
    $month = date('m', $timestamp);
    $year = date('Y', $timestamp);
//    $nightstamp = strtotime('' . $month . '-' . $day . '-' . $year . ' 3:00am');
//    if ($nightstamp > strtotime($startdate) && $nightstamp < strtotime($enddate)) {
//        $dayornight = true;
//    } else {
//        $dayornight = false;
//    }
    $nightstamp = strtotime('' . $year . '-' . $month . '-' . $day . ' 19:00:00');

    if ($nightstamp <= $timestamp) {
        return true;
    } else {
        return false;
    }
}

function maxGroupHours($users, $group) {
    $hours = array();
    foreach ($users as $user) {
        if ($user['group'] == $group) {
            $hours[] = $user['hours'];
        }
    }
    return max($hours);
}

function maxPreference($users, $group) {
    $hours = array();
    foreach ($users as $user) {
        if ($user['group'] == $group) {
            $hours[] = $user['weight'];
        }
    }
    return max($hours);
}

function avgHours($users, $group) {
    $hours = 0;
    $i = 0;
    foreach ($users as $user) {
        if ($user['group'] == $group) {
            $hours = $hours + $user['hours'];
            $i++;
        }
    }
    $avg = ($hours / $i);
    return $avg;
}

function getNumberOfPastShifts($groupcode, $scheduleId, $shiftId, $userId, $schedule = false, $days = null) {

    $dayArray = array();
    $count = 0;
    if ($days == null) {
        $days = getConfig($groupcode, 'maxConsecWorkingDays');
    }

    if ($schedule) {

        foreach ($schedule['schedule'] as $shift) {
            if ($shift['id'] == $shiftId) {
                $start = strtotime($shift['start']);
            }
        }

        $end = strtotime('-' . $days . ' day', $start);

        foreach ($schedule['schedule'] as $shift) {
            $shiftend = strtotime($shift['endreal']);
            if ($shiftend > $end && $shiftend < $start) {
                foreach ($shift['users'] as $user) {
                    if (in_array($userId, $user)) {
                        $count++;
                    }
                }
            }
        }
    }

    return $count;
}

//function getNumberOfDaysConsec($groupcode, $shiftId, $userId, $schedule = false, $days = null) {
//
//    $dayArray = array();
//    $daysworked = array();
//    $count = 0;
//    if ($days == null) {
//        $days = getConfig($groupcode, 'maxConsecWorkingDays');
//    }
//
//    if ($schedule) {
//        $start = null;
//        $daySchedule = array();
//        foreach ($schedule as $shift) {
//            $datekey = (int) date('j', strtotime($shift['start']));
//            if ($datekey == $thiskey) {
//                $daySchedule[$datekey][] = $shift;
//            } else {
//                $thiskey = (int) date('j', strtotime($shift['start']));
//                $daySchedule[$thiskey] = $shift;
//            }
//            if ($shift['id'] == $shiftId) {
//                $start = strtotime($shift['start']);
//            }
//        }
//
//        $dayarray = array();
//        for ($i = 1; $i <= $days; $i++) {
//            $date = (int) date('j', $start);
//            $date = ($date - $i);
//            if ($date <= 0) {
//                $date = 1;
//            }
//            $dayarray[] = $date;
//        }
//
//        foreach ($dayarray as $day) {
//            $worked = null;
//            foreach ($daySchedule[$day] as $shift) {
//                foreach ($shift['users'] as $user) {
//                    if ($userId == $user['id']) {
//                        $tempdate = date('Y-m-d', strtotime($shift['start']));
//                        $worked = $tempdate;
//                    }
//                }
//            }
//            if ($worked != null) {
//                $daysworked[] = $worked;
//            } else {
//                break;
//            }
//        }
//    }
//    return count(array_unique($daysworked));
//}

function getNumberOfDaysConsec($groupcode, $shiftId, $userId, $schedule = false, $days = null) {
    $daysworked = array();
    $daymatches = 0;
    $count = 0;
    if ($days == null) {
        $days = getConfig($groupcode, 'maxConsecWorkingDays');
    }

    if ($schedule) {
        foreach ($schedule as $shift) {
            if ($shift['id'] == $shiftId) {
                $start = strtotime($shift['start']);
            }
            foreach ($shift['users'] as $user) {
                if ($userId == $user['id']) {
                    $tempdate = date('Y-m-j', strtotime($shift['start']));
                    $daysworked[] = $tempdate;
                }
            }
        }
        $daysworked = array_unique($daysworked);
        $dayarray = array();
        for ($i = 1; $i <= $days; $i++) {
            $date = (int) date('j', $start);
            $month = date('m', $start);
            $year = date('Y', $start);
            $date = ($date - $i);
            if ($date <= 0) {
                $date = 1;
            }
            $dayarray[] = $year . '-' . $month . '-' . $date;
        }

        foreach ($dayarray as $day) {
            if (in_array($day, $daysworked)) {
                $daymatches++;
            } else {
                break;
            }
        }
    }
    return $daymatches;
}

function getNextDayShiftId($groupcode, $date, $shiftname, $schedule) {

    $nextday = strtotime(date("Y-m-d", strtotime($date)) . " +1 day");
    if ($schedule) {
        foreach ($schedule as $shift) {
            if ((date('Y-m-d', $nextday) == date('Y-m-d', strtotime($shift['start']))) && ($shift['shiftName'] == $shiftname)) {
                $shiftId = $shift['id'];
            }
        }
    }

    return $shiftId;
}

function getNextDayShift($groupcode, $date, $shiftId, $schedule) {
    $retShift = false;
    $nextday = strtotime($date . " +1 day");
    foreach ($schedule as $shift) {
        if (($nextday == strtotime($shift['start'])) && ($shift['shiftId'] == $shiftId)) {
            $retShift = $shift;
            break;
        }
    }
    return $retShift;
}

function getWeekendCountForUser($groupcode, $userId, $month, $year) {
    $time = date("Y-m-d H:i:s", strtotime('' . $year . '-' . $month . '-01'));
    $schedules = getUserSchedule($userId, $groupcode, 2, 'previous', $time);
    $weekendcount = 0;
    foreach ($schedules as $schedule) {
        foreach ($schedule['schedule'] as $shift) {
            if (isWeekend($shift['start'])) {
                $weekendcount++;
            }
        }
    }

    return $weekendcount;
}

function getNightCountForUser($groupcode, $userId, $month, $year) {
    $time = date("Y-m-d H:i:s", strtotime('' . $year . '-' . $month . '-01'));
    $schedules = getUserSchedule($userId, $groupcode, 2, 'previous', $time);
    $nightcount = 0;
    foreach ($schedules as $schedule) {
        foreach ($schedule['schedule'] as $shift) {
            if (isNight($shift['start'], $shift['endreal'])) {
                $nightcount++;
            }
        }
    }

    return $nightcount;
}

function getPreviousShiftWorked($schedule, $userId, $shiftId) {
    $retShift = false;
    for ($i = 0; $i < $shiftId; $i++) {
        $userList = $schedule[$i]['users'];
        if (empty($schedule[$i]['users'])) {
            $userList = array();
        }
        if (in_array($userId, $userList)) {
            $retShift = $schedule[$i];
        }
    }
    return $retShift;
}

function getNextShiftWorked($schedule, $userId, $shiftId) {
    $retShift = false;
    $count = count($schedule);
    $count--;
    $count--;
    for ($i = $count; $i > $shiftId; $i--) {
        $userList = $schedule[$i]['users'];
        if (empty($schedule[$i]['users'])) {
            $userList = array();
        }
        if (in_array($userId, $userList)) {
            $retShift = $schedule[$i];
        }
    }
    return $retShift;
}

//Gets the next available shift after the current shift
function getNextAvailableShift($schedule, $shiftId) {
    $retShift = false;
    $count = count($schedule);
    $count--;
    for ($i = $count; $i > $shiftId; $i--) {
        if ($schedule[$i]['number'] < count($schedule[$i]['users'])) {
            $retShift = $schedule[$i];
        }
    }
    return $retShift;
}

//Get first available shift by template shiftId
function getFirstAvailableShift($user, $schedule, $shiftId, $offset = 0, $weekday = '6') {
    $retShift = false;
    $offsetCount = 0;
    $hours = $user['hours'];
    $max = $user['max_hours'];
    $count = count($schedule);
    $count--;
    for ($i = 0; $i < $count; $i++) {
        $duration = $schedule[$i]['duration'];
        $tempHours = $duration + $hours;
        if ($tempHours <= $max) {
            $usercount = 0;
            if (date('N', strtotime($schedule[$i]['start'])) < $weekday) {
                if (($schedule[$i]['shiftId'] == $shiftId)) {
                    $userList = $schedule[$i]['users'];
                    if (empty($schedule[$i]['users'])) {
                        $userList = array();
                    }

                    foreach ($userList as $user) {
                        $usercount++;
                    }
                    if (($schedule[$i]['number'] > $usercount)) {
                        if ($offsetCount == $offset) {
                            $retShift = $schedule[$i];
                            break;
                        } else {
                            $offsetCount++;
                        }
                    }
                }
            }
        }
    }
    return $retShift;
}

//Get first available shift by template shiftId
function getFirstAvailable($schedule) {
    $retShift = false;
    $count = count($schedule);
    $count--;
    for ($i = 0; $i < $count; $i++) {
        $usercount = 0;
        $userList = $schedule[$i]['users'];
        if (empty($schedule[$i]['users'])) {
            $userList = array();
        }

        foreach ($userList as $user) {
            $usercount++;
        }
        if (($schedule[$i]['number'] > $usercount)) {

            $retShift = $schedule[$i];
            break;
        }
    }
    return $retShift;
}

//create a new ideas
function updateSchedule($groupcode, $scheduleId) {
    $db = new MONGORILLA_DB;
    $arg = array('col' => $groupcode, 'type' => 'tempShift', 'where' => array('scheduleId' => $scheduleId, 'day' => array('$ne' => '00')));
    $results = $db->find($arg);
    $schedule = array();
    foreach ($results as $shift) {
//$shift['users'][] = array('first_name' => $pickeduser['first_name'], 'last_name' => $pickeduser['last_name'], 'user_name' => $pickeduser['user_name'], 'id' => $this->db->_id($pickeduser['_id']));
        $schedule[$shift['id']] = $shift;
    }


    $obj = array('schedule' => $schedule);
    $arg = array('col' => $groupcode, 'type' => 'schedule', 'obj' => $obj, 'id' => $scheduleId);
    $results = $db->upsert($arg);
    return $db->_id($results);
}

?>