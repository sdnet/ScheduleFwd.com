//This method initialises the modal popup
function displayPopup(scheduleId, shiftId, sessionId, grpcode, divId, role) {
	
	// Check to see if the user who clicked on a shift is working it
	if (role != "Admin") {
		return;	
	}
	
	$.ajaxSetup({async:false});
	var shiftObj = "";
	$.post('ws/getLiveShift', {"sessionId":sessionId,"grpcode":grpcode,"shiftId":shiftId,"scheduleId":scheduleId} , function(data) {
		shiftObj = data.data;
	});

	var start = shiftObj.start.split(" ");
	start = start[0];
	
	var end = shiftObj.endreal.split(" ");
	end = end[0];
	
	var source = '<div style="float: right;"><img src="../images/big_x.png" onclick="closePopup(300)" style="cursor: pointer; margin-top: -35px; margin-right: -36px;" title="Close Window" /></div>';
	source = source + '<div id="divSpinner" style="display: none; width: 100%; text-align: center;"><img src="/images/ajax-loader.gif"></div>';
	source = source + '<h2 style="text-align: center; font: 1.4em Verdana; margin-bottom: 3px;">' + shiftObj.shiftName + '</h2>';
	source = source + '<div style="margin: 0 auto; width: 80%; text-align: center;"><span style="font-weight: bold;">' + start + " at " + getTimeFromDate(shiftObj.start) + '</span> through <span style="font-weight: bold;">' + end + " at " + getTimeFromDate(shiftObj.end) + '</span></div>';
	source = source + '<div style="text-align: left;">';
		
		source = source + '<div style="width: 250px; float: right; text-align: right; padding-right: 20px; font-size: 0.9em;"><img src="../images/user_add.png" /> ';
		source = source + '<span style="cursor: pointer; text-decoration: Underline;" id="addUsers" class=\''+scheduleId+shiftId+'\' onclick="preProcessAddUserToShift(\''+scheduleId+'\',\''+shiftId+'\',\''+shiftObj.number+'\',\''+sessionId+'\',\''+grpcode+'\',\''+divId+'\')">Add users</span>';
		
		source = source + '</div> <br />';
		source = source + '<div style="font: 1.2em Verdana; margin-bottom: 3px;">Users in shift</div>';
		source = source + '<div class="userHolder" style="width: 95%; background-color: #F2F2F2; border-radius: 5px; padding: 10px; margin-bottom: 10px;">';
		var user = "No users in shift!";
		if (shiftObj.users != null) {
			if ((shiftObj.users.length > 0)) {
				for (var i = 0; i < shiftObj.users.length; i++) {
					var isExt = 0;
					var user;
					if ((shiftObj.users[i].first_name == undefined) && (shiftObj.users[i].last_name == undefined)) {
						user = "No users in shift!"; 
					} else {
						if (shiftObj.users[i].first_name == null) { shiftObj.users[i].user_name.toLowerCase() = shiftObj.users[i].id; shiftObj.users[i].first_name = ""; isExt = 1; }
						user = shiftObj.users[i].first_name + ' ' + shiftObj.users[i].last_name;
					}
						var t = divId.split("_");
						divId = t[0] + "_" + t[1] + "_" + shiftObj.users[i].user_name.toLowerCase();
						divId = divId.toLowerCase();
						source = source + '<span class="userHolderInstance" user="' + shiftObj.users[i].user_name.toLowerCase() + '" onclick="removeUserFromShift(\''+scheduleId+'\',\''+shiftId+'\',\''+shiftObj.users[i].user_name.toLowerCase()+'\',\''+isExt+'\',this,\''+sessionId+'\',\''+grpcode+'\',\''+divId+'\')" style="background-color: #E6E6E6; border-radius: 5px; cursor: pointer;"><img src="../images/user_delete.png" /> ' + user + '</span> &nbsp; ';
				}
			} else {
				source = source + '<span class="userHolder" style="background-color: #E6E6E6; border-radius: 5px; cursor: pointer;"><img src="../images/user_delete.png" />No users in shift!</span> &nbsp; ';
			}
		}
		source = source + '</div>';
		
		var historyObj = "";
		$.post('ws/getTradeHistory', {"sessionId":sessionId,"grpcode":grpcode,"shiftId":shiftId,"scheduleId":scheduleId} , function(data) {
			historyObj = data.data;
		});
		
		source = source + '<div style="font: 1.2em Verdana; margin-bottom: 3px;">Shift trade history</div>';
		source = source + '<table style="width: 100%;">';
			source = source + '<thead><th style="width: 150px; font-weight: bold; background-color: #BFCBD4;">&nbsp; Approved Date</th><th style="background-color: #BFCBD4; font-weight: bold;">&nbsp; From Provider</th><th style="background-color: #BFCBD4; font-weight: bold;">&nbsp; To Provider</th></head>';
				source = source + '<tbody>';
					if (historyObj != null) {
						for (var i = 0; i < historyObj.length; i++) {
							var fromName = historyObj[i].original_user.first_name + " " + historyObj[i].original_user.last_name;
							var toName = historyObj[i].target_user.first_name + " " + historyObj[i].target_user.last_name;
							source = source + '<tr style="border-bottom: 1px dotted #E6E6E6;"><td style="padding: 5px; background-color: #FFF;">' + historyObj[i].date_created + '</td><td style="padding: 5px; background-color: #FFF;">' + fromName + '</td><td style="padding: 5px; background-color: #FFF;">' + toName + '</td></tr>';
						}
					} else {
						source = source + '<tr><td colspan="3" style="padding: 5px; background-color: #FFF;">No trade history available for this shift</td></tr>';
					}
				source = source + '</tbody>';
		source = source + '</table>';
	source = source + '</div>';
	
	var align = 'center';									//Valid values; left, right, center
	var top = 100; 											//Use an integer (in pixels)
	var width = 700; 										//Use an integer (in pixels)
	var padding = 10;										//Use an integer (in pixels)
	var backgroundColor = '#FFFFFF'; 						//Use any hex code
	var borderColor = '#333333'; 							//Use any hex code
	var borderWeight = 4; 									//Use an integer (in pixels)
	var borderRadius = 5; 									//Use an integer (in pixels)
	var fadeOutTime = 300; 									//Use any integer, 0 = no fade
	var disableColor = '#666666'; 							//Use any hex code
	var disableOpacity = 40; 								//Valid range 0-100
	var loadingImage = '/images/ajax-loader.gif';		//Use relative path from this page
	modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, source, loadingImage);
}

