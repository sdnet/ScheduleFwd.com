<? require("../php_includes/isLoggedIn.php"); ?>
<? include("../php_includes/protectedPage.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html><head>
<title>Schedule Forward :: Medical scheduling software made easy</title>
<?php include("../html_includes/adminMeta.php"); ?>
<link rel='stylesheet' type='text/css' href='fullcalendar/fullcalendar.css' />
<link rel='stylesheet' type='text/css' href='fullcalendar/fullcalendar.print.css' media='print' />
<link rel='stylesheet' type='text/css' href='/css/freeow.css' />
<link rel='stylesheet' type='text/css' href='/css/freeow-demo.css' />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.8.23/jquery-ui.min.js" type="text/javascript"></script>
<script type='text/javascript' src='fullcalendar/fullcalendar.js'></script>
<script src="/js/jquery.freeow.min.js" type="text/javascript"></script>
<script src="/js/freeow-demo.js" type="text/javascript"></script>
<script language="javascript" src="../js/modal.popup.js"></script>
<style type='text/css'>
		#calendar {
			width: 95%;
			margin: 0 auto;
			font-size: 80%;
		}
		
		#adminLinks a, #adminLinks a:visited {
			color: #FFF;	
			text-decoration: none;
		}
		
		#adminLinks a:hover, #adminLinks a:active {
			color: #CCC;	
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

<script type="text/javascript">

$(function() {
 
    // grab the initial top offset of the navigation
    var sticky_navigation_offset_top = $('#sticky_navigation').offset().top;
     
    // our function that decides weather the navigation bar should have "fixed" css position or not.
    var sticky_navigation = function(){
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
 
});


function publishSchedule() {
	if (confirm("Are you sure you would like to publish this schedule?  After publishing, the schedule will be viewable by all providers.")) {
		$.post('../ws/publishSchedule', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","scheduleId":scheduleId} , function(data) {
			$('#publishSchedule').css("width","400px");
			$('#publishSchedule').html("Schedule successfully published");
		});	
	}
}

function regenerateSchedule() {
	if (window.scheduleId == undefined) {
		window.scheduleId = "0";	
	}
	$('#publishScheduleWaiting').show();
	$.post('../ws/generateSchedule', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","scheduleId":window.scheduleId,"month":m,"year":y} , function(data) {
		$('#publishSchedule').css("width","400px;");
		$('#publishSchedule').html("Schedule successfully re-generated.  It is NOT yet published.");
	});	
	$('#publishScheduleWaiting').fadeOut();
	// location.reload();
}

function createPDF() {
	var calendar = $('html').html();
	$.post('../ws/createPDF', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","type":"schedule","content":calendar} , function(data) {
		pdfUrl = data.data[0];
		
	});
}
</script>

</head>
<body>

<div id="header-wrapper">
<?php include("../html_includes/header.php"); ?>
<div class="5grid-clear"></div>
</div>

<div id="content-wrapper">
				<div id="content">

<? if ($role == "User") {?>
	<div style="width: 100%; text-align: center;">
		Show only (toggle): <a href="#" onClick="toggleShifts('mine')">Your shifts</a> 
    </div>
<? } ?>

<div id="freeow" class="freeow freeow-top-right"></div>

<div style="width: 90%; margin: 0 auto;">
<? include("../html_includes/loggedInAs.php"); ?>
</div> <br /><br />

<?
if ($role == "Admin") {
?>

<div id="publishScheduleWaiting" style="display: none; text-align: center; margin-top: 10px;">
	<h2>Schedule generation in progress...</h2>
	<img src="/images/ajax-loader.gif" alt="Schedule generation in progress" title="Schedule generation in progress" />
</div>

<div id="publishSchedule" style="text-align: center;" onClick="publishSchedule()">
	<img src="/images/star.png" /> Publish this schedule <img src="/images/star.png" />
</div>
<?
}
?>

