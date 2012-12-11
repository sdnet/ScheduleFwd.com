<? require("php_includes/isLoggedIn.php"); ?>
<? require("php_includes/protectedPage.php"); ?>
<html>
	<head>
		<title>Schedule Forward :: Medical scheduling software made easy</title>
		<?php include("html_includes/adminMeta.php"); ?>

<style>
.multiselect {
	text-align: left;
	font-size: 0.7em;
    width:25em;
    height:20em;
    border:solid 1px #c0c0c0;
    overflow:auto;
}
 
.multiselect label {
    display:block;
}
 
.multiselect-on {
    color:#000000;
    background-color:#E6F9F0;
}
</style>

        <script language="javascript" type="text/javascript">
            function expandRow(divId) {
                $('#' + divId).css("padding","15px");
                $('#' + divId).toggle('fast');
            }

            function reduceRow(divId) {
                $('#' + divId).hide('slow');
            }
			
			function getSites() {
				$.ajaxSetup({async:false});
				$.post('ws/getLocations', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
					if (data.message == "success") {

					}
				});	
			}
			
			function deleteUser(id,type) {
				var r = confirm("Are you sure that you want to delete this provider?")
				if (r == true) {
					if (type == "external") {
						$.post('ws/deleteExternal', {"sessionId":"<?=$sessionId;?>","userId":id,"grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
							if (data.message == "success") {
								alert("Provider successfully deleted!");
								window.location.reload();
							}
						});
					} else {
						$.post('ws/deleteUser', {"sessionId":"<?=$sessionId;?>","userId":id,"grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
							if (data.message == "success") {
								alert("Provider successfully deleted!");
								window.location.reload();
							}
						});
					}
				}
			}
			
			// Query for the provider and translate the returned JSON into an array that DataTables can read
			$.ajaxSetup({async:false});
			$.post('ws/getUsers', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","format":"dt"} , function(data) {
				userObj = data.data;
			});
			
			function clearForm() {
				$('#username').val();
				$('#password').val();
				$('#firstname').val();
				$('#lastname').val();
				$('#email').val();
				$('#phone').val();
				$('#group').val();
				$('#role').val();
				$('#priority').val();
				$('#location').val();
				$('#scheduleProvider').val();
			}
				
			userArray = new Array();
			var inc = 0;
			for (var user in userObj) {
				var id = userObj[user]._id.$id;
				var firstname = userObj[user].first_name;
				var lastname = userObj[user].last_name;
				var username = userObj[user].user_name;
				var email = userObj[user].email;
				var phone = userObj[user].phone;
				var group = userObj[user].group;
				var loc = userObj[user].location;
				var role = (userObj[user].role != undefined ? userObj[user].role : 'User');
				var edit = "<a style=\"cursor: pointer;\" title=\"Edit provider's Details\" class=\"openEditUser\" onClick=\"processEditUserModal('" + username + "')\"><img src=\"images/user_edit.png\" alt=\"Edit\" /></a> <a title=\"Edit provider's Scheduling Preferences\" href=\"editUserPrefs?user=" + username + "\"><img src=\"images/wrench.png\" alt=\"Edit\" title=\"Edit Preferences\" /></a> <a title=\"Edit provider's Timeoff Requests\" href=\"editUserTimeoffs?user=" + id + "\"><img src=\"images/calendar_view_day.png\" alt=\"Edit\" title=\"Submit Timeoff Requests\" /></a>";
				var del = "<a href=\"#\"><img src=\"images/user_delete.png\" alt=\"Delete\" title=\"Delete\" onClick=\"deleteUser('" + id + "','')\" /></a>";
				
				userArray[inc] = new Array(lastname,firstname,email,phone,group,role,edit,del);
				inc++;
			}

			$(document).ready(function(){
				$('#tblUserMgmt').dataTable({
					"aoColumns": [
						null,
						null,
						null,
						null,
						null,
						null,
						{ "bSortable": false },
						{ "bSortable": false }
					],
					"sDom": 'T<"clear">lfrtip',
					"bStateSave": true,
					"bProcessing": true,
					"iDisplayLength": -1,
					"oTableTools": {
						"aButtons": [
							{
								"sExtends": "csv",
								"sButtonText": "CSV",
								"mColumns": [ 0, 1, 2, 3, 4, 5 ]
							},
							{
								"sExtends": "xls",
								"sButtonText": "Excel",
								"mColumns": [ 0, 1, 2, 3, 4, 5 ]
							},
							{
								"sExtends": "pdf",
								"sButtonText": "PDF",
								"mColumns": [ 0, 1, 2, 3, 4, 5 ]
							},
						]
					},
					"aaData": userArray
				});
				
				$("#phone").mask("(999) 999-9999? x99999");
				$("#maxHours").mask("9?99");
				$("#minHours").mask("9?99");
				$('a[title]').qtip();
				var typeDefined = getParameterByName("edit");
				if ((typeDefined != undefined) && (typeDefined != "")) {
					$('#divExtUser').show();
					$('#divUser').hide();
					filterBy("Externals");
				}
				
				$(".multiselect").multiselect();
			});
			
			jQuery.fn.multiselect = function() {
				$(this).each(function() {
					var checkboxes = $(this).find("input:checkbox");
					checkboxes.each(function() {
						var checkbox = $(this);
						// Highlight pre-selected checkboxes
						if (checkbox.attr("checked"))
							checkbox.parent().addClass("multiselect-on");
			 
						// Highlight checkboxes that the user selects
						checkbox.click(function() {
							if (checkbox.attr("checked"))
								checkbox.parent().addClass("multiselect-on");
							else
								checkbox.parent().removeClass("multiselect-on");
						});
					});
				});
			};
			
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
			
			function processUser(type) {
				// Clear error div of any error messages
				$('#newUserError').html('');
				
				// Collect form data
				var username = $('#username').val();
				var password = $('#password').val();
				var firstname = $('#firstname').val();
				var lastname = $('#lastname').val();
				var email = $('#email').val();
				var phone = $('#phone').val();
				var group = $('#group').val();
				
				var includeShifts = [];
				var excludeShifts = [];
				$('.shiftSelections').each(function() {
					var checked = $(this).attr('checked');
					var value = $(this).attr('value');
					
					if (checked == "checked") {
						includeShifts.push(value);
					} else {
						excludeShifts.push(value);
					}
				});
				
				var role = $('#role').val();
				var priority = $('#priority').val();
				var scheduleProvider = $('#scheduleProvider').val();
				var location = $('#location').val();
				var max = $('#maxHours').val();
				var min = $('#minHours').val();
				var userId = $('#userId').val();
				
				// Form the json object to hold the user data
				var jsonUser = {
					"sessionId":"<?=$sessionId;?>",
					"username": "" + username + "",
					"id": "" + userId + "",
					"password": "" + password + "",
					"firstname": "" + firstname + "",
					"lastname": "" + lastname + "",
					"email": "" + email + "",
					"phone": "" + phone + "",
					"shifts": "" + includeShifts + "",
					"notShifts": "" + excludeShifts + "",
					"group": "" + group + "",
					"role": "" + role + "",
					"priority": "" + priority + "",
					"location": "" + location + "",
					"scheduleProvider": "" + scheduleProvider + "",
					"grpcode": "<?=$_SESSION['grpcode'];?>"
				};
				
				var stopImage = "<img src=\"images/stop.png\" alt=\"Error\" />";
				var goImage = "<img src=\"images/accept.png\" alt=\"Success\" />";
				var wsEndPoint = (type == "new" ? "ws/addUser" : "ws/editUser");
				
				$.post(wsEndPoint, jsonUser,
                    function (data) {
						if (data.message == "emailInvalid") {
							$('#newUserError').html(stopImage + ' Email address is invalid.');
						}
						if (data.message == "emailExists") {
							$('#newUserError').html(stopImage + ' Email already exists; please choose a unique email address.');
						}
						if (data.message == "userExists") {
							$('#newUserError').html(stopImage + ' Username already exists; please choose a unique username.');
						}
						if (data.message == "emptyFields") {
							$('#newUserError').html(stopImage + ' Please fill out all fields.');
						}
						if (data.message == "success") {
							if (type == "new") {
								$('#newUserSuccess').html(goImage + ' Provider successfully created!');
								$('#tblUserMgmt').dataTable().fnAddData( [
								lastname,
								firstname,
								email,
								phone,
								group,
								location,
								role,
								"<a title=\"Edit provider's Details\" class=\"openEditUser\" onClick=\"processEditUserModal('" + username + "')\"><img src=\"images/user_edit.png\" alt=\"Edit\" /></a> <a title=\"Edit provider's Scheduling Preferences\" href=\"editUserPrefs?user=" + username + "\"><img src=\"images/wrench.png\" alt=\"Edit\" title=\"Edit Preferences\" /></a> <a title=\"Edit provider's Timeoff Requests\" href=\"editUserTimeoffs?user=" + id + "\"><img src=\"images/calendar_view_day.png\" alt=\"Edit\" title=\"Submit Timeoff Requests\" /></a>",
								"<a href=\"\"><img src=\"images/user_delete.png\" alt=\"Delete\" title=\"Delete\" onClick=\"deleteUser('" + id + "')\" /></a>"
								]
								);
								window.location.reload();
							} else {
								$('#newUserSuccess').html(goImage + ' User successfully edited!');
								$('#pleaseWait').show();
								$('#btnSubmitNewShift').hide();
								setInterval(function(){window.location.reload()},1000);
							}
						}
				});
			}
			
			function processExtUser(type) {
				// Clear error div of any error messages
				$('#newUserError').html('');
				
				// Collect form data
				var orgname = $('#orgname').val();
				var orgstreet = $('#orgstreet').val();
				var orgcity = $('#orgcity').val();
				var orgstate = $('#orgstate').val();
				var firstname = $('#orgfirstname').val();
				var lastname = $('#orglastname').val();
				var email = $('#orgemail').val();
				var phone = $('#orgphone').val();
				var userId = $('#orguserId').val();
				
				// Form the json object to hold the user data
				var jsonUser = {
					"sessionId":"<?=$sessionId;?>",
					"id": "" + userId + "",
					"orgname": "" + orgname + "",
					"address": "" + orgstreet + "",
					"city": "" + orgcity + "",
					"state": "" + orgstate + "",
					"firstname": "" + firstname + "",
					"lastname": "" + lastname + "",
					"email": "" + email + "",
					"phone": "" + phone + "",
					"grpcode": "<?=$_SESSION['grpcode'];?>"
				};
				
				var stopImage = "<img src=\"images/stop.png\" alt=\"Error\" />";
				var goImage = "<img src=\"images/accept.png\" alt=\"Success\" />";
				var wsEndPoint = (type == "new" ? "ws/addExternal" : "ws/editExternal");
				
				$.post(wsEndPoint, jsonUser,
                    function (data) {
						if (data.message == "emptyFields") {
							$('#newUserError').html(stopImage + ' Please provide an organization name.');
						}
						if (data.message == "success") {
							if (type == "new") {
								$('.orgNewUserSuccess').html(goImage + ' External provider successfully created!');
								$('#tblUserMgmt').dataTable().fnAddData( [
								lastname,
								firstname,
								username,
								email,
								phone,
								role,
								"<a style=\"cursor: pointer;\" class=\"openEditUser\" username=\"" + username + "\" onClick=\"processEditExternalUserModal('" + username + "')\"><img src=\"images/user_edit.png\" alt=\"Edit\" title=\"Edit\" /></a> | <a href=\"editUserPrefs?user=" + username + "\"><img src=\"images/wrench.png\" alt=\"Edit\" title=\"Edit Preferences\" /></a>",
								"<a href=\"\"><img src=\"images/user_delete.png\" alt=\"Delete\" title=\"Delete\" onClick=\"deleteExternalUser('" + id + "')\" /></a>"
								]
								);
								setInterval(function(){window.location = "/userMgmt?edit=1"},1000);
							} else {
								$('.orgNewUserSuccess').html(goImage + ' External provider successfully edited!');
								$('#orgPleaseWait').show();
								$('#orgbtnSubmitNewShift').hide();
								setInterval(function(){window.location = "/userMgmt?edit=1"},1000);
							}
						}
				});
			}
			
			function checkEmptyFields(jsonUser) {
				for (var key in jsonUser) {
					if ((jsonUser[key] == undefined) || (jsonUser[key] == "")) {
						return false;
					}
				}
				
				return true;
			}

			function filterBy(type) {
				var endPoint = "";
				var tblDivName = "";
				var tblName = "";
				userArray = new Array();
				
				if (type == "Providers") {
					
					$('#userMgmtLinks').html('<img src="images/user_add.png" alt="Add New User" /> <a onClick="openNewUser()">Add New Provider</a>');
					endPoint = "ws/getUsers";
					$('#divExternal').hide();
					$('#divUser').show();
					tblDivName = "divUser";
					tblName = "tblUserMgmt";
					
					var oTable = $('#'+tblName).dataTable();
					oTable.fnDestroy();
					
					// Query for the users and translate the returned JSON into an array that DataTables can read
					$.ajaxSetup({async:false});
					$.post(endPoint, {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","format":"dt"} , function(data) {
						userObj = data.data;
					});
						
					var inc = 0;
					for (var user in userObj) {
						var id = userObj[user]._id.$id;
						var firstname = userObj[user].first_name;
						var lastname = userObj[user].last_name;
						var username = userObj[user].user_name;
						var email = userObj[user].email;
						var phone = userObj[user].phone;
						var group = userObj[user].group;
						var role = (userObj[user].role != undefined ? userObj[user].role : 'User');
						var edit = "<a title=\"Edit User's Details\" class=\"openEditUser\" onClick=\"processEditUserModal('" + username + "')\"><img src=\"images/user_edit.png\" alt=\"Edit\" /></a> <a title=\"Edit User's Scheduling Preferences\" href=\"editUserPrefs?user=" + username + "\"><img src=\"images/wrench.png\" alt=\"Edit\" title=\"Edit Preferences\" /></a> <a title=\"Edit User's Timeoff Requests\" href=\"editUserTimeoffs?user=" + id + "\"><img src=\"images/calendar_view_day.png\" alt=\"Edit\" title=\"Submit Timeoff Requests\" /></a>";
						var del = "<a><img src=\"images/user_delete.png\" alt=\"Delete\" title=\"Delete\" onClick=\"deleteUser('" + id + "','')\" /></a>";
						
						userArray[inc] = new Array(lastname,firstname,email,phone,group,role,edit,del);
						inc++;	
					}
					
					$('#'+tblName).dataTable({
						"aoColumns": [
							null,
							null,
							null,
							null,
							null,
							null,
							{ "bSortable": false },
							{ "bSortable": false }
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
									"mColumns": [ 0, 1, 2, 3, 4 ]
								},
								{
									"sExtends": "xls",
									"sButtonText": "Excel",
									"mColumns": [ 0, 1, 2, 3, 4 ]
								},
								{
									"sExtends": "pdf",
									"sButtonText": "PDF",
									"mColumns": [ 0, 1, 2, 3, 4 ]
								},
							]
						},
						"aaData": userArray
					});
					
				} else {
					
					$('#userMgmtLinks').html('<img src="images/user_add.png" alt="Add New User" /> <a style="cursor: pointer;" onClick="openNewExtUser()">Add New External Provider</a>');
					endPoint = "ws/getExternals";
					$('#divUser').hide();
					$('#divExternal').show();
					tblDivName = "divExternal";
					tblName = "tblExtUserMgmt";
					
					var oTable = $('#'+tblName).dataTable();
					oTable.fnDestroy();
					
					// Query for the users and translate the returned JSON into an array that DataTables can read
					$.ajaxSetup({async:false});
					$.post(endPoint, {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","format":"dt"} , function(data) {
						userObj = data.data;
					});
						
					var inc = 0;
					for (var user in userObj) {
						var id = userObj[user]._id.$id;
						var firstname = userObj[user].first_name;
						var lastname = userObj[user].last_name;
						var username = userObj[user].user_name;
						var email = userObj[user].email;
						var phone = userObj[user].phone;
						var orgname = userObj[user].org_name;
						var role = (userObj[user].role != undefined ? userObj[user].role : 'User');
						var edit = "<a title=\"Edit User's Details\" class=\"openEditUser\" onClick=\"processEditExtUserModal('" + id + "')\"><img src=\"images/user_edit.png\" alt=\"Edit\" title=\"Edit\" /></a>";
						var del = "<a href=\"\"><img src=\"images/user_delete.png\" alt=\"Delete\" title=\"Delete\" onClick=\"deleteUser('" + id + "','external')\" /></a>";
						
						userArray[inc] = new Array(orgname,lastname,firstname,email,phone,edit,del);
						inc++;
					}
					
					$('#'+tblName).dataTable({
						"aoColumns": [
							null,
							null,
							null,
							null,
							null,
							{ "bSortable": false },
							{ "bSortable": false }
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
									"mColumns": [ 0, 1, 2, 3, 4 ]
								},
								{
									"sExtends": "xls",
									"sButtonText": "Excel",
									"mColumns": [ 0, 1, 2, 3, 4 ]
								},
								{
									"sExtends": "pdf",
									"sButtonText": "PDF",
									"mColumns": [ 0, 1, 2, 3, 4 ]
								},
							]
						},
						"aaData": userArray
					});
				}
			}

			function resetForm() {
				$('#newUserSuccess').html("");
				$('#newUserError').html("");
				$('#location').html("");
			
				$('#overrides').hide();
				$('#overridesLink').show();
				$.post("ws/getGroups", {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"}, 
                    function (data) {
						var select = '<select name="group" id="group" onChange="getGroupDefaults()">';
						var temparray = data["data"];
						select = select + '<option value="">-- Select Below --</option>';
						for(i=0; i<temparray.length; i++) {
							select = select + '<option value="' + temparray[i]['name'] + '">' + temparray[i]['name'] + '</option>';
						}
						select = select + "</select>";
						$('.group-select').html(""); //clear old options
						$('.group-select').html(select);
				});
				
				$.post('ws/getLocations', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
					if (data.message == "success") {
						var locs = data.data;
						for (var loc in locs) {
							$('#location').append('<option value="' + locs[loc]._id + '">' + locs[loc].name + '</option>');
						}
					}
				});
			}
    </script>

	<script>
	// increase the default animation speed to exaggerate the effect
	$.fx.speeds._default = 1000;
	
	$( "#newUser" ).dialog({
		autoOpen: false,
		title: $( "#newUser" ).attr( "title"),
		modal: true,
		width: 800
	});

	$( "#newExtUser" ).dialog({
		autoOpen: false,
		title: $( "#newExtUser" ).attr( "title"),
		modal: true,
		width: 800
	});
	
	function openNewUser() {
		resetForm();
		getSites();
		$('#username').attr("disabled", false);
		$('#username').val("");
		$('#frmPassword').show();
		$('#frmChangePassword').hide();
		$('#password').val("");		
		$('#firstname').val("");
		$('#lastname').val("");
		$('#email').val("");
		$('#phone').val("");
		$('#group').val("");
		$('#role').val("");
		$('#location').val("");
		$('#location').val("");
		$('#scheduleProvider').val("");
		
		$( "#newUser" ).dialog( "open" );
	}
	
	function openNewExtUser() {
		resetForm();
		$('#orgusername').attr("disabled", false);
		$('#orgusername').val("");	
		$('#orgfirstname').val("");
		$('#orglastname').val("");
		$('#orgemail').val("");
		$('#orgphone').val("");
		$('#orgname').val("");
		$('#orgstreet').val("");
		$('#orgcity').val("");
		$('#orgstate').val("");
		
		$( "#newExtUser" ).dialog( "open" );
	}
	
	$(function() {
	
		$( "#newUser" ).dialog({
			autoOpen: false,
			title: $( "#newUser" ).attr( "title"),
			modal: true,
			width: 800
		});

		$( "#newExtUser" ).dialog({
			autoOpen: false,
			title: $( "#newExtUser" ).attr( "title"),
			modal: true,
			width: 800
		});

	});
	
		function processEditUserModal(username) {
			var userObj = "";
			resetForm();
		
			$.post('ws/getUser', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","username":username,"format":"dt"} , function(data) {
				userObj = data.data[0];
			});

			$('#username').attr("disabled", "disabled");
			$('#username').val(userObj.user_name);
			$('#userId').val(userObj._id.$id);
			$('#frmPassword').hide();
			$('#frmChangePassword').show();		
			$('#firstname').val(userObj.first_name);
			$('#lastname').val(userObj.last_name);
			$('#email').val(userObj.email);
			$('#phone').val(userObj.phone);
			$('#group').val(userObj.group);
			$('#maxHours').val(userObj.max_hours);
			$('#minHours').val(userObj.min_hours);
			$('#role').val(userObj.role);
			$('#priority').val(userObj.priority);
			$('#location').val(userObj.location);
			$('#scheduleProvider').val(userObj.scheduleProvider);
			$('#btnSubmitNewUser').attr("value" , " Edit User ");
			$('#btnSubmitNewUser').attr("onClick" , "processUser('edit')");
			$('#newUser').attr("title" , "Edit User Below");
			getShiftsForGroup(userObj.group,userObj.preferences.not_shifts);
			$( "#newUser" ).dialog( "open" );
			return false;
		}
		
		function processEditExtUserModal(username) {
			var userObj = "";
			resetForm();
		
			$.post('ws/getExternal', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":username,"format":"dt"} , function(data) {
				userObj = data.data[0];
			});

			$('#orguserId').val(userObj._id.$id);
			$('#orgfirstname').val(userObj.first_name);
			$('#orglastname').val(userObj.last_name);
			$('#orgemail').val(userObj.email);
			$('#orgphone').val(userObj.phone);
			$('#orgname').val(userObj.org_name);
			$('#orgstreet').val(userObj.address);
			$('#orgcity').val(userObj.city);
			$('#orgstate').val(userObj.state);
			$('#orgbtnSubmitNewUser').attr("value" , " Edit External User ");
			$('#orgbtnSubmitNewUser').attr("onClick" , "processExtUser('edit')");
			$('#newExtUser').attr("title" , "Edit User Below");

			$( "#newExtUser" ).dialog( "open" );
			return false;
		}
		
		function displayOverrides() {
			$('#overrides').slideDown("slow");
			$('#overridesLink').hide();
		}
		
		function getGroupDefaults() {
			var name = $('#group').val();
			$.post('ws/getGroup', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","name":name} , function(data) {
				groupObj = data.data[0];
				$('#maxHours').val(groupObj.max_hours);
				$('#minHours').val(groupObj.min_hours);
			});
			
			getShiftsForGroup(name);
		}
		
		function getShiftsForGroup(groupName,notShifts) {
			$.post('ws/getShifts', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","group":groupName} , function(data) {
				shiftsObj = data.data;
				shiftContents = "";
				if (shiftsObj != null) {
					for (s in shiftsObj) {
						if (notShifts != undefined) {
							var checked = ((notShifts.indexOf(shiftsObj[s]._id.$id) > -1) ? '' : 'checked = checked');
						} else {
							var checked = 'checked = checked';
						}
						shiftContents += '<label><input type="checkbox"' + checked + '" class="shiftSelections" name="option[]" value="' + shiftsObj[s]._id.$id + '" />' + shiftsObj[s].name + '</label>';
					}
				} else {
					shiftContents += '<div style="padding: 10px;">No shifts available for group</div>';	
				}
				$('.multiselect').html('');
				$('.multiselect').append(shiftContents);
			});	
		}
	</script>

	</head>
	<body>
	
	<div id="newUser" title="Add a New Provider" style="display: none; font-size: 11pt;">
		<div id="newUserError" style="width: 100%; text-align: center; color: #CC0000;"></div>
		<div id="newUserSuccess" style="width: 100%; text-align: center; color: #66CC00;"></div>
		<table cellspacing="10">
			<tr>
				<td>Username: &nbsp; </td>
				<td><input name="username" id="username" type="text" value=""> &nbsp; <br /></td>
				<td>Password: &nbsp; </td>
				<td>
					<div id="frmPassword" style="display: none;">
						<input name="password" id="password" type="text" value=""> &nbsp; <br />
					</div>
					<div id="frmChangePassword" style="display: none;">
						<input name="resetPassword" id="resetPassword" type="checkbox" value=""> Reset User's Password <br />
					</div>
				</td>
			</tr>
			<tr>
				<td>First name: &nbsp; </td>
				<td><input name="firstname" id="firstname" type="text" value=""> &nbsp; <br /></td>
				<td>Last name: &nbsp; </td>
				<td><input name="lastname" id="lastname" type="text" value=""> &nbsp; <br /></td>
			</tr>
			<tr>
				<td>Email: &nbsp; </td>
				<td><input name="email" id="email" type="text" value=""> &nbsp; <br /></td>
				<td>Phone: &nbsp; </td>
				<td><input name="phone" id="phone" type="text" value=""> &nbsp; <br /></td>
			</tr>
			<tr>
				<td>Group (Type): &nbsp; </td>
				<td>
					<div class="group-select"></div>
				</td>
				<td>Role: &nbsp; </td>
				<td>
					<select name="role" id="role">
						<option value="User">User</option>
						<option value="Admin">Admin</option>
                        <option value="Scribe">Scribe</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Properties: </td>
				<td colspan="4">
					<table style="width: 100%">
						<tr>
							<td style="background-color: #F2F2F2; text-align: center;">Monthly hours</td>
                            <td style="background-color: #F2F2F2; text-align: center;">Provider seniority</td>
						</tr>
						<tr>
							<td style="text-align: center;">Min: <input type="text" name="minHours" id="minHours" style="width: 40px;" /> &nbsp; Max: <input type="text" name="maxHours" id="maxHours" style="width: 40px;" /></td>
                            <td style="text-align: center;">
                            	<select name="priority" id="priority">
                                	<option value="1">1 - Lowest</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5 - Highest</option>
                                </select>
                            </td>
						</tr>
						<tr>
							<td style="background-color: #F2F2F2; text-align: center;">Schedule provider?</td>
                            <td style="background-color: #F2F2F2; text-align: center;">Sites provider can work</td>
						</tr>
						<tr>
							<td style="text-align: center;">
							  	<select name="scheduleProvider" id="scheduleProvider">
                                	<option value="Yes">Yes</option>
									<option value="No">No</option>
                                </select>
							</td>
                            <td style="text-align: center;">
                            	<select name="location" id="location">
                                	<div style="padding: 10px;">Select group to display shifts</div>
                                </select>
                            </td>
						</tr>
						<tr>
							<td style="background-color: #F2F2F2; text-align: center;">Override working shifts for user</td>
                            <td style="background-color: #F2F2F2; text-align: center;"></td>
						</tr>
						<tr>
							<td style="text-align: center; padding: 10px;">
							  	<div class="multiselect">
                                    
                                </div>
							</td>
                            <td style="text-align: left; font-size: 0.8em; text-height: 8px; padding: 10px;">
								<span style="font-weight: bold;">Instructions...</span> Select a shift from the left to either assign or unassign a shift from a particular provider.  
                            </td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="4" style="text-align: center; margin-top: 10px;">
					<input name="userId" id="userId" type="hidden" value="">
					<input type="button" name="btnSubmitNewUser" id="btnSubmitNewUser" value=" Add User " onClick="processUser('new');" />
					<div id="pleaseWait" style="display: none;"><img src="images/ajax-loader.gif" /> Please wait, reloading page...</a>
				</td>
			</tr>
		</table>
	</div>
    
	<div id="newExtUser" title="Create New External Provider" style="display: none; font-size: 11pt;">
		<div id="newUserError" class="orgNewUserError" style="width: 100%; text-align: center; color: #CC0000;"></div>
		<div id="newUserSuccess" class="orgNewUserSuccess" style="width: 100%; text-align: center; color: #66CC00;"></div>
        <div id="popupInstructions">Please provide the organization name below, and optional details</div>
		<table cellspacing="10">
			<tr>
            	<td style="padding: 5px; background-color: #E6E6E6;" colspan="4">Organization Details</td>
            </tr>
            <tr>
				<td>Name: &nbsp; </td>
				<td><input name="orgname" id="orgname" type="text" value=""> &nbsp; <br /></td>
				<td>Street Address: &nbsp; </td>
				<td><input name="orgstreet" id="orgstreet" type="text" value=""> &nbsp; <br /></td>
			</tr>
            <tr>
				<td>City: &nbsp; </td>
				<td><input name="orgcity" id="orgcity" type="text" value=""> &nbsp; <br /></td>
				<td>State: &nbsp;</td>
				<td><input name="orgstate" id="orgstate" type="text" value=""> &nbsp; <br /></td>
			</tr>
			<tr>
            	<td style="padding: 5px; background-color: #E6E6E6;" colspan="4">Contact Details</td>
            </tr>
			<tr>
				<td>First name: &nbsp; </td>
				<td><input name="orgfirstname" id="orgfirstname" type="text" value=""> &nbsp; <br /></td>
				<td>Last name: &nbsp; </td>
				<td><input name="orglastname" id="orglastname" type="text" value=""> &nbsp; <br /></td>
			</tr>
			<tr>
				<td>Email: &nbsp; </td>
				<td><input name="orgemail" id="orgemail" type="text" value=""> &nbsp; <br /></td>
				<td>Phone: &nbsp; </td>
				<td><input name="orgphone" id="orgphone" type="text" value=""> &nbsp; <br /></td>
			</tr>
			<tr>
				<td colspan="4" style="text-align: center; margin-top: 10px;">
					<input name="orguserId" id="orguserId" type="hidden" value="">
					<input type="button" name="orgbtnSubmitNewUser" id="orgbtnSubmitNewUser" value=" Add External Provider " onClick="processExtUser('new');" />
					<div id="orgPleaseWait" style="display: none;"><img src="images/ajax-loader.gif" /> Please wait, reloading page...</a>
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
										<img src="images/user_add.png" alt="Add New User" /> <a style="cursor: pointer;" class="openNewEditUser" onClick="openNewUser()">Add New Provider</a>
									</div>
									
                                    <h2 id="dashboard">Provider Management</h2>
                                    
                                    <div style="width: 270px; padding: 5px; margin: 0 auto; text-align: center; background-color: #333; color: #FFF;">
                                    	Filter by: <span style="cursor: pointer; border-bottom: 1px dotted #CCC;" onClick="filterBy('Providers');">Provider</span> | 
                                        <span style="cursor: pointer; border-bottom: 1px dotted #CCC;" onClick="filterBy('External');">External Providers</span>
                                    </div>
                                    
                                    <div id="divUser">
                                        <table id="tblUserMgmt" class="display" style="width: 100%;">
                                            <thead>
                                            <tr>
                                                <th>Last Name</th>
                                                <th>First Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Group</th>
                                                <th>Roles</th>
                                                <th style="width: 60px;">Edit</th>
                                                <th style="width: 30px;">Delete</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    
                                    <div id="divExternal" style="display: none;">
                                        <table id="tblExtUserMgmt" class="display" style="width: 100%;">
                                            <thead>
                                            <tr>
                                            	<th>Org. Name</th>
                                                <th>Last Name</th>
                                                <th>First Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th style="width: 30px;">Edit</th>
                                                <th style="width: 30px;">Delete</th>
                                            </tr>
                                            </thead>
                                        </table>
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