<? require("php_includes/isLoggedIn.php"); ?>
<? require("php_includes/protectedPage.php"); ?>
<!DOCTYPE html>
	<head>
		<title>Schedule Forward :: Medical scheduling software made easy</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<?php include("html_includes/adminMeta.php"); ?>
        <script language="javascript" src="js/modal.popup.js"></script>
        <script language="javascript" src="js/mySchedule.js"></script>
        <script src="js/jquery.freeow.min.js" type="text/javascript"></script>
        <script src="js/bootstrap/js/bootstrap.js" type="text/javascript"></script>
        <link href="js/bootstrap/css/bootstrap.css" rel="stylesheet">
		<script src="js/freeow-demo.js" type="text/javascript"></script>
        <script language="javascript" src="js/modal.popup.js"></script>
        
        <style>
			.calShift {
				font: 0.7em Arial, Helvetica, sans-serif;	
			}
			
			#calTable {
				width: 95%;
				margin: 0 auto;	
			}
			
			#calTable .userDisplay {
				float: right;
				border-bottom: 1px dotted #999999;	
			}
			
			#calTable .openShift {
				background-color: #CC0000;
			}
			
			#calTable th {
				font: 2.0em Arial, Helvetica, sans-serif;
				font-weight: bold;
			}
			
			#calTable #daysOfWeek td {
				font: 1.0em Arial, Helvetica, sans-serif;
				background-color: #6699CC;
				padding: 4px;
				color: #FFF;
				text-align: center;
				text-shadow: 0.1em 0.1em 0.05em #666
			}
			
			#calTable .shiftDayNum {
				width: 100%;
				text-align: right;
				color: #999;
			}
			
			#calTable .calShift {
				background-color: #E6E6E6;
				border: 1px solid #CCCCCC;
				margin-bottom: 1px;
				padding: 5px;
			}
			
			#calTable .calShift:hover {
				background-color: #D9D9D9;
				cursor: pointer;
			}
			
			#calTable #shiftDays td {
				font: 1.0em Arial, Helvetica, sans-serif;
				padding: 6px;
				color: #333333;
				background-color: #F2F2F2;
				border: 1px solid #9FBFDF;
			}
			
			#leftCol {
				float: left;
				font-size: 0.4em;
				padding: 5px;
			}
			
			#leftCol a:link, #leftCol a:visited {
				color: #2A7AA2;
			}
			
			#leftCol a:hover, #leftCol a:active {
				color: #49A3D0;	
			}
			
			.demo_container { width: 90%; margin: 0 auto; text-align: center; }
		#demo_top_wrapper { margin:0 0 20px 0; z-index: 1000; position: relative; }
		#demo_top { height:100px; padding:20px 0 0 0; }
		#my_logo { font:70px Georgia, serif; }
		 
		/* our menu styles */
		#sticky_navigation_wrapper { width:100%; height:50px; }
		#sticky_navigation { width:100%; margin-right: 90px; height:50px; background:url(../images/trans-black-60.png); -moz-box-shadow: 0 0 5px #999; -webkit-box-shadow: 0 0 5px #999; box-shadow: 0 0 5px #999; }
		#sticky_navigation ul { list-style:none; margin:0; padding:5px; }
		#sticky_navigation ul li { margin:0; padding:0; }
		#sticky_navigation ul li a { display:block; float:left; margin:0 0 0 5px; padding:0 20px; height:40px; line-height:40px; font-size:14px; font-family:Arial, serif; font-weight:bold; color:#ddd; background:#333; -moz-border-radius:3px; -webkit-border-radius:3px; border-radius:3px; }
		#sticky_navigation ul li a:hover, #sticky_navigation ul li a.selected { color:#fff; background:#111; }	
		</style>
        
	</head>
	<body>
		<!-- Header -->
			<div id="header-wrapper">
				<? include("html_includes/header.php"); ?>
                <div class="5grid-clear"></div>
			</div>

		<!-- Content -->
			<div id="content-wrapper">
				<div id="content">
                        <? include("html_includes/loggedInAs.php"); ?>
               
<?php	

error_reporting(E_ALL);
ini_set('display_errors', '1');

// These are globals and will be used in multiple places within the schedule code
$month = date("m");
$year = date("Y");
$gpCode = $_SESSION['grpcode'];
$sessionId = session_id();
$scheduleId = "";
$mineOnly = (isset($_GET['mine']) ? $_GET['mine'] : "");

