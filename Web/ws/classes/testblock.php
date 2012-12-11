<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require('block.php');

runBlockTest();

function runBlockTest() {
	$blockObj = new block();
	echo $blockObj->numberOfDaysInBlock('19', '01', '2013', '5033161f69e95eb75b8c62ec', 'testy');
}





?>