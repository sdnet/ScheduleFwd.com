        <script language="javascript">			
			$(document).ready(function(){
				$('a[title]').qtip();
				loadShifts();
				loadForecast();
				getAlerts();
				highlightNavLink();
				getScheduleStats();
			});
			
			$(function() {
				$( ".column" ).sortable({
					connectWith: ".column"
				});

				$( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
					.find( ".portlet-header" )
						.addClass( "ui-widget-header ui-corner-all" )
						.prepend( "<span class='ui-icon ui-icon-minusthick'></span>")
						.end()
					.find( ".portlet-content" );

				$( ".portlet-header .ui-icon" ).click(function() {
					$( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
					$( this ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
				});

				$( ".column" ).disableSelection();
			});
			
			function ISODateString(secs) {
 				var t = new Date(1970,0,1);
				t.setSeconds(secs);
				var date = t.getDate();
				var month = t.getMonth() + 1;
				var year = t.getFullYear();
				t = month + "/" + date + "/" + year;
				return t;
			}
			
			function getAlerts() {
				$.post('ws/getAlerts', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
					if (data.message == "success") {
						var alertsObj = data.data.alerts;
						$('#alertNum').html(data.data.count);
						$('#alertNum').fadeIn();
						if (alertsObj != null) {
							for (var i = 0; i < alertsObj.length; i++) {
								if (alertsObj[i].severity == "First") {
									var id = alertsObj[i]._id.$id;
									$('#alertsDisplay').html('<img src="/images/firstlogin.png">');
									$('#alertsDisplay').fadeIn();
									setInterval(function(){
										$.post('ws/deleteAlerts', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","alertId":id} , function(data) {});
										$('#alertsDisplay').fadeOut();
									},4000);
										
								}
							}
						}
					}
				});	
			}
			
			function getScheduleStats() {
				$.post('ws/getScheduleStats', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","month":11,"year":2012} , function(data) {
					if (data.message == "success") {
						// success
					}
				});	
			}
			
			function getTimeFromDate(inDate) {
				var retDate = "";
				inDate = $.trim(inDate);

				// Extract hour from the time
				var inHour = inDate.split(":");
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
			
			function loadShifts() {
				// Latest time off requests
				$.post('ws/getCurrentDay', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
					if (data.message == "success") {
						var currentDayOff = data.data;
						for (var i = 0; i < currentDayOff.length; i++) {
							var start = currentDayOff[i].start;
							start = start.substring(start.indexOf(" "),start.length);
							
							var end = currentDayOff[i].end;
							end = end.substring(end.indexOf(" "),end.length);
							
							var users = currentDayOff[i].users;
							var displayUsers = "";
							for (var ii = 0; ii < users.length; ii++) {
								displayUsers += users[ii].first_name + " " + users[ii].last_name + ", ";
							}
							displayUsers = displayUsers.substring(0,displayUsers.length-2);
							
							var content = '<tr>';
							content += '<td style="background-color: #FFFFFF; padding-left: 4px;">' + currentDayOff[i].shiftName + '</td>';
							content += '<td style="background-color: #FFFFFF; padding-left: 4px;">' + displayUsers + '</td>';
							content += '<td style="background-color: #FFFFFF; padding-left: 4px;">' + getTimeFromDate(start) + ' - ' + getTimeFromDate(end) + '</td>';
							content += '</tr>';
							$('#tblCurrentDay > tbody').append(content);
						}
					}
				});	
			}
			
			function loadForecast() {
				// Latest time off requests
				$.post('ws/getForecast', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","number":"3"} , function(data) {
					if (data.message == "success") {
						var forecastObj = data.data;
						var content = "";
						for (var key in forecastObj) {
							var loop = 0;
							if ((key != undefined) || (key != "")) {
								content += '<td style="width: 33%; border: 1px solid #ECF1F8;">';
									content += '<div style="background-color: #ECF1F8; text-align: center; font-weight: bold;">' + key + '</div>';
									content += '<div style="padding-left: 5px;">';
									while (loop < forecastObj[key].length) {
										var shiftTime = forecastObj[key][loop].time;
										shiftTime = shiftTime.split(" ");
										shiftTime = shiftTime[1];
										shiftTime = shiftTime.split(":");
										if (shiftTime[0] == 00) {
											shiftTime = "12am";	
										} else if (shiftTime[0] > 12) {
											shiftTime = shiftTime[0] - 12 + "pm";	
										} else {
											shiftTime = shiftTime[0] + "am";	
										}
										
										var shiftName = forecastObj[key][loop].name;
										content += '<span style="font-weight: bold;">' + shiftName + ' - ' + shiftTime + '</span><br />';
										
										var shiftUsers = forecastObj[key][loop].users;
										if (shiftUsers != null) {
											shiftUsers = shiftUsers.split(",");
											
											for (var i = 0; i < shiftUsers.length; i++) {
												content += '&nbsp; &nbsp; ' + shiftUsers[i] + ' <br />';
											}
										} else {
											content += '<span style="color: #CC0000; font-weight: bold;">-- OPEN --</span> <br />';	
										}
										
										loop++;
									}
									content += '</div>';
								content += '</td>';
							}
						}
						$('#tblForecast > tbody').append(content);
						var tmpHeight = $('#tblForecast').height();
						$('.4u-first').css({ minHeight: tmpHeight });
					}
				});
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
			
			function highlightNavLink() {
				var view = getParameterByName("view");
				if (view != undefined) {
					if (view == "cw") {
						$('#currentlyWorking').addClass('highlightLink');	
					} else if (view == "u") {
						$('#providers').addClass('highlightLink');	
					} else if (view == "t") {
						$('#timeoffRequests').addClass('highlightLink');	
					} else if (view == "u") {
						$('#providers').addClass('highlightLink');	
					} else if (view == "a") {
						$('#alerts').addClass('highlightLink');	
					} else {
						$('#currentlyWorking').addClass('highlightLink');
					}
				}
			}
			
			function goTo(where) {
				if (where == "calendar") {
					window.location = "/YourCalendar/";	
				} else if (where == "reports") { 
					window.location = "/home?view=u";
				}
			}
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

		<!-- Header -->
			<div id="header-wrapper">
				<? include("html_includes/header.php"); ?>
                <div class="5grid-clear"></div>
			</div>

		<!-- Content -->
			<div id="content-wrapper">
				<div id="content">
                	<div class="5grid">
                    	<div id="alertsDisplay" style="width: 100%; text-align: center; margin-bottom: 10px; display: none;">
                        
                        </div>
                    	<? include("html_includes/loggedInAs.php"); ?>
                        <div class="3u-first">
                            <!-- Sidebar -->
                            <section>
                                <header>
                                    <h3>Navigate Your Dashboard</h3>
                                </header>
                                <ul class="link-list">
                                    <li id="currentlyWorking"><a href="/home?view=cw">Who's Currently Working</a></li>
                                    <!--<li id="tradedShifts"><a href="#">Latest Traded Shifts</a></li>-->
                                    <li id="timeoffRequests"><a href="/home?view=t">Latest Timeoff Requests</a></li>
                                    <li id="providers"><a href="/home?view=u">Providers and Staff</a></li>
                                    <li id="alerts"><a href="/home?view=a">Alerts and Notifications</a> <span id="alertNum" style="display: none; background-color: #CC0000; color: #FFF; border-radius: 2px; padding: 2px; font-weight: bold; font: 0.6em Verdana, sans-serif;"></span></li>
                                </ul>
                            </section>
                        </div>
						<div class="9u">
							<div class="4u-first" style="width: 100%;">
							<!-- Box #1 -->
								<section id="sectionContainer">
<div style="float: right;">
                                    	<span onClick="goTo('calendar')" style="cursor: pointer; background-color: #C6D7EC; padding: 5px; border-radius: 5px;"><img src="images/table.gif" /> View Monthly Schedule</span> &nbsp; 
                                        <span onClick="goTo('reports')" style="cursor: pointer; background-color: #C6D7EC; padding: 5px; border-radius: 5px;"><img src="images/list_unordered.gif" /> Monthly Scheduling Reports</span>
                                    </div>
                                
                                    <h2 id="dashboard">Who's Currently Working</h2>

                                    <table id="tblCurrentDay" style="width: 100%;">
                                    	<thead>
                                            <tr style="border-bottom: 1px solid #E6E6E6;">
                                                <td style="background-color: #F2F2F2; font-weight: bold; padding-left: 4px;">Shift Name</td>
                                                <td style="background-color: #F2F2F2; font-weight: bold; padding-left: 4px;">Providers</td>
                                                <td style="background-color: #F2F2F2; font-weight: bold; padding-left: 4px; width: 75px;">Times</td>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                    
                                    <br />
                                    
                                    <h2 id="dashboard">Three Day Schedule Forecast</h2>

									<table id="tblForecast" style="width: 100%; border: 1px solid #DAE4F1; border-top-left-radius: 5px; border-top-right-radius: 5px;">
                                    	<tbody>

                                        </tbody>
                                    </table>
									
									</div>							
								</section>
                                
						</div>
					</div>
				</div>
			</div>