function tradeShift(scheduleId, shiftId, tThis, sessionId, grpcode) {
	var align = 'center';									//Valid values; left, right, center
	var top = 100; 											//Use an integer (in pixels)
	var width = 400; 										//Use an integer (in pixels)
	var padding = 10;										//Use an integer (in pixels)
	var backgroundColor = '#FFFFFF'; 						//Use any hex code
	var borderColor = '#333333'; 							//Use any hex code
	var borderWeight = 4; 									//Use an integer (in pixels)
	var borderRadius = 5; 									//Use an integer (in pixels)
	var fadeOutTime = 300; 									//Use any integer, 0 = no fade
	var disableColor = '#666666'; 							//Use any hex code
	var disableOpacity = 40; 								//Valid range 0-100
	var loadingImage = '/images/ajax-loader.gif';			//Use relative path from this page
	var shiftUserContents = $(tThis).html();
	
	var lastName = "<?php echo $_SESSION['lastName'] ?>";
	tradeType = "Trade Shift Away";
	
	var shiftObj = "";
	$.ajaxSetup({async:false});
	$.post('ws/getLiveShift', {"sessionId":sessionId,"grpcode":grpcode,"shiftId":shiftId,"scheduleId":scheduleId} , function(data) {
		shiftObj = data.data;
	});

	var userObj = "";
	var userSelHTML = "";
	$.post('ws/getUsers', {"sessionId":sessionId,"grpcode":grpcode,"group":shiftObj.groups[0],"exclude":1} , function(data) {
		userObj = data.data;
	});
	
	for (var i = 0; i < userObj.length; i++) {
		userSelHTML = userSelHTML += '<option value="' + userObj[i]._id.$id + '">' + userObj[i].first_name + ' ' + userObj[i].last_name + ' (' + userObj[i].user_name.toLowerCase() + ')</option>';	
	}
	
	var source = '<div style="float: right;"><img src="../images/big_x.png" onclick="closePopup(300)" style="cursor: pointer; margin-top: -35px; margin-right: -36px;" title="Close Window" /></div>';
	source = source + '<h1 style="text-align: center; font: 2.0em Verdana, sans-serif;">' + tradeType + '</h1>';
	source = source + '<table style="width: 100%;">';
		source = source + '<tr>';
			source = source + '<td style="background-color: #E6E6E6; font-weight: bold;">&nbsp; Shift name</td>';
			source = source + '<td style="background-color: #E6E6E6; font-weight: bold; width: 75px;">Start time</td>';
			source = source + '<td style="background-color: #E6E6E6; font-weight: bold; width: 75px;">End time</td>';
		source = source + '</tr>';
		source = source + '<tr>';
			source = source + '<td style="border-bottom: 1px solid #F2F2F2;">&nbsp; ' + shiftObj.shiftName + '</td>';
			source = source + '<td style="border-bottom: 1px solid #F2F2F2;">' + getTimeFromDate(shiftObj.start) + '</td>';
			source = source + '<td style="border-bottom: 1px solid #F2F2F2;">' + getTimeFromDate(shiftObj.end) + '</td>';
		source = source + '</tr>';
	source = source + '</table> <br />';
	source = source + '<div id="tradeSuccess" style="display: none; background-color: #009E28; color: #FFF; padding: 5px; margin-bottom: 10px; text-align: center; font-weight: bold;">Trade submitted successfully!</div>';
	source = source + '<div style="background-color: #F2F2F2; border-radius: 4px; padding: 10px;">';
		source = source + '<div style="font: 1.5em Tahoma; color: #00779E; width: 100%; text-align: center;">Trade shift with another user</div>';
		source = source + '<div style="line-height: 18px; margin-top: 6px; margin-bottom: 10px;"><span style="font-weight: bold;">First</span>, specify the user to trade with:</div> ';
		source = source + '<select name="selNames" id="selNames" style="width: 80%" onChange="getShiftsByUser(\'' + scheduleId + '\',\'' + shiftId + '\',\'' + sessionId + '\',\'' + grpcode + '\')">';
			source = source + '<option value="">-- Select User --</option>';
			source = source + userSelHTML;
		source = source + '</select> <br />';
		source = source + '<div style="line-height: 18px; margin-top: 6px; margin-bottom: 10px;"><span style="font-weight: bold;">Next</span>, pick the user\'s shift (leave blank for one-way trade):</div> ';
		source = source + '<select name="selShifts" id="selShifts" style="width: 80%">';
			source = source + '<option value="">-- Select Shift --</option>';
		source = source + '</select> <br />';
		source = source + '<div style="line-height: 18px; margin-top: 6px; margin-bottom: 10px;"><span style="font-weight: bold;">Lastly</span>, any comments about the trade?</div> ';
		source = source + '<textarea name="txtComments" id="txtComments" style="width: 90%;"></textarea>';
		
		var origShift = shiftObj.id;
		
		source = source + '<div style="width: 100%; text-align: center;"><input onClick="processTrade(\'' + scheduleId + '\',\'' + origShift + '\',0,\'' + sessionId + '\',\'' + grpcode + '\')" type="button" id="btnSwitchUser" name="btnSwitchUser" value="Request Shift Trade"></div>';
	source = source + '</div>';

	modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, source, loadingImage);
}

