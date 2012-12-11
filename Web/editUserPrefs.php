<? require("php_includes/isLoggedIn.php"); ?>
<? require("php_includes/protectedPage.php"); ?>
<html>
	<head>
		<title>Schedule Forward :: Medical scheduling software made easy</title>
		<?php include("html_includes/adminMeta.php"); ?>

        <script language="javascript" type="text/javascript">
			$(document).ready(function(){
				loadUserData();
				loadPreferences();
				$( "#sortable" ).sortable();
				$( "#sortable" ).disableSelection();
			});
			
			var userId;
			
			function loadUserData() {
				var username = getParameterByName("user");
				$.ajaxSetup({async:false});
				$.post('ws/getUser', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","username":username} , function(data) {
					userObj = data.data[0];
				});
				userId = userObj._id.$id;
				var fName = userObj.first_name;
				var lName = userObj.last_name;
				var displayName = fName + " " + lName;
				$('#editingFor').html(displayName);
			}
			
			function loadPreferences() {
				$.ajaxSetup({async:false});
				var prefObj;
				$.post('ws/getPreferences', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":userId} , function(data) {
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
			
			function getParameterByName(name)
			{
			 	name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
			  	var regexS = "[\\?&]" + name + "=([^&#]*)";
			  	var regex = new RegExp(regexS);
			  	var results = regex.exec(window.location.search);
			  	if(results == null)
					return "";
			  	else
					return decodeURIComponent(results[1].replace(/\+/g, " "));
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
								
				$.post('ws/editPreferences', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":userId,"shifts":shifts,"days":days,"blockweekend":blockWeekends,"blockdays":blockWeekdays,"maxnights":maxNights,"desirednights":desiredNights,"maxdays":maxDays,"desireddays":desiredDays,"circadian":overrideCircadian,"afterNightShift":afterNightShift} , function(data) {
					if (data.message == "success") {
						$('#profileSuccess').html('<img src="images/accept.png" alt="Success" /> You have successfully updated this user\'s preferences!');
						$('#profileSuccess').fadeIn();
						setInterval(function(){$('#profileSuccess').fadeOut();},2000);
					} else {
						$('#profileError').html('<img src="images/stop.png" alt="Error" /> There was an error updating this user\'s preferences!');
						$('#profileError').fadeIn();
						setInterval(function(){$('#profileSuccess').fadeOut();},2000);
					}
				});
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
								<section>
                                
									<div id="userMgmtLinks">
										<img src="images/user_add.png" alt="Add New User" /> <a href="/userMgmt">Back to Provider Management</a>
									</div>
									
                                    <h2 id="dashboard">Provider Management</h2>
                                    
                                    <div id="profileError" style="width: 100%; text-align: center; color: #CC0000;"></div>
									<div id="profileSuccess" style="width: 50%;"></div>
                                    
                                    <h3>Editing user preferences for: <span id="editingFor"></span></h3>

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
                                                	<div style="background-color: #F2F2F2; border: 1px solid #E6E6E6; border-radius: 7px; padding: 5px; width: 95%">
                                                        Select your block working day preferences: <br />
                                                        <span style="font-weight: bold;">Weekends</span>: <input type="radio" id="blockDaysWeekend" name="blockDaysWeekend" value="Yes">Yes 
                                                        <input type="radio" id="blockDaysWeekend" name="blockDaysWeekend" value="No">No &nbsp; | &nbsp;  
                                                        <span style="font-weight: bold;">Weekdays</span>: <input type="radio" id="blockDaysWeekdays" name="blockDaysWeekdays" value="Yes">Yes 
                                                        <input type="radio" id="blockDaysWeekdays" name="blockDaysWeekdays" value="No">No
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
                                                            <td style="padding: 10px;"><span class="daySelect"><input type="checkbox" class="prefDays" id="Monday" value="Monday" />Monday</span></td>
                                                            <td style="padding: 10px;"><span class="daySelect"><input type="checkbox" class="prefDays" id="Tuesday" value="Tuesday" />Tuesday</span></td>
                                                            <td style="padding: 10px;"><span class="daySelect"><input type="checkbox" class="prefDays" id="Wednesday" value="Wednesday" />Wednesday</span></td>
                                                            <td style="padding: 10px;"><span class="daySelect"><input type="checkbox" class="prefDays" id="Thursday" value="Thursday" />Thursday</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 10px;"><span class="daySelect"><input type="checkbox" class="prefDays" id="Friday" value="Friday" />Friday</span></td>
                                                            <td style="padding: 10px;"><span class="daySelect"><input type="checkbox" class="prefDays" id="Saturday" value="Saturday" />Saturday</span></td>
                                                            <td style="padding: 10px;"><span class="daySelect"><input type="checkbox" class="prefDays" id="Sunday" value="Sunday" />Sunday</span></td>
                                                        </tr>
                                                    </table>
                                                    </div>
                                                    <hr />
                                                    <div style="background-color: #F2F2F2; border: 1px solid #E6E6E6; border-radius: 7px; padding: 5px; width: 95%">
                                                        <div style="line-height: 18px; margin-bottom: 6px;">After a night shift (ie: <span style="font-weight: bold;">ending at 7am Tuesday</span>), when is the 
                                                        earliest that you would prefer to work the next Thursday? </div>
                                                        <select id="afterNightShift">
                                                            <option value="">-- Select shift preference below --</option>
                                                            <option value="Thurs7am">Thursday, 7am</option>
                                                            <option value="Thurs12pm">Thursday, 12pm</option>
                                                            <option value="Thurs7pm">Thursday, 7pm</option>
                                                        </select>
                                                    	</div>
                                                    <hr />
                                                    <div style="background-color: #F2F2F2; border: 1px solid #E6E6E6; border-radius: 7px; padding: 5px; width: 95%">
                                                    Number of consecutive <span style="font-weight: bold;">night</span> shifts: <br />
                                                    Max: 
                                                    <select id="maxNights">
                                                        <option value="0"> -- Select Below -- </option>
                                                        <option value="1">1 night</option>
                                                        <option value="2">2 nights</option>
                                                        <option value="3">3 nights</option>
                                                        <option value="4">4 nights</option>
                                                        <option value="5">5 nights</option>
                                                        <option value="6">6 nights</option>
                                                    </select> &nbsp; | &nbsp;
                                                    Desired: 
                                                    <select id="desiredNights">
                                                        <option value="0"> -- Select Below -- </option>
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
                                                        <option value="0"> -- Select Below -- </option>
                                                        <option value="1">1 day</option>
                                                        <option value="2">2 days</option>
                                                        <option value="3">3 days</option>
                                                        <option value="4">4 days</option>
                                                        <option value="5">5 days</option>
                                                        <option value="6">6 days</option>
                                                    </select> &nbsp; | &nbsp;
                                                    Desired: 
                                                    <select id="desiredDays">
                                                        <option value="0"> -- Select Below -- </option>
                                                        <option value="1">1 day</option>
                                                        <option value="2">2 days</option>
                                                        <option value="3">3 days</option>
                                                        <option value="4">4 days</option>
                                                        <option value="5">5 days</option>
                                                        <option value="6">6 days</option>
                                                    </select> <br />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                        	<td colspan="2" style="text-align: center;"><br /><input type="button" name="btnPrefs" id="btnPrefs" value="Save scheduling preferences" onClick="processPreferences()" /></td>
                                        </tr>
                                    </table>

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