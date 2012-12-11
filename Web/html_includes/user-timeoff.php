		<script>
			$(document).ready(function(){
				$('a[title]').qtip();
				loadCalendar();
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
			
			function selectShift(id,inDate,priority) {
				isSelected = document.getElementById(id).style.textDecoration;
				isColor = document.getElementById(id).style.color;
				var passedInUser = getParameterByName("user");
				$("#priority_"+id).html("");

				if (priority == undefined) {
					if (((isSelected != "line-through") && (isColor == "rgb(71, 79, 81)")) || ((isSelected == "") && (isColor == ""))) {
						document.getElementById(id).style.textDecoration = "line-through";
						document.getElementById(id).style.fontWeight = "bold";
						document.getElementById(id).style.color = "#474f51";
						$("#priority_"+id).html("<img src=\"/images/action_delete.png\" />");
						id = id.split("_");
						id = id[1];
						if (passedInUser != "") {
							$.post('ws/addTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":passedInUser,"shiftId":id,"date":inDate} , function(data) {
								timeoffObj = data.data;
							});
						} else {
							$.post('ws/addTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","shiftId":id,"date":inDate} , function(data) {
								timeoffObj = data.data;
							});
						}
					} else if (isSelected == "line-through") {
						document.getElementById(id).style.textDecoration = "";
						document.getElementById(id).style.color = "green";
						document.getElementById(id).style.fontWeight = "bold";
						$("#priority_"+id).html("");
						id = id.split("_");
						id = id[1];
						if (passedInUser != "") {
							$.post('ws/addTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","mustwork":true,"id":passedInUser,"shiftId":id,"date":inDate} , function(data) {
								timeoffObj = data.data;
							}); 
						} else {
							$.post('ws/addTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","mustwork":true,"shiftId":id,"date":inDate} , function(data) {
								timeoffObj = data.data;
							}); 
						}
					} else if (isColor == "green") {
						document.getElementById(id).style.textDecoration = "none";
						document.getElementById(id).style.color = "#474f51";
						document.getElementById(id).style.fontWeight = "normal";
						$("#priority_"+id).html("");
						id = id.split("_");
						id = id[1];
						if (passedInUser != "") {
							$.post('ws/deleteTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":passedInUser,"shiftId":id,"date":inDate} , function(data) {
								timeoffObj = data.data;
							}); 
						} else {
							$.post('ws/deleteTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","shiftId":id,"date":inDate} , function(data) {
								timeoffObj = data.data;
							}); 
						}
					} else {
						document.getElementById(id).style.textDecoration = "line-through";
						document.getElementById(id).style.fontWeight = "bold";
						document.getElementById(id).style.color = "#474f51";
						id = id.split("_");
						id = id[1];
						if (passedInUser != "") {
							$.post('ws/addTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":passedInUser,"shiftId":id,"date":inDate} , function(data) {
								timeoffObj = data.data;
							});
						} else {
							$.post('ws/addTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","shiftId":id,"date":inDate} , function(data) {
								timeoffObj = data.data;
							});
						}
					}
				} else {
					// This is in response to a change in priority (must have vs. nice to have)
					if (isColor == "rgb(71, 79, 81)") {
						document.getElementById(id).style.color = "red";
						$("#priority_"+id).html("<img src=\"/images/action_check.png\" />");
					} else {
						document.getElementById(id).style.color = "#474f51";
						$("#priority_"+id).html("<img src=\"/images/action_delete.png\" />");
					}
				}
			}
			
			function selectDaysShifts(e) {
				<?php
					// Make another date instance to determine whether or not the viewable month is changable
					$tmpDate = date("Y-m-d");
					$advanceDate2 = strtotime(date("Y-m-d", strtotime($tmpDate)) . " +2 month");
					$month2 = date("m",$advanceDate2);
					$year2 = date("Y",$advanceDate2);					
				?>
				<?php if ((!isset($_GET['month']) && !isset($_GET['year'])) || ($_GET['month'] == $month2 && $_GET['year'] == $year2)) { ?>
				
				$('#'+e).children().each(function () {
					if ($(this).attr("class") == "shifts") {
						selectShift($(this).attr("id"),$(this).attr("shiftDate"));
					}
				});
				
				<?php } ?>
			}
			
			function changeDates() {
				var passedInUser = getParameterByName("user");
				var passedInName = getParameterByName("name");
				var toDate = $('#selChangeDates').val();
				var toDate = new Date(toDate);
				var year = toDate.getFullYear();
				var theDate = Date("Y-n-d",toDate);
				var month = toDate.getMonth()+2;
				if (month > 12) {
					month = month - 12;
					year++;
				}
				var userVar = "";
				var redirectTo = "timeoff";
				if (passedInUser != "") {
					userVar = passedInUser;
					redirectTo = "editUserTimeoffs";
				}
				window.location = "/" + redirectTo +"?user=" + userVar + "&month=" + month + "&year=" + year + "&name=" + passedInName;	
			}
			
			function togglePriority(id, inDate) {
				var passedInUser = getParameterByName("user");
				var content = $("#priority_"+id).html();
				var inDate = $.trim(inDate);
				var priorityId = id;
				var compId = id;
				id = id.split("_");
				id = id[1]; 
				if (content.indexOf("check") > -1) {
					$("#priority_"+priorityId).html("<img src=\"/images/action_delete.png\" />");
					$.post('ws/addTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":passedInUser,"shiftId":id,"date":inDate,"priority":"0","update":"true"} , function(data) {
						timeoffObj = data.data;
					});
				} else {
					$("#priority_"+priorityId).html("<img src=\"/images/action_check.png\" />");
					$.post('ws/addTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":passedInUser,"shiftId":id,"date":inDate,"priority":"1","update":"true"} , function(data) {
						timeoffObj = data.data;
					});
				}
				
				selectShift(compId,inDate,true);
			}
			
			function loadCalendar() {
				
				// Get user for the id passed in through the querystring
				if (getParameterByName("user") != "") {
					var userId = getParameterByName("user");
					$.post('ws/getUser', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":userId} , function(data) {
						userObj = data.data[0];
						$('#editingFor').html(userObj.first_name + " " + userObj.last_name + " (" + userObj.user_name + ") ");
					});
				}
				
				var timeoffObj = "";
				var d = new Date();
				var month;
				var year;
				
				<?php if (isset($_GET['month']) && isset($_GET['year'])) { 
					$month = $_GET['month'];
					$year = $_GET['year'];
				?>
					month = "<?php $_GET['month']; ?>"
					year = "<?php $_GET['year']; ?>"
				<?php } else { 
					$date = date("Y-m-d");// current date
					$advanceDate = strtotime(date("Y-m-d", strtotime($date)) . " +2 month");
					$month = Date("m",$advanceDate);
					$year = Date("Y",$advanceDate);
				?>
					d.setMonth(d.getMonth() + 2);
					month = d.getMonth();
					year = d.getFullYear();	
				<?php } ?>
				
				<?
					// Count months between launch month and current month
					$d1 = Date("2012-06-01");
					$date = date("Y-m-d");
					$d2 = strtotime ( '+2 month' , strtotime ($date ));
					$d2 = Date('Y-m-d',$d2);
					$diff = abs(strtotime($d2) - strtotime($d1));
					$years = floor($diff / (365*60*60*24));
					$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
				?>
				
				prevMonthContent = "<select id='selChangeDates' onChange='changeDates()'>";
					<?php
						if (isset($_GET['month']) && isset($_GET['year'])) {
							$month = $_GET['month'];
							if (strlen($month) == 1) { $month = "0" . $month; }
							$year = $_GET['year'];
							$passedInDate = Date("$year-$month-01");
						} else {
							$passedInDate = "";	
						}
						$d2 = strtotime ( '+2 month' , strtotime ($date ));
						$date = Date('Y-m-d',$d2);
						
						for ($i = 0; $i < $months; $i++) {
							$d2 = strtotime ( "-$i month" , strtotime ( $date ) );
							//$tmpDate = Date('Y-m-01',$d2);
							//$d2 = strtotime ( "+1 month" , strtotime ( $tmpDate ) );
							$d2Formatted = Date('Y-m-01',$d2);
							$tmpMonth = Date('F', $d2);
							$tmpYear = Date('Y', $d2);
							$displayName = $tmpMonth . " " . $tmpYear;
							if ($passedInDate == $d2Formatted) {
								
								echo "prevMonthContent += \"<option value='$d2Formatted' selected>$displayName</option>\"; \n"; 
							} else {
								echo "prevMonthContent += \"<option value='$d2Formatted'>$displayName</option>\"; \n";
							}
						}
					?>	
				prevMonthContent += "</select>";
				
				$('#spnPrevMonth').html(prevMonthContent);
				var passedInUser = getParameterByName("user");
				
				$.ajaxSetup({async:false});
				if (passedInUser != "") {
					$.post('ws/getTimeOffSchedule', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":passedInUser,"month":<?=$month;?>,"year":<?=$year;?>} , function(data) {
							timeoffObj = data.data;
					});
				} else {
					$.post('ws/getTimeOffSchedule', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","month":<?=$month;?>,"year":<?=$year;?>} , function(data) {
							timeoffObj = data.data;
					});

				}
				
				if (timeoffObj != null) {
					for (var i = 0; i < timeoffObj.length; i++) {
						var mustwork = "";
						
						var startDate = timeoffObj[i].date;
						var status = timeoffObj[i].status;
						var priority = timeoffObj[i].priority;
						mustwork = timeoffObj[i].mustwork;
						var isRequested = timeoffObj[i].timeoff;
						var textdecoration = "";
						var fontweight = "";
						var days = startDate.split("-");
						var color = "";
						
						if (status == "-1") {
							color = "#FFB3B3";
						} else if (status == "1") {
							color = "#B3FFD9";	
						} else {
							color = "#E5F3FF";	
						}

						day = days[2];
						var textcolor = "";
						if (isRequested == 1) { 
							textdecoration = "line-through"; fontweight = "bold"; 
							
							if (priority == "1") {
								priority = "<img src=\"/images/action_check.png\" />";	
								textcolor = "red"; 
							} else {
								priority = "<img src=\"/images/action_delete.png\" />";
								textcolor = "#474f51";
							}
							
							if (mustwork == "true") {
								textcolor = "green";	
							}
							
						} else {
							priority = "";	
						}

						if (mustwork == "true") {
							priority = "";	
							textdecoration = "none";
							fontweight = "bold";
						}
						<?php
							// Make another date instance to determine whether or not the viewable month is changable
							$tmpDate = date("Y-m-d");
							$advanceDate2 = strtotime(date("Y-m-d", strtotime($tmpDate)) . " +2 month");
							$month2 = date("m",$advanceDate2);
							$year2 = date("Y",$advanceDate2);					
						?>
						<?php if ((((!isset($_GET['month']) && !isset($_GET['year'])) || ($_GET['month'] == $month2 && $_GET['year'] == $year2))) || $_SESSION['role'] == 'Admin') { ?>
							var content = '<span id=\'' + timeoffObj[i].id + '_' + timeoffObj[i].shiftId + '\' shiftDate=\"' + startDate + '\" style=\"text-decoration: ' + textdecoration + '; font-weight: ' + fontweight + '\; color: ' + textcolor + '\" class=\"shifts\" onClick=\"selectShift(\'' + timeoffObj[i].id + '_' + timeoffObj[i].shiftId + '\',\'' + startDate + '\')\">' + timeoffObj[i].shiftName + '</span> <span onclick="togglePriority(\'' + timeoffObj[i].id + '_' + timeoffObj[i].shiftId + '\',\'' + startDate + '\')" id="priority_' + timeoffObj[i].id + '_' + timeoffObj[i].shiftId + '">' + priority + '</span> <br />';
						<? } else { ?>
						var content = '<span class=\"shifts\" id=\'' + timeoffObj[i].id + '_' + timeoffObj[i].shiftId + '\' shiftDate=\"' + startDate + '\" style=\"text-decoration: ' + textdecoration + '; font-weight: ' + fontweight + '; background-color: ' + color + '">' + timeoffObj[i].shiftName + '</span> <span id="priority_' + timeoffObj[i].id + '_' + timeoffObj[i].shiftId + '">' + priority + '</span> <br />';
						<? } ?>
						$('#'+day).append(content);	
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


                           <section>
                           <div style="float: right;">
                           		<span style="font-weight: bold;">Icon legend</span> <br />
                               <img src="/images/action_delete.png" /> = Nice to have <br />
                               <img src="/images/action_check.png" /> = Must have off
                           </div>
                           
                           <h2 id="dashboard">Schedule Requests</h2>
                           <?php 
                               if($_SESSION['role'] == 'Admin') {      
                           ?>
                           
                           <h3 style="text-align: center;">Editing user preferences for: <span id="editingFor"></span></h3>
                                                                       
						   <?php
                               } else {
                           ?>
                               To request time off, please select from the shifts below, or choose a day to select all shifts.
                           <?
							   }
						   ?>
                                	
                                    <br /><br />
                                    <div style="background-color: #F2F2F2; padding: 5px; border-top-left-radius: 6px; border-top-right-radius: 6px;">
                                	<span id="spnPrevMonth"></span>
                                    &nbsp; &nbsp; &nbsp; 
                                        <span style="font-weight: bold;">
                                            Instructions: &nbsp; 
                                        </span>
                                        
                                        Click <span style="font-weight: bold;">once</span> to request shift off.  Click <span style="font-weight: bold;">twice</span> to mark as 'must work'.  Click<span style="font-weight: bold;"> third time</span> to clear request.
                                    </div>
                                
									<?php 
                                     //This gets today's date 
                                     $date = time() ; 
                                    
                                     //This puts the day, month, and year in seperate variables                                    
                                     $today = time();
									 $offset = 2;
                                     // $year = date('Y',strtotime("+$offset months", $today));
                                     // $month =  date('n',strtotime("+$offset months", $today));
                                     $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                                    
                                    // Here we generate the first day of the month (starting on Monday, so we subtract a day)
                                    $first_day = mktime(0,0,0,$month, 1, $year)-1;
									
									// When on the 1st, the month will read the 31st of the previous month due to our offset
									// subtraction above; keep a non-offset date available for month display
									$first_day_without_offset = mktime(0,0,0,$month, 1, $year);
                                    
                                    // This gets us the month name
                                    $title = date('F', $first_day_without_offset);
                                     
                                     //Here we find out what day of the week the first day of the month falls on 
                                     $day_of_week = date('D', $first_day) ; 

                                    // Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero
                                     switch($day_of_week) { 
                                         case "Mon": $blank = 1; break; 
                                         case "Tue": $blank = 2; break; 
                                         case "Wed": $blank = 3; break; 
                                         case "Thu": $blank = 4; break; 
                                         case "Fri": $blank = 5; break; 	
                                         case "Sat": $blank = 6; break; 
                                         case "Sun": $blank = 7; break; 
                                     }

                                     //We then determine how many days are in the current month
                                     $days_in_month = cal_days_in_month(0, $month, $year) ;
                                     
                                      //Here we start building the table heads 
                                     echo "<table id=\"tblShiftTrade\">";
                                     echo "<tr><th colspan=7> $title $year </th></tr>";
                                     echo "<tr id=\"tblShiftTradeDays\"><td width=42>Mon</td><td width=42>Tues</td><td 
                                    		width=42>Wed</td><td width=42>Thurs</td><td width=42>Fri</td><td 
                                    		width=42>Sat</td><td width=42>Sun</td></tr>";

                                     //This counts the days in the week, up to 
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

                                     //count up the days, until we've done all of them in the month
                                     while ( $day_num <= $days_in_month ) {
										 if (strlen($day_num) == 1) { $day_num = "0" . $day_num; } 
										echo "<td style=\"padding: 5px; border: 1px solid #F2F2F2;\"> <div id=\"\" style=\"width:90%; cursor: pointer; margin: 0 auto; background-color: #F2F2F2; padding-right: 10px; text-align: right; font-size: smaller;\" onClick=\"selectDaysShifts('$day_num')\">$day_num</div> 
												<div id=\"$day_num\"></div>
											</td>"; 
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
                                     
                                     ?>
								</section>
						</div>
					</div>
				</div>
			</div>
