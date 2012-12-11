<?php

include('cws.php');
include('classes/hash.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$username = $_POST['username'];
$groupcode = $_POST['grpcode'];
$password = $_POST['password'];
$firstName = $_POST['firstname'];
$lastName = $_POST['lastname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$group = $_POST['group'];
$role = $_POST['role'];
$min = $_POST['min'];
$max = $_POST['max'];
$location = $_POST['location'];
$priority = $_POST['priority'];
$scheduleProvider = $_POST['scheduleProvider'];
$shifts = $_POST['shifts'];
$notShifts = $_POST['notShifts'];
$hourCheck = true;
// check session
if (VerifySession($sessionId, $groupcode, $_SESSION['_id'], 'Admin') == true) {
    if (isset($min) || isset($max)) {
        $hourCheck = hourCheck($min, $max);
    }
    if ($hourCheck) {
        if (ValidEmail($email)) {
            if (($username == "" || $groupcode == "" || $password == "" || $firstName == "" || $lastName == "" || $group == "" || $role == "")) {
                $message = "emptyFields";
            } else {
                //check to see if user exists
                $where = array('user_name' => "$username");
                $arg = array('col' => "$groupcode", 'type' => 'user', 'where' => $where, 'limit' => 1);
                $results = $db->find($arg);
                if ($results == null) {

                    $where = array('email' => "$email");
                    $arg = array('col' => $groupcode, 'type' => 'user', 'where' => $where, 'limit' => 1);
                    $result = $db->find($arg);
                    if ($result == null) {
                        if (!isset($priority)) {
                            $priority = 1;
                        }
                        $preferences = array('days' => 
                            array(
                                '0' => 'Monday',
                                '1' => 'Tuesday',
                                '2' => 'Wednesday',
                                '3' => 'Thursday',
                                '4' => 'Friday',
                            ),
                                       'shifts' => $shifts,
										'not_shifts' => $notShifts,
                            'block_days' => '1',
                            'block_weekend' => '1',
                            'max_days' => '5',
                            'max_nights' => '2',
                            'desired_nights' => '1',
                            'desired_days' => '3',
                            'circadian' => '1',
                            'afterNightShift' => 'Wed7pm',
                        );
						$obj = array('user_name' => "$username'",'preferences' => $preferences, 'active' => 1, 'email' => $email, 'first_name' => $firstName, 'last_name' => $lastName, 'phone' => $phone, 'password' => create_hash($password), 'priority' => $priority, 'group' => $group, 'role' => $role, 'location' => $location, 'scheduleProvider' => $scheduleProvider, 'date_created' => new MongoDate());
                        if (isset($min) || isset($max)) {
                            $hourArray = Array('min_hours' => $min, 'max_hours' => $max);
                            $obj = array_merge($obj, $hourArray);
                        }
                        $id = $db->upsert(array('col' => $groupcode, 'type' => "user", 'obj' => $obj));
                        $data = array('userId' => $id, 'user_name' => "$username");
                        $message = "success";
                    } else {
                        $message = "emailExists";
                    }
                } else {
                    $message = "userExists";
                }
            }
        } else {
            $message = "emailInvalid";
        }
    } else {
        $message = "overrideInvalid";
    }
} else {
//return auth failure
    $message = "authFailure";
}

echo json_encode(array('message' => $message, 'data' => $data));
?>