function getShiftsByUser(scheduleId,shiftId,sessionId,grpcode) {
	var selUser = $('#selNames :selected').val();
	var shiftObj = "";
	$.ajaxSetup({async:false});
	$.post('ws/getShiftsByUserId', {"sessionId":sessionId,"grpcode":grpcode,"scheduleId":scheduleId,"userId":selUser,"shiftId":shiftId} , function(data) {
		shiftObj = data.data;
	});
	
	if (shiftObj == null) {
		$('#selShifts option').each(function() {
			$(this).remove();
		});
		$('#selShifts')
				.append($('<option>', { id : "" })
				.text("-- Select Shift --"));
	} else {
		$('#selShifts option').each(function() {
			$(this).remove();
		});
		
		$('#selShifts')
			.append($('<option>', { id : "" })
			.text("-- Select Shift --"));	
		
		for (var i = 0; i < shiftObj.length; i++) {
			id = shiftObj[i].id;
			name = shiftObj[i].name;
			
			$('#selShifts')
				.append($('<option>', { id : id })
				.text(name));	
		}
	}
}

function processTrade(scheduleId,origShift,override,sessionId,grpcode) {
	var newShift = $('#selShifts :selected').attr("id");
	var toUser = $('#selNames :selected').val();
	var comments = $('#txtComments').val();
	if (override == undefined) {
		override = "0";	
	}
	
	if (toUser != "") {
		$.ajaxSetup({async:false});
		$.post('ws/addTrade', {"sessionId":sessionId,"grpcode":grpcode,"target":newShift,"scheduleId":scheduleId,"original":origShift,"targetUser":toUser,"comments":comments,"override":override} , function(data) {
			shiftObj = data;
			if (shiftObj.message == "success") {
				var i = setTimeout(function(){closePopup(300);},2000);
				
				$('#tradeSuccess').fadeIn();	
			} else if (shiftObj.message == "error") {
				if (confirm(shiftObj.data[0] + "  Do you still wish to proceed?")) {
					processTrade(scheduleId,origShift,"1",sessionId,grpcode);
				}
			}
		});
	} else {
		alert("Please specify a provider to trade with before submitting request.");	
	}
}

function getTimeFromDate(inDate) {
	var retDate = "";
	
	// Get time from the date
	var inDate = inDate.split(" ");
	var inTime = inDate[1];
	
	// Extract hour from the time
	var inHour = inTime.split(":");
	inHour = inHour[0];
	inHour = parseInt(inHour);
	
	// Get the am/pm stuff taken care of
	if (inHour == 12) {
		retDate = "12pm";	
	} else if (inHour < 12) {
		retDate = inHour + "am";	
	} else if (inHour > 12) {
		retDate = (inHour - 12) + "pm";	
	}
	
	if (retDate == "0am") {
		retDate = "12am";
	}
	
	return retDate;	
}

