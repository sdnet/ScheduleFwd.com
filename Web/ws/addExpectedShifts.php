<?
include('cws.php');
header('Content-type: application/json');
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
$month = $_POST['month'];
$year = $_POST['year'];
if(isset($_POST['id']) && $_POST['id'] != ""){
	$userId = $_POST['id'];
}else{
	$userId = $_SESSION['_id'];
}
$expectedShifts = trim($_POST['expectedShifts']);
// check session
if(VerifySession($sessionId,$groupcode,false,false) == true){
	if($groupcode == "") {
		$message =  "emptyFields";
	} else {
		
		$where = array('year' => $year, 'month' => $month, 'userId' => $userId);
		$arg = array('col' => "$groupcode", 'type' => 'expectedShifts',  'where' => $where);
		$result = $db->find($arg);
		$timeOffId = $db->_id($result[0]['_id']);
		$arg = array('id' => $timeOffId, 'col' => "$groupcode", 'type' => 'expectedShifts',  'obj' => array('year' => $year, 'month' => $month, 'userId' => $userId, 'shifts' => (int)$expectedShifts));
		$results = $db->upsert($arg);	

		if($results != null){
			$data = $results;
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