<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$scheduleId = $_POST['scheduleId'];
$user = $_POST['username'];

if (isset($_POST['id'])) {
    $userId = $_POST['id'];
} else {
    $userId = $_SESSION['_id'];
}
// check session
if (VerifySession($sessionId, $groupcode, $userId, 'Admin') == true) {
    $userList = array();
    $UID = getUserId($user,$groupcode);
    $where = array('users.id' => array('$in' => array($UID)), 'scheduleId' => $scheduleId);
    $arg = array('col' => "$groupcode", 'type' => 'tempShift', 'where' => $where);
    $results = $db->find($arg);
    if ($results != null) {
        foreach ($results as $shift) {
            $key = ''.date('d', ''.$shift['start']->sec.'') .'_' . $shift['shiftId'] . '_' . $user;

            $userShifts = getPreferredShiftsByUser($groupcode, $userId);
            $position = array_search($shift['shiftId'], $userShifts);
            $count = count($userShifts);
            $first = ($count)/3;
            $second = $first+$first;

            if ($position <= $first) {
                $status = '#95E495';
            } elseif ($position <= $second) {
                $status = 'yellow';
            } else {
                $status = '#E49595';
            }
            $userList[$key] = $status;
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