<div id="demo_top_wrapper" style="display: none;">
    <!-- this will be our navigation menu -->
    <div id="sticky_navigation_wrapper">
        <div id="sticky_navigation">
            <div class="demo_container" style="text-align: center;">
                <ul>
                    <li><span style="cursor: pointer; border-bottom: 1px dotted #CCC;" onClick="toggleShifts('open')"><a name="openShifts">Open shifts</a></span></li>
                    <li><span style="cursor: pointer; border-bottom: 1px dotted #CCC;" onClick="regenerateSchedule()"><a name="regen">Re-generate Schedule</a></span></li>
                    <li><span style="cursor: pointer; border-bottom: 1px dotted #CCC;" onClick=""><a id="printLink" href="">Print Schedule</a></span></li>
                    <span id="archiveDisplayStart" style="display: none;">
                    	<li><a href="" id="archiveStart" href="">Start Archive Print</a></li>
                    </span>
                    <span id="archiveDisplayEnd" style="display: none;">
                        <li><a href="" id="archiveEnd" href="">End Archive Print</a></li>
                    </span>
                    <li><a id="printLink" onclick="displayStatsPopup()"><img src="../images/statistics.png" style="width: 25px; height: 25px; margin-top: 8px;" /></a></li>
                </ul>
            </div>
        </div>
    </div>
 
</div><!-- #demo_top_wrapper -->

<div id='calendar' style="width: 90%;"></div> <br /><br />

<div id="addUserDiv" style="display: none; width: 900px; background-color: #EEF3F6; border: 5px solid #CEDBE4; border-radius: 10px;">
	<div style="float: right; width: 300px; margin-left: 0px; margin-top: 0px; color: #000000; background-color: #CEDBE4; text-align: left;">
    	<div style="font-size: 1.2em; font-weight: bold; padding: 6px; font-size: 1.3em; text-align: center;">External Providers</div>
        <div style="background-color: #EEF3F6; width: 100%; height: 300px; margin-top: 6px;">
        	<div style="font-style: italic; padding: 8px;">You may <span style="font-weight: bold;">override hospital rules</span> to assign the following users to this shift.</div> 
            <div id="dispExternalUsers" style="overflow: auto; height: 215px;">
            </div>
        </div>
    </div>
    
	<div style="float: right; width: 300px; margin-left: 0px; margin-top: 0px; color: #000000; background-color: #CEDBE4; text-align: left;">
    	<div style="font-size: 1.2em; font-weight: bold; padding: 6px; font-size: 1.3em; text-align: center;">All Department Providers</div>
        <div style="background-color: #EEF3F6; width: 100%; height: 300px; margin-top: 6px;">
        	<div style="font-style: italic; padding: 8px;">You may <span style="font-weight: bold;">override hospital rules</span> to assign the following users to this shift.</div> 
            <div id="dispAllUsers" style="overflow: auto; height: 215px;">
            </div>
        </div>
    </div>

	<div style="float: left; width: 300px; margin-left: 0px; margin-top: 0px; color: #000000; background-color: #CEDBE4; text-align: left;">
    	<div style="font-size: 1.2em; font-weight: bold; padding: 6px; font-size: 1.3em; text-align: center;">Available Group Providers</div>
        <div style="background-color: #EEF3F6; width: 100%; height: 300px; margin-top: 6px;">
        	<div style"font-style: italic; padding: 8px;">The following users do not violate any hospital rules and are available for this shift.</div>
            <div id="dispAvailUsers" style="overflow: auto; height: 215px;">
            </div>
        </div>
    </div>
</div>

</div>
</div>

</body>

<script type='text/javascript'>

