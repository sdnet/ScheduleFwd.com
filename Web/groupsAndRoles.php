<? require("php_includes/isLoggedIn.php"); ?>
<? require("php_includes/protectedPage.php"); ?>
<html>
	<head>
		<title>Schedule Forward :: Medical scheduling software made easy</title>
		<?php include("html_includes/adminMeta.php"); ?>
		
		<script>
			$(function() {
				$( "#newSite" ).dialog({
					autoOpen: false,
					modal: true,
					width: 800
				});
			
				$( ".openNewEditSite" ).click(function() {
					// resetGroupForm();
					$( "#newSite" ).dialog( "open" );
					return false;
				});
				
				$( "#newGroup" ).dialog({
					autoOpen: false,
					modal: true,
					width: 800
				});
			
				$( ".openNewEditGroup" ).click(function() {
					resetGroupForm();
					$( "#newGroup" ).dialog( "open" );
					return false;
				});
			});
			
			// Grab data for the groups datatable
			
			var groupObj;
			var inc = 0;
			var groupArray = new Array();
			$.ajaxSetup({async:false});
			// Query for the shifts and translate the returned JSON into an array that DataTables can read
			$.post('ws/getGroups', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
				groupObj = data.data;
			});
			
			for (var i=0; i<groupObj.length; i++) {
				var id = groupObj[i]._id.$id;
				var name = groupObj[i].name;
				var description = groupObj[i].description;
				var max = groupObj[i].max_hours;
				var min = groupObj[i].min_hours;
				var hours = groupObj[i].count;
							
				var print = '<a href="/toPDF/?type=preferences&group=' + name + '&print=1" target="_blank"><img src="images/calendar_edit.png" alt="Make PDF of group preferences page" title="Make PDF of group preferences page" /></a> <a href="/toPDF/?type=timeoff&group=' + name + '&print=1" target="_blank"><img src="images/calendar_view_day.png" alt="Make PDF of timeoff requests" title="Make PDF of timeoff requests" /></a>';
				var edit = '<a href="#" class="" onclick="editGroup(\'' + id + '\')"><img src="images/wrench.png" alt="Edit Group" title="Edit Group" /></a>';
				var del = '<a href="#" class="" onclick="deleteGroup(\'' + id + '\')"><img src="images/cancel.png" alt="Delete Group" title="Delete Group" /></a>';

				groupArray[inc] = new Array(name,max,min,hours,print,edit);
				inc++;
			}
			
			// Grab data for the sites (locations) data table
			
			var siteObj;
			var inc = 0;
			var siteObj = new Array();
			$.ajaxSetup({async:false});
			// Query for the shifts and translate the returned JSON into an array that DataTables can read
			$.post('ws/getLocations', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
				siteObj = data.data;
			});
			
			if (siteObj != null) {
				for (var i=0; i<siteObj.length; i++) {
					var id = siteObj[i]._id.$id;
					var name = siteObj[i].name;
					var description = siteObj[i].short;
					var numOfUsers = "10";
								
					var edit = '<a href="#" class="" onclick="editSite(\'' + id + '\')"><img src="images/wrench.png" alt="Edit Site" title="Edit Site" /></a>';
					var del = '<a href="#" class="" onclick="deleteSite(\'' + id + '\')"><img src="images/cancel.png" alt="Delete Site" title="Delete Site" /></a>';
	
					siteObj[inc] = new Array(name,description,numOfUsers,edit,del);
					inc++;
				}
			}
			
			function deleteGroup(id) {
				var r = confirm("Are you sure that you want to delete this group?")
				if (r == true) {
					$.post('ws/deleteGroup', {"sessionId":"<?=$sessionId;?>","id":id,"grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
						if (data.message == "success") {
							alert("Group successfully deleted!");
							window.location.reload();
						}
					});
				}
			}
			
			function deleteSite(id) {
				var r = confirm("Are you sure that you want to delete this location?")
				if (r == true) {
					$.post('ws/deleteLocation', {"sessionId":"<?=$sessionId;?>","id":id,"grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
						if (data.message == "success") {
							alert("Location successfully deleted!");
							window.location.reload();
						}
					});
				}
			}
			
			function processGroup(type) {
				var id = $('#hiddenId').val();
				var name = $('#name').val();
				var description = $('#description').val();
				var minHours = $('#minHours').val();
				var maxHours = $('#maxHours').val();
				
				var stopImage = "<img src=\"images/stop.png\" alt=\"Error\" />";
				var goImage = "<img src=\"images/accept.png\" alt=\"Success\" />";
				var wsEndPoint = (type == "new" ? "ws/addGroup" : "ws/editGroup");
				
				$.post(wsEndPoint, {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","name":name,"id":id,"description":description,"min":minHours,"max":maxHours} , function(data) {
					if (data.message == "emptyFields") {
						$('#newUserError').html(stopImage + ' Please fill out all fields.');
					} else if (data.message == "overrideInvalid") {
						$('#newUserError').html(stopImage + ' Please double-check your minimum and maximum hour values and try again.');
					} else if (data.message == "groupExists") {
						$('#newUserError').html(stopImage + ' That group name already exists; please choose another name.');
					}
					if (data.message == "success") {
						if (type == "new") {
							$('#newUserSuccess').html(goImage + ' Group successfully created!');
							$('#pleaseWait').show();
							$('#btnSubmitNewGroup').hide();
							setInterval(function(){window.location.reload()},1000);
						} else {
							$('#newUserSuccess').html(goImage + ' Group successfully edited!');
							$('#pleaseWait').show();
							$('#btnSubmitNewGroup').hide();
							setInterval(function(){window.location.reload()},1000);
						}
					}
				});
			}

			function processSite(type) {
				var id = $('#hiddenSiteId').val();
				var name = $('#siteName').val();
				var description = $('#siteDesc').val();
				
				var stopImage = "<img src=\"images/stop.png\" alt=\"Error\" />";
				var goImage = "<img src=\"images/accept.png\" alt=\"Success\" />";
				var wsEndPoint = (type == "new" ? "ws/addLocation" : "ws/editLocation");
				
				$.post(wsEndPoint, {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","name":name,"id":id,"short":description} , function(data) {
					if (data.message == "emptyFields") {
						$('#newUserError').html(stopImage + ' Please fill out all fields.');
					} else if (data.message == "siteExists") {
						$('#newUserError').html(stopImage + ' That site name already exists; please choose another name.');
					}
					if (data.message == "success") {
						if (type == "new") {
							$('#newUserSuccess').html(goImage + ' Site successfully created!');
							$('#pleaseWait').show();
							$('#btnSubmitNewGroup').hide();
							setInterval(function(){window.location.reload()},1000);
						} else {
							$('#newUserSuccess').html(goImage + ' Site successfully edited!');
							$('#pleaseWait').show();
							$('#btnSubmitNewGroup').hide();
							setInterval(function(){window.location.reload()},1000);
						}
					}
				});
			}

			function editGroup(id) {
				var groupObj;
				$.post('ws/getGroup', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":id} , function(data) {
					groupObj = data.data[0];
				});
				
				var name = groupObj.name;
				var description = groupObj.description;
				var max = groupObj.max_hours;
				var min = groupObj.min_hours;
				
				$('#name').val(name);
				$('#description').val(description);
				$('#minHours').val(min);
				$('#maxHours').val(max);
				$('#hiddenId').val(id);
				
				$("#btnSubmitNewGroup").attr("value"," Edit Group ");
				$("#btnSubmitNewGroup").attr("onclick"," processGroup('edit') ");
				$("#popupInstructions").html("Use the form below to edit the parameters of this group.");
				$("#newGroup").dialog( "open" );
				return false;
			}
			
			function editSite(id) {
				var groupObj;
				$.post('ws/getLocation', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":id} , function(data) {
					siteObj = data.data[0];
				});
				
				var name = siteObj.name;
				var description = siteObj.short;
				
				$('#siteName').val(name);
				$('#siteDesc').val(description);
				$('#hiddenSiteId').val(id);
				
				$("#btnSubmitNewSite").attr("value"," Edit Site ");
				$("#btnSubmitNewSite").attr("onclick"," processSite('edit') ");
				$("#popupInstructions").html("Use the form below to edit the parameters of this site.");
				$("#newSite").dialog( "open" );
				return false;
			}
			
			function resetGroupForm() {
				$('#name').val("");
				$('#description').val("");
				$('#maxHours').val("");
				$('#minHours').val("");
				$('#popupInstructions').html("Use the form below to define the parameters of your new group.");
				$('#newUserError').hide();
				$('#newUserSuccess').hide();
			}
			
			$(document).ready(function(){
				$('#tblSiteMgmt').dataTable({
					"aoColumns": [
						null,
						null,
						null,
						null,
						null
					],
					"iDisplayLength": -1,
					"bStateSave": true,
					"bLengthChange": false,
					"bFilter": false,
					"aaData": siteObj
				});
				$('#tblGroupMgmt').dataTable({
					"aoColumns": [
						null,
						null,
						null,
						null,
						null,
						null
					],
					"iDisplayLength": -1,
					"bStateSave": true,
					"bLengthChange": false,
					"bFilter": false,
					"aaData": groupArray
				});
				
				$('#tblRoleMgmt').dataTable({
					"aoColumns": [
						null,
						null
					],
					"iDisplayLength": -1,
					"bStateSave": true,
					"bLengthChange": false,
					"bFilter": false
				});
				$("#maxHours").mask("9?99");
				$("#minHours").mask("9?99");
				
				$('a[title]').qtip();
			});
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

                                    <h2 id="dashboard">Site, Group and Role Management</h2>
							
                            		<!--
									<div id="userMgmtLinks">
										<img src="images/group_add.png" alt="Add New Group" /> <a href="#" class="openNewEditGroup">Add New Group</a>
									</div>
									-->
                                    
                                    <div style="float: right;">
                                    	<img src="images/house.png" alt="Add new site" title="Add new site"> <a href="#" class="openNewEditSite">Add New Site</a>
                                    </div>
									<h3>Location Management</h3>

									<table id="tblSiteMgmt" class="display" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th style="width: 70px;">Site Name</th>
                                            <th>Site Description</th>
											<th style="width: 70px;"># at Site</th>
                                            <th style="width: 30px;">Edit</th>
                                            <th style="width: 30px;">Delete</th>
                                        </tr>
                                        </thead>
                                    </table>

									<br /><br />
                                    
									<h3>Group Management</h3>

									<table id="tblGroupMgmt" class="display" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th>Group Name</th>
											<th style="width: 70px;">Max Hours</th>
											<th style="width: 70px;">Min Hours</th>
                                            <th style="width: 60px;"># Users</th>
                                            <th style="width: 60px;">PDF Print</th>
                                            <th style="width: 30px;">Edit</th>
                                        </tr>
                                        </thead>
                                    </table>

									<br /><br />

									<h3>Available Role</h3>

									<table id="tblRoleMgmt" class="display" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th>Role Name</th>
                                            <th style="width: 60px;"># Users</th>
                                        </tr>
                                        </thead>
										<tbody>
											<tr>
												<td>Admin</td>
												<td>7</td>
											</tr>
											<tr>
												<td>Users</td>
												<td>4</td>
											</tr>
										</tbody>
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

		<div id="newSite" class="popupAddEdit" title="Site Management" style="display: none; font-size: 11pt;">
		
			<div id="popupInstructions">Use the form below to define the parameters of your new site.</div>
		
			<div id="newUserError" style="width: 100%; text-align: center; color: #CC0000;"></div>
			<div id="newUserSuccess" style="width: 100%; text-align: center; color: #66CC00;"></div>
			
			<table cellspacing="10">
				<tr>
					<td>Site Name: </td>
					<td><input name="siteName" id="siteName" type="text" value=""></td>
					<td valign="top">Short Description: </td>
					<td><input name="siteDesc" id="siteDesc" type="text" value=""></td>
				</tr>
				<tr>
					<td colspan="4" style="text-align: center; margin-top: 10px;">
						<input type="hidden" name="hiddenSiteId" id="hiddenSiteId" value="" />
						<input type="button" name="btnSubmitNewSite" id="btnSubmitNewSite" value=" Add Site " onClick="processSite('new');" />
						<div id="pleaseWait" style="display: none;"><img src="images/ajax-loader.gif" /> Please wait, reloading page...</a>
					</td>
				</tr>
			</table>
		</div>

		<div id="newGroup" title="Group Management" style="display: none; font-size: 11pt;">
		
			<div id="popupInstructions">Use the form below to define the parameters of your new group.</div>
		
			<div id="newUserError" style="width: 100%; text-align: center; color: #CC0000;"></div>
			<div id="newUserSuccess" style="width: 100%; text-align: center; color: #66CC00;"></div>
			
			<table cellspacing="10">
				<tr>
					<td>Group Name: </td>
					<td><input name="name" id="name" type="text" value=""></td>
					<td valign="top">Description: </td>
					<td><input name="description" id="description" type="text" value=""></td>
				</tr>
				<tr>
					<td>Min Hours: </td>
					<td><input name="minHours" id="minHours" type="text" value="" style="width: 40px;"></td>
					<td>Max Hours: </td>
					<td><input name="maxHours" id="maxHours" type="text" value="" style="width: 40px;"></td>
				</tr>
				<tr>
					<td colspan="4" style="text-align: center; margin-top: 10px;">
						<input type="hidden" name="hiddenId" id="hiddenId" value="" />
						<input type="button" name="btnSubmitNewGroup" id="btnSubmitNewGroup" value=" Add Group " onClick="processGroup('new');" />
						<div id="pleaseWait" style="display: none;"><img src="images/ajax-loader.gif" /> Please wait, reloading page...</a>
					</td>
				</tr>
			</table>
		</div>

	</body>
</html>