$linkMonthP = $month;
$linkYearP = $year;
$linkMonthN = $month;
$linkYearN = $year;

if (isset($_GET['month'])) {
	$m = $_GET['month'];
} else {
	$m = $month;	
}

$linkMonthP = $m - 1;
$linkMonthN = $m + 1;

if (isset($_GET['year'])) {
	$y = $_GET['year'];
} else {
	$y = $year;	
}

$linkYearP = $y;
$linkYearN = $y;

if ($m == "12") {
	$linkYearN = $y + 1;
	$linkMonthN = "1";
}

if ($m == "1") {
	$linkYearP = $y - 1;
	$linkMonthP = "12";
}

if (isset($_GET['year'])) {
	$linkYear = $_GET['year'];	
}

$schedule = getSchedule();
$scheduleId = $schedule['data']['schedule']['1']['scheduleId'];
$isPublished = $schedule['data']['published'];
$isPublishedAction = "hide";

?>
                
<script language="javascript"> 

	$(document).ready(function() {
		// $('.userDisplay').popover({trigger:'hover'});
		<? if ($role == "Admin") { ?>
			var role = "Admin";
			$('#demo_top_wrapper').show();
		<? }else { ?>
			var role = "User";
		<? } ?>
		
		var sticky_navigation_offset_top = $('#sticky_navigation').offset().top;
		 
		// our function that decides weather the navigation bar should have "fixed" css position or not.
		var sticky_navigation = function() {
			var scroll_top = $(window).scrollTop(); // our current vertical position from the top
			 
			// if we've scrolled more than the navigation, change its position to fixed to stick to top,
			// otherwise change it back to relative
			if (scroll_top > sticky_navigation_offset_top) {
				$('#sticky_navigation').css({ 'position': 'fixed', 'top':0});
			} else {
				$('#sticky_navigation').css({ 'position': 'relative' });
			}  
		};
		 
		// run our function on load
		sticky_navigation();
		 
		// and run it again every time you scroll
		$(window).scroll(function() {
			 sticky_navigation();
		});
		
		<?php if (($isPublished == 0) && ($role == "Admin")) { ?>
			$('#publishSchedule').show();
		<?php } ?>
		
		var date = new Date();
		var d = date.getDate();
		m = date.getMonth();
		y = date.getFullYear();
		m++;
		
		if (m < 10) {
			m = "0" + m;	
		}
		
		m2 = m;
		y2 = y;
		
		var scheduleDate = new Date();
		var month = getParameterByName("month");
		var year = getParameterByName("year");
		
		month2 = month;
		month2--;
		year2 = year;
		
		if ((month2 != undefined) && (month2 != "")) {
			scheduleDate.setMonth(month2);	
		}
		
		if ((year2 != undefined) && (year2 != "")) {
			scheduleDate.setYear(year2);	
		}
		
		// Determine whether or not to display the print archive links
		if ((month <= m) && (year2 <= y)) {
			$('#archiveDisplay').show();
			$('#archiveDisplayStart').show();
		}
	
		if ((month != "") && (month < m) && (year2 <= y)) {
			$('#archiveDisplay').show();
			$('#archiveDisplayEnd').show();
		}
		// End Determine whether or not to display the print archive links
	
		if ((month != undefined) && (month != "")) {
			m = month;	
		}
		
		if ((year != undefined) && (year != "")) {
			y = year;	
		}
		
		$('#ddlMonth').val(m);
		$('#ddlYear').val(y);
		
		constructLinkForPrintSchedule(m,y);
		getUsersForHighlight();
	});
	
	function switchMonth() {
		var month = $('#ddlMonth :selected').val();
		var year = $('#ddlYear :selected').val();
		window.location = "mySchedule.php?month=" + month + "&year=" + year;
	}
	
	function constructLinkForPrintSchedule(m,y) {
		var baseURL = "/toPDF/?type=mainschedule";
		
		$('#printLink').attr("href",baseURL + "&month=" + m + "&year=" + y + "&print=1");
		$('#archiveStart').attr("href",baseURL + "&month=" + m + "&year=" + y + "&print=1&when=start");
		$('#archiveEnd').attr("href",baseURL + "&month=" + m + "&year=" + y + "&print=1&when=end");
	}
	
	var highlightUser;
	function getUsersForHighlight() {
		$.post('ws/getUsersForHighlight', {"sessionId":"<?=session_id()?>","grpcode":"<?=$_SESSION['grpcode']?>","scheduleId":'<?=$GLOBALS['scheduleId']?>'} , function(data) {
			userObj = data.data;
			highlightUser = userObj;

			for (var user in userObj) {
				var first = userObj[user].first_name;
				var last = userObj[user].last_name;
				var user = userObj[user].user_name;
				var display = first + " " + last;
				$('#ddlUsers').append($('<option>', { 
					value: user,
					text : display 
				}));
			}
		});
	}
	
	function getHighlightForUser(username) {
		var highlightObj;
		
		$.post('ws/getHighlightForUser', {"sessionId":"<?=session_id()?>","grpcode":"<?=$_SESSION['grpcode']?>","scheduleId":'<?=$GLOBALS['scheduleId']?>',"username":username} , function(data) {
			highlightObj = data.data;
		});
		
		return highlightObj;
	}
	
	function processHighlightRequest() {
		var user = $('#ddlUsers :selected').val();
		var type = $('#ddlType :selected').val();

		if ((user != "") && (type != "")) {
			$('.calShift').css("background-color","#F2F2F2");
			$('[id*=Open]').css("color","#333333");
			$('[id*=open]').css("color","#333333");
			for (var u in highlightUser) {
				if (user == highlightUser[u].user_name) {
					if (type == "timeoff") {
						var timeoffs = highlightUser[u].timeoffs;
						for (var t in timeoffs) {
							var shift = timeoffs[t][0];
							var color = timeoffs[t][1];
							$('[id*=' + shift + ']').css("background-color","" + color + "");
							$('#filterLegend').show();
							$('#filterLegendTimeoffs').show();
							$('#filterLegendShifts').hide();
						}
					} else if (type == "shifts") {
						$.post('ws/getHighlightForUser', {"sessionId":"<?=session_id()?>","grpcode":"<?=$_SESSION['grpcode']?>","scheduleId":'<?=$GLOBALS['scheduleId']?>',"username":user} , function(data) {
							highlightObj = data.data;
							for (h in highlightObj) {
								$('[id*=' + h + ']').css("background-color","" + highlightObj[h] + "");	
							}
							$('#filterLegend').show();
							$('#filterLegendShifts').show();
							$('#filterLegendTimeoffs').hide();
						});
					}
				}
			}
		} else if (type == "") {
			$('#filterLegend').hide();
			$('.calShift').css("background-color","#F2F2F2");
			$('[id*=Open]').css("background-color","#CC0000");
			$('[id*=open]').css("background-color","#CC0000");
			$('[id*=Open]').css("color","#FFFFFF");
			$('[id*=open]').css("color","#FFFFFF");
		} else {
			$('#filterLegend').hide();	
		}
	}

