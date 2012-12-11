<?php

if(isset($_GET['when'])) {
	$archieved = True;
	$when = $_GET['when'];	
}

if(isset($_GET['month']) && isset($_GET['year']) && isset($_GET['day'])) {
	$month = $_GET['month'];
	$day = $_GET['day'];
	$year = $_GET['year'];
}
else {
	$date = date("Y-m-d");// current date
	$advanceDate = strtotime(date("Y-m-d", strtotime($date)) . " +2 month");
	$month = Date("m",$advanceDate);
	$day = Date("d",$advanceDate);
	$year = Date("Y",$advanceDate);
}

$sessionId = session_id();

if(isset($when)) {
	$schedule = getArchive($sessionId, $_SESSION['grpcode'], $month, $year, $when);
}
else {
	$schedule = getSchedule($sessionId, $_SESSION['grpcode'], $month, $year);
}

// Here we generate the first day of the month (starting on Monday, so we subtract a day)
$first_day = mktime(0,0,0,$month, 1, $year)-1;

// When on the 1st, the month will read the 31st of the previous month due to our offset
// subtraction above; keep a non-offset date available for month display
$first_day_without_offset = mktime(0,0,0,$month, 1, $year);

// This gets us the month name
$title = date('F', $first_day_without_offset);
 
//Here we find out what day of the week the first day of the month falls on 
$day_of_week = date('D', $first_day); 


// determine how many days that are needed from prev month
$numPrevDays = 0;

switch($day_of_week){
	case "Mon":
		$numPrevDays = 1;
		break; 
	case "Tue":
		$numPrevDays = 2;
		break; 
	case "Wed":
		$numPrevDays = 3;
		break; 
	case "Thu":
		$numPrevDays = 4;
		break; 
	case "Fri":
		$numPrevDays = 5;
		break; 
	case "Sat":
		$numPrevDays = 6;
		break;
	case "Sun":
		$numPrevDays = 7;
		break;
}


$prevMonth = $month-1;

// pad if necessary
if($prevMonth < 10) {
	$prevMonth = "0".$prevMonth;
}

$htmlShifts = "";

$thisMonthsSchedule = organizeShiftsIntoDays($schedule);