//This method initialises the modal popup
function displayStatsPopup(scheduleId, shiftId) {
	var source = '<div style="float: right;"><img src="../images/big_x.png" onclick="closePopup(300,\'norefresh\')" style="cursor: pointer; margin-top: -35px; margin-right: -36px;" title="Close Window" /></div>';
	source = source + '<h1 style="text-align: center; font: 1.8em Verdana;">Monthly scheduling statistics</h1>';
	
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

function constructLinkForPrintSchedule(m,y) {
	var baseURL = "/toPDF/?type=mainschedule";
	
	$('#printLink').attr("href",baseURL + "&month=" + m + "&year=" + y + "&print=1");
	$('#archiveStart').attr("href",baseURL + "&month=" + m + "&year=" + y + "&print=1&when=start");
	$('#archiveEnd').attr("href",baseURL + "&month=" + m + "&year=" + y + "&print=1&when=end");
}

function toggleStatsDiv() {
	var w = $('#fl_menu').css("width");
	if (w == "90px") {
		$('#fl_menu').css("width","600px");
		$('#stats').fadeIn("slow");
		$('#statsMenuIcon').html('<img src="/images/action_delete.png" />');	
	} else {
		$('#stats').fadeOut("slow");
		$('#fl_menu').css("width","90px");
		$('#statsMenuIcon').html('<img src="/images/action_check.png" />');
	}
}

jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", Math.max(0, (($(window).height() - this.outerHeight()) / 2) + 
                                                $(window).scrollTop()) + "px");
    this.css("left", Math.max(0, (($(window).width() - this.outerWidth()) / 2) + 
                                                $(window).scrollLeft()) + "px");
    return this;
}

	var id = "";

		<? if ($role == "Admin") { ?>
			var role = "Admin";
			$('#demo_top_wrapper').show();
		<? }else { ?>
			var role = "User";
		<? } ?>

		$('.fc-button-content').click(function() {
			alert("Hello");
		});

	var m;
	var y;

    $(document).ready(function () {

		// $("#fl_menu").makeFloat({x:($(window).width()/2)-300,y:$(window).height()-100,Speed:"fast"});

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
		
		var month = getParameterByName("month");
		var year = getParameterByName("year");
		
		// Determine whether or not to display the print archive link
		if ((month != undefined) && (year != undefined)) {
			if (y > year) {
				$('#archiveDisplayStart').show();
				$('#archiveDisplayEnd').show();
			} else if (m > month) {
				$('#archiveDisplayStart').show();
				$('#archiveDisplayEnd').show();
			} else if (m == month) {
				$('#archiveDisplayStart').show();	
			}
		} else {
			$('#archiveDisplay').show();
		}
		
		if ((month != undefined) && (month != "")) {
			m = month;	
		}
		
		if ((year != undefined) && (year != "")) {
			y = year;	
		}
		
		constructLinkForPrintSchedule(m,y);
		
		$.post('../ws/getTimeOffSchedule', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","month":m2,"year":y2} , function(data) {
			userObj = data.data;
		});
		//var schedule = userObj;
		
		var userObj;
		var schedule;
		$.ajaxSetup({async:false});
		$.post('../ws/getSchedule', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","month":m,"year":y} , function(data) {
			userObj = data.data;
		});

		var schedule = userObj;
		
		if ((schedule != null) || (schedule != undefined)) {
		
			window.scheduleId = "";
			for (var i = 1; i < 2; i++) {
				window.scheduleId = schedule[i].scheduleId;
			}
			
			window.isPublished = "0";
			for (var i = 0; i < 1; i++) {
				window.isPublished = schedule[i].published;	
			}
			
			if (window.isPublished != "1") {
				$('#publishSchedule').fadeIn();	
			}
		
		}
		
		var defaultview = "<?php if (isset($_GET['day'])) { echo "basicWeek"; } else { echo "month"; } ?>";
		var day = "<?php if (isset($_GET['day'])) { echo $_GET['day']; } else { echo "1"; } ?>";
		
		var timesRun = 1;

        $('#calendar').fullCalendar({
            theme: true,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,basicWeek'
            },
			firstDay: 1,
			month: m-1,
			year: parseInt(y),
			date: day,
			defaultView: defaultview,
            editable: false,
            events: function(start, end, callback) {
				callback(schedule);
				if (timesRun > 1) {
					window.location = "/YourCalendar/";
				}
				timesRun++;
			}
        });
		
		//This method hides the popup when the escape key is pressed
		$(document).keyup(function(e) {
			if (e.keyCode == 27) {
				closePopup(fadeOutTime);
			}
		});

    });
	
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
	
	function removeUserFromShift(scheduleId,shiftId,username,isExt,theDiv) {
		if (isExt == 1) {
			isExt = username;	
			username = "";
		} else {
			isExt = "";	
		}
		$.post('../ws/deleteUserFromShift', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","scheduleId":scheduleId,"shiftId":shiftId,"username":username,"orgId":isExt} , function(data) {
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
				
					$('#indiUser_'+scheduleId+shiftId+username).remove();
					var numOfDivs = $('#users_'+scheduleId+shiftId).find('.usersInShift').size();
					$('#outerDiv_'+scheduleId+shiftId).css("background-color","#FF3D3D");
					// $('#outerDiv_'+scheduleId+shiftId).css("color","#FFFFFF");
				}
			}
		});	
	}
	
	function addUserToList(scheduleId,shiftId,username,tThis,userMax) {
		var selectedUser = username;
		var displayUser = "";
		var user = "";
		var userObj;
		var id = "";
		
		$.post('../ws/getUser', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","username":selectedUser} , function(data) {
			if (data.data != null) {
				userObj = data.data[0];
				user = userObj.first_name + " " + userObj.last_name;
				displayUser = userObj.first_name.substring(0,1) + ". " + userObj.last_name;
				username = "";
			}
		});
		
		if (userObj == undefined) {
			$.post('../ws/getExternal', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":selectedUser} , function(data) {
				userObj = data.data[0];
				id = userObj._id.$id;
				user = userObj.org_name;
			});
		}
		
		$.post('../ws/addUserToShift', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","orgId":id,"org_name":username,"username":selectedUser,"scheduleId":scheduleId,"shiftId":shiftId} , function(data) {
			message = data.message;
			if ((message == "success") || (message == "warning")) {
				$("#freeow").freeow("User added to shift", selectedUser + " has been added successfully to the shift.", {
					classes: ["gray", "success"],
					autoHide: true
				});
				$('#users_'+scheduleId+shiftId).append("<div style=\"margin-left: 3px; margin-bottom: 0px; margin-top: 0px; line-height: 12px;\">" + displayUser + "</div>");
				var numOfDivs = $('#users_'+scheduleId+shiftId).find('.usersInShift').size();
			}
		});
		
		updateCalendarBox(scheduleId,shiftId,"Username");
		
		var currentUserSpan = $('.userHolder').html();
		if (currentUserSpan != null) {
			if (currentUserSpan.indexOf("No users in shift") > -1) {
				$('.userHolder').html("");	
			}
		}
		
		$('.userHolder').append('<span class="userHolderInstance" onclick="removeUserFromShift(\''+scheduleId+'\',\''+shiftId+'\',\''+selectedUser+'\',this)" style="background-color: #E6E6E6; border-radius: 5px; cursor: pointer;"><img src="../images/user_delete.png" /> ' + user + '</span> &nbsp; ');
	}
	
	function updateCalendarBox(scheduleId,shiftId,user) {
		$('#div'+scheduleId+shiftId).append('<span class="userHolderInstance" user="' + user + '" onclick="removeUserFromShift(\''+scheduleId+'\',\''+shiftId+'\',\''+user+'\',this)" style="background-color: #E6E6E6; border-radius: 5px; cursor: pointer;"><img src="../images/user_delete.png" /> ' + user + '</span> &nbsp;');
	}
	
	function replaceOldContent(scheduleId,shiftId) {
		var content = reDisplayPopup(scheduleId,shiftId);
		$('#innerModalPopupDiv').html(unescape(content));
	}
	
	function preProcessAddUserToShift(scheduleId,shiftId,userMax) {
		
		// Display spinning wheel here
		$('#divSpinner').show();
		
		var shiftUserObj;
		$.ajaxSetup({async:false});
		$.post('../ws/getUsersForShift', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","scheduleId":scheduleId,"shiftId":shiftId} , function(data) {
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
					dispAvailUsers += '<span onclick="addUserToList(\''+scheduleId+'\',\''+shiftId+'\',\''+shiftUserObj.available[i].user_name+'\',this,\''+userMax+'\')" style="background-color: #DBE4CE; padding: 4px; border-radius: 4px; margin: 4px; cursor: pointer;">' +  display + '</span> <br />';
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
					dispAllUsers += '<span onclick="addUserToList(\''+scheduleId+'\',\''+shiftId+'\',\''+tmpKey[key][i].user_name+'\',this,\''+userMax+'\')" style="background-color: #DBE4CE; padding: 4px; border-radius: 4px; cursor: pointer;">' +  display + '</span> <br />';
				}
			}
		}
		
		// Get remainder of the users for this shift
		if (shiftUserObj != null) {
			for (var i = 0; i < shiftUserObj.external.length; i++) {
				var display = shiftUserObj.external[i].display;
				dispExternalUsers += '<span onclick="addUserToList(\''+scheduleId+'\',\''+shiftId+'\',\''+shiftUserObj.external[i].id+'\',this,\''+userMax+'\')" style="background-color: #DBE4CE; padding: 4px; border-radius: 4px; cursor: pointer;">' +  display + '</span> <br />';
			}
		}
		
		addUserToShift(dispAvailUsers, dispAllUsers, dispExternalUsers, scheduleId, shiftId);
	}
	
	function addUserToShift(dispAvailUsers, dispAllUsers, dispExternalUsers, scheduleId, shiftId) {
		
		var oldContent = $('#innerModalPopupDiv').html();
		
		var content = '<span style="font: 1.9em Verdana, sans-serif;">Available Users</span>';
		
		content += '<div id="addUserResult"></div>';
		
		content += '<div style="width: 95%; background-color: #FFFFFF; text-align: right; padding: 8px;"><a style="cursor: pointer;" onClick=\"replaceOldContent(\'' + scheduleId + '\',\'' + shiftId + '\')\">Return to shift details</a></div>';
		
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
	
	function displayUserSelect() {
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
		var loadingImage = 'lib/release-0.0.1/loading.gif';		//Use relative path from this page
		source = "testing";
		modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, source, loadingImage);	
	}
	
	function displayShiftPopup() {
		var align = 'center';									//Valid values; left, right, center
		var top = 100; 											//Use an integer (in pixels)
		var width = 500; 										//Use an integer (in pixels)
		var padding = 10;										//Use an integer (in pixels)
		var backgroundColor = '#FFFFFF'; 						//Use any hex code
		var borderColor = '#333333'; 							//Use any hex code
		var borderWeight = 4; 									//Use an integer (in pixels)
		var borderRadius = 5; 									//Use an integer (in pixels)
		var fadeOutTime = 300; 									//Use any integer, 0 = no fade
		var disableColor = '#666666'; 							//Use any hex code
		var disableOpacity = 40; 								//Valid range 0-100
		var loadingImage = 'lib/release-0.0.1/loading.gif';		//Use relative path from this page
		
		source = "Hello dolly!";
        modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, source, loadingImage);	
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
	
	function DateDiff(date1, date2) {
    	var datediff = date1.getTime() - date2.getTime(); //store the getTime diff - or +
    	return (datediff / (24*60*60*1000)); //Convert values to -/+ days and return value      
	}

	function getShiftsByUser(scheduleId,shiftId) {
		var selUser = $('#selNames :selected').val();
		var shiftObj = "";
		$.post('../ws/getShiftsByUserId', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","scheduleId":scheduleId,"userId":selUser,"shiftId":shiftId} , function(data) {
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
	
	function processTrade(origShift,override) {
		var newShift = $('#selShifts :selected').attr("id");
		var toUser = $('#selNames :selected').val();
		var comments = $('#txtComments').val();
		if (override == undefined) {
			override = "0";	
		}
		
		if (toUser != "") {
			$.post('../ws/addTrade', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","target":newShift,"scheduleId":scheduleId,"original":origShift,"targetUser":toUser,"comments":comments,"override":override} , function(data) {
				shiftObj = data;
				if (shiftObj.message == "success") {
					var i = setTimeout(function(){closePopup(300);},2000);
					
					$('#tradeSuccess').fadeIn();	
				} else if (shiftObj.message == "error") {
					if (confirm(shiftObj.data[0] + "  Do you still wish to proceed?")) {
						processTrade(origShift,"1");
					}
				}
			});
		} else {
			alert("Please specify a provider to trade with before submitting request.");	
		}
	}
	
	function tradeShift(scheduleId, shiftId, tThis) {
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
		var loadingImage = '/images/ajax-loader.gif';		//Use relative path from this page
		var shiftUserContents = $(tThis).html();
		var isShiftYellow = shiftUserContents.indexOf("fffc5c");
		
		if (isShiftYellow != -1) {
		
			var lastName = "<?php echo $_SESSION['lastName'] ?>";
			tradeType = "Trade Shift Away";
			
			var shiftObj = "";
			$.post('../ws/getLiveShift', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","shiftId":shiftId,"scheduleId":scheduleId} , function(data) {
				shiftObj = data.data;
			});
			
			var userObj = "";
			var userSelHTML = "";
			$.post('../ws/getUsers', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","group":shiftObj.groups[0],"exclude":1} , function(data) {
				userObj = data.data;
			});
			
			for (var i = 0; i < userObj.length; i++) {
				userSelHTML = userSelHTML += '<option value="' + userObj[i]._id.$id + '">' + userObj[i].first_name + ' ' + userObj[i].last_name + ' (' + userObj[i].user_name + ')</option>';	
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
				source = source + '<select name="selNames" id="selNames" style="width: 80%" onChange="getShiftsByUser(\'' + scheduleId + '\',\'' + shiftId + '\')">';
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
				
				source = source + '<div style="width: 100%; text-align: center;"><input onClick="processTrade(\'' + origShift + '\')" type="button" id="btnSwitchUser" name="btnSwitchUser" value="Request Shift Trade"></div>';
			source = source + '</div>';
	
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, source, loadingImage);
		
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
	
	//This method initialises the modal popup
    function displayPopup(scheduleId, shiftId) {
		
		var shiftObj = "";
		$.post('../ws/getLiveShift', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","shiftId":shiftId,"scheduleId":scheduleId} , function(data) {
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
			source = source + '<span style="cursor: pointer; text-decoration: Underline;" id="addUsers" class=\''+scheduleId+shiftId+'\' onclick="preProcessAddUserToShift(\''+scheduleId+'\',\''+shiftId+'\',\''+shiftObj.number+'\')">Add users</span>';
			
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
							if (shiftObj.users[i].first_name == null) { shiftObj.users[i].user_name = shiftObj.users[i].id; shiftObj.users[i].first_name = ""; isExt = 1; }
							user = shiftObj.users[i].first_name + ' ' + shiftObj.users[i].last_name;
						}
							source = source + '<span class="userHolderInstance" user="' + shiftObj.users[i].user_name + '" onclick="removeUserFromShift(\''+scheduleId+'\',\''+shiftId+'\',\''+shiftObj.users[i].user_name+'\',\''+isExt+'\',this)" style="background-color: #E6E6E6; border-radius: 5px; cursor: pointer;"><img src="../images/user_delete.png" /> ' + user + '</span> &nbsp; ';
					}
				} else {
					source = source + '<span class="userHolder" style="background-color: #E6E6E6; border-radius: 5px; cursor: pointer;"><img src="../images/user_delete.png" />No users in shift!</span> &nbsp; ';
				}
			}
			source = source + '</div>';
			
			var historyObj = "";
			$.post('../ws/getTradeHistory', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","shiftId":shiftId,"scheduleId":scheduleId} , function(data) {
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

	//This method initialises the modal popup
    function reDisplayPopup(scheduleId, shiftId) {
		var shiftObj = "";
		$.post('../ws/getLiveShift', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","shiftId":shiftId,"scheduleId":scheduleId} , function(data) {
			shiftObj = data.data;
		});
		
		var source = '<div style="float: right;"><img src="../images/big_x.png" onclick="closePopup(300)" style="cursor: pointer; margin-top: -35px; margin-right: -36px;" title="Close Window" /></div>';
		source = source + '<h2 style="text-align: center;">' + shiftObj.shiftName + '</h2>';
		source = source + '<strong>' + shiftObj.start + ' through ' + shiftObj.end + '</strong> <br />';
		source = source + '<div style="text-align: left;">';
			source = source + '<div style="width: 250px; float: right; text-align: right; padding-right: 20px; font-size: 0.9em;"><img src="../images/user_add.png" /> ';
			source = source + '<span style="cursor: pointer; text-decoration: Underline;" id="addUsers" class=\''+scheduleId+shiftId+'\' onclick="preProcessAddUserToShift(\''+scheduleId+'\',\''+shiftId+'\')">Add users</span>';
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
							if (shiftObj.users[i].first_name == null) { shiftObj.users[i].user_name = shiftObj.users[i].id; shiftObj.users[i].first_name = ""; isExt = 1; }
							user = shiftObj.users[i].first_name + ' ' + shiftObj.users[i].last_name;
						}
						source = source + '<span class="userHolderInstance" user="' + shiftObj.users[i].user_name + '" onclick="removeUserFromShift(\''+scheduleId+'\',\''+shiftId+'\',\''+shiftObj.users[i].user_name+'\',\''+isExt+'\',this)" style="background-color: #E6E6E6; border-radius: 5px; cursor: pointer;"><img src="../images/user_delete.png" /> ' + user + '</span> &nbsp; ';
					}
				} else {
					source = source + '<span class="userHolder" style="background-color: #E6E6E6; border-radius: 5px; cursor: pointer;"><img src="../images/user_delete.png" />No users in shift!</span> &nbsp; ';
				}
			}
			source = source + '</div>';
			source = source + '<h3 style="margin-bottom: 3px;">Shift trade history</h3>';
			source = source + '<table style="width: 100%; border: 1px solid #CCC;">';
				source = source + '<thead><th style="width: 100px; background-color: #CCC;">&nbsp; Date</th><th style="background-color: #CCC;">&nbsp; From</th><th style="background-color: #CCC;">&nbsp; To</th></head>';
					source = source + '<tbody style="border-bottom: 1px dotted #666;">';
						source = source + '<tr><td>2012-08-30</td><td>Steve Adcock</td><td>James Flanagan</td></tr>';
						source = source + '<tr><td>2012-08-29</td><td>George Corliss</td><td>Andy Walker</td></tr>';
					source = source + '</tbody>';
			source = source + '</table>';
		source = source + '</div>';

	return (escape(source));
    }

		var showMine = false;
		var showOpen = false;
		$('.ui-icon-circle-triangle-e').click(function() {
    		alert("The current date of the calendar is ");
		});
		
		function toggleShifts(type) {
			if (type == "mine") {
				if (showMine == false) {
					$('.fc-event-skin').each(function(index) {
						var color = $(this).css('background-color');
						var parts = color.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
						delete(parts[0]);
						for (var i = 1; i <= 3; ++i) {
							parts[i] = parseInt(parts[i]).toString(16);
							if (parts[i].length == 1) parts[i] = '0' + parts[i];
						}
						color = '#' + parts.join('');
		
						if (color != "#fffc5c") {
							$(this).hide();	
						}
					});
					showMine = true;
				} else {
					$('.fc-event-skin').each(function(index) {
						$(this).show();	
					});
					showMine = false;
				}
			}
			
			if (type == "open") {
				if (showOpen == false) {
					$('.fc-event-skin').each(function(index) {
						var color = $(this).css('background-color');
						var parts = color.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
						delete(parts[0]);
						for (var i = 1; i <= 3; ++i) {
							parts[i] = parseInt(parts[i]).toString(16);
							if (parts[i].length == 1) parts[i] = '0' + parts[i];
						}
						color = '#' + parts.join('');
		
						if (color != "#ff3d3d") {
							$(this).hide();	
						}
					});
					showOpen = true;
				} else {
					$('.fc-event-skin').each(function(index) {
						$(this).show();	
					});
					showOpen = false;
				}
			}
		}

</script>

</html>
