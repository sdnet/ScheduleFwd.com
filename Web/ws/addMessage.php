<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
if(isset($_POST['id'])){
	$userId = $_POST['id'];
}else{
	$userId = $_SESSION['_id'];
}
$to = $_POST['to'];
$from = $_POST['from'];
$body = $_POST['body'];
$folder = $_POST['folder'];
$status = $_POST['status'];
$subject = $_POST['subject'];


// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	if($groupcode == "" || $userId == null || $to == "" || $from == "" || $body == ""  || $subject == "") {
			$message =  "emptyFields";
	} else {
		$messageId = uniqid();
		$to = explode(",", $to);
		foreach($to as $index=>$word){
			$to[$index] = trim($word);  
		}
		$toArray = array();
		$fromArray = array();
		//get the from information
		$arg = array('id' => getUserId($from,$groupcode), 'col' => "$groupcode", 'type' => 'user');
		$fromresult = $db->find($arg);
		$fromArray['last_name'] = $fromresult[0]['last_name'];
		$fromArray['first_name'] = $fromresult[0]['first_name'];
		$fromArray['user_name'] = $fromresult[0]['user_name'];
		
		$idList = array();
		foreach($to as $r){
			if(startsWith($r, 'role:')){
				$rolename = substr($r,5);
				$roles = getRoleIds($rolename,$groupcode);
				foreach($roles as $role){
					$idList[] = $role;
				}	
				$toArray[] = array('user_name' => $r,'first_name' => 'Role:','last_name' => $rolename); 
			}elseif(startsWith($r, 'group:')){
				$groupname = substr($r,6);
				$groups = getGroupIds($groupname,$groupcode);
				foreach($groups as $group){
					$idList[] = $group;
				}
				$toArray[] = array('user_name' => $r,'first_name' => 'Group:','last_name' => $groupname); 
			}else{
				$tempId = getUserId($r,$groupcode);
				$idList[] = $tempId;
				$arg = array('id' => $tempId, 'col' => "$groupcode", 'type' => 'user', 'limit' => 1);
				$result = $db->find($arg);
				$toArray[] = array('user_name' => $result[0]['user_name'],'first_name' => $result[0]['first_name'],'last_name' => $result[0]['last_name']); 
			}		
		}
		$idList[] = $userId;
		$idList = array_unique($idList);
		foreach($idList as $id){
			if($id == $userId){
				$uu = getUserById($groupcode,$id);
				$from = $uu['first_name'] . " " . $uu['last_name'];
				$folder = 'Sent';
			}else{
				$uu = getUserById($groupcode,$id);
				mailIt("" . $uu['email'] . "","You have a new message!","You've received a new message at Schedule Forward. You can view/reply to it by going to http://www.schedulefwd.com/messages Below is a preview of the message sent.
	
	From: " . $from . "
				
	Subject: " . $subject . " 
		
	Message: " . $body . ""); 
			 $folder = 'Inbox';	
			}
		$obj = array('userId' => $id, 'to' => $toArray, 'from' => $fromArray, 'date_created' => new MongoDate(), 'read' => 0, 'active' => 1, 'folder' => $folder, 'subject' => $subject, 'body' => $body, 'messageId' => $messageId, 'status' => $status);
		$arg = array('col' => "$groupcode", 'type' => 'message', 'obj' => $obj);
		$results = $db->upsert($arg);
		
		}
		
		if($results != null){
			$message = "success";
		}
		else{
			$message = "noRecords";	
		}
	}				
}else{
	//return auth failure
	$message = "authFailure";	
}

if($format == 'dt'){
	$data = dtFormat($data);
}
else{

}
echo json_encode(array('message' => $message, 'data'=>$data));
?>