</script>
                        
<?php 
	$openOnly = (isset($_GET['open']) ? $_GET['open'] : "");
	
	// Build the link for the 'Open/All Shifts' link in static navigation
	$shiftFilterLink = "";
	$shiftFilterVal = "1";
	if ($GLOBALS['openOnly'] == "1") {
		$shiftFilterLink = "All shifts";
		$shiftFilterVal = "0";	
	} else {
		$shiftFilterLink = "Open shifts";
	}
?>
                        
<div class="container" style="width: 100%;">
                        
<br /><br />
<div id="demo_top_wrapper" style="display: none;">
    <!-- this will be our navigation menu -->
    <div id="sticky_navigation_wrapper">
        <div id="sticky_navigation">
            <div class="demo_container" style="text-align: center;">
                <ul>
                    <li><span style="cursor: pointer; border-bottom: 1px dotted #CCC;"><a name="openShifts" href="mySchedule.php?open=<?=$shiftFilterVal;?>"><?=$shiftFilterLink;?></a></span></li>
                    <li><span style="cursor: pointer; border-bottom: 1px dotted #CCC;" onClick="regenerateSchedule('<?=session_id();?>','<?=$_SESSION['grpcode'];?>','<?=$GLOBALS['scheduleId'];?>','<?=$GLOBALS['y'];?>','<?=$GLOBALS['m'];?>')"><a name="regen">Generate Schedule</a></span></li>
                    <li><span style="cursor: pointer; border-bottom: 1px dotted #CCC;" onClick=""><a name="printSchedule" id="printLink" href="">Print Schedule</a></span></li>
                    <span id="archiveDisplay" style="display: none;">
                        <span id="archiveDisplayStart" style="display: none;">
                            <li><a href="" id="archiveStart" href="">Start Archive Print</a></li>
                        </span>
                        <span id="archiveDisplayEnd" style="display: none;">
                            <li><a href="" id="archiveEnd" href="">End Archive Print</a></li>
                        </span>
                    </span>
                    <li><a name="">
                    	<select id="ddlUsers" name="ddlUsers" style="margin-top: 10px;" onchange="processHighlightRequest()">
                        	<option value="">-- Highlight by User --</option>
                        </select>
                        </a>
                    </li>
                    <li><a name="">
                    	<select id="ddlType" name="ddlType" style="margin-top: 10px;" onchange="processHighlightRequest()">
                        	<option value="">-- Select filter type --</option>
                            <option value="shifts">Shifts</option>
                            <option value="timeoff">Timeoff Requests</option>
                        </select>
                        </a>
                    </li>
                    <li><a id="printLink" onclick="displayStatsPopup('<?=session_id();?>','<?=$_SESSION['grpcode'];?>')"><img src="../images/statistics.png" id="statsLink" style="width: 25px; height: 25px; margin-top: 8px;" /></a></li>
                </ul>
            </div>
        </div>
    </div>