function preProcessAddUserToShift(scheduleId,shiftId,userMax,sessionId,grpcode,divId) {
	
	// Display spinning wheel here
	$('#divSpinner').show();
	
	var shiftUserObj;
	$.ajaxSetup({async:false});
	$.post('ws/getUsersForShift', {"sessionId":sessionId,"grpcode":grpcode,"scheduleId":scheduleId,"shiftId":shiftId} , function(data) {
		shiftUserObj = data.data;
	});
	
	$('#divSpinner').hide();

	var dispAvailUsers = "";
	var dispAllUsers = "";
	var dispExternalUsers = "";
	
	// Get the available users for this shift
	if (shiftUserObj != null) {
		var availableLength = shiftUserObj.available.length;
		if (availableLength > 0) {
			for (var i = 0; i < availableLength; i++) {
				var display = shiftUserObj.available[i].display;
				dispAvailUsers += '<span onclick="addUserToList(\''+scheduleId+'\',\''+shiftId+'\',\''+shiftUserObj.available[i].user_name.toLowerCase()+'\',this,\''+userMax+'\',\''+sessionId+'\',\''+grpcode+'\',\''+divId+'\')" style="background-color: #DBE4CE; padding: 4px; border-radius: 4px; margin: 4px; cursor: pointer;">' +  display + '</span> <br />';
			}
		} else {
			dispAvailUsers += '<span style="background-color: #DBE4CE; padding: 4px; border-radius: 4px; margin: 4px; cursor: pointer;">No providers available</span> <br />';
		}
	}
	
	// Get remainder of the users for this shift
	if (shiftUserObj != null) {
		var tmpKey = shiftUserObj.allusers;
		for (var key in tmpKey) {
			dispAllUsers += '<span style="font-weight: bold">' + key + '</span> <br />';
			for (var i = 0; i < tmpKey[key].length; i++) {
				var display = tmpKey[key][i].display;
				dispAllUsers += '<span onclick="addUserToList(\''+scheduleId+'\',\''+shiftId+'\',\''+tmpKey[key][i].user_name.toLowerCase()+'\',this,\''+userMax+'\',\''+sessionId+'\',\''+grpcode+'\',\''+divId+'\')" style="background-color: #DBE4CE; padding: 4px; border-radius: 4px; cursor: pointer;">' +  display + '</span> <br />';
			}
		}
	}
	
	// Get remainder of the users for this shift
	if (shiftUserObj != null) {
		if (shiftUserObj.external != null) {
			for (var i = 0; i < shiftUserObj.external.length; i++) {
				var display = shiftUserObj.external[i].display;
				dispExternalUsers += '<span onclick="addUserToList(\''+scheduleId+'\',\''+shiftId+'\',\''+shiftUserObj.external[i].id+'\',this,\''+userMax+'\',\''+sessionId+'\',\''+grpcode+'\',\''+divId+'\')" style="background-color: #DBE4CE; padding: 4px; border-radius: 4px; cursor: pointer;">' +  display + '</span> <br />';
			}
		}
	}
	
	addUserToShift(dispAvailUsers, dispAllUsers, dispExternalUsers, scheduleId, shiftId, sessionId, grpcode, divId);
}

function addUserToShift(dispAvailUsers, dispAllUsers, dispExternalUsers, scheduleId, shiftId, sessionId, grpcode, divId) {
	var oldContent = $('#innerModalPopupDiv').html();
	
	var content = '<span style="font: 1.9em Verdana, sans-serif;">Available Users</span>';
	
	content += '<div id="addUserResult"></div>';
	
	content += '<div style="width: 95%; background-color: #FFFFFF; text-align: right; padding: 8px;"><a style="cursor: pointer;" onClick=\"replaceOldContent(\'' + scheduleId + '\',\'' + shiftId + '\',\'' + sessionId + '\',\'' + grpcode + '\',\'' + divId + '\')\">Return to shift details</a></div>';
	
	content += '<table style="width: 100%;">';
		content += '<tr>';
			content += '<td style="width:33%; background-color: #CCC; font-weight: bold;">Available Group Providers</td>';
			content += '<td style="width:33%; background-color: #CCC; font-weight: bold;">Any Available Providers</td>';
			content += '<td style="width:33%; background-color: #CCC; font-weight: bold;">External Providers</td>';
		content += '</tr>';
		content += '<tr>';
			content += '<td style="width:33%;"><div id="dispAvailUsers" style="text-align: left;">' + dispAvailUsers + '</div></td>';
			content += '<td style="width:33%;><div id="dispAllUsers" style="text-align: left;">' + dispAllUsers + '</div></td>';
			content += '<td style="width:33%;><div id="dispExternalUsers" style="text-align: left;">' + dispExternalUsers +'</div></td>';
		content += '</tr>';
	content += '</table>';
	
	$('#innerModalPopupDiv').html(content);

	/*
	$('#addUserDiv').lightbox_me({
		centered: false, 
		onLoad: function() { 
				$('#addUserDiv').find('input:first').focus();
				$('#addUserDiv').offset({ top: top, left: left});
				$('#addUserDiv').center();
			}
	});
	*/
}

