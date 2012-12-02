<html>
<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set("max_execution_time", "500");

require_once('' . $_SERVER['DOCUMENT_ROOT'] . '/ws/functions.php');

$year = "2013";
$month = "01";
$groupcode = "testy";

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

for ($i=0;$i<count($inMonth);$i++) {
	$where = array('month'=> ''.$inMonth[$i].'', 'year' => ''.$inYear[$i].'');
	$arg = array('col' => "$groupcode", 'type' => 'schedule', 'where' => $where);
	$results = $db->find($arg);
	
	foreach ($results[0]['schedule'] as $shift) {
		$date = $shift['start'];
		$users = $shift['users'];
		$group = $shift['groups'][0];
		$userHours[$group] = "";
		$userHours[$group]['groupTotal'] = 0;
		if ($users) {
			foreach ($users as $user) {
				$username = $user['user_name'];
				$fname = $user['first_name'];
				$lname = $user['last_name'];
				$userHours[$group]['users'][$username]['numWeekendShifts'] = "";
				$userHours[$group]['users'][$username]['weekendPercentage'] = "";
				$userHours[$group]['users'][$username]['numWeekendShifts']++;
				$userHours[$group]['users'][$username]['weekendPercentage'] = round(($userHours[$group]['users'][$username]['numWeekendShifts'] / 3),2);
				
				if (isWeekend($date) == true) {
					$userHours[$group]['groupTotal'] = $userHours[$group]['groupTotal'] + 1;
				}
			}
		}
	}
}

print_r($userHours);

foreach ($userHours as $key => $value) {
	echo "<h4>" . $key . "</h4>";	
	foreach ($value['users'] as $userKey => $userValue) {
		echo $userKey . " - Weekends: " . $userValue['numWeekendShifts'] . "Weekend percentage: " . $userValue['weekendPercentage'] . "<br />";
	}
}


/*
echo "<table style=\"width: 100%;\">";
	echo "<thead>";
		echo "<tr>";
			echo "<th>User</th><th>Num of Weekend Shifts</th><th>Weekend Shift Percentage (Total / 3)</th>";
		echo "</tr>";
	echo "</thead>";
	echo "<tbody>";
		foreach ($userHours as $key => $value) {
			echo "<tr>";
				echo "<td style=\"text-align: center;\">" . $key . "</td>";
				echo "<td style=\"text-align: center;\">" . $value['numWeekendShifts'] . "</td>";
				echo "<td style=\"text-align: center;\">" . $value['weekendPercentage'] . "</td>";
			echo "</tr>";
		}
	echo "</tbody>";
echo "</table>";
*/

?>