</div><!-- #demo_top_wrapper -->

<div id="publishScheduleWaiting" style="display: none; text-align: center; margin-top: 10px;">
	<h2>Schedule generation in progress...</h2>
	<img src="/images/ajax-loader.gif" alt="Schedule generation in progress" title="Schedule generation in progress" />
</div>

<div id="publishSchedule" style="text-align: center;" onClick="publishSchedule('<?=session_id();?>','<?=$_SESSION['grpcode'];?>','<?=$GLOBALS['scheduleId']?>')">
	<img src="/images/star.png" /> Publish this schedule <img src="/images/star.png" />
</div>

<div id="filterLegend" style="display: none; margin: 0 auto; width: 400px; border: 1px solid #333;">
	<div id="filterLegendShifts" style="display: none; width: 330px; float: right; background-color: #F2F2F2; color: #333;">
    &nbsp; &nbsp; 
    	<span style="background-color: #95E495; padding: 3px;">Preferred Shift</span> &nbsp; 
        <span style="background-color: yellow; padding: 3px;">Neutral Preference</span> &nbsp; 
        <span style="background-color: #E49595; padding: 3px;">Undesired Shift</span>
    </div>
	<div id="filterLegendTimeoffs" style="display: none; width: 330px; float: right; background-color: #F2F2F2; color: #333;">
    &nbsp; &nbsp; 
    	<span style="background-color: yellow; padding: 3px;">Requested off</span> &nbsp; 
        <span style="background-color: #E49595; padding: 3px;">Mandatory request off</span> &nbsp; 
        <span style="background-color: #95E495; padding: 3px;">Must work</span>
    </div>
	<div style="width: 70px; background-color: #333; color: #FFF; text-align: center; font-weight: bold;">Legend</div>
