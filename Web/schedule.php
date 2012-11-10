<? require("php_includes/isLoggedIn.php"); ?>
<? require("php_includes/protectedPage.php"); ?>
<html>
	<head>
		<title>Schedule Forward :: Medical scheduling software made easy</title>
		<?php include("html_includes/adminMeta.php"); ?>
        
        <link rel='stylesheet' type='text/css' href='YourCalendar/cupertino/theme.css' />
        <link rel='stylesheet' type='text/css' href='YourCalendar/fullcalendar/fullcalendar.css' />
        <link rel='stylesheet' type='text/css' href='YourCalendar/fullcalendar/fullcalendar.print.css' media='print' />
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
		<script src="http://code.jquery.com/ui/1.8.23/jquery-ui.min.js" type="text/javascript"></script>
        <script type='text/javascript' src='YourCalendar/fullcalendar/fullcalendar.js'></script>
        <script language="javascript" src="js/modal.popup.js"></script>

		<script type='text/javascript'>
        
            var id = "";
        
            $(document).ready(function () {
        
                var date = new Date();
                var d = date.getDate();
                var m = date.getMonth();
                var y = date.getFullYear();
                
                if (m < 10) {
                    m = "0" + m;	
                }
                
                var userObj;
                var schedule;
                $.ajaxSetup({async:false});
                $.post('ws/getSchedule', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","month":m,"year":y} , function(data) {
                    userObj = data.data;
                });
        
                var schedule = userObj;
                //id = userObj[0]._id.$id;
                // document.write(JSON.stringify(schedule))
        
                $('#calendar').fullCalendar({
                    theme: true,
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,basicWeek'
                    },
                    editable: false,
                    events: schedule
                });
                
                //This method hides the popup when the escape key is pressed
                $(document).keyup(function(e) {
                    if (e.keyCode == 27) {
                        closePopup(fadeOutTime);
                    }
                });
        
            });
            
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
                var loadingImage = 'images/cancel.png';		//Use relative path from this page
                
                source = "Hello dolly!";
                modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, source, loadingImage);	
            }
            
            //This method initialises the modal popup
            function displayPopup(scheduleId, shiftId) {
                var shiftObj = "";
                $.post('ws/getLiveShift', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","shiftId":shiftId,"scheduleId":scheduleId} , function(data) {
                    shiftObj = data.data;
                });
                
                var source = '<div style="float: right;"><img src="../images/big_x.png" onclick="closePopup(300)" style="cursor: pointer; margin-top: -35px; margin-right: -36px;" title="Close Window" /></div>';
                source = source + '<h2 style="text-align: center;">' + shiftObj.shiftName + '</h2>';
                source = source + '<strong>' + shiftObj.start + ' through ' + shiftObj.end + '</strong> <br />';
                source = source + '<div style="text-align: left;">';
                    source = source + '<div style="width: 250px; float: right; text-align: right; padding-right: 20px; font-size: 0.9em;"><img src="../images/user_add.png" /> ';
                    source = source + '<select><option> -- Add User to Shift -- </option><option>Username 1</option><option>Username 2</option><option>Username 3</option></select>';
                    source = source + '</div>';
                    source = source + '<h3 style="margin-bottom: 3px;">Users in shift</h3>';
                    source = source + '<div style="width: 95%; background-color: #F2F2F2; border-radius: 5px; padding: 10px; margin-bottom: 10px;">';
                        // TODO for (while user in shiftObj.users then shiftObj.users[user]...
                        var user;
                        if ((shiftObj.users.first_name == undefined) || (shiftObj.users.last_name == undefined)) {
                            user = "No users in shift!"; 
                        } else {
                            user = shiftObj.users.first_name + ' ' + shiftObj.users.last_name;
                        }
                        source = source + '<span style="background-color: #E6E6E6; border-radius: 5px; cursor: pointer;"><img src="../images/user_delete.png" /> ' + user + '</span> &nbsp; ';
                    source = source + '</div>';
                    source = source + '<div style="text-align: right; margin-right: 2px;"><input type="button" value="Submit Changes"></div>';
                    source = source + '<h3 style="margin-bottom: 3px;">Shift trade history</h3>';
                    source = source + '<table style="width: 100%; border: 1px solid #CCC;">';
                        source = source + '<thead><th style="width: 100px; background-color: #CCC;">&nbsp; Date</th><th style="background-color: #CCC;">&nbsp; From</th><th style="background-color: #CCC;">&nbsp; To</th></head>';
                            source = source + '<tbody style="border-bottom: 1px dotted #666;">';
                                source = source + '<tr><td>2012-08-30</td><td>Steve Adcock</td><td>James Flanagan</td></tr>';
                                source = source + '<tr><td>2012-08-29</td><td>George Corliss</td><td>Andy Walker</td></tr>';
                            source = source + '</tbody>';
                    source = source + '</table>';
                source = source + '</div>';
                
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
                modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, source, loadingImage);
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
									Show only (toggle): <a href="#" onClick="toggleShifts('mine')">Your shifts</a> | <a href="#" onClick="toggleShifts('open')">Open shifts</a> <br /><br />
									<div id='calendar'></div>
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