//This method initialises the modal popup
function reDisplayPopup(scheduleId, shiftId, sessionId, grpcode, divId) {
	var shiftObj = "";
	$.post('ws/getLiveShift', {"sessionId":sessionId,"grpcode":grpcode,"shiftId":shiftId,"scheduleId":scheduleId} , function(data) {
		shiftObj = data.data;
	});
	
	var start = shiftObj.start.split(" ");
	start = start[0];
	
	var end = shiftObj.endreal.split(" ");
	end = end[0];
	
	var source = '<div style="float: right;"><img src="../images/big_x.png" onclick="closePopup(300)" style="cursor: pointer; margin-top: -35px; margin-right: -36px;" title="Close Window" /></div>';
	source = source + '<div id="divSpinner" style="display: none; width: 100%; text-align: center;"><img src="/images/ajax-loader.gif"></div>';
	source = source + '<h2 style="text-align: center; font: 1.4em Verdana; margin-bottom: 3px;">' + shiftObj.shiftName + '</h2>';
	source = source + '<div style="margin: 0 auto; width: 80%; text-align: center;"><span style="font-weight: bold;">' + start + " at " + getTimeFromDate(shiftObj.start) + '</span> through <span style="font-weight: bold;">' + end + " at " + getTimeFromDate(shiftObj.end) + '</span></div>';
	source = source + '<div style="text-align: left;">';
		source = source + '<div style="width: 250px; float: right; text-align: right; padding-right: 20px; font-size: 0.9em;"><img src="../images/user_add.png" /> ';
		source = source + '<span style="cursor: pointer; text-decoration: Underline;" id="addUsers" class=\''+scheduleId+shiftId+'\' onclick="preProcessAddUserToShift(\''+scheduleId+'\',\''+shiftId+'\',\''+shiftObj.number+'\',\''+sessionId+'\',\''+grpcode+'\',\''+divId+'\')">Add users</span>';
		source = source + '</div> <br />';
		source = source + '<h3 style="margin-bottom: 3px;">Users in shift</h3>';
		source = source + '<div class="userHolder" style="width: 95%; background-color: #F2F2F2; border-radius: 5px; padding: 10px; margin-bottom: 10px;">';
		var user = "No users in shift!";
		if (shiftObj.users != null) {
			if ((shiftObj.users.length > 0)) {
				for (var i = 0; i < shiftObj.users.length; i++) {
					var isExt = 0;
					var user;
					if ((shiftObj.users[i].first_name == undefined) && (shiftObj.users[i].last_name == undefined)) {
						user = "No users in shift!"; 
					} else {
						if (shiftObj.users[i].first_name == null) { shiftObj.users[i].user_name.toLowerCase() = shiftObj.users[i].id; shiftObj.users[i].first_name = ""; isExt = 1; }
						user = shiftObj.users[i].first_name + ' ' + shiftObj.users[i].last_name;
					}
					var t = divId.split("_");
					divId = t[0] + "_" + t[1] + "_" + shiftObj.users[i].user_name.toLowerCase();
					divId = divId.toLowerCase();
					source = source + '<span class="userHolderInstance" user="' + shiftObj.users[i].user_name.toLowerCase() + '" onclick="removeUserFromShift(\''+scheduleId+'\',\''+shiftId+'\',\''+shiftObj.users[i].user_name.toLowerCase()+'\',\''+isExt+'\',this,\''+sessionId+'\',\''+grpcode+'\',\''+divId+'\')" style="background-color: #E6E6E6; border-radius: 5px; cursor: pointer;"><img src="../images/user_delete.png" /> ' + user + '</span> &nbsp; ';
				}
			} else {
				source = source + '<span class="userHolder" style="background-color: #E6E6E6; border-radius: 5px; cursor: pointer;"><img src="../images/user_delete.png" />No users in shift!</span> &nbsp; ';
			}
		}
		source = source + '</div>';
		var historyObj = "";
		$.post('ws/getTradeHistory', {"sessionId":sessionId,"grpcode":grpcode,"shiftId":shiftId,"scheduleId":scheduleId} , function(data) {
			historyObj = data.data;
		});
		
		source = source + '<div style="font: 1.2em Verdana; margin-bottom: 3px;">Shift trade history</div>';
		source = source + '<table style="width: 100%;">';
			source = source + '<thead><th style="width: 150px; font-weight: bold; background-color: #BFCBD4;">&nbsp; Approved Date</th><th style="background-color: #BFCBD4; font-weight: bold;">&nbsp; From Provider</th><th style="background-color: #BFCBD4; font-weight: bold;">&nbsp; To Provider</th></head>';
				source = source + '<tbody>';
					if (historyObj != null) {
						for (var i = 0; i < historyObj.length; i++) {
							var fromName = historyObj[i].original_user.first_name + " " + historyObj[i].original_user.last_name;
							var toName = historyObj[i].target_user.first_name + " " + historyObj[i].target_user.last_name;
							source = source + '<tr style="border-bottom: 1px dotted #E6E6E6;"><td style="padding: 5px; background-color: #FFF;">' + historyObj[i].date_created + '</td><td style="padding: 5px; background-color: #FFF;">' + fromName + '</td><td style="padding: 5px; background-color: #FFF;">' + toName + '</td></tr>';
						}
					} else {
						source = source + '<tr><td colspan="3" style="padding: 5px; background-color: #FFF;">No trade history available for this shift</td></tr>';
					}
				source = source + '</tbody>';
		source = source + '</table>';
	source = source + '</div>';

return (escape(source));
}

