<?php

if(isset($_GET['when'])) {
	$archieved = True;
	$when = $_GET['when'];	
}


if(isset($_GET['month']) && isset($_GET['year'])) {
	$month = $_GET['month'];
	$year = $_GET['year'];
}
else {
	$date = date("Y-m-d");// current date
	$advanceDate = strtotime(date("Y-m-d", strtotime($date)) . " +2 month");
	$month = Date("m",$advanceDate);
	$year = Date("Y",$advanceDate);
}

$passedInUser = $_SESSION['_id'];
$gpCode = $_SESSION['grpcode'];
$sessionId = session_id();

$print = $_GET['print'];


if(isset($when)) {
	$schedule = getArchive($sessionId, $_SESSION['grpcode'], $month, $year, $when);
}
else {
	$schedule = getSchedule($sessionId, $_SESSION['grpcode'], $month, $year);
}

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

$theKey = "";

$htmlBody = "";
$shiftArr = array();
if ($schedule != null) {
	$theDay = "00";
	/*
for ($i = 0; $i < count($schedule); $i++) {
		$startDate = $schedule[$i]['date'];
		$status = $schedule[$i]['status'];
		$priority = $schedule[$i]['priority'];
		$isRequested = $schedule[$i]['timeoff'];
		$textdecoration = "";
		$fontweight = "";
		$days = explode('-', $startDate);
		$color = "E5F3FF";
		
		$day = $days[2];
		
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
		
		
		// Make another date instance to determine whether or not the viewable month is changable
		$tmpDate = date("Y-m-d");
		$advanceDate2 = strtotime(date("Y-m-d", strtotime($tmpDate)) . " +2 month");
		$month2 = date("m",$advanceDate2);
		$year2 = date("Y",$advanceDate2);
		
		$content = "<span id=\"".$schedule[$i]['id']."_".$schedule[$i]['shiftId']."\" shiftDate=\"".$startDate."\" style=\"text-decoration: ".$textdecoration."; font-weight: ".$fontweight."\" class=\"shifts\">".$schedule[$i]['shiftName']."</span><br />";
		
		array_push($shiftArr, $content);
		
		if($i == count($schedule)-1) {
			$theDay = trim($theDay);
			$shiftArray[$theDay] = $shiftArr;
		}
	}
*/


// Count months between launch month and current month
$d1 = Date("2012-06-01");
$date = date("Y-m-d");
$d2 = strtotime ( '+2 month' , strtotime ($date ));
$d2 = Date('Y-m-d',$d2);
$diff = abs(strtotime($d2) - strtotime($d1));
$years = floor($diff / (365*60*60*24));
$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));