</div>
							<?  
                                function getSchedule() {
									$time_start = microtime(true);
                                    $data_array = array('sessionId'=>session_id(),'grpcode'=>$_SESSION['grpcode'], 'month'=>$GLOBALS['m'], 'year'=>$GLOBALS['y'], 'id'=>$_SESSION['_id']);
                                    $data = http_build_query($data_array);
                                
                                    $response = do_post_request('http://schedulefwd.com/dev/ws/getSchedule', $data);
                                    $response = json_decode($response, true); 	    

									$time_end = microtime(true);
									$time = $time_end - $time_start;
									
                                    return $response;
                                }
								
								function getTimePreference() {
                                    $data_array = array('sessionId'=>$GLOBALS['sessionId'],'grpcode'=>$GLOBALS['gpCode'], 'key'=>"displayTimes");
                                    $data = http_build_query($data_array);
                                
                                    $response = do_post_request('http://schedulefwd.com/dev/ws/getConfigByKey', $data);
                                    $response = json_decode($response, true);
									
                                    return $response['data']['displayTimes'];
								}
                                
								function getSortPreference() {
                                    $data_array = array('sessionId'=>$GLOBALS['sessionId'],'grpcode'=>$GLOBALS['gpCode'], 'key'=>"sortBy");
                                    $data = http_build_query($data_array);
                                
                                    $response = do_post_request('http://schedulefwd.com/dev/ws/getConfigByKey', $data);
                                    $response = json_decode($response, true);
									
									//time or siteTime
                                    return $response['data']['sortBy'];
								}
								
								function getLocations() {
                                    $data_array = array('sessionId'=>$GLOBALS['sessionId'],'grpcode'=>$GLOBALS['gpCode']);
                                    $data = http_build_query($data_array);
                                
                                    $response = do_post_request('http://schedulefwd.com/dev/ws/getLocations', $data);
                                    $response = json_decode($response, true);

                                    return $response['data'];
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
                                
                                function getScheduledShiftsForMonthByDay() {
                                    $schedule = $GLOBALS['schedule'];
									$GLOBALS['timePref'] = getTimePreference();
									$GLOBALS['sortBy'] = getSortPreference();
									$GLOBALS['locations'] = getLocations();

                                    $shiftHolder = array();
                                	$i = 0;
									
									if ($schedule['data'] != null) {
										foreach ($schedule['data']['schedule'] as $s) {
											if (isset($s['start'])) {
												// Extract the date from the start date of the shift
												
												$tmpDate = $s['day'];
												$tmpShiftHolder = array();
												
												// Build the shift structure
												$tmpShift = array();
												$tmpShift['name'] = $s['shiftName'];
												$tmpShift['start'] = $s['start'];
												$tmpShift['end'] = $s['endreal'];
												$tmpShift['traded'] = $s['traded'];
												$tmpShift['users'] = $s['users'];
												$tmpShift['id'] = $s['shiftId'];
												$tmpShift['location'] = $s['location'];
												$tmpShift['shiftNum'] = $i;
												
												if (array_key_exists($tmpDate,$shiftHolder)) {
													foreach ($shiftHolder[$tmpDate] as $shift) {
														array_push($tmpShiftHolder, $shift);	
													}
												}
												
												array_push($tmpShiftHolder, $tmpShift);
												ksort($tmpShiftHolder);
												
												$shiftHolder[$tmpDate] = $tmpShiftHolder;
												
												$i++;
											}
										}
									} else {
										$shiftHolder = false;	
									}
                                
                                    return $shiftHolder;
                                }
                                
                                function buildCalendar() {
                                    $date = time();
                                    $shiftDays = getScheduledShiftsForMonthByDay();
									
                                    $month = $GLOBALS['m'];
									$year = $GLOBALS['y'];
									
                                    // Here we generate the first day of the month (starting on Monday, so we subtract a day)
                                    $first_day = mktime(0,0,0,$month, 1, $year)-1;
									
									// When on the 1st, the month will read the 31st of the previous month due to our offset
									// subtraction above; keep a non-offset date available for month display
									$first_day_without_offset = mktime(0,0,0,$month, 1, $year);
                                    
                                    // This gets us the month name
                                    $title = date('F', $first_day_without_offset);
                                    
                                    // Here we find out what day of the week the first day of the month falls on 
                                    $day_of_week = date('D', $first_day) ; 
                                
                                    // Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero
                                     switch($day_of_week) { 
                                         case "Mon": $blank = 1; break; 
                                         case "Tue": $blank = 2; break; 
                                         case "Wed": $blank = 3; break; 
                                         case "Thu": $blank = 4; break; 
                                         case "Fri": $blank = 5; break; 	
                                         case "Sat": $blank = 6; break; 
                                         case "Sun": $blank = 7; break; 
                                     }

                                     // We then determine how many days are in the current month
                                     $days_in_month = cal_days_in_month(0, $month, $year);
                                     
                                     // Here we start building the table heads 
                                     echo '<table border=1 id="calTable">';
                                        echo "<tr>";
                                            echo "<th colspan=7>$title $year</th>";
                                        echo "</tr>";
                                        echo "<tr>";
                                            echo "<th colspan=7 style=\"background-color: #F2F2F2;\">";
												echo '<div id="leftCol">';
													echo '<a href="mySchedule.php?month=' . $GLOBALS['linkMonthP'] . '&year=' . $GLOBALS['linkYearP'] . '">Previous Month</a> | ';
													echo '<a href="mySchedule.php?month=' . $GLOBALS['linkMonthN'] . '&year=' . $GLOBALS['linkYearN'] . '">Next Month</a> &nbsp; &nbsp;';
													echo 'Or, select: ';
													echo '<select name="ddlMonth" id="ddlMonth">';
														echo '<option value="1">January</option>';
														echo '<option value="2">February</option>';
														echo '<option value="3">March</option>';
														echo '<option value="4">April</option>';
														echo '<option value="5">May</option>';
														echo '<option value="6">June</option>';
														echo '<option value="7">July</option>';
														echo '<option value="8">August</option>';
														echo '<option value="9">September</option>';
														echo '<option value="10">October</option>';
														echo '<option value="11">November</option>';
														echo '<option value="12">December</option>';
													echo '</select>';
													echo '<select name="ddlYear" id="ddlYear">';
														echo '<option value="2012">2012</option>';
														echo '<option value="2013">2013</option>';
													echo '</select>';
													echo '<input type="button" value="GO" onclick="switchMonth();">';
												echo '</div>';
											echo "</th>";
                                        echo "</tr>";
                                        echo '<tr id="daysOfWeek">';
                                            echo "<td width=42>Monday</td>";
                                            echo "<td width=42>Tuesday</td>";
                                            echo "<td width=42>Wednesday</td>";
                                            echo "<td width=42>Thursday</td>";
                                            echo "<td width=42>Friday</td>";
                                            echo "<td width=42>Saturday</td>";
                                            echo "<td width=42>Sunday</td>";
                                        echo "</tr>";
										if ($shiftDays != false) {
                                    
										 // This counts the days in the week, up to 7
										 $day_count = 1;
										 echo '<tr id="shiftDays">';
										
										 // First we take care of those blank days
										 while ( $blank > 0 ) { 
											echo "<td></td>"; 
											$blank = $blank-1; 
											$day_count++;
										 }
										 
										 // Sets the first day of the month to 1 
										 $day_num = 1;
                                    
										 // Count up the days, until we've done all of them in the month
										 while ( $day_num <= $days_in_month ) { 
											echo '<td id="' . $day_num . '"> <div class="shiftDayNum">' . $day_num . '</div>';
											if (strlen($day_num) == 1) {
												$day_num = "0" . $day_num;	
											}
											
											if ($GLOBALS['sortBy'] == "siteTime") {
												foreach ($GLOBALS['locations'] as $location) {
													$locName = $location['name'];
													echo '<div style="width: 100%; font: 0.8em Verdana; margin-top: 10px;">' . $locName . "</div>";
													// Loop through each shift for this day
													foreach ($shiftDays[$day_num] as $shift) {
														if ($locName == $shift['location']) {
															$start = strtotime($shift['start']);
															$end = strtotime($shift['end']);
															$traded = $shift['traded'];
															$inc = 0;
															
															if ($GLOBALS['openOnly'] != "1") {
																displayFilledShift($shift,$start,$end,$day_num,$traded);
																displayOpenShift($shift,$start,$end,$day_num);
															} else {
																displayOpenShift($shift,$start,$end,$day_num);
															}	
														}
													}
												}
											} else {
												foreach ($shiftDays[$day_num] as $shift) {
													$start = strtotime($shift['start']);
													$end = strtotime($shift['end']);
													$traded = $shift['traded'];
													$inc = 0;
													
													if ($GLOBALS['openOnly'] != "1") {
														displayFilledShift($shift,$start,$end,$day_num,$traded);
														displayOpenShift($shift,$start,$end,$day_num);
													} else {
														displayOpenShift($shift,$start,$end,$day_num);
													}	
												}	
											}
									
											echo "</td>"; 
											$day_num++; 
											$day_count++;
											
											// Make sure we start a new row every week
											if ($day_count > 7) {
												echo '</tr><tr id="shiftDays">';
												$day_count = 1;
											}
										 } 
										 
										// Finaly we finish out the table with some blank details if needed
										while ( $day_count >1 && $day_count <=7 ) { 
											echo "<td> </td>"; 
											$day_count++; 
										}
									 } else {
										echo '<td colspan="7">';
											echo '<div style="background-color: #E6E6E6; width: 100%; text-align: center; font: 1.5em Verdana;">';
												echo '<div style="padding: 5px;">No schedule generated</div>';
											echo '</div>';
										echo '</td>'; 
									 }
                                    
                                    echo "</tr></table>"; 
                                }
								
								function displayFilledShift($shift,$start,$end,$day_num,$traded) {
									if ($shift['users'] != "") {
										foreach ($shift['users'] as $user) {
											$bgColor = "#E6E6E6";
											$timeString = " " . date('hA', $start) . "-" . date('hA', $end);
											if ($traded == 1) {
												$bgColor = "#00CC30";
											}
											if (($GLOBALS['timePref'] == "false") && ($GLOBALS['role'] == "Admin")) {
												$timeString = "";
											}

											if (strlen($user['first_name']) < 1) {
												if ($GLOBALS['role'] == "Admin") {
													$bgColor = "#CC0000";
												}
												echo '<div onclick="displayPopup(\'' . $GLOBALS['scheduleId'] . '\', \'' . $shift['shiftNum'] . '\', \'' . session_id() . '\', \'' . $_SESSION['grpcode'] . '\', \'' . $day_num . "_" . $shift['id'] . "_" . strtolower($user['user_name']) . '\', \'' . $GLOBALS['role'] . '\')" removeId="' . $day_num . "_" . $shift['id'] . '" class="calShift" style="background-color: ' . $bgColor . '; color: #FFF;" id="' . $day_num . "_" . $shift['id'] . "_" . strtolower($user['user_name']) . '">';
													echo '<div class="userDisplay Open">';
														echo "OPEN";
													echo '</div>';
													echo $shift['name'] . $timeString;
												echo '</div>';
											} else {
												$displayUser = substr($user['first_name'], 0, 1) . ". " . $user['last_name'];
												if ($_SESSION['userName'] == strtolower($user['user_name'])) {
													$bgColor = "#FFFF70";
													echo '<div onclick="tradeShift(\'' . $GLOBALS['scheduleId'] . '\', \'' . $shift['shiftNum'] . '\', this, \'' . session_id() . '\', \'' . $_SESSION['grpcode'] . '\')" removeId="' . $day_num . "_" . $shift['id'] . '" class="calShift" style="background-color: ' . $bgColor . ';" id="' . $day_num . "_" . $shift['id'] . "_" . strtolower($user['user_name']) . '">';
														echo '<div class="userDisplay ' . strtolower($user['user_name']) . '">';
															echo $displayUser;
														echo '</div>';
														echo $shift['name'] . $timeString;
													echo '</div>';
												} else {
													echo '<div onclick="displayPopup(\'' . $GLOBALS['scheduleId'] . '\', \'' . $shift['shiftNum'] . '\', \'' . session_id() . '\', \'' . $_SESSION['grpcode'] . '\', \'' . $day_num . "_" . $shift['id'] . "_" . strtolower($user['user_name']) . '\', \'' . $GLOBALS['role'] . '\')" removeId="' . $day_num . "_" . $shift['id'] . '" removeId="' . $day_num . "_" . $shift['id'] . '" class="calShift" style="background-color: ' . $bgColor . ';" id="' . $day_num . "_" . $shift['id'] . "_" . strtolower($user['user_name']) . '">';
														echo '<div class="userDisplay ' . strtolower($user['user_name']) . '">';
															echo $displayUser;
														echo '</div>';
														echo $shift['name'] . $timeString;
													echo '</div>';
												}
											}
										}
									}
								}
								
								function displayOpenShift($shift,$start,$end,$day_num) {
									if ($shift['users'] == "") {
										$timeString = " " . date('hA', $start) . "-" . date('hA', $end);

										if (($GLOBALS['timePref'] == "false") && ($GLOBALS['role'] == "Admin")) {
											$timeString = "";
										}
										
										if ($GLOBALS['role'] == "Admin") {
											$bgColor = "#CC0000";
											$ftColor = "#FFFFFF";
										} else {
											$bgColor = "#E6E6E6";
											$ftColor = "#333333";	
										}
										
										echo '<div onclick="displayPopup(\'' . $GLOBALS['scheduleId'] . '\', \'' . $shift['shiftNum'] . '\', \'' . session_id() . '\', \'' . $_SESSION['grpcode'] . '\', \'' . $day_num . "_" . $shift['id'] . "_Open" . '\', \'' . $GLOBALS['role'] . '\')" removeId="' . $day_num . "_" . $shift['id'] . '" class="calShift" style="background-color: ' . $bgColor . '; color: ' . $ftColor . ';" id="' . $day_num . "_" . $shift['id'] . "_Open" . '">';
											echo '<div class="userDisplay Open">';
												echo "OPEN";
											echo '</div>';
											echo $shift['name'] . $timeString;
										echo '</div>';
                                	}
								}

                                buildCalendar();
                                
                            ?>
					</div>
			</div>
            </div>

		<!-- Copyright -->
			<div id="copyright">
				(c) 2012 Forward Intelligence Systems, LLC. All rights reserved.
			</div>

	</body>
</html>