function replaceOldContent(scheduleId,shiftId,sessionId,grpcode,divId) {
		var content = reDisplayPopup(scheduleId,shiftId,sessionId,grpcode,divId);
		$('#innerModalPopupDiv').html(unescape(content));
}

function addUserToList(scheduleId,shiftId,username,tThis,userMax,sessionId,grpcode,divId,shiftObj) {
	var selectedUser = username;
	var displayUser = "";
	var user = "";
	var userObj;
	var shiftObj;
	var id = "";
	var timePref = "true";
	
	$.post('ws/getConfigByKey', {"sessionId":sessionId,"grpcode":grpcode,"key":"displayTimes"} , function(data) {
		if (data.data != null) {
			timePref = data.data.displayTimes;
		}
	});
	
	$.post('ws/getUser', {"sessionId":sessionId,"grpcode":grpcode,"username":selectedUser} , function(data) {
		if (data.data != null) {
			userObj = data.data[0];
			user = userObj.first_name + " " + userObj.last_name;
			displayUser = userObj.first_name.substring(0,1) + ". " + userObj.last_name;
			username = "";
		}
	});
	
	if (userObj == undefined) {
		$.post('ws/getExternal', {"sessionId":sessionId,"grpcode":grpcode,"id":selectedUser} , function(data) {
			if (data.data != null) {
				userObj = data.data[0];
				id = userObj._id.$id;
				user = userObj.org_name;
			}
		});
	}
	
	var t = divId.split("_");
	$.post('ws/getShift', {"sessionId":sessionId,"grpcode":grpcode,"id":t[1]} , function(data) {
		if (data.data != null) {
			shiftObj = data.data[0];
		}
	});

	$.post('ws/addUserToShift', {"sessionId":sessionId,"grpcode":grpcode,"orgId":id,"org_name":username,"username":selectedUser,"scheduleId":scheduleId,"shiftId":shiftId} , function(data) {
		message = data.message;
		if ((message == "success") || (message == "warning")) {
			$("#freeow").freeow("User added to shift", selectedUser + " has been added successfully to the shift.", {
				classes: ["gray", "success"],
				autoHide: true
			});
			
			var newId = t[0] + "_" + t[1] + "_" + userObj.user_name.toLowerCase();
			var newShiftUser = "";
			newShiftUser += '<div onclick="displayPopup(\'' + scheduleId + '\', \'' + shiftId + '\', \'' + sessionId + '\', \'' + grpcode + '\', \'' + newId + '\')" removeId="' + t[0] + '_' + t[1] + '" class="calShift" id="' + newId + '">';			
				newShiftUser += '<div class="userDisplay ' + username + '">';
				newShiftUser += userObj.first_name.substring(0,1) + ". " + userObj.last_name;
				newShiftUser += '</div>';
				newShiftUser += shiftObj['name'];
				if (timePref == "true") {
					newShiftUser += " " + convertToDisplayTime(shiftObj['start']) + "-" + convertToDisplayTime(shiftObj['end']);
				}
			newShiftUser += '</div>';
			
			divId = newId;
			
			var tmpSpltDiv = divId.split("_");
			var removeId = tmpSpltDiv[0] + "_" + tmpSpltDiv[1];
			var numLeft = $("div[removeId='" + removeId + "']").length;
			if (numLeft == 1) {
				var id = $("div[removeId='" + removeId + "']").attr("id");
				if ($('#'+id+' .userDisplay').html() == "OPEN") {
					$('#'+id).css("background-color","");
					$('#'+id).css("color","#333333");
					$('#'+id+' .userDisplay').html(userObj.first_name.substring(0,1) + ". " + userObj.last_name);
					$('#'+id).attr("id",newId);
				} else {
					$("div[removeId='" + removeId + "']").after(newShiftUser);
				}
			} else {
				// $('#'+divId).after(newShiftUser);
				$("div[removeId='" + removeId + "']").last().after(newShiftUser);
			}
		}
	});
	
	$(tThis).remove();

	var currentUserSpan = $('.userHolder').html();
	if (currentUserSpan != null) {
		if (currentUserSpan.indexOf("No users in shift") > -1) {
			$('.userHolder').html("");	
		}
	}
	
	// $('.userHolder').append('<span class="userHolderInstance" onclick="removeUserFromShift(\''+scheduleId+'\',\''+shiftId+'\',this,\''+selectedUser+'\',this,\''+sessionId+'\',\''+grpcode+'\',\''+divId+'\')" style="background-color: #E6E6E6; border-radius: 5px; cursor: pointer;"><img src="../images/user_delete.png" /> ' + user + '</span> &nbsp; ');
}

