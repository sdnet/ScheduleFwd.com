<?php
require_once 'urbanairship.php';
$APP_MASTER_SECRET = '3O47qiGETZiqwHuOLVSCqw';
$APP_KEY = 'NfA2eZORT2ya8K1WUUv1vw';



function sendNotification($tokens,$message) {
// Create Airship object
$airship = new Airship($APP_KEY, $APP_MASTER_SECRET);
	$messageArray = array('aps'=>array('alert'=>'' . $message . ''));
	try{
		foreach($tokens as $key=>$value){
			if($value != null){
				$airship->push($messageArray, $value, null);
			}
		}
		return $tokens;
	}catch(exception $e)
	{$db = new MONGORILLA_DB;
		$obj = array('device_tokens' => $device_tokens, 'message' => $message, 'active' => 1);
		$data = $db->upsert(array('col' => 'notifications', 'type' => 'notification', 'obj' => $obj ));
	}

}

function resendNotifications($col = false){
	$db = new MONGORILLA_DB;
	$arg = array('col' => "notifications", 'type' => 'notification');
	$results = $db->find($arg);
	foreach($results as $result){
		try{
		$airship->push($result['message'], $result['device_tokens'], null);
			$db->delete(array('col' => 'notifications', 'id' => $result['_id']));
		}catch(exception $e)
		{
		}
	}
}

?>