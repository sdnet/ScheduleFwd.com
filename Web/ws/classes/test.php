<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require('rules.php');
require('rulesEngine.php');

$groupCode = 'testy';
$userName = 'jbrown';

$rules = rules::getInstance($groupCode)->resultsArray;
$rulesEngine = rulesEngine::getInstance($groupCode, $userName);

$rulesEngine->basicRule($rules);

//getLastShifts($groupCode, '5057daea69e95eb75b8c6790', '2012-09-01 00:00:00', 5, 'user');
//getScheduleId($groupCode, '9', '2012');
if(checkPreviousShiftLessThan($groupCode, $userName, '2012-10-03 01:30:00', '2012-10-03 03:00:00')) {
	print_r('fails');
}
else {
	print_r('does not fail');
}

?>