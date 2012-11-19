<? require("php_includes/isLoggedIn.php"); ?>
<? require("php_includes/protectedPage.php"); ?>
<html>
	<head>
		<title>Schedule Forward :: Medical scheduling software made easy</title>
		<?php include("html_includes/adminMeta.php"); ?>
		
		<script>
		
			$("body").on({
				ajaxStart: function() { 
					$(this).addClass("loading"); 
				},
				ajaxStop: function() { 
					$(this).removeClass("loading"); 
				}    
			});

		
			$.fx.speeds._default = 1000;
			$(function() {
				$( "#newShift" ).dialog({
					autoOpen: false,
					modal: true,
					width: 800
				});
			
				$( ".openNewEditShift" ).click(function() {
					resetForm();
					$( "#newShift" ).dialog( "open" );
					
					$('#dayMonday').attr("class","shiftDaySelected");
					$('#dayTuesday').attr("class","shiftDaySelected");
					$('#dayWednesday').attr("class","shiftDaySelected");
					$('#dayThursday').attr("class","shiftDaySelected");
					$('#dayFriday').attr("class","shiftDaySelected");
					$('#daySaturday').attr("class","shiftDaySelected");
					$('#daySunday').attr("class","shiftDaySelected");
					
					return false;
				});
				
				$( ".shiftDaySelect" ).click(function() {
					var dayValue = $(this).attr("day");
					var currClass = $(this).attr("class");
					
					if (currClass == "shiftDaySelect") {
						$(this).attr("class","shiftDaySelected");
					} else {
						$(this).attr("class","shiftDaySelect");
					}
				});
			});
			
			function resetForm() {
				$('#dayMonday').attr("class","shiftDaySelect");
				$('#dayTuesday').attr("class","shiftDaySelect");
				$('#dayWednesday').attr("class","shiftDaySelect");
				$('#dayThursday').attr("class","shiftDaySelect");
				$('#dayFriday').attr("class","shiftDaySelect");
				$('#daySaturday').attr("class","shiftDaySelect");
				$('#daySunday').attr("class","shiftDaySelect");
				$('#pleaseWait').hide();
				$('#btnSubmitNewShift').show();

				$("#hiddenId").val("");
				$('#name').val("");
				$('#color').val("");
				$('#start').val("");
				$('#end').val("");
				$('#numProviders').val("");
				$('#group').val("");
				$('#newUserError').html("");
				$('#newUserSuccess').html("");
                                $('#location').html("");
				
				$.post("ws/getGroups", {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"}, 
                    function (data) {
						var select = '<select name="group" id="group">';
						var temparray = data["data"];
						select = select + '<option value="">-- Select Below --</option>';
						for(i=0; i<temparray.length; i++) {
							select = select + '<option value="' + temparray[i]['name'] + '">' + temparray[i]['name'] + '</option>';
						}
						select = select + "</select>";
						$('.group-select').html(""); //clear old options
						$('.group-select').html(select);
				});
			
                        
                        $.post("ws/getLocations", {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"}, 
                    function (data) {
						var select = '<select name="location" id="location" onChange="getLocationDefaults()">';
						var temparray = data["data"];
						for(i=0; i<temparray.length; i++) {
							select = select + '<option value="' + temparray[i]['name'] + '">' + temparray[i]['name'] + '</option>';
						}
						select = select + "</select>";
						$('.location-select').html(""); //clear old options
						$('.location-select').html(select);
				});
			}
			function editShift(id) {
				$.post('ws/getShift', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":id} , function(data) {
					userObj = data.data[0];
				});
				
				var name = userObj.name;
				var color = userObj.color;
				var start = userObj.start;
				var end = userObj.end;
				var numProviders = userObj.number;
				var groups = userObj.groups;
				var days = userObj.days;
                                var location = userObj.location;
				
				resetForm();
				
				for (var i=0;i<days.length;i++) {
					if (days[i] == "Monday") {
						$('#dayMonday').attr("class","shiftDaySelected");
					} 
					if (days[i] == "Tuesday") {
						$('#dayTuesday').attr("class","shiftDaySelected");
					} 
					if (days[i] == "Wednesday") {
						$('#dayWednesday').attr("class","shiftDaySelected");
					} 
					if (days[i] == "Thursday") {
						$('#dayThursday').attr("class","shiftDaySelected");
					} 
					if (days[i] == "Friday") {
						$('#dayFriday').attr("class","shiftDaySelected");
					} 
					if (days[i] == "Saturday") {
						$('#daySaturday').attr("class","shiftDaySelected");
					} 
					if (days[i] == "Sunday") {
						$('#daySunday').attr("class","shiftDaySelected");
					} 
				}
				
				$("#hiddenId").attr("value",id);
				$('#name').val(name);
				$('#color').val(color);
				$('#startTime').val(start);
				$('#endTime').val(end);
				$('#numProviders').val(numProviders);
				$('#group').val(groups);
                                $('#location').val(location);
				
				$("#btnSubmitNewShift").attr("value"," Edit Shift ");
				$("#btnSubmitNewShift").attr("onclick"," processShift('edit') ");
				$("#popupInstructions").html("Use the form below to edit the parameters of this shift.");
				$("#newShift").dialog( "open" );
				return false;
			}
			
			function deleteShift(id) {
				var r = confirm("Are you sure that you want to delete this shift?")
				if (r == true) {
					$.post('ws/deleteShift', {"sessionId":"<?=$sessionId;?>","id":id,"grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
						if (data.message == "success") {
							alert("Shift successfully deleted!");
							window.location.reload();
						}
					});
				}
			}
			
			function processShift(type) {
				// Determine selected days
				var daysSelected = "";
				if ($('#dayMonday').attr("class") == "shiftDaySelected") {
					daysSelected = "Monday,";
				}
				if ($('#dayTuesday').attr("class") == "shiftDaySelected") {
					daysSelected += "Tuesday,";
				}
				if ($('#dayWednesday').attr("class") == "shiftDaySelected") {
					daysSelected += "Wednesday,";
				}
				if ($('#dayThursday').attr("class") == "shiftDaySelected") {
					daysSelected += "Thursday,";
				}
				if ($('#dayFriday').attr("class") == "shiftDaySelected") {
					daysSelected += "Friday,";
				}
				if ($('#daySaturday').attr("class") == "shiftDaySelected") {
					daysSelected += "Saturday,";
				}
				if ($('#daySunday').attr("class") == "shiftDaySelected") {
					daysSelected += "Sunday,";
				}
				daysSelected = daysSelected.slice(0,daysSelected.length-1);
				
				var id = $('#hiddenId').val();
				var name = $('#name').val();
				var color = $('#color').val();
				var startTime = $('#startTime').val();
				var endTime = $('#endTime').val();
				var numProviders = $('#numProviders').val();
				var groups = $('#group').val();
                                var location = $('#location').val();
				
				var stopImage = "<img src=\"images/stop.png\" alt=\"Error\" />";
				var goImage = "<img src=\"images/accept.png\" alt=\"Success\" />";
				var wsEndPoint = (type == "new" ? "ws/addShift" : "ws/editShift");
				
				$.post(wsEndPoint, {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":id,"name":name,"color":color,"start":startTime,"end":endTime,"number":numProviders,"groups":groups,"days":daysSelected, "location":location} , function(data) {
					if (data.message == "emptyFields") {
						$('#newUserError').html(stopImage + ' Please fill out all fields.');
					} else if (data.message == "nameExists") {
						$('#newUserError').html(stopImage + ' Shift name already exist; please choose another name.');
					}
					if (data.message == "success") {
						if (type == "new") {
							$('#newUserSuccess').html(goImage + ' Shift successfully created!');
							$('#pleaseWait').show();
							$('#btnSubmitNewShift').hide();
							setInterval(function(){window.location.reload()},1000);
						} else {
							$('#newUserSuccess').html(goImage + ' Shift successfully edited!');
							$('#pleaseWait').show();
							$('#btnSubmitNewShift').hide();
							setInterval(function(){window.location.reload()},1000);
						}
					}
				});
			}
			
			var userObj;
			var inc = 0;
			var shiftArray = new Array();
			$.ajaxSetup({async:false});
			// Query for the shifts and translate the returned JSON into an array that DataTables can read
			$.post('ws/getShifts', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","format":"dt"} , function(data) {
				userObj = data.data;
			});
			
			for (var i=0; i<userObj.length; i++) {
				var id = userObj[i]._id.$id;
				var name = userObj[i].name;
				var times = userObj[i].start + " through " + userObj[i].end;
				var frequency = userObj[i].days;
				var group = userObj[i].groups[0];
				var tmpDayHolder = "";
                                var loc = userObj[i].location;

				if (jQuery.inArray("Monday", frequency)> -1) {
					tmpDayHolder += '<span class="shiftDayWorking">Mon</span>';
				} else {
					tmpDayHolder += '<span class="shiftDayNotWorking">Mon</span>';
				}
				
				if (jQuery.inArray("Tuesday", frequency)> -1) {
					tmpDayHolder += '<span class="shiftDayWorking">Tues</span>';
				} else {
					tmpDayHolder += '<span class="shiftDayNotWorking">Tues</span>';
				}
				
				if (jQuery.inArray("Wednesday", frequency)> -1) {
					tmpDayHolder += '<span class="shiftDayWorking">Wed</span>';
				} else {
					tmpDayHolder += '<span class="shiftDayNotWorking">Wed</span>';										
				}
				
				if (jQuery.inArray("Thursday", frequency)> -1) {
					tmpDayHolder += '<span class="shiftDayWorking">Thurs</span>';
				} else {
					tmpDayHolder += '<span class="shiftDayNotWorking">Thurs</span>';
				}
				
				if (jQuery.inArray("Friday", frequency)> -1) {
					tmpDayHolder += '<span class="shiftDayWorking">Fri</span>';
				} else {
					tmpDayHolder += '<span class="shiftDayNotWorking">Fri</span>';
				}
				
				if (jQuery.inArray("Saturday", frequency)> -1) {
					tmpDayHolder += '<span class="shiftDayWorking">Sat</span>';
				} else {
					tmpDayHolder += '<span class="shiftDayNotWorking">Sat</span>';
				}
				
				if (jQuery.inArray("Sunday", frequency)> -1) {
					tmpDayHolder += '<span class="shiftDayWorking">Sun</span>';
				} else {
					tmpDayHolder += '<span class="shiftDayNotWorking">Sun</span>';
				}	
							
				var edit = '<a href="#" class="" onclick="editShift(\'' + id + '\')"><img src="images/wrench.png" alt="Edit Shift" title="Edit Shift" /></a>';
				var del = '<a href="#" class="" onclick="deleteShift(\'' + id + '\')"><img src="images/cancel.png" alt="Delete Shift" title="Delete Shift" /></a>';

				shiftArray[inc] = new Array(name,loc,times,tmpDayHolder,group,edit,del);
				inc++;
			}
			
			$(document).ready(function(){
				$('#tblShiftMgmt').dataTable({
					"aoColumns": [
                                                null,
                                                { "bSortable": false },
                                       		null,
						{ "bSortable": false },
						{ "bSortable": false },
						{ "bSortable": false },
						{ "bSortable": false },
					],
					"sDom": 'T<"clear">lfrtip',
					"iDisplayLength": -1,
					"bStateSave": true,
					"bProcessing": true,
					"oTableTools": {
						"aButtons": [
							{
								"sExtends": "csv",
								"sButtonText": "CSV",
								"mColumns": [ 0, 1, 2 ]
							},
							{
								"sExtends": "xls",
								"sButtonText": "Excel",
								"mColumns": [ 0, 1, 2 ]
							},
							{
								"sExtends": "pdf",
								"sButtonText": "PDF",
								"mColumns": [ 0, 1, 2 ]
							},
						]
					},
					"aaData": shiftArray
				});
				$('a[title]').qtip();
			});
		</script>

	</head>
	<body>

		<div id="newShift" title="Add a New Shift" style="display: none; font-size: 11pt;">
		
			<div id="popupInstructions">Use the form below to define the parameters of your new shift.</div>
		
			<div id="newUserError" style="width: 100%; text-align: center; color: #CC0000;"></div>
			<div id="newUserSuccess" style="width: 100%; text-align: center; color: #66CC00;"></div>
			
			<table cellspacing="10">
				<tr>
					<td>Shift Name: </td>
					<td><input name="name" id="name" type="text" value=""></td>
					<td># providers (FTEs): </td>
					<td><input id="numProviders" type="text" value="1"></td>
				</tr>
				<tr>
					<td>Shift Start: </td>
					<td>
						<select id="startTime" name="startTime">
							<option value="0000">12:00 Midnight</option>
							<option value="0030">12:30 am</option>
							<option value="0100">1:00 am</option>
							<option value="0130">1:30 am</option>
							<option value="0200">2:00 am</option>
							<option value="0230">2:30 am</option>
							<option value="0300">3:00 am</option>
							<option value="0330">3:30 am</option>
							<option value="0400">4:00 am</option>
							<option value="0430">4:30 am</option>
							<option value="0500">5:00 am</option>
							<option value="0530">5:30 am</option>
							<option value="0600">6:00 am</option>
							<option value="0630">6:30 am</option>
							<option value="0700">7:00 am</option>
							<option value="0730">7:30 am</option>
							<option value="0800">8:00 am</option>
							<option value="0830">8:30 am</option>
							<option value="0900">9:00 am</option>
							<option value="0930">9:30 am</option>
							<option value="1000">10:00 am</option>
							<option value="1030">10:30 am</option>
							<option value="1100">11:00 am</option>
							<option value="1130">11:30 am</option>
							<option value="1200">12:00 pm</option>
							<option value="1230">12:30 pm</option>
							<option value="1300">1:00 pm</option>
							<option value="1330">1:30 pm</option>
							<option value="1400">2:00 pm</option>
							<option value="1430">2:30 pm</option>
							<option value="1500">3:00 pm</option>
							<option value="1530">3:30 pm</option>
							<option value="1600">4:00 pm</option>
							<option value="1630">4:30 pm</option>
							<option value="1700">5:00 pm</option>
							<option value="1730">5:30 pm</option>
							<option value="1800">6:00 pm</option>
							<option value="1830">6:30 pm</option>
							<option value="1900">7:00 pm</option>
							<option value="1930">7:30 pm</option>
							<option value="2000">8:00 pm</option>
							<option value="2030">8:30 pm</option>
							<option value="2100">9:00 pm</option>
							<option value="2130">9:30 pm</option>
							<option value="2200">10:00 pm</option>
							<option value="2230">10:30 pm</option>
							<option value="2300">11:00 pm</option>
							<option value="2330">11:30 pm</option>
						</select>
					</td>
					<td>Shift End: </td>
					<td>
						<select id="endTime" name="endTime">
							<option value="0000">12:00 Midnight</option>
							<option value="0030">12:30 am</option>
							<option value="0100">1:00 am</option>
							<option value="0130">1:30 am</option>
							<option value="0200">2:00 am</option>
							<option value="0230">2:30 am</option>
							<option value="0300">3:00 am</option>
							<option value="0330">3:30 am</option>
							<option value="0400">4:00 am</option>
							<option value="0430">4:30 am</option>
							<option value="0500">5:00 am</option>
							<option value="0530">5:30 am</option>
							<option value="0600">6:00 am</option>
							<option value="0630">6:30 am</option>
							<option value="0700">7:00 am</option>
							<option value="0730">7:30 am</option>
							<option value="0800">8:00 am</option>
							<option value="0830">8:30 am</option>
							<option value="0900">9:00 am</option>
							<option value="0930">9:30 am</option>
							<option value="1000">10:00 am</option>
							<option value="1030">10:30 am</option>
							<option value="1100">11:00 am</option>
							<option value="1130">11:30 am</option>
							<option value="1200" selected="selected">12:00 Noon</option>
							<option value="1230">12:30 pm</option>
							<option value="1300">1:00 pm</option>
							<option value="1330">1:30 pm</option>
							<option value="1400">2:00 pm</option>
							<option value="1430">2:30 pm</option>
							<option value="1500">3:00 pm</option>
							<option value="1530">3:30 pm</option>
							<option value="1600">4:00 pm</option>
							<option value="1630">4:30 pm</option>
							<option value="1700">5:00 pm</option>
							<option value="1730">5:30 pm</option>
							<option value="1800">6:00 pm</option>
							<option value="1830">6:30 pm</option>
							<option value="1900">7:00 pm</option>
							<option value="1930">7:30 pm</option>
							<option value="2000">8:00 pm</option>
							<option value="2030">8:30 pm</option>
							<option value="2100">9:00 pm</option>
							<option value="2130">9:30 pm</option>
							<option value="2200">10:00 pm</option>
							<option value="2230">10:30 pm</option>
							<option value="2300">11:00 pm</option>
							<option value="2330">11:30 pm</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Frequency: </td>
					<td colspan="3" style="margin-left: 20px;">
						&nbsp;
						<span id="dayMonday" class="shiftDaySelect" day="Monday">Monday</span> 
						<span id="dayTuesday" class="shiftDaySelect" day="Tuesday">Tuesday</span> 
						<span id="dayWednesday" class="shiftDaySelect" day="Wednesday">Wednesday</span> 
						<span id="dayThursday" class="shiftDaySelect" day="Thursday">Thursday</span> 
						<span id="dayFriday" class="shiftDaySelect" day="Friday">Friday</span> 
						<span id="daySaturday" class="shiftDaySelect" day="Saturday">Saturday</span> 
						<span id="daySunday" class="shiftDaySelect" day="Sunday">Sunday</span>
					</td>
				</tr>
				<tr>
					<td>Group: </td>
					<td><div class="group-select"></div></td>
				</tr>
                                <tr>
					<td>Location: </td>
					<td><div class="location-select"></div></td>
				</tr>
				<tr>
					<td colspan="4" style="text-align: center; margin-top: 10px;">
						<input type="hidden" name="hiddenId" id="hiddenId" value="" />
						<input type="button" name="btnSubmitNewShift" id="btnSubmitNewShift" value=" Add Shift " onClick="processShift('new');" />
						<div id="pleaseWait" style="display: none;"><img src="images/ajax-loader.gif" /> Please wait, reloading page...</a>
					</td>
				</tr>
			</table>
		</div>
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
										<img src="images/tag_blue_add.png" alt="Add New Shift" /> <a href="#" class="openNewEditShift">Add New Shift</a>
									</div>
									
                                    <h2 id="dashboard">Shift Management</h2>				

									<table id="tblShiftMgmt" class="display" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th style="width: 200px;">Name</th>
                                              <th style="width: 100px;">Location</th>
											<th>Times</th>
											<th>Frequency</th>
                                            <th>Group</th>
                                            <th style="width: 30px;">Edit</th>
											<th style="width: 30px;">Delete</th>
                                        </tr>
                                        </thead>
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
			<div class="modal"><!-- Place at bottom of page --></div>
	</body>
</html>