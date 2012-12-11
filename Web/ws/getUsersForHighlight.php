<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$scheduleId = $_POST['scheduleId'];

if (isset($_POST['id'])) {
    $userId = $_POST['id'];
} else {
    $userId = $_SESSION['_id'];
}
// check session
if (VerifySession($sessionId, $groupcode, $userId, 'Admin') == true) {
    $userList = array();
    $where = array();
    $where['active'] = 1;
    $where['group'] = array('$ne' => 'Staff');

    //	$where = array('email' => "$email", 'user_name' => "$username", 'first_name' => "$firstName", 'last_name' => "$lastName", 'group' => "$group", 'role' => "$role");
    $arg = array('col' => "$groupcode", 'type' => 'user', 'where' => $where, 'keys' => array("first_name" => 1, "last_name" => 1, "user_name" => 1, "_id" => 1, "group" => 1));
    $results = $db->find($arg);
    if ($results != null) {
        $monthAndYear = getMonthYearFromScheduleId($groupcode, $scheduleId);
        $month = $monthAndYear['month'];
        $year = $monthAndYear['year'];
        foreach ($results as $user) {
            //$shifts = getShiftsFromSchedule($db->_id($user['_id']), $groupcode, $scheduleId);
            //if($shifts){
            //}
            $timeoffs = array();
            $timeoffrequests = getTimeOffByUserId($groupcode, $db->_id($user['_id']), $month, $year);
            foreach ($timeoffrequests as $key => $timeoff) {
                if($timeoff['priority'] == '1'){
                    $status = '#E49595';
                }elseif($timeoff['mustwork'] == 'true'){
                    $status = '#95E495';
                }else{
                    $status = 'yellow';
                }
                    
                foreach ($timeoff['time_off'] as $k => $v) {
                    
                    $timeoffs[] = array(''.date('d', strtotime($k)) .'_' . $v, $status);
                }
            }
            $user['timeoffs'] = $timeoffs;
            $userList[] = $user;
        }
        $data = $userList;
        $message = "success";
    } else {
        $message = "noRecords";
    }
} else {
    //return auth failure
    $message = "authFailure";
}

if ($format == 'dt') {
    $data = dtFormat($data);
} else {
    
}
echo json_encode(array('message' => $message, 'data' => $data));
?>