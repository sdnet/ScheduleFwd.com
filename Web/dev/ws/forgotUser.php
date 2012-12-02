<?php 
require('classes/ResetId.php');
require('cws.php');
if ($_POST['submit'] == "true") {
	$return = "false";
	
	$email = $uregex = new MongoRegex("/^" . $_POST['email'] . "$/i");
	$groupcode = $_POST['grpcode'];
	
	if (($email == "" || $groupcode == "")) {
		echo "emptyFields";
	} else {
		
		$arg = array('col' => "$groupcode", 'type' => 'user', 'limit' => 1, 'where' => array('email' => $email, 'active' => 1), 'keys' => array("first_name" => 1, "user_name" => 1, "last_name" => 1, "email" => 1, "_id" => 1));
		$result = $db->find($arg);
		$results = $result[0];
		if($results != null){
			// the user id and password match,
			// now, get the link that allows a user to reset the password
			$guid = createResetId();
			$link = "http://" . $_SERVER['HTTP_HOST'] . "/reset?c=". $groupcode ."&guid=" . $guid . "";
			//now let's insert the guid into the resets table
			$obj = array('guid' => $guid, 'userId' => $db->_id($results['_id']));
			$id = $db->upsert(array('col' => $groupcode, 'type' => "reset", 'obj' => $obj ));
			
			//Build the email time
			$name = $results['first_name'] . " " . $results['last_name'];
			$username = $results['user_name'];
			$email = $results['email'];
			$subject = 'Schedule Forward Password Recovery';
			$message = '*** NOTE THIS IS AN AUTOMATED MESSAGE ***
 

Dear ' . $name . ',

A password reset request has been issued for your username (' . $username . '), please visit ' . $link . ' to reset your password. If you did not request this email, please immediately contact your Schedule Forward administrator!


 Thank you,
 The Schedule Forward Team';
			$headers = 'From:  Schedule Forward <support@scheduleforward.com>' . "\r\n" .
				'Reply-To: support@scheduleforward.com' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
			mail($email, $subject, $message, $headers);
			$device_tokens = getDeviceTokens($groupcode,$db->_id($results['_id']));
			if($device_tokens != null){
			$pushMessage = "Your password has been reset, visit " . $link . " to set a new one.";
				$tokens = sendNotification($device_tokens,$pushMessage);	
			}
			echo "success";

		} else {
			echo "authFailure";
		}
	}
} else {
	echo "NoSubmission";	
}

?>