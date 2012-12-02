<?php

include('cws.php');
$sessionId = $_GET['sessionId'];
$groupcode = $_GET['grpcode'];

if(isset($_POST['id'])){
	$uId = $_POST['id'];	
}else{
	$uId = $_SESSION['_id']; 	
}

$year = $_GET['year'];
$month = $_GET['month'];
// check session
if(VerifySession($sessionId,$groupcode,$uId,'User') == true){
	if($year == null || $year == ""){
		$year = date('Y');
	}
	if($month == null || $month == ""){
		$month = date('m');
	}
	$where = array('year' => $year, 'month' => $month);
	$args = array('col'=>$groupcode,'type' => 'schedule', 'where' => $where);
	$results = $db->find($args);
/* Lets fetch them and output the vCal data */
	if ($results != null) {
		$tz = getConfig($groupcode,'timezone');
		$vCalOutput = "BEGIN:VCALENDAR\r\nVERSION:1.0\r\nPRODID:ScheduleForward\r\n";
        // This gets every returned row, and puts each in a hash
		foreach($results[0]['schedule'] as $result) {
			foreach($result['users'] as $user){
				if($user['id'] == $uId){
					
					/* format event data to vCal */
					$vCalDescription = str_replace("\r", "\\n", $result['group'][0]);
					$vCalLocation = str_replace("\r", "\\n", $result["location"]);
					if ($result["allday"] == 'true') {
						$vCalStart = date("Ymd", strtotime($result["start"]));
						$vCalEnd = date("Ymd", strtotime($result["endreal"]));
					} else {
						$vCalStart = date("Ymd\THi00", strtotime($result["start"]) );
						$vCalEnd = date("Ymd\THi00", strtotime($result["endreal"]) );
					}

					/* output the event */
					
					if ($result["allday"] == 'true') {
						$vCalOutput = $vCalOutput."BEGIN:VEVENT\r\n";
						$vCalOutput = $vCalOutput."SUMMARY:".$result['shiftName']."\r\n";
						$vCalOutput = $vCalOutput."DESCRIPTION:".$vCalDescription."\r\n";
						$vCalOutput = $vCalOutput."DTSTART;TZID=US/Pacific:".$vCalStart."\r\n";
						$vCalOutput = $vCalOutput."LOCATION:".$vCalLocation."\r\n";
						$vCalOutput = $vCalOutput."URL;VALUE=URI:".$result["url"]."\r\n";
						$vCalOutput = $vCalOutput."DTEND:".$vCalEnd."\r\n";
						$vCalOutput = $vCalOutput."END:VEVENT\r\n";
					} else {
						$dayinc = 1;
						while ($vCalStart <= $vCalEnd) {
							$vCalOutput = $vCalOutput."BEGIN:VEVENT\r\n";
							$vCalOutput = $vCalOutput."SUMMARY:".$result['shiftName']."\r\n";
							$vCalOutput = $vCalOutput."DESCRIPTION:".$vCalDescription."\r\n";
							$vCalOutput = $vCalOutput."DTSTART;TZID=US/Pacific:".$vCalStart."\r\n";
							$vCalOutput = $vCalOutput."LOCATION:".$vCalLocation."\r\n";
							$vCalOutput = $vCalOutput."URL;VALUE=URI:".$result["url"]."\r\n";
							$vCalOutput = $vCalOutput."END:VEVENT\r\n";
							$vCalYear = $vCalStart[0] . $vCalStart[1] . $vCalStart[2] . $vCalStart[3];
							$vCalMonth = $vCalStart[4] . $vCalStart[5];
							$vCalDay = $vCalStart[6] . $vCalStart[7];
							$nextday = mktime(0, 0, 0, $vCalMonth, $vCalDay + 1, $vCalYear);
							$vCalStart = date("Ymd", $nextday);
						}
					}
				}
			}
}
	
$vCalOutput = $vCalOutput."END:VCALENDAR";

/* echo($vCalOutput); */
	$name ='' . $_SESSION['lastName'] . '-' . date('F-Y') . '.ics';
//	$icsfile=fopen($name,'w');
//fputs($icsfile, $vCalOutput);
//fclose($icsfile);
header("Content-Type: text/Calendar");
header("Content-Disposition: inline; filename=$name");
echo $vCalOutput;
}
}else{
//return auth failure
$message = "authFailure";		
}
				

?>