$htmlBody = $htmlBody."<html id=\"pageHTML\">
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
    
    
    
    //This gets today's date 
    $date = time() ; 
                                    
	//This puts the day, month, and year in seperate variables                                    
	$today = time();
	$offset = 2;
	// $year = date('Y',strtotime("+$offset months", $today));
	// $month =  date('n',strtotime("+$offset months", $today));
	$days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	
	// Here we generate the first day of the month (starting on Monday, so we subtract a day)
	$first_day = mktime(0,0,0,$month, 1, $year)-1;
	
	// When on the 1st, the month will read the 31st of the previous month due to our offset
	// subtraction above; keep a non-offset date available for month display
	$first_day_without_offset = mktime(0,0,0,$month, 1, $year);
	
	// This gets us the month name
	$title = date('F', $first_day_without_offset);
	 
	//Here we find out what day of the week the first day of the month falls on 
	$day_of_week = date('D', $first_day) ; 
	
	//Once we know what day of the week it falls on, we know how many blank days occure before it. 
	 switch($day_of_week) { 
		 case "Mon": $blank = 1; break; 
		 case "Tue": $blank = 2; break; 
		 case "Wed": $blank = 3; break; 
		 case "Thu": $blank = 4; break; 
		 case "Fri": $blank = 5; break; 	
		 case "Sat": $blank = 6; break; 
		 case "Sun": $blank = 7; break; 
	 }
	
	//We then determine how many days are in the current month
	$days_in_month = cal_days_in_month(0, $month, $year) ;
	
	//Here we start building the table heads 
    $htmlBody = $htmlBody."<table id=\"tblShiftTrade\" style=\"width:100%\">
                           <tr><th colspan=7> $title $year </th></tr>
                           <tr id=\"tblShiftTradeDays\"><td width=42>M</td><td width=42>T</td><td 
                                    		width=42>W</td><td width=42>TH</td><td width=42>F</td><td 
                                    		width=42>S</td><td width=42>Su</td></tr>";

                                     //This counts the days in the week, up to 
                                     $day_count = 1;
                                     $htmlBody = $htmlBody."<tr>";
                                    
                                     //first we take care of those blank days
                                     while ( $blank > 0 ) { 
										$htmlBody = $htmlBody."<td></td>"; 
										$blank = $blank-1; 
										$day_count++;
									 }
                                    
                                     //sets the first day of the month to 1
                                     $day_num = 1;

                                     //count up the days, until we've done all of them in the month
                                     while ( $day_num <= $days_in_month ) {
										 if (strlen($day_num) == 1) { $day_num = "0" . $day_num; } 
										$htmlBody = $htmlBody."<td valign=\"top\" style=\"padding: 5px; border: 1px solid #F2F2F2;\"> <div id=\"\" style=\"width:90%; cursor: pointer; margin: 0 auto; background-color: #F2F2F2; padding-right: 10px; text-align: right;\" onClick=\"selectDaysShifts('$day_num')\"><strong>$day_num</strong></div>";
										foreach ($shiftArray[$day_num] as $shift) {
											$startTime = $shift['start'];
											$startTime = explode(' ', $startTime);
											$startTime = $startTime[1];
											$startTime = explode(':', $startTime);
											$startTime = intVal($startTime[0]);
											
											if($startTime > 12) {
												$startTime = ($startTime - 12)."p";
											}
											else {
												$startTime = $startTime."a";
											}
											
											$endTime = $shift['end'];
											$endTime = explode(' ', $endTime);
											$endTime = $endTime[1];
											$endTime = explode(':', $endTime);
											$endTime = intVal($endTime[0]);
											
											if($endTime > 12) {
												$endTime = ($endTime - 12)."p";
											}
											else {
												$endTime = $endTime."a";
											}
											
											
										
										
											$htmlBody = $htmlBody."<div id=\"\" style=\"padding: 3px; font-size:1.0em;\"><strong>".$shift['shiftName']. "</strong> <span style=\"font-size:1.0em;\">(".$startTime."-".$endTime.")</span><br>";
											$htmlBody = $htmlBody."<span style=\"font-size:1.0em;\">";
											foreach($shift['users'] as $user) {
												$htmlBody = $htmlBody.$user['first_name']." ".$user['last_name']."<br>";
											}
											
											$htmlBody = $htmlBody."</span></div>";
										}										
												
											$htmlBody = $htmlBody."</td>"; 
                                    		$day_num++; 
                                    		$day_count++;

                                     		//Make sure we start a new row every week
                                     		if ($day_count > 7) {
                                    			$htmlBody = $htmlBody."</tr><tr>";
                                    			$day_count = 1;
                                     		}
                                     }
                                     
                                      //Finaly we finish out the table with some blank details if needed
                                     while ( $day_count >1 && $day_count <=7 ) { 
										$htmlBody = $htmlBody."<td></td>"; 
                                    	$day_count++; 
                                     } 
 
                                     $htmlBody = $htmlBody."</tr></table>";
                                     
    $htmlBody = $htmlBody."</div></form></div></body></html>";
    
    
    }
else {
	$htmlBody = $htmlBody."<div style=\"width:100%; text-align:center;\">";
	$htmlBody = $htmlBody."<h1>Timeoffs for ".$group."</h1>";
    
    $htmlBody = $htmlBody."<p>There are no shifts created for this user group.</p></div></div></body></html>";
}
                                     
    
    if($print == 1) {
	    toPdf($htmlBody, $sessionId, $gpCode);
    }
    else {
	    echo($htmlBody);
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
     
     function toPdf($stringContent, $sessId, $grpCode) {
     print_r("hello");
	     $data_array = array('id'=>$_SESSION['_id'],'sessionId'=>$sessId,'grpcode'=>$grpCode, 'content'=>$stringContent, 'type'=>'timeoff');
	     $data = http_build_query($data_array);
	     
	     
	     $response = do_post_request('http://schedulefwd.com/ws/createPDF', $data);
	    
	    header('Content-disposition: attachment; filename=schedule.pdf');
	    header('Content-type: application/pdf');
	    echo $response; 
     }
     
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

<script type="text/javascript">

function getParameterByName(name) {
  name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
  var regexS = "[\\?&]" + name + "=([^&#]*)";
  var regex = new RegExp(regexS);
  var results = regex.exec(window.location.search);
  if(results == null)
	return "";
  else
	return decodeURIComponent(results[1].replace(/\+/g, " "));
}


function sendDivToPDF(divToPDF) {
	var content = $('#pageHTML').html();
	$('#contentId').val(content);
	$('#frmTimeOff').submit();
	
	var prefObj;
	var div = $('#'+divToPDF).html();
	$.post('/ws/createPDF', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","content":div,"type":"timeoff"} , function(data) {
		var url = $(this).attr("PDFTest.php");
		var windowName = "popUp";//$(this).attr("name");
		var windowSize = 700;
		var w = window.open(url, windowName, windowSize);
		$(w.document.body).html(data);
		event.preventDefault();
	});	
	
}

</script>

