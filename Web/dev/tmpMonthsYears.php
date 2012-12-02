<?php

$month = 7;
$year = 2012;

//check to see if months add up and get 6 months out
$monthArray = array();
$monthArray2 = array();
$combinedArray = array();
$multiYears = 0;

array_push($monthArray,$month-1);
array_push($monthArray,$month);

for ($i = 1; $i <= 4; $i++) {
	if ($month + $i > 12) {
		$tmpMonth = ($month + $i) - 12;
		$multiYears = 1;
		array_push($monthArray2, $tmpMonth);	
	} else {
		array_push($monthArray, $month + $i);
	}
}

$combinedArray[$year] = $monthArray;
if ($multiYears > 0) { $combinedArray[$year+1] = $monthArray2; }

print_r($combinedArray);

?>