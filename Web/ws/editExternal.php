<?php
include('cws.php');
include('classes/hash.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$orgId = $_POST['id'];
$orgName = $_POST['orgname'];
$groupcode = $_POST['grpcode'];
$firstName = $_POST['firstname'];
$lastName = $_POST['lastname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$city = $_POST['city'];
$state = $_POST['state'];
$zip = $_POST['zip'];

// check session
if(VerifySession($sessionId,$groupcode,$_SESSION['_id'],'Admin') == true){
	if(($groupcode == "" || $orgName == "" || $orgId == "" )) {
				$message =  "emptyFields";
			} else {
				//check to see if user exists
		$where = array('org_name' => "$orgName", '_id' => array( '$ne' => new MongoId($orgId)));
		$arg = array('col' => "$groupcode", 'type' => 'extUser', 'where' => $where, 'limit' => 1);
				$results = $db->find($arg);
				if($results == null){
			
				$obj = array('org_name' => "$orgName", 'active' => 1, 'email' => $email, 'first_name' => $firstName, 'last_name' => $lastName, 'phone' => $phone, 'address' => $address, 'zip' => $zip, 'state' => $state, 'city' => $city, 'date_created' => new MongoDate());
				$id = $db->upsert(array('id' => $orgId, 'col' => $groupcode, 'type' => "extUser", 'obj' => $obj ));
						$data = $id;
						$message = "success";
					
					
				}else{
					$message =  "userExists";	
				}
			}

}else{
//return auth failure
$message = "authFailure";	
}

echo json_encode(array('message' => $message, 'data'=>$data));

?>