if($numPrevDays > 0) {
	if(isset($when)) {
		$prevMonthSchedule = getArchive($sessionId, $_SESSION['grpcode'], $prevMonth, $year, $when);
	}
	else {
		$prevMonthSchedule = getSchedule($sessionId, $_SESSION['grpcode'], $prevMonth, $year);
	}
	
	$prevMonthSchedule = organizeShiftsIntoDays($prevMonthSchedule);
	
	$numOfPrevDaysUsed = count($prevMonthSchedule);
	
	$numOfPrevDaysUsed -= $numPrevDays;
	$numOfPrevDaysUsed++;
		
	$addedCount = 0;
	
	while($numOfPrevDaysUsed <= count($prevMonthSchedule)) {
		
		
		$htmlShifts .= "<td valign=\"top\" style=\"padding: 5px; border: 1px solid #F2F2F2;\"> <div id=\"\" style=\"width:90%; cursor: pointer; margin: 0 auto; background-color: #F2F2F2; padding-right: 10px; text-align: right;\" onClick=\"selectDaysShifts('$numOfPrevDaysUsed')\"><strong>$numOfPrevDaysUsed</strong></div>";
		
		$addedCount++;
		
		
		foreach ($prevMonthSchedule[$numOfPrevDaysUsed] as $shift) {
			$startTime = $shift['start'];
			$startTime = explode(' ', $startTime);
			$startTime = $startTime[1];
			$startTime = explode(':', $startTime);
			$startTime = intVal($startTime[0]);
			
			if($startTime > 12) {
				$startTime = ($startTime - 12)."pm";
			}
			else {
				$startTime = $startTime."am";
			}
			
			$endTime = $shift['end'];
			$endTime = explode(' ', $endTime);
			$endTime = $endTime[1];
			$endTime = explode(':', $endTime);
			$endTime = intVal($endTime[0]);
			
			if($endTime > 12) {
				$endTime = ($endTime - 12)."pm";
			}
			else {
				$endTime = $endTime."am";
			}
			
			$htmlShifts .= "<div id=\"\" style=\"padding: 3px; font-size:1.0em;\"><strong>".$shift['shiftName']. "</strong> <span style=\"font-size:1.0em;\">(".$startTime."-".$endTime.")</span><br>";
			$htmlShifts .= "<span style=\"font-size:1.0em;\">";
			foreach($shift['users'] as $user) {
				$htmlShifts .= $user['first_name']." ".$user['last_name']."<br>";
											}
											
			$htmlShifts .= "</span></div>";
		}
		
		$htmlShifts .= "</td>"; 
	
		//array_push($weekSchedule, $prevMonthSchedule[$numOfPrevDaysUsed]);
		$numOfPrevDaysUsed ++;
	}
	
	$dayNum = 1;
	
	foreach($thisMonthsSchedule as $daySched) {
		if($addedCount > 6) {
			break;
		}
	
	
		$dayNum = "0".$dayNum;
		
		$htmlShifts .= "<td valign=\"top\" style=\"padding: 5px; border: 1px solid #F2F2F2;\"> <div id=\"\" style=\"width:90%; cursor: pointer; margin: 0 auto; background-color: #F2F2F2; padding-right: 10px; text-align: right;\" onClick=\"selectDaysShifts('$dayNum')\"><strong>$dayNum</strong></div>";
		
		foreach ($thisMonthsSchedule[$dayNum] as $shift) {
			$startTime = $shift['start'];
			$startTime = explode(' ', $startTime);
			$startTime = $startTime[1];
			$startTime = explode(':', $startTime);
			$startTime = intVal($startTime[0]);
			
			if($startTime > 12) {
				$startTime = ($startTime - 12)."pm";
			}
			else {
				$startTime = $startTime."am";
			}
			
			$endTime = $shift['end'];
			$endTime = explode(' ', $endTime);
			$endTime = $endTime[1];
			$endTime = explode(':', $endTime);
			$endTime = intVal($endTime[0]);
			
			if($endTime > 12) {
				$endTime = ($endTime - 12)."pm";
			}
			else {
				$endTime = $endTime."am";
			}
			
			$htmlShifts .= "<div id=\"\" style=\"padding: 3px; font-size:1.0em;\"><strong>".$shift['shiftName']. "</strong> <span style=\"font-size:1.0em;\">(".$startTime."-".$endTime.")</span><br>";
			$htmlShifts .= "<span style=\"font-size:1.0em;\">";
			foreach($shift['users'] as $user) {
				$htmlShifts .= $user['first_name']." ".$user['last_name']."<br>";
											}
											
			$htmlShifts .= "</span></div>";
		}
		
		$htmlShifts .= "</td>";
		
		$addedCount++;
		$dayNum++;
	}
	
	
}
else {

	$dayNum = 1;
	$addedCount = 0;
	$weekSchedule = $thisMonthsSchedule;
	
	foreach($thisMonthsSchedule as $daySched) {
		if($addedCount > 6) {
			break;
		}
		
		$dayNum = "0".$dayNum;
		
		$htmlShifts .= "<td valign=\"top\" style=\"padding: 5px; border: 1px solid #F2F2F2;\"> <div id=\"\" style=\"width:90%; cursor: pointer; margin: 0 auto; background-color: #F2F2F2; padding-right: 10px; text-align: right;\" onClick=\"selectDaysShifts('$dayNum')\"><strong>$dayNum</strong></div>";
		
		foreach ($thisMonthsSchedule[$dayNum] as $shift) {
			$startTime = $shift['start'];
			$startTime = explode(' ', $startTime);
			$startTime = $startTime[1];
			$startTime = explode(':', $startTime);
			$startTime = intVal($startTime[0]);
			
			if($startTime > 12) {
				$startTime = ($startTime - 12)."pm";
			}
			else {
				$startTime = $startTime."am";
			}
			
			$endTime = $shift['end'];
			$endTime = explode(' ', $endTime);
			$endTime = $endTime[1];
			$endTime = explode(':', $endTime);
			$endTime = intVal($endTime[0]);
			
			if($endTime > 12) {
				$endTime = ($endTime - 12)."pm";
			}
			else {
				$endTime = $endTime."am";
			}
			
			$htmlShifts .= "<div id=\"\" style=\"padding: 3px; font-size:1.0em;\"><strong>".$shift['shiftName']. "</strong> <span style=\"font-size:1.0em;\">(".$startTime."-".$endTime.")</span><br>";
			$htmlShifts .= "<span style=\"font-size:1.0em;\">";
			foreach($shift['users'] as $user) {
				$htmlShifts .= $user['first_name']." ".$user['last_name']."<br>";
											}
											
			$htmlShifts .= "</span></div>";
		}
		
		$htmlShifts .= "</td>";
		
		
		$addedCount++;
		$dayNum++;
	}
	
	
}

function organizeShiftsIntoDays($schedule) {
	//fix this hack when schedule creation is fixed
	$break = false;
	$theDay = "00";
	
	$shiftArray = array();
	
	$shiftArr = array();
	
	
	$openArray = array("user_name" => "Open", "first_name" => "", "last_name" => "Open", "id" => "NOPE");
	
	foreach($schedule['data'] as $shift) {
		if(!array_key_exists("start", $shift)) {
		
		}
		else {
			$startDate = $shift['start'];
			$days = explode('-', $startDate);
			$days = explode(' ', $days[2]);
			$day = $days[0];
			
			
			if(($theDay != $day) && $theDay != "00") {
				$theDay = trim($theDay);
				$shiftArray[$theDay] = $shiftArr;
				$theDay = $day;
				$shiftArr = array();
			}
			if($theDay == "00") {
				$shiftArr = array();
				$theDay = $day;
			}
			if($shift['users'] == null) {
				$shift['users'] = array(0 => $openArray);
			}
			array_push($shiftArr, $shift);
		}
	}
	
	$shiftArray[$theDay] = $shiftArr;
	
	return $shiftArray;
}


function do_post_request($url, $data, $optional_headers = null) {
	$params = array('http' => array(
              'method' => 'POST',
              'content' => $data,
              'header'=> "Content-type: application/x-www-form-urlencoded\r\n"
            . "Content-Length: " . strlen($data) . "\r\n",
           ));
    if ($optional_headers !== null) {
    	$params['http']['header'] = $optional_headers;
    }
    $ctx = stream_context_create($params);
    $fp = fopen($url, 'rb', false, $ctx);
    if (!$fp) {
    	throw new Exception("Problem with $url, $php_errormsg");
    }
    $response = @stream_get_contents($fp);
    if ($response === false) {
    	throw new Exception("Problem reading data from $url, $php_errormsg");
    }
 	return $response;
 }


function getSchedule($sessionId, $gpCode, $month, $year){

    $data_array = array('sessionId'=>$sessionId,'grpcode'=>$gpCode, 'month'=>$month, 'year'=>$year, 'id'=>$_SESSION['_id']);
    $data = http_build_query($data_array);
    
    $response = do_post_request('http://schedulefwd.com/ws/getSchedule', $data);
    
    $response = json_decode($response, true);
    	    	    	    
    return $response;
}
	
function getArchive($sessionId, $gpCode, $month, $year, $when){

    $data_array = array('sessionId'=>$sessionId,'grpcode'=>$gpCode, 'startOrEnd'=>$when, 'month'=>$month, 'year'=>$year, 'id'=>$_SESSION['_id']);
    $data = http_build_query($data_array);
    
    $response = do_post_request('http://schedulefwd.com/ws/getArchive', $data);
    
    $response = json_decode($response, true);
    	    	    	    
    return $response;
}

$htmlBody = "";

$htmlBody .="<html id=\"pageHTML\">
	<head>
		<title>Schedule Forward :: Medical scheduling software made easy</title>
        <link rel=\"stylesheet\" href=\"/css/pdf.css\" />
        <script src=\"//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js\" type=\"text/javascript\"></script>
        <script type=\"text/javascript\">
			$(document).ready(function() {
				onPageLoad();
			});
		</script>
    </head>
    <body>
        
			<div id=\"wrapper\">
				<div id=\"header\">Schedule Forward :: Automated Scheduling Made Easy</div>
			</div>
            
            <!-- Content -->
            <div id=\"content\">




<form id=\"frmTimeOff\" action=\"/ws/createPDF.php\" method=\"post\">
    <input type=\"hidden\" id=\"contentId\" name=\"content\" value=\"\" />
    <input type=\"hidden\" id=\"sessionId\" name=\"sessionId\" value=".$sessionId." />
    <input type=\"hidden\" id=\"grpcode\" name=\"grpcode\" value=".$_SESSION['grpcode']." />
    <input type=\"hidden\" id=\"type\" name=\"type\" value=\"preferences\" />

    <div id=\"toPDFDiv\">";
    
    
    $htmlBody .= "<table id=\"tblShiftTrade\" style=\"width:100%\">
    	<tr><th colspan=7> $title $year </th></tr> 
    	<tr id=\"tblShiftTradeDays\"><td width=42>M</td><td width=42>T</td>
    	<td width=42>W</td><td width=42>Th</td><td width=42>F</td>
    	<td width=42>S</td><td width=42>Su</td></tr>";
    	
    $htmlBody .= $htmlShifts;
    	
    	
    	
    	
    	
                                    		
    $htmlBody .= "</table>";
    
    


$htmlBody .= "</div></form></div></body></html>";

echo($htmlBody);

?>


<style>
	#tblShiftTrade {
		width:100%;
		border: 1px solid #999;	
	}
	
	#tblShiftTrade th {
		background-color: #999;
	}
	
	#tblShiftTrade tr#tblShiftTradeDays {
		background-color: #CCC;	
		text-align: center;
	}
	
	#tblShiftTrade tr#tblShiftTradeDays td {
		width: 14%;
	}
</style>
	