function removeUserFromShift(scheduleId,shiftId,username,isExt,theDiv,sessionId,grpcode,divId) {
	if (isExt == 1) {
		isExt = username;	
		username = "";
	} else {
		isExt = "";	
	}
	$.post('ws/deleteUserFromShift', {"sessionId":sessionId,"grpcode":grpcode,"scheduleId":scheduleId,"shiftId":shiftId,"username":username,"orgId":isExt} , function(data) {
		userObj = data.message;
		if (userObj == "success") {
			$(theDiv).hide();
			if ($('.userHolder').html() == "") {
				$('.userHolder').append('<span class="userHolder" style="background-color: #E6E6E6; border-radius: 5px; cursor: pointer;"><img src="../images/user_delete.png" /> No users in shift!</span> &nbsp; ');
			
			}
			if ((userObj == "success") || (userObj == "warning")) {
				$("#freeow").freeow("User removed from shift", username + " has been removed successfully from the shift.", {
					classes: ["gray", "success"],
					autoHide: true
			});
				var tmpSpltDiv = divId.split("_");
				var removeId = tmpSpltDiv[0] + "_" + tmpSpltDiv[1];
				var numLeft = $("div[removeId='" + removeId + "']").length;
				if (numLeft == 1) {
					var id = $("div[removeId='" + removeId + "']").attr("id");
					$('#'+id).css("background-color","#CC0000");
					$('#'+id).css("color","#FFFFFF");
					$('#'+id+' .userDisplay').html("OPEN");
				} else {
					$('#'+divId).remove();
				}
			}
		}
	});	
}

function getTimeFromDate(inDate) {
	var retDate = "";
	
	// Get time from the date
	var inDate = inDate.split(" ");
	var inTime = inDate[1];
	
	// Extract hour from the time
	var inHour = inTime.split(":");
	inHour = inHour[0];
	inHour = parseInt(inHour);
	
	// Get the am/pm stuff taken care of
	if (inHour == 12) {
		retDate = "12pm";	
	} else if (inHour < 12) {
		retDate = inHour + "am";	
	} else if (inHour > 12) {
		retDate = (inHour - 12) + "pm";	
	}
	
	if (retDate == "0am") {
		retDate = "12am";
	}
	
	return retDate;	
}

function convertToDisplayTime(inTime) {
	var retTime = "";
	switch (inTime) {
		case '0000':
			retTime = "12am";
			break;
		case '0030':
			retTime = "12:30am";
			break;
		case '0100':
			retTime = "1am";
			break;
		case '0130':
			retTime = "1:30am";
			break;
		case '0200':
			retTime = "2am";
			break;
		case '0230':
			retTime = "2:30am";
			break;
		case '0300':
			retTime = "3am";
			break;
		case '0330':
			retTime = "3:30am";
			break;
		case '0400':
			retTime = "4am";
			break;
		case '0430':
			retTime = "4:30am";
			break;
		case '0500':
			retTime = "5am";
			break;
		case '0530':
			retTime = "5:30am";
			break;
		case '0600':
			retTime = "6am";
			break;
		case '0630':
			retTime = "6:30am";
			break;
		case '0700':
			retTime = "7am";
			break;
		case '0730':
			retTime = "7:30am";
			break;
		case '0800':
			retTime = "8am";
			break;
		case '0830':
			retTime = "8:30am";
			break;
		case '0900':
			retTime = "9am";
			break;
		case '0930':
			retTime = "9:30am";
			break;
		case '1000':
			retTime = "10am";
			break;
		case '1030':
			retTime = "10:30am";
			break;
		case '1100':
			retTime = "11am";
			break;
		case '1130':
			retTime = "11:30am";
			break;
		case '1200':
			retTime = "12pm";
			break;
		case '1230':
			retTime = "12:30pm";
			break;
		case '1300':
			retTime = "1pm";
			break;
		case '1330':
			retTime = "1:30pm";
			break;
		case '1400':
			retTime = "2pm";
			break;
		case '1430':
			retTime = "2:30pm";
			break;
		case '1500':
			retTime = "3pm";
			break;
		case '1530':
			retTime = "3:30pm";
			break;
		case '1600':
			retTime = "4pm";
			break;
		case '1630':
			retTime = "4:30pm";
			break;
		case '1700':
			retTime = "5pm";
			break;
		case '1730':
			retTime = "5:30pm";
			break;
		case '1800':
			retTime = "6pm";
			break;
		case '1830':
			retTime = "6:30pm";
			break;
		case '1900':
			retTime = "7pm";
			break;
		case '1930':
			retTime = "7:30pm";
			break;
		case '2000':
			retTime = "8pm";
			break;
		case '2030':
			retTime = "8:30pm";
			break;
		case '2100':
			retTime = "9pm";
			break;
		case '2130':
			retTime = "9:30pm";
			break;
		case '2200':
			retTime = "10pm";
			break;
		case '2230':
			retTime = "10:30pm";
			break;
		case '2300':
			retTime = "11pm";
			break;
		case '2330':
			retTime = "11:30pm";
			break;
	}
	
	return retTime;
}

