        <script language="javascript">			
			$(document).ready(function(){
				$('div[title]').qtip();
				$('img[title]').qtip();
				loadAccountInfo();
				loadPreferences();
				loadAlerts();
				loadNextFive();
				loadStats();
			});
			
			function loadStats() {
				var statsObj;
				$.post('ws/getMyStats', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
						statsObj = data.data; 
				});
				
				var hours = "";
				var remaining = "";
				var traded = "";
				var totalshifts = "";
				var shifts = "";
				var total = "";
				
				if (statsObj != null) {
					hours = statsObj.hours;
					remaining = statsObj.remaining;
					traded = statsObj.traded;
					totalshifts = statsObj.totalshifts;
					shifts = statsObj.shifts;
					total = statsObj.total;
				} else {
					hours = "N/A";
					remaining = "N/A";
					traded = "N/A";
					totalshifts = "N/A";
					shifts = "N/A";
					total = "N/A";
				}
				
				$('#hours').html(hours);
				$('#remaining').html(remaining);
				$('#traded').html(traded);
				$('#totalshifts').html(totalshifts);
				$('#shifts').html(shifts);
				$('#yearlyhours').html(total);
			}
			
			function processPreferences() {
				// Get the checked day values for preferences
				var days = "";
				$('.prefDays').each(function(index) {
					if (this.checked) {
						days = days + $(this).val() + ",";
					}
				});
				days = days.substring(0,days.length-1);
				
				// Get the checked shift values for preferences
				var shifts = "";
				$('.ui-state-default').each(function(index) {
					shifts = shifts + "" + $(this).attr('shiftId') + ",";
				});
				shifts = shifts.substring(0,shifts.length-1);
				
				// Get block working values
				var blockWeekends = 0;
				var blockWeekdays = 0;
				if ($('#blockDaysWeekend:checked').val() == "Yes") {
					blockWeekends = 1;
				}
				if ($('#blockDaysWeekdays:checked').val() == "Yes") {
					blockWeekdays = 1;
				}
				
				var overrideCircadian = 0;
				if ($('#overrideCircadian:checked').val() == "Yes") {
					overrideCircadian = 1;
				}
				
				var afterNightShift = $('#afterNightShift').val();
				
				// Get maximum and desired consecutive shift values
				var maxNights = $('#maxNights').val();
				var maxDays = $('#maxDays').val();
				var desiredNights = $('#desiredNights').val();
				var desiredDays = $('#desiredDays').val();
								
				$.post('ws/editPreferences', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":"<?=$_SESSION['_id']?>","shifts":shifts,"days":days,"blockweekend":blockWeekends,"blockdays":blockWeekdays,"maxnights":maxNights,"desirednights":desiredNights,"maxdays":maxDays,"desireddays":desiredDays,"circadian":overrideCircadian,"afterNightShift":afterNightShift} , function(data) {
					if (data.message == "success") {
						$("#freeow").freeow("Scheduling preferences updated", "You have successfully updated your scheduling preferences.", {
							classes: ["gray", "success"],
							autoHide: true
						});
					} else {
						$("#freeow").freeow("Scheduling preferences error", "An error occurred while updating your work preferences.", {
							classes: ["gray", "error"],
							autoHide: true
						});
					}
				});
			}
			
			$(function() {
				 $( "#sortable" ).sortable();
				 $( "#sortable" ).disableSelection();
		 	});

			function clearAlert(id) {
				var alertObj;
				$.post('ws/deleteAlerts', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","alertId":id} , function(data) {
						if (data.message == "success") {
							$('#' + id).remove();
						}
				});
			}
			
			function getDayOfWeek(inDate) {
				var weekday=new Array(7);
				weekday[0]="Sunday";
				weekday[1]="Monday";
				weekday[2]="Tuesday";
				weekday[3]="Wednesday";
				weekday[4]="Thursday";
				weekday[5]="Friday";
				weekday[6]="Saturday";
				var n = weekday[inDate.getDay()];
				return n;
			}
			
			function loadNextFive() {
				var shiftObj;
				$.post('ws/getNextFive', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
						shiftObj = data.data;
				});
				
				// $('#tblNextFive > tbody:last').appendTo('<tr>');
				$('<tr>').appendTo('#tblNextFive > tbody:last');
				
				if (shiftObj != null) {
					for (var i = 0; i < shiftObj.length-1; i++) {
						var date = $.trim(shiftObj[i].start);
						var users = shiftObj[i].users;
						var displayUsers = "";
						var strStartDate = $.trim(shiftObj[i].start);
						strStartDate = strStartDate.replace(" ","T");
						var dStart = new Date(strStartDate);
						var dDay = "";
						var ampm = "am";
	
						for (var ii = 0; ii < users.length; ii++) {
							displayUsers += users[ii].first_name.substring(0,1) + " " + users[ii].last_name + "<br />";	
						}
	
						startTime = dStart.getHours();
						dDay = getDayOfWeek(dStart);
						var month = dStart.getMonth() + 1;
						startDate = dStart.getFullYear() + "-" + month + "-" + dStart.getDate();
						
						if (startTime > 12) {
							startTime = startTime - 12;
							ampm = "pm";	
						}
						if (startTime == 0) {
							startTime = 12;	
						}
						// $('#tblNextFive > tbody:last').appendTo('<td><div style="width: 120px; padding: 3px; border: 1px solid #C9C9C9; font: 0.8em \'Courier New\', Courier, monospace; line-height: 15px;"><div style="width: 97%; background-color: #C9C9C9; padding: 3px; margin: 0 auto;" title="' + date + '">' + startTime + ampm + ' ' + dDay + '</div><span style="font-weight: bold;">' + name + '</span></div></td>');
						$('<td><div style="width: 140px; padding: 6px; border: 1px solid #C9C9C9; font: 0.8em \'Courier New\', Courier, monospace; line-height: 15px;"><div style="width: 97%; background-color: #C9C9C9; padding: 2px; margin: 0 auto;" title="' + date + '">' + startTime + ampm + ' ' + startDate + '</div><span style="font-weight: bold;">' + displayUsers + '</span></div></td>').appendTo('#tblNextFive > tbody');
					}
				}
				
				// $('#tblSingle > tbody:last').appendTo('</tr>');
				$('</tr>').appendTo('#tblSingle > tbody:last')
			}

				function loadAlerts() {
					var alertObj;
					$.post('ws/getAlerts', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
							alertObj = data.data;
				});
				
				if (alertObj != null) {
				
					for (i = 0; i < alertObj.length; i++) {
						var id = alertObj[i]._id.$id;
						var severity = alertObj[i].severity;
						var message = strongitizeMessage(alertObj[i].message);
							
					
						var icon;
						if (severity == "Alert") {
							icon = '<img src="images/exclamation.png" />';	
						} else {
							icon = '<img src="images/valid.png" />';
						}
						
						// $('#tblAlerts > tbody:last').appendTo('<tr id="' + id + '"><td style="margin: 1px; background-color: #F2F2F2; padding: 3px; border: 1px solid #C9C9C9; font: 0.8em \'Times New Roman\', Times, serif; line-height: 15px;"><span style="float: right;"><img src="images/cancel.png" onclick="clearAlert(\'' + id + '\')" /></span>' + icon + ' ' + message + '</td></tr>');	
						$('<tr id="' + id + '"><td style="margin: 1px; background-color: #F2F2F2; padding: 3px; border: 1px solid #C9C9C9; font: 0.8em \'Times New Roman\', Times, serif; line-height: 15px;"><span style="float: right;"><img src="images/cancel.png" onclick="clearAlert(\'' + id + '\')" /></span>' + icon + ' ' + message + '</td></tr>').appendTo('#tblAlerts > tbody:last');	
					
					}
				
				} else {
						// $('#tblAlerts > tbody:last').appendTo('<tr><td style="margin: 1px; background-color: #F2F2F2; padding: 3px; border: 1px solid #C9C9C9; font: 0.8em \'Times New Roman\', Times, serif; line-height: 15px;"><em>No unread alerts</em></td></tr>');	
						$('<tr><td style="margin: 1px; background-color: #F2F2F2; padding: 3px; border: 1px solid #C9C9C9; font: 0.8em \'Times New Roman\', Times, serif; line-height: 15px;"><em>No unread alerts</em></td></tr>').appendTo('#tblAlerts > tbody:last');	
				}
			}

			function strongitizeMessage(message) {
				var strong = '<span style="font-weight: bold;">';
				var endStrong = '</span>';
				var words = ['Disapproved','Approved','time off request'];
				
				for (var i = 0; i < words.length; i++) {
					message = message.replace(words[i],"" + strong + words[i] + endStrong);
				}
				return message;
			}

			function loadPreferences() {
				$.ajaxSetup({async:false});
				var prefObj;
				$.post('ws/getPreferences', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":"<?=$_SESSION['_id']?>"} , function(data) {
						prefObj = data.data;
				});
				
				if (prefObj != null) {
					var days = prefObj.days;
					for (var i = 0; i < days.length; i++) {
						if (days[i] != undefined) {
							$('#'+days[i]).attr("checked","checked");	
						}
					}
					
					var shifts = prefObj.shifts;
					for (var i = 0; i < shifts.length; i++) {
						if (shifts[i] != undefined) {
							$('<li class="ui-state-default" shiftId="' + shifts[i][0] + '" style="padding-left: 10px;">' + shifts[i][1] + '</li>').appendTo('#sortable');	
						}
					}
				}
				
				if (prefObj.block_weekend == 0) {
					$('input:radio[id="blockDaysWeekend"]').filter('[value="No"]').attr('checked', true);
				} else {
					$('input:radio[id="blockDaysWeekend"]').filter('[value="Yes"]').attr('checked', true);
				}
				
				if (prefObj.block_days == 0) {
					$('input:radio[id="blockDaysWeekdays"]').filter('[value="No"]').attr('checked', true);
				} else {
					$('input:radio[id="blockDaysWeekdays"]').filter('[value="Yes"]').attr('checked', true);
				}

				if (prefObj.circadian == 0) {
					$('input:radio[id="overrideCircadian"]').filter('[value="No"]').attr('checked', true);
				} else {
					$('input:radio[id="overrideCircadian"]').filter('[value="Yes"]').attr('checked', true);
				}
				
				$('#afterNightShift').val(prefObj.afterNightShift);
				$('#desiredDays').val(prefObj.desired_days);
				$('#desiredNights').val(prefObj.desired_nights);
				$('#maxDays').val(prefObj.max_days);
				$('#maxNights').val(prefObj.max_nights);
			}

			function loadAccountInfo() {
				$.ajaxSetup({async:false});
				var userObj;
				$.post('ws/getUser', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":"<?=$_SESSION['_id']?>"} , function(data) {
						userObj = data.data[0];
				});
			
				$('#pflName').html(userObj.first_name + ' ' + userObj.last_name);
				$('#pflGroup').html(userObj.group);
				$('#pflPhone').html(userObj.phone);
				$('#pflEmail').html(userObj.email);
			}
			
			function printSchedule() {
				var date = new Date();
				var d = date.getDate();
				m = date.getMonth();
				y = date.getFullYear();
				m++;
				if (m < 10) {
					m = "0" + m;	
				}

				var baseURL = "/toPDF/?type=yourschedule";
                window.location.href=baseURL + "&month=" + m + "&year=" + y + "&print=1";	
			}
			
			var userObj;
				$.post('ws/getiCal', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","month":"10","year":"2012"} , function(data) {
						userObj = data.data[0];
				});
        </script>
		
		<style>
			.column { width: 50%; float: left; padding-bottom: 100px; }
			.portlet { margin: 0 1em 1em 0; background-color: #FFF; }
			.portlet-header { padding: 5px; background-image:url('images/email_header.gif'); text-align: center; margin: 1px; }
			.portlet-heading { padding: 5px; font: 0.9em "Lucida Sans Unicode", "Lucida Grande", sans-serif; background-image:url('images/email_header.gif'); text-align: center; margin: 1px;}
			.portlet-heading .ui-icon { float: right; }
			.portlet-content { padding: 0.4em; font: 0.9em Arial, sans-serif; }
			.ui-sortable-placeholder { border: 1px dotted black; visibility: visible !important; height: 50px !important; }
			.ui-sortable-placeholder * { visibility: hidden; }
		</style>

	</head>
	<body>

		<div id="freeow" class="freeow freeow-top-right"></div>

		<!-- Header -->
			<div id="header-wrapper">
				<? include("html_includes/header.php"); ?>
                <div class="5grid-clear"></div>
			</div>

		<!-- Content -->
			<div id="content-wrapper">
				<div id="content">
					<div class="5grid">
                        <? include("html_includes/loggedInAs.php"); ?>
						<div class="9u-first">

							<!-- Box #1 -->
								<section>
                                
                                    <div style="margin-top: 2px; margin-right: 5px; float: right;">
                                                                            <a href="ws/getiCal?sessionId=<?=$sessionId;?>&grpcode=<?=$_SESSION['grpcode'];?>" target="_new"><img src="images/sync.jpg" title="Sync Calendar with Mobile Devices" /></a> &nbsp; <a href="/timeoff"><img src="images/timeoff.jpg" title="Request Days Off" /></a> &nbsp; <a href="/YourCalendar"><img src="images/sched.jpg" title="View Complete Schedule" /></a> &nbsp; <a href="#" onclick="printSchedule()"><img src="images/print.jpg" title="Print Your Schedule" /></a>
                                                                        </div>
                                
                                    <h2 id="dashboard">Your Next Four Shifts</h2>

									<table id="tblNextFive" style="width:100%">
                                    	<tbody>
                                        
                                        </tbody>
                                    </table> <br />
                                    
                                    <h3>Latest alerts and notifications</h3>
                                    
                                    <table id="tblAlerts" style="width:100%">
                                    	<tbody>

                                        </tbody>
                                    </table> <br />
                                    
                                    <h3>Set your work and scheduling preferences</h3>
                                    
                                    <table style="width: 100%;">
                                    	<tr>
                                        	<td style="width: 45%; font-weight: bold; text-align: center; padding-right: 10px; background-color: #F2F2F2;">
                                            	<h5>Rank shifts, best (top) to worst (bottom)</h5>
                                            </td>
                                        	<td style="font-weight: bold; text-align: center; padding-right: 10px; background-color: #F2F2F2;">
												<h5>Select your working preferences</h5>
                                            </td>
                                        </tr>
                                        <tr>
                                        	<td style="padding: 5px;">
                                                <ul id="sortable" style="font-size: 0.7em;">
                                                </ul>
                                            </td>
                                            <td style="padding: 5px;">
                                            	<div>
                                                	<div style="background-color: #F2F2F2; border: 1px solid #E6E6E6; border-radius: 7px; padding: 5px; width: 95%">
                                                        Select your block working day preferences: <br />
                                                        <span style="font-weight: bold;">Weekends</span>: <input type="radio" id="blockDaysWeekend" name="blockDaysWeekend" value="Yes">Yes 
                                                        <input type="radio" id="blockDaysWeekend" name="blockDaysWeekend" value="No">No &nbsp; | &nbsp;  
                                                        <span style="font-weight: bold;">Weekdays</span>: <input type="radio" id="blockDaysWeekdays" name="blockDaysWeekdays" value="Yes">Yes 
                                                        <input type="radio" id="blockDaysWeekdays" name="blockDaysWeekdays" value="No">No <br />
                                                    </div>
                                                    <hr />
                                                    <div style="background-color: #F2F2F2; border: 1px solid #E6E6E6; border-radius: 7px; padding: 5px; width: 95%">
                                                        Would you like to abide by circadian rhythm? 
                                                        <input type="radio" id="overrideCircadian" name="overrideCircadian" value="Yes">Yes 
                                                        <input type="radio" id="overrideCircadian" name="overrideCircadian" value="No">No
                                                    </div>
                                                    <hr />
                                                    <div style="background-color: #F2F2F2; border: 1px solid #E6E6E6; border-radius: 7px; padding: 5px; width: 95%">
                                                        Which days do you prefer to work? <br />
                                                        <table>
                                                            <tr>
                                                                <td style="padding: 10px;">
                                                                    <span class="daySelect">
                                                                        <input type="checkbox" class="prefDays" id="Monday" value="Monday" />
                                                                        <label for="Monday">Monday</label>
                                                                    </span>
                                                                </td>
                                                                <td style="padding: 10px;">
                                                                    <span class="daySelect">
                                                                        <input type="checkbox" class="prefDays" id="Tuesday" value="Tuesday" />
                                                                        <label for="Tuesday">Tuesday</label>
                                                                    </span>
                                                                </td>
                                                                <td style="padding: 10px;">
                                                                    <span class="daySelect">
                                                                        <input type="checkbox" class="prefDays" id="Wednesday" value="Wednesday" />
                                                                        <label for="Wednesday">Wednesday</label>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="padding: 10px;">
                                                                    <span class="daySelect">
                                                                        <input type="checkbox" class="prefDays" id="Thursday" value="Thursday" />
                                                                        <label for="Thursday">Thursday</label>
                                                                    </span>
                                                                </td>
                                                                <td style="padding: 10px;">
                                                                    <span class="daySelect">
                                                                        <input type="checkbox" class="prefDays" id="Friday" value="Friday" />
                                                                        <label for="Friday">Friday</label>
                                                                    </span>
                                                                </td>
                                                                <td style="padding: 10px;">
                                                                    <span class="daySelect">
                                                                        <input type="checkbox" class="prefDays" id="Saturday" value="Saturday" />
                                                                        <label for="Saturday">Saturday</label>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="padding: 10px;">
                                                                    <span class="daySelect">
                                                                        <input type="checkbox" class="prefDays" id="Sunday" value="Sunday" />
                                                                        <label for="Sunday">Sunday</label>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <hr />
                                                    <div style="background-color: #F2F2F2; border: 1px solid #E6E6E6; border-radius: 7px; padding: 5px; width: 95%">
                                                        <div style="line-height: 18px; margin-bottom: 6px;">After a night shift (ie: <span style="font-weight: bold;">ending at 7am Tuesday</span>), when is the 
                                                        earliest that you would prefer to work next? </div>
                                                        <select id="afterNightShift">
                                                            <option value="">-- Select shift preference below --</option>
                                                            <option value="Wed7am">Wednesday, 7am</option>
                                                            <option value="Wed12pm">Wednesday, 12pm</option>
                                                            <option value="Wed7pm">Wednesday, 7pm</option>
                                                            <option value="Thurs7am">Thursday, 7am</option>
                                                        </select>
                                                    </div>
                                                    <hr />
                                                    <div style="background-color: #F2F2F2; border: 1px solid #E6E6E6; border-radius: 7px; padding: 5px; width: 95%">
                                                        Number of consecutive <span style="font-weight: bold;">night</span> shifts: <br />
                                                        Max: 
                                                        <select id="maxNights">
                                                            <option value="0"> -- Select -- </option>
                                                            <option value="1">1 night</option>
                                                            <option value="2">2 nights</option>
                                                            <option value="3">3 nights</option>
                                                            <option value="4">4 nights</option>
                                                            <option value="5">5 nights</option>
                                                            <option value="6">6 nights</option>
                                                        </select> &nbsp; | &nbsp;
                                                        Desired: 
                                                        <select id="desiredNights">
                                                            <option value="0"> -- Select -- </option>
                                                            <option value="1">1 night</option>
                                                            <option value="2">2 nights</option>
                                                            <option value="3">3 nights</option>
                                                            <option value="4">4 nights</option>
                                                            <option value="5">5 nights</option>
                                                            <option value="6">6 nights</option>
                                                        </select> <br />
                                                        Number of consecutive <span style="font-weight: bold;">day</span> shifts: <br />
                                                        Max: 
                                                        <select id="maxDays">
                                                            <option value="0"> -- Select -- </option>
                                                            <option value="1">1 day</option>
                                                            <option value="2">2 days</option>
                                                            <option value="3">3 days</option>
                                                            <option value="4">4 days</option>
                                                            <option value="5">5 days</option>
                                                            <option value="6">6 days</option>
                                                        </select> &nbsp; | &nbsp;
                                                        Desired: 
                                                        <select id="desiredDays">
                                                            <option value="0"> -- Select -- </option>
                                                            <option value="1">1 day</option>
                                                            <option value="2">2 days</option>
                                                            <option value="3">3 days</option>
                                                            <option value="4">4 days</option>
                                                            <option value="5">5 days</option>
                                                            <option value="6">6 days</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                        	<td colspan="2" style="text-align: center;"><br /><input type="button" name="btnPrefs" id="btnPrefs" value="Update scheduling preferences" onClick="processPreferences()" /></td>
                                        </tr>
                                    </table>

								</section>
						</div>
                        
						<div class="3u">
							
							<!-- Sidebar -->
								<section style="line-height: 25px;">
									<header>
										<h2>Your account</h2>
									</header>
                                    
                                    <img src="http://forwardintel.com/images/tom.smallwood.jpg" style="float: left; padding-right: 5px; padding-bottom: 5px;" />
                                    
                                    <span id="pflName" style="font-weight: bold; font-size: larger;"></span> <br />
                                    <span id="pflGroup" style="font-size: smaller;"></span> <br />
                                    <span id="pflPhone" style="font-size: smaller;"></span> <br />
                                    <span id="pflEmail" style="font-size: smaller;"></span> <br />
                                    
                                    <div style="width: 100%; text-align: center;"><a href="/profile">Edit your profile</a></div>
								</section>
						</div>
                        
						<div class="3u">
							
							<!-- Sidebar -->
								<section style="line-height: 25px;">
									<header>
										<h2>Your statistics</h2>
                                        <div style="margin: 0 auto; font-style: italic;">Through today</div>
									</header>
                                    
                                    <table style="width: 100%;">
                                    	<tr>
                                        	<td>Hours worked:</td>
                                            <td style="width: 30px;"><div id="hours"></div></td>
                                        </tr>
                                    	<tr>
                                        	<td>Hours remaining:</td>
                                            <td style="width: 30px;"><div id="remaining"></div></td>
                                        </tr>
                                    	<tr>
                                        	<td>Traded shifts:</td>
                                            <td style="width: 30px;"<div id="traded"></div></td>
                                        </tr>
                                    	<tr>
                                        	<td>Total monthly shifts:</td>
                                            <td style="width: 30px;"<div id="totalshifts"></div></td>
                                        </tr>
                                    	<tr>
                                        	<td>Total yearly shifts:</td>
                                            <td style="width: 30px;"<div id="shifts"></div></td>
                                        </tr>
                                   	<tr>
                                        	<td>Total yearly hours:</td>
                                            <td style="width: 30px;"<div id="yearlyhours"></div></td>
                                        </tr>
                                    </table>

								</section>
						</div>
                        
					</div>
				</div>
			</div>