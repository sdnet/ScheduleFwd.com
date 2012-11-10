        <script language="javascript">			
			$(document).ready(function(){
				$('a[title]').qtip();
				loadHeatMap();
				getAlerts();
				highlightNavLink();
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
			
			function loadHeatMap() {
				$.post('ws/getTimeOffHeatMap', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
					if (data.message == "success") {
						heatObj = data.data;
						for (var i = 0; i < heatObj.length; i++) {
							var date = heatObj[i].date;
							date = date.split("-");
							date = date[2];
							
							var color = heatObj[i].color;
							var shiftName = heatObj[i].shiftName;
							
							if (shiftName != "") {
							
								var content = "<div style=\"font-weight: normal; border: 1px solid #FFF; background-color: " + color + "; width: 95%;\">&nbsp; " + shiftName + "</div>";
							
							}
							
							$('#'+date).append(content);	
						}
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
					}
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
                    	<? include("html_includes/loggedInAs.php"); ?>
                        <div class="3u-first">
                            <!-- Sidebar -->
                            <section>
                                <header>
                                    <h3>Navigate Your Dashboard</h3>
                                </header>
                                <ul class="link-list">
                                    <li id="currentlyWorking"><a href="/home?view=cw">Who's Currently Working</a></li>
                                    <li id="tradedShifts"><a href="#">Latest Traded Shifts</a></li>
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

									<div id="legend" style="float: right; text-align: right;">
                                    	Legend: <span style="background-color: #003366; color: #FFF; padding: 4px;"> Most time off requests </span> &nbsp; <span style="background-color: #4DA6FF; color: #FFF; padding: 4px;"> Least time off requests </span> &nbsp; <span style="background-color: #FFFFFF; color: #000; padding: 4px;"> No requests </span>
                                    </div>

                                    <h2 id="dashboard">Timeoff Requests Heat Map</h2>
                                    
                              <?php 
							 //This gets today's date 
							 $date = time();
							 $date = strtotime('+2 months',$date);  
							
							 //This puts the day, month, and year in seperate variables 
							 $day = date('d', $date) ; 
							 $month = date('m', $date) ; 
							 $year = date('Y', $date) ;

							 //Here we generate the first day of the month 
							 $first_day = mktime(0,0,0,$month, 1, $year) ; 

							 //This gets us the month name 
							 $title = date('F', $first_day) ; 
                            
                            //Here we find out what day of the week the first day of the month falls on 
							 $day_of_week = date('D', $first_day) ; 
							
							
							
							 //Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero
							
							 switch($day_of_week){ 
							
							 case "Sun": $blank = 0; break; 
							
							 case "Mon": $blank = 1; break; 
							
							 case "Tue": $blank = 2; break; 
							
							 case "Wed": $blank = 3; break; 
							
							 case "Thu": $blank = 4; break; 
							
							 case "Fri": $blank = 5; break; 
							
							 case "Sat": $blank = 6; break; 
							
							 }
							
							
							
							 //We then determine how many days are in the current month
							
							 $days_in_month = cal_days_in_month(0, $month, $year) ; 
														
                            //Here we start building the table heads 

							echo '<div id="calendarDisplay" style="text-align: center; width: 100%; margin: 0 auto;">';
							echo '<h3>' . $title . " " . $year . '</h3>';
							echo "<table border=1 width=100% style=\"border: 1px solid #9C9C9C;\">";
							 	echo "<tr><td style=\"background-color: #D9D9D9; text-align: center;\" width=42>S</td><td style=\"background-color: #D9D9D9; text-align: center;\" width=42>M</td><td style=\"background-color: #D9D9D9; text-align: center;\" width=42>T</td><td style=\"background-color: #D9D9D9; text-align: center;\" width=42>W</td><td style=\"background-color: #D9D9D9; text-align: center;\" width=42>T</td><td style=\"background-color: #D9D9D9; text-align: center;\" width=42>F</td><td style=\"background-color: #D9D9D9; text-align: center;\" width=42>S</td></tr>";
							 	
								//This counts the days in the week, up to 7
								$day_count = 1;
								echo "<tr>";
							
							 	//first we take care of those blank days
								while ( $blank > 0 ) { 
							 		echo "<td></td>"; 
									$blank = $blank-1; 
									$day_count++;
 								}                             
                            
								//sets the first day of the month to 1 
								$day_num = 1;

								//count up the days, untill we've done all of them in the month
								while ( $day_num <= $days_in_month ) {
									if (strlen($day_num) == 1) { $day_num = "0" . $day_num; } 
									echo "<td style=\"background-color: #FFFFFF; color: #000000; font-weight: bold; text-align: center;\"> $day_num <div id=\"$day_num\" style=\"text-align: left;\"></div></td>"; 
							 		$day_num++; 
							 		$day_count++;
									
									//Make sure we start a new row every week
									if ($day_count > 7) {
							 			echo "</tr><tr>";
										$day_count = 1;
									}
							 	} 
								
								//Finaly we finish out the table with some blank details if needed
							 	while ( $day_count >1 && $day_count <=7 ) { 
									echo "<td> </td>"; 
									$day_count++; 
								} 
								echo "</tr></table>"; 
							echo '</div>';
                            ?>
								</section>
                                
						</div>
					</div>
				</div>
			</div>