<?php

require_once 'classes/urbanairship.php';

// Your testing data
$APP_MASTER_SECRET = '3O47qiGETZiqwHuOLVSCqw';
$APP_KEY = 'NfA2eZORT2ya8K1WUUv1vw';
$TEST_DEVICE_TOKEN = '8C73346B6FDF22CE7ED64480012BF04D2608F100E3391A13ADCEA95AB7F5F93C';

// Create Airship object
$airship = new Airship($APP_KEY, $APP_MASTER_SECRET);

// Test feedback

//$time = new DateTime('now', new DateTimeZone('UTC'));
//$time->modify('-1 day');
//echo $time->format('c') . '\n';
//print_r($airship->feedback($time));

// Test register

//$airship->register($TEST_DEVICE_TOKEN, 'testTag');

// Test get device token info
print_r($airship->get_device_token_info($TEST_DEVICE_TOKEN));

// Test get device tokens

$tokens = $airship->get_device_tokens();
echo 'Device tokens count is:' . count($tokens);
	foreach ($tokens as $item) {
    var_dump($item);
}

// Test deregister

//$airship->deregister($TEST_DEVICE_TOKEN);


// Test push

//$message = array('aps'=>array('alert'=>'hello'));
//$airship->push($message, $TEST_DEVICE_TOKEN, array('testTag'));

// Test broadcast

$broadcast_message = array('aps'=>array('alert'=>'Your schedule for October has been generated!'));
$airship->broadcast($broadcast_message, null);

?>