function displayStatsPopup(sessionId, grpcode, scheduleId, shiftId) {
	$('#statsLink').attr("src","images/ajax-loader2.gif");
	displayStatsPopup2(sessionId, grpcode, scheduleId, shiftId);
}

//This method initialises the modal popup
function displayStatsPopup2(sessionId, grpcode, scheduleId, shiftId) {
	var stats;

	var date = new Date();
	var d = date.getDate();
	
	m = date.getMonth();
	y = date.getFullYear();
	m++;
	if (m < 10) {
		m = "0" + m;	
	}
	
	var month = getParameterByName("month");
	var year = getParameterByName("year");
	
	if ((month != undefined) && (month != "")) {
		m = month;	
	}
		
	if ((year != undefined) && (year != "")) {
		y = year;	
	}

	$.post('ws/getScheduleStats', {"sessionId":sessionId,"grpcode":grpcode,"month":m,"year":y} , function(data) {
		if (data.message == "success") {
			stats = data.data;	
			var statsDisplay = "<table style=\"width: 100%; margin: 0px;\" id=\"scheduleStats\" class=\"tablesorter\">";
				statsDisplay += "<thead>";
					statsDisplay += "<tr>";
						statsDisplay += "<th style=\"width: 250px;\">User</th><th>Total Hours</th><th>Number of Shifts</th><th>Weekend Shifts</th><th>Night Shifts</th><th>Timeoffs</th><th>Avg Hours / Shift</th>";
					statsDisplay += "</tr>";
				statsDisplay += "</thead>";
				statsDisplay += "<tbody>";
					jQuery.each(stats, function() {
						statsDisplay += "<tr>";
							statsDisplay += "<td style=\"text-align: left; padding-left: 5px; font-size: larger;\"><span style=\"font-weight: bold;\">" + this.userName + "</span></td>";
							statsDisplay += "<td style=\"text-align: center;\">" + this.totalhours + "</td>";
							statsDisplay += "<td style=\"text-align: center;\">" + this.numshifts + "</td>";
							statsDisplay += "<td style=\"text-align: center;\">" + this.numWeekendShifts + "</td>";
							statsDisplay += "<td style=\"text-align: center;\">" + this.numNightShifts + "</td>";
							statsDisplay += "<td style=\"text-align: center;\">" + this.totalTimeOff + "</td>";
							statsDisplay += "<td style=\"text-align: center;\">" + this.averageHoursPerShift + "</td>";	
						statsDisplay += "</tr>";
					});
				statsDisplay += "</tbody>";
			statsDisplay += "</table>";
			
			var source = "";
			
			source += '<div style="width: 100%; text-align: center; background-color: #F2F2F2; font: 1.6em Georgia; border-bottom: 1px solid #E6E6E6;">';
				source += '<div style="padding: 3px;">';
				source += '<img src="/images/table.png" /> Monthly Scheduling Statistics <img src="/images/table.png" />';
				source += '</div>';
			source += '</div>';
			
			source += '<div style="width: 100%; text-align: center;"><a style="cursor: pointer;" onclick="closePopup(300,\'norefresh\')">Close Stats Window</a></div>';
			source = source + '<div style="overflow: auto;">';
			source = source + statsDisplay;
			  source = source + '</div>';
			source += '<div style="width: 100%; text-align: center;"><a style="cursor: pointer;" onclick="closePopup(300,\'norefresh\')">Close Stats Window</a></div>';
			
			var align = 'center';									//Valid values; left, right, center
			var top = 100; 											//Use an integer (in pixels)
			var width = 800; 										//Use an integer (in pixels)
			var padding = 0;										//Use an integer (in pixels)
			var backgroundColor = '#FFFFFF'; 						//Use any hex code
			var borderColor = '#333333'; 							//Use any hex code
			var borderWeight = 4; 									//Use an integer (in pixels)
			var borderRadius = 5; 									//Use an integer (in pixels)
			var fadeOutTime = 300; 									//Use any integer, 0 = no fade
			var disableColor = '#666666'; 							//Use any hex code
			var disableOpacity = 40; 								//Valid range 0-100
			var loadingImage = '/images/ajax-loader.gif';		//Use relative path from this page
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, source, loadingImage);
			$("#scheduleStats").tablesorter(); 
			$("#scheduleStats tr").not(':first').hover(
			  function () {
				$(this).css("background","yellow");
			  }, 
			  function () {
				$(this).css("background","");
			  }
			);
	
			}
	});
	
	$('#statsLink').attr("src","images/statistics.png");
}

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

function regenerateSchedule(sessionId, grpcode, scheduleId, year, month) {
	if (window.scheduleId == undefined) {
		window.scheduleId = "0";	
	}

	$('#publishScheduleWaiting').show();
	$.post('ws/generateSchedule', {"sessionId":sessionId,"grpcode":grpcode,"scheduleId":scheduleId,"month":month,"year":year} , function(data) {
		$('#publishSchedule').css("width","400px;");
		$('#publishSchedule').html("Schedule successfully generated.  It is NOT yet published.");
	});	
	$('#publishScheduleWaiting').fadeOut();
	//location.reload();
}