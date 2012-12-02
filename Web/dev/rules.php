<? require("php_includes/isLoggedIn.php"); ?>
<? require("php_includes/protectedPage.php"); ?>
<html>
	<head>
		<title>Schedule Forward :: Medical scheduling software made easy</title>
		<?php include("html_includes/adminMeta.php"); ?>
        <script src="js/jquery.cookie.js" type="text/javascript"></script>

        <script language="javascript">

			$(document).ready(function(){
				$('a[title]').qtip();
				$('img[title]').qtip();
				$("#minHoursBetweenShifts").mask("9?9");
				$("#maxConsecDay").mask("9?9");
				$("#maxConsecNight").mask("9?9");
				$("#maxNightsPerMonth").mask("9?9");
				$("#maxConsecWorkingDays").mask("9?9");
				loadConfigs();
			});
			
			function expandCollapse(div) {
				$('#' + div).toggle();
			}
			
			function loadConfigs() {
				$.ajaxSetup({async:false});
				var configObj = "";
				$.post("ws/getConfig", {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"}, function (data) {
						if (data.data != null) {
							configObj = data.data[0];
							if (configObj.dayOfWeekStart == "Sunday") {
								$('#dayOfWeekStart1').attr("checked","true");
							} else {
								$('#dayOfWeekStart2').attr("checked","true");
							}
							$('#timezone').val(configObj.timezone);
							if (configObj.emailAutoSend == "true") {
								$('#emailAutoSend1').attr("checked","true");
							} else {
								$('#emailAutoSend2').attr("checked","true");
							}
							if (configObj.timeoffReminder3 == "true") {
								$('#timeoffReminder3').attr("checked","true");
							} 
							if (configObj.timeoffReminder2 == "true") {
								$('#timeoffReminder2').attr("checked","true");
							}
							if (configObj.timeoffReminder == "true") {
								$('#timeoffReminder').attr("checked","true");
							}
							if (configObj.emailOptIn == "true") {
								$('#emailOptIn1').attr("checked","true");
							} else {
								$('#emailOptIn2').attr("checked","true");
							}
							if (configObj.autoPublish == "true") {
								$('#autoPublish1').attr("checked","true");
							} else {
								$('#autoPublish2').attr("checked","true");
							}
							$('#timeoffDeadline').val(configObj.timeoffDeadline);
							$('#sortBy').val(configObj.sortBy);
							$('#scheduleGenerate').val(configObj.scheduleGenerate);
							if (configObj.circadian == "true") {
								$('#circadian1').attr("checked","true");
							} else {
								$('#circadian2').attr("checked","true");
							}
							if (configObj.overrideCircadian == "true") {
								$('#overrideCircadian1').attr("checked","true");
							} else {
								$('#overrideCircadian2').attr("checked","true");
							}
							$('#minHoursBetweenShifts').val(configObj.minHoursBetweenShifts);
							$('#maxConsecDay').val(configObj.maxConsecDay);
							$('#maxConsecNight').val(configObj.maxConsecNight);
							$('#maxNightsPerMonth').val(configObj.maxNightsPerMonth);
							$('#maxConsecWorkingDays').val(configObj.maxConsecWorkingDays);
							if (configObj.attendingsLowerLevel == "true") {
								$('#attendingsLowerLevel1').attr("checked","true");
							} else {
								$('#attendingsLowerLevel2').attr("checked","true");
							}
							if (configObj.weekendShifts == "true") {
								$('#weekendShifts1').attr("checked","true");
							} else {
								$('#weekendShifts2').attr("checked","true");
							}
							if (configObj.tradeApproval == "true") {
								$('#tradeApproval1').attr("checked","true");
							} else {
								$('#tradeApproval2').attr("checked","true");
							}
							if (configObj.displayTimes == "true") {
								$('#displayTimes1').attr("checked","true");
							} else {
								$('#displayTimes2').attr("checked","true");
							}
							if (configObj.shiftsOrHours == "Shifts") {
								$('#shiftsOrHours1').attr("checked","true");
							} else {
								$('#shiftsOrHours2').attr("checked","true");
							}
						}
					}	
				);
			}
			
			function saveConfigs() {
				var dayOfWeekStart = $("input[@name=dayOfWeekStart]:checked").attr('value');
				var timezone = $('#timezone').val();
				var emailAutoSend = ($('#emailAutoSend1').attr('checked') == "checked" ? "true" : "false");
				var timeoffReminder3 = ($('#timeoffReminder3').attr('checked') == "checked" ? "true" : "false");
				var timeoffReminder2 = ($('#timeoffReminder2').attr('checked') == "checked" ? "true" : "false");
				var timeoffReminder = ($('#timeoffReminder').attr('checked') == "checked" ? "true" : "false");
				var emailOptIn = ($('#emailOptIn1').attr('checked') == "checked" ? "true" : "false");
				var autoPublish = ($('#autoPublish1').attr('checked') == "checked" ? "true" : "false");
				var timeoffDeadline = $('#timeoffDeadline').val();
				var sortBy = $('#sortBy').val();
				var scheduleGenerate = $('#scheduleGenerate').val();
				var circadian = ($('#circadian1').attr('checked') == "checked" ? "true" : "false");
				var overrideCircadian = ($('#overrideCircadian1').attr('checked') == "checked" ? "true" : "false");
				var minHoursBetweenShifts = $('#minHoursBetweenShifts').val();
				var maxConsecDay = $('#maxConsecDay').val();
				var maxConsecNight = $('#maxConsecNight').val();
				var maxNightsPerMonth = $('#maxNightsPerMonth').val();
				var maxConsecWorkingDays = $('#maxConsecWorkingDays').val();
				var attendingsLowerLevel = ($('#attendingsLowerLevel1').attr('checked') == "checked" ? "true" : "false");
				var weekendShifts = ($('#weekendShifts1').attr('checked') == "checked" ? "true" : "false");
				var tradeApproval = ($('#tradeApproval1').attr('checked') == "checked" ? "true" : "false");
				var shiftsOrHours = ($('#shiftsOrHours1').attr('checked') == "checked" ? "Shifts" : "Hours");
				var displayTimes = ($('#displayTimes1').attr('checked') == "checked" ? "true" : "false");
				
				var jsonConfigs = {
					"sessionId":"<?=$sessionId;?>",
					"dayOfWeekStart": "" + dayOfWeekStart + "",
					"timezone": "" + timezone + "",
					"emailAutoSend": "" + emailAutoSend + "",
					"timeoffReminder3": "" + timeoffReminder3 + "",
					"timeoffReminder2": "" + timeoffReminder2 + "",
					"timeoffReminder": "" + timeoffReminder + "",
					"emailOptIn": "" + emailOptIn + "",
					"autoPublish": "" + autoPublish + "",
					"timeoffDeadline": "" + timeoffDeadline + "",
					"sortBy": "" + sortBy + "",
					"scheduleGenerate": "" + scheduleGenerate + "",
					"circadian": "" + circadian + "",
					"overrideCircadian": "" + overrideCircadian + "",
					"minHoursBetweenShifts": "" + minHoursBetweenShifts + "",
					"maxConsecDay": "" + maxConsecDay + "",
					"maxConsecNight": "" + maxConsecNight + "",
					"maxNightsPerMonth": "" + maxNightsPerMonth + "",
					"maxConsecWorkingDays": "" + maxConsecWorkingDays + "",
					"attendingsLowerLevel": "" + attendingsLowerLevel + "",
					"weekendShifts": "" + weekendShifts + "",
					"tradeApproval": "" + tradeApproval + "",
					"shiftsOrHours": "" + shiftsOrHours + "",
					"displayTimes": "" + displayTimes + "",
					"grpcode": "<?=$_SESSION['grpcode'];?>"
				};
				
				$.post("ws/editConfig", jsonConfigs,
                    function (data) {
						if (data.message == "success") {
							$('.newUserSuccess').html('Configurations successfully saved');
							$('.newUserSuccess').fadeIn();
							setInterval(function(){$('.newUserSuccess').fadeOut();},2000);
						} else {
							// Oops, there was a problem performing the save!	
						}
					}
				);

			}

        </script>

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
					<div class="5grid">				
                        <? include("html_includes/loggedInAs.php"); ?>
						<div class="4u-first" style="width: 100%;">

							<!-- Box #1 -->
								<section style="min-height: 100%;">
                                
                                    <h2 id="dashboard">System Rules</h2>
                                    
                                    <div style="width: 200px; text-align: center; margin: 0 auto; margin-bottom: 10px;">
                                    	<div class="newUserSuccess" style="width: 100%; text-align: center; color: #66CC00;"></div>
                                    	<input type="button" onClick="saveConfigs()" name="btnSubmitConfigs" id="btnSubmitConfigs" value="Save Configurations">
                                    </div>
                                    
                                    <div class="expCol" onClick="expandCollapse('expCol1')">
                                    	<img src="images/bullet_toggle_plus.png" /><span style="font-weight: bold;">System rules and behaviors</span>
                                    </div>
                                    
                                    <div id="expCol1">
										<table id="tblRules">
                                        	<!--
                                            <tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>What day do weeks begin on?</h3> 
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<input type="radio" id="dayOfWeekStart1" name="dayOfWeekStart" value="Sunday"> <label for="dayOfWeekStart1">Sunday</label> <input type="radio" id="dayOfWeekStart2" name="dayOfWeekStart" value="Monday"> <label for="dayOfWeekStart2">Monday</label>
                                                </td>
                                            </tr>
                                            -->
											<tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>Select a timezone for your location</h3> 
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
												<select name="timezone" id="timezone">
                                                    <option value="-12.0">(GMT -12:00) Eniwetok, Kwajalein</option>
                                                      <option value="-11.0">(GMT -11:00) Midway Island, Samoa</option>
                                                      <option value="-10.0">(GMT -10:00) Hawaii</option>
                                                      <option value="-9.0">(GMT -9:00) Alaska</option>
                                                      <option value="-8.0">(GMT -8:00) Pacific Time (US &amp; Canada)</option>
                                                      <option value="-7.5">(GMT -7:00) Mountain Time (Arizona)</option>
                                                      <option value="-7.0">(GMT -7:00) Mountain Time (US &amp; Canada)</option>
                                                      <option value="-6.0">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
                                                      <option value="-5.0">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
                                                      <option value="-4.0">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
                                                      <option value="-3.5">(GMT -3:30) Newfoundland</option>
                                                      <option value="-3.0">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
                                                      <option value="-2.0">(GMT -2:00) Mid-Atlantic</option>
                                                      <option value="-1.0">(GMT -1:00 hour) Azores, Cape Verde Islands</option>
                                                      <option value="0.0">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
                                                      <option value="1.0">(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
                                                      <option value="2.0">(GMT +2:00) Kaliningrad, South Africa</option>
                                                      <option value="3.0">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
                                                      <option value="3.5">(GMT +3:30) Tehran</option>
                                                      <option value="4.0">(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
                                                      <option value="4.5">(GMT +4:30) Kabul</option>
                                                      <option value="5.0">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
                                                      <option value="5.5">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
                                                      <option value="5.75">(GMT +5:45) Kathmandu</option>
                                                      <option value="6.0">(GMT +6:00) Almaty, Dhaka, Colombo</option>
                                                      <option value="7.0">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
                                                      <option value="8.0">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
                                                      <option value="9.0">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
                                                      <option value="9.5">(GMT +9:30) Adelaide, Darwin</option>
                                                      <option value="10.0">(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
                                                      <option value="11.0">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
                                                      <option value="12.0">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
                                                </select> 
                                                </td>
                                            </tr>
											<tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>Display times on Admin's Schedule?</h3> 
                                                </td>
                                                 <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<input type="radio" id="displayTimes1" name="displayTimes" value="Yes"> <label for="displayTimes1">Yes</label> <input type="radio" id="displayTimes2" name="displayTimes" value="No"> <label for="displayTimes2">No</label>
                                                </td>
                                            </tr>
											<tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>Show statistics on schedules by shifts or hours?</h3> 
                                                </td>
                                                 <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<input type="radio" id="shiftsOrHours1" name="shiftsOrHours" value="Shifts"> <label for="shiftsOrHours1">Shifts</label> <input type="radio" id="shiftsOrHours2" name="shiftsOrHours" value="Hours"> <label for="shiftsOrHours2">Hours</label>
                                                </td>
                                            </tr>
											<tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>Automatically send emails when schedules are published?</h3> 
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<input type="radio" id="emailAutoSend1" name="emailAutoSend" value="Yes"> <label for="emailAutoSend1">Yes</label> <input type="radio" id="emailAutoSend2" name="emailAutoSend" value="No"> <label for="emailAutoSend2">No</label>
                                                </td>
                                            </tr>
											<tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>Send users timeoff deadline reminders?</h3> 
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<input type="checkbox" name="timeoffReminder3" id="timeoffReminder3" value="3"> <label for="timeoffReminder3">3 Days Before</label>
                                                	<input type="checkbox" name="timeoffReminder2" id="timeoffReminder2" value="2"> <label for="timeoffReminder2">2 Days Before</label>
                                                	<input type="checkbox" name="timeoffReminder" id="timeoffReminder" value="1"> <label for="timeoffReminder">1 Day Before</label>
                                                </td>
                                            </tr>
                                            <tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                    <h3>Allow users to opt out of receiving new schedule email alerts</h3>
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<input type="radio" id="emailOptIn1" name="emailOptIn" value="Yes"> <label for="emailOptIn1">Yes</label> <input type="radio" id="emailOptIn2" name="emailOptIn" value="No"> <label for="emailOptIn2">No</label>
                                                </td>
                                            </tr>
                                            <tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                    <h3>Should the schedule auto publish each month?</h3>
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<input type="radio" id="autoPublish1" name="autoPublish" value="Yes"> <label for="autoPublish1">Yes</label> <input type="radio" id="autoPublish2" name="autoPublish" value="No"> <label for="autoPublish2">No</label>
                                                </td>
                                            </tr>
											<tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>Last day providers can submit timeoff requests per month</h3> 
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<select name="timeoffDeadline" id="timeoffDeadline" style="z-index: 1000;">
                                                    	<?php 
															for ($i = 1; $i <= 31; $i++) {
																echo "<option value=\"$i\">$i</option>";	
															}
														?>
                                                    </select>
                                                </td>
                                            </tr>
											<tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>Day of month to automatically generate schedule</h3> 
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<select name="scheduleGenerate" id="scheduleGenerate" style="z-index: 1000;">
                                                    	<?php 
															for ($i = 1; $i <= 31; $i++) {
																echo "<option value=\"$i\">$i</option>";	
															}
														?>
                                                    </select>
                                                </td>
                                            </tr>
                                    	</table>
                                    </div> <br /> 
                                    
                                    <div class="expCol" onClick="expandCollapse('expCol2')"><img src="images/bullet_toggle_plus.png" /><span style="font-weight: bold;">Basic scheduling configurations</span></div>
                                    <div id="expCol2">
										<table id="tblRules">
                                            <tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>Would you like the schedule to abide by circadian rhythm?</h3> 
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<input type="radio" id="circadian1" name="circadian" value="Yes"> <label for="circadian1">Yes</label> <input type="radio" id="circadian2" name="circadian" value="No"> <label for="circadian2">No</label>
                                                </td>
                                            </tr>
                                            <tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>If circadian rhythm is active, can providers override it?</h3> 
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<input type="radio" id="overrideCircadian1" name="overrideCircadian" value="Yes"> <label for="overrideCircadian1">Yes</label> <input type="radio" id="overrideCircadian2" name="overrideCircadian" value="No"> <label for="overrideCircadian2">No</label>
                                                </td>
                                            </tr>
                                            <tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>Minimum off hours between shifts per provider</h3> 
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<input type="type" id="minHoursBetweenShifts" name="minHoursBetweenShifts" maxlength="2" value="" style="width: 25px;">
                                                </td>
                                            </tr>
                                            <tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>Maximum consecutive <strong>day</strong> shifts per provider</h3> 
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<input type="type" id="maxConsecDay" name="maxConsecDay" maxlength="2" value="" style="width: 25px;">
                                                </td>
                                            </tr>
                                            <tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>Maximum consecutive <strong>night</strong> shifts per provider</h3> 
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<input type="type" id="maxConsecNight" name="maxConsecNight" maxlength="2" value="" style="width: 25px;">
                                                </td>
                                            </tr>
                                            <tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>Minimum <strong>night</strong> hours per provider, per month</h3> 
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<input type="type" id="maxNightsPerMonth" name="maxNightsPerMonth" maxlength="2" value="" style="width: 25px;">
                                                </td>
                                            </tr>
                                            <tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>Maximum number of consecutive working days per provider</h3> 
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<input type="type" id="maxConsecWorkingDays" name="maxConsecWorkingDays" maxlength="2" value="" style="width: 25px;">
                                                </td>
                                            </tr>
                                            <tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>Can attendings work a lower level shift?</h3> 
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<input type="radio" id="attendingsLowerLevel1" name="attendingsLowerLevel" value="Yes"> <label for="attendingsLowerLevel1">Yes</label> <input type="radio" id="attendingsLowerLevel2" name="attendingsLowerLevel" value="No"> <label for="attendingsLowerLevel2">No</label>
                                                </td>
                                            </tr>
                                            <tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>Equalize weekend shifts between providers per month</h3> 
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<input type="radio" id="weekendShifts1" name="weekendShifts" value="Yes"> <label for="weekendShifts1">Yes</label> <input type="radio" id="weekendShifts2" name="weekendShifts" value="No"> <label for="weekendShifts2">No</label>
                                                </td>
                                            </tr>
                                            <tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>All shift trade requests need admin approval</h3> 
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<input type="radio" id="tradeApproval1" name="tradeApproval" value="Yes"> <label for="tradeApproval1">Yes</label> <input type="radio" id="tradeApproval2" name="tradeApproval" value="No"> <label for="tradeApproval2">No</label>
                                                </td>
                                            </tr>
                                            <tr onMouseOver="this.bgColor='#F3F5D6'" onMouseOut="this.bgColor='#FFFFFF'">
                                                <td style="width: 50%; padding: 5px;">
                                                	<h3>Sort shifts on schedule page by</h3> 
                                                </td>
                                                <td style="width: 50%; text-align: right; padding: 5px;">
                                                	<select name="sortBy" id="sortBy" style="z-index: 1000;">
															<option value="time">Time only</option>
                                                            <option value="siteTime">Site then Time</option>
                                                    </select>
                                                </td>
                                            </tr>
                                    	</table>
                                    </div>
                                    
                                    <div style="width: 200px; text-align: center; margin: 0 auto; margin-top: 10px;">
                                    	<input type="button" onClick="saveConfigs()" name="btnSubmitConfigs" id="btnSubmitConfigs" value="Save Configurations">
                                        <div class="newUserSuccess" style="width: 100%; text-align: center; color: #66CC00;"></div>
                                    </div>
                                    
								</section>
						</div>
					</div>
				</div>
			</div>

		<!-- Copyright -->
			<div id="copyright">
				(c) 2012 Forward Intelligence Systems, LLC. All rights reserved.
			</div>

	</body>
</html>