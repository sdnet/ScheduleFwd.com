		<script>
			$(document).ready(function(){
				$('a[title]').qtip();
				refreshDatePickerBoxes();
				loadApprovalTable('Pending');
			});
			
			function loadApprovalTable(status) {
					// Query for the users and translate the returned JSON into an array that DataTables can read
					$.ajaxSetup({async:false});
					$.post('ws/getTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","status":status} , function(data) {
						timeoffObj = data.data;
					});
						
					timeoffArray = new Array();
					var inc = 0;
					for (var timeoff in timeoffObj) {
						var id = timeoffObj[timeoff]._id.$id;
						var date_created = timeoffObj[timeoff].date_created.sec;
						date_created = ISODateString(date_created);
						var firstname = timeoffObj[timeoff].first_name;
						var lastname = timeoffObj[timeoff].last_name;
						var username = timeoffObj[timeoff].user_name;
						var status = "<span id='" + id + "'>" + timeoffObj[timeoff].status + "</span>";
						var timeOff = timeoffObj[timeoff].time_off;
						var displayTimeOff = "";
						if (timeOff.length == 1) {
							displayTimeOff = timeOff[0];
						} else {
							displayTimeOff = timeOff[0] + " through " + timeOff[timeOff.length - 1];
						}					
						var action = '<span style="cursor: pointer; background-color: #99CC66; border: 1px solid #999; padding: 1px;" onclick="processAction(\'approve\',\'' + id + '\')"> Approve </span> &nbsp; ';
						action = action + '<span style="cursor: pointer; background-color: #CC6666; border: 1px solid #999; padding: 1px;" onclick="processAction(\'reject\',\'' + id + '\')"> Disapprove </span>';
						
						timeoffArray[inc] = new Array(date_created,username,displayTimeOff,status,action);
						inc++;
					}
					
					var oTable = $('#tblUserMgmt').dataTable({
						"aoColumns": [
							null,
							null,
							null,
							null,
							{ "bSortable": false },
						],
						"bStateSave": true,
						"bRetrieve": true,
						"bProcessing": true,
						"aaData": timeoffArray
					});
					
			}
			
			function filterBy(type) {
				var oTable = $('#tblUserMgmt').dataTable();
				
				oTable.fnDestroy();
				
				// Query for the users and translate the returned JSON into an array that DataTables can read
				$.ajaxSetup({async:false});
				$.post('ws/getTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","status":type} , function(data) {
					timeoffObj = data.data;
				});
				
					timeoffArray = new Array();
					var inc = 0;
					for (var timeoff in timeoffObj) {
						var id = timeoffObj[timeoff]._id.$id;
						var date_created = timeoffObj[timeoff].date_created.sec;
						date_created = ISODateString(date_created);
						var firstname = timeoffObj[timeoff].first_name;
						var lastname = timeoffObj[timeoff].last_name;
						var username = timeoffObj[timeoff].user_name;
						var status = "<span id='" + id + "'>" + timeoffObj[timeoff].status + "</span>";
						var timeOff = timeoffObj[timeoff].time_off;
						var displayTimeOff = "";
						if (timeOff.length == 1) {
							displayTimeOff = timeOff[0];
						} else {
							displayTimeOff = timeOff[0] + " through " + timeOff[timeOff.length - 1];
						}
						if (type == "Pending") {					
						var action = '<span style="cursor: pointer; background-color: #99CC66; border: 1px solid #999; padding: 1px;" onclick="processAction(\'approve\',\'' + id + '\')"> Approve </span> &nbsp; ';
						action = action + '<span style="cursor: pointer; background-color: #CC6666; border: 1px solid #999; padding: 1px;" onclick="processAction(\'reject\',\'' + id + '\')"> Disapprove </span>';
						} else {
							action = '';	
						}
						
						timeoffArray[inc] = new Array(date_created,username,displayTimeOff,status,action);
						inc++;
					}
					
					var oTable = $('#tblUserMgmt').dataTable({
						"aoColumns": [
							null,
							null,
							null,
							null,
							{ "bSortable": false },
						],
						"bStateSave": true,
						"bRetrieve": true,
						"bProcessing": true,
						"aaData": timeoffArray
					});
			}
			
			function ISODateString(secs) {
 				var t = new Date(1970,0,1);
				t.setSeconds(secs);
				var date = t.getDate();
				var month = t.getMonth() + 1;
				var year = t.getFullYear();
				t = month + "/" + date + "/" + year;
				return t;
			}
			
			function processAction(type,id) {
				var stopImage = "<img src=\"images/stop.png\" alt=\"Error\" />";
				var goImage = "<img src=\"images/accept.png\" alt=\"Success\" />";
				
				if (type == "approve") {
					$.post('ws/approveTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","status":"Approved","timeOffId":id} , function(data) {
						if (data.message == "success") {
							$('#' + id).html('Approved');
							$('#profileSuccess').html(goImage + ' Time off request successfully approved!');
							$('#profileSuccess').fadeIn();
							setInterval(function(){$('#profileSuccess').fadeOut();},2000);
						}
					})		
				} else if (type == "reject") {
					$.post('ws/approveTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","status":"Disapproved","timeOffId":id} , function(data) {
						if (data.message == "success") {
							$('#' + id).html('Disapproved');
							$('#profileSuccess').html(goImage + ' Time off request successfully disapproved!');
							$('#profileSuccess').fadeIn();
							setInterval(function(){$('#profileSuccess').fadeOut();},2000);
						}
					})		
				}
			}
			
			function refreshDatePickerBoxes() {
				$('.singleDate').each(function(){
    				$(this).datepicker({
						defaultDate: "+1w",
						changeMonth: true,
						numberOfMonths: 1,
					});
				});
				$( ".multiStartDate" ).datepicker({
					defaultDate: "+1w",
					changeMonth: true,
					numberOfMonths: 2,
					onSelect: function( selectedDate ) {
						// $( ".multiEndDate" ).datepicker( "option", "minDate", selectedDate );
					}
				});
				$( ".multiEndDate" ).datepicker({
					defaultDate: "+1w",
					changeMonth: true,
					numberOfMonths: 2,
					onSelect: function( selectedDate ) {
						// $( ".multiStartDate" ).datepicker( "option", "maxDate", selectedDate );
					}
				});
			}
			
			function toggleViews(view) {
				if (view == "single") {
					$('#timeoffMulti').hide();
					$('#timeoffPrevious').hide();
					$('#timeoffSingle').show(300);	
				} else if (view == "multi") {
					$('#timeoffSingle').hide();
					$('#timeoffPrevious').hide();
					$('#timeoffMulti').show(300);						
				} else if (view == "previous") {
					$('#timeoffSingle').hide();
					$('#timeoffMulti').hide();
					getPreviousTimeOffRequests();
					$('#timeoffPrevious').show(300);						
				}
			}
			
			function addRowSingle() {
				var rowCount = $('#tblSingle tbody tr').length;
				var count = rowCount + 1;
				$('#tblSingle > tbody:last').append('<tr style="border-bottom: 1px solid #CCC; background-color:#FFF;"><td style="text-align:center"><span style="padding: 3px; border-bottom: 1px dotted #666;">#' + count +'</span></td><td><input type="text" name="singleDate" class="singleDate" /></td></tr>');
				refreshDatePickerBoxes();	
			}
			
			function addRowMulti() {
				var rowCount = $('#tblMulti tbody tr').length;
				var count = rowCount + 1;
				$('#tblMulti > tbody:last').append('<tr style="border-bottom: 1px solid #CCC; background-color:#FFF;"><td style="text-align:center"><span style="padding: 3px; border-bottom: 1px dotted #666;">#' + count +'</span></td><td>From: <input type="text" name="multiStartDate" class="multiStartDate" style="width: 125px;" /> &nbsp; To: <input type="text" name="multiEndDate" class="multiEndDate" style="width: 125px;" /></td></tr>');
				refreshDatePickerBoxes();	
			}

			function getPreviousTimeOffRequests() {
				
				// First, clear out the table
				$('.previousReqRow').each(function(){
					$(this).remove();
				});
				
				// Second, call the data and populate the table
				$.post('ws/getTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":"<?=$_SESSION['_id']?>"} , function(data) {
					if (data.message == "success") {
						var reqObj = data.data;
						for (var i = 0; i<reqObj.length; i++) {
							var date_created = new Date(reqObj[i].date_created.sec);
							date_created = ISODateString(date_created);
							var timeOff = reqObj[i].time_off;
							var status = reqObj[i].status;
							var displayTimeOff = "";
							var bgColor = "#FFFFFF";
							if (timeOff.length == 1) {
								displayTimeOff = timeOff[0];
							} else {
								displayTimeOff = timeOff[0] + " through " + timeOff[timeOff.length - 1];
							}
							if (status == "Approved") {
								bgColor = "#99CC99";
							} else if (status == "Disapproved") {
								bgColor = "#D58181";	
							}
							$('#tblPrevious > tbody').append('<tr class="previousReqRow" style="background-color: ' + bgColor + ';"><td style="padding-left: 10px;">' + status +'</td><td>' + displayTimeOff +'</td><td>' + date_created + '</td></tr>');
						}
					}
				});	
			}

			function submitSingle() {
				var response = "success";
				var stopImage = "<img src=\"images/stop.png\" alt=\"Error\" />";
				var goImage = "<img src=\"images/accept.png\" alt=\"Success\" />";
				
				$('.singleDate').each(function(){
					var dateValue = $(this).val();
					$.ajaxSetup({async:false});
					if ((dateValue != undefined) && (dateValue != "")) {
						$.post('ws/addTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":"<?=$_SESSION['_id']?>","timeoff":dateValue} , function(data) {
								if (data.message != "success") {
									response = data.message;	
								}
						});
					}
				});
				
				if (response == "success") {
					$('#profileSuccess').html(goImage + ' Your time off request(s) have been successfully submitted!');
					$('#profileSuccess').fadeIn();
					setInterval(function(){$('#profileSuccess').fadeOut();},2000);		
				} else {
					$('#profileError').html(stopImage + ' There was a problem submitting your time off requests.  Please contact your shift administrator.');
				}
			}

			function submitMulti() {
				var response = "success";
				var stopImage = "<img src=\"images/stop.png\" alt=\"Error\" />";
				var goImage = "<img src=\"images/accept.png\" alt=\"Success\" />";
				
				$('.multiStartDate').each(function(){
					var startDateValue = $(this).val();
					var dateList = "";
					
					// Find the value of the cooresponding end input
					var inp = $('input');
					var index = inp.index(this);
					var next = inp[index+1];
					var endDateValue = $(next).val();
					
					// Find the dates in-between the two start and end values
					startTime = Date.parse(startDateValue);
					endTime = Date.parse(endDateValue);

					for (var loopTime = startTime; loopTime <= endTime; loopTime += 86400000) {
						var loopDay=new Date(loopTime);
						var month = (loopDay.getMonth() + 1).toString();
						if (month.length == 1) { month = "0" + month; }
						var date = (loopDay.getDate()).toString();
						if (date.length == 1) { date = "0" + date; }
						
						// Hip hip, horray, we finaly have a properly formatted date!
						strLoopDay = month + "/" + date + "/" + loopDay.getFullYear();
						dateList += strLoopDay + ",";
					}
					
				// Insert the dates into the database as time off requests for this user
				$.ajaxSetup({async:false});
				dateList = dateList.substring(0,dateList.length-1);
				if ((dateList != undefined) && (dateList != "")) {
					$.post('ws/addTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":"<?=$_SESSION['_id']?>","timeoff":dateList} , function(data) {
						if (data.message != "success") {
							response = data.message;	
						}
					});
				}
					
				});
				
				if (response == "success") {
					$('#profileSuccess').html(goImage + ' Your time off request(s) have been successfully submitted!');
					$('#profileSuccess').fadeIn();
					setInterval(function(){$('#profileSuccess').fadeOut();},2000);		
				} else {
					$('#profileError').html(stopImage + ' There was a problem submitting your time off requests.  Please contact your shift administrator.');
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
									<? include("html_includes/adminHeader.php"); ?>
									
									<br />
									
                                    <h2 id="dashboard">Time Off Requests</h2>

									<? if ($_SESSION['role'] == "User") { ?>

 									<div id="profileError" style="width: 100%; text-align: center; color: #CC0000;"></div>
									<div id="profileSuccess"></div> 

									<div id="newUser" style="text-align: center; background-color: #E6E6E6; padding: 20px; border-radius: 4px; border: 1px solid #D9D9D9;">
                                   
                                    	Use the form below to request a day off.  The administator will approve all requests before they take effect. <br />
										<table cellspacing="10" style="width: 60%; margin: 0 auto;">
											<tr>
												<td style="width: 33%; text-align:center;"><img src="images/single-day-request.gif" style="cursor: pointer;" title="Single Day Request" onClick="toggleViews('single')" /></td>
												<td style="width: 33%; text-align:center"><img src="images/multi-day-request.gif" style="cursor: pointer;" title="Multi Day Request" onClick="toggleViews('multi')" /></td>
                                                <td style="width: 33%; text-align:center"><img src="images/previous-requests.gif" style="cursor: pointer;" title="Previous Requests" onClick="toggleViews('previous')" /></td>
											</tr>
                                        </table>
                                        
                                        <br />
                                        
                                        <div id="timeoffSingle" style="display: none;">
                                        	<table id="tblSingle" style="margin: 0 auto; border: 1px solid #999;">
                                            	<thead>
                                                	<th style="width: 100px; background-color: #666; color: #FFF;">Request #</th>
                                                    <th style="background-color: #666; color: #FFF;">Date of Request</th>
                                                </thead>
                                                <tbody>
                                                    <tr style="border-bottom: 1px solid #CCC; background-color:#FFF;">
                                                        <td style="text-align:center"><span style="padding: 3px; border-bottom: 1px dotted #666;">#1</span></td>
                                                        <td><input type="text" name="singleDate" class="singleDate" /></td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                	<tr style="background-color:#F2F2F2;">
                                                    	<td colspan="2" style="text-align:right; padding-right: 7px;"><a href="#" onClick="addRowSingle()">Add another date</a></td>
                                                    </tr>
                                                </tfoot>
                                            </table> <br />
                                            
                                            <input type="button" name="frmSubmit" id="frmSubmit" value="Submit Single Day Request" onClick="submitSingle()" />
                                        </div>
                                        
                                        <div id="timeoffMulti" style="display: none;">
                                         	<table id="tblMulti" style="margin: 0 auto; border: 1px solid #999;">
                                            	<thead>
                                                	<th style="width: 100px; background-color: #666; color: #FFF;">Request #</th>
                                                    <th style="background-color: #666; color: #FFF;">Date Range of Request</th>
                                                </thead>
                                                <tbody>
                                                    <tr style="border-bottom: 1px solid #CCC; background-color:#FFF;">
                                                        <td style="text-align:center"><span style="padding: 3px; border-bottom: 1px dotted #666;">#1</span></td>
                                                        <td>From: <input type="text" name="multiStartDate" class="multiStartDate" style="width: 125px;" /> &nbsp; 
                                            				To: <input type="text" name="multiEndDate" class="multiEndDate" style="width: 125px;" /></td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                	<tr style="background-color:#F2F2F2;">
                                                    	<td colspan="2" style="text-align:right; padding-right: 7px;"><a href="#" onClick="addRowMulti()">Add another date</a></td>
                                                    </tr>
                                                </tfoot>
                                            </table> <br />

                                            <input type="button" name="frmSubmit" id="frmSubmit" value="Submit Multi-Day Request" onClick="submitMulti()" />
                                        </div>
                                        
                                        <div id="timeoffPrevious" style="display: none;">
                                        	<table id="tblPrevious" style="margin: 0 auto; border: 1px solid #999;">
                                            	<thead>
                                                	<th style="width: 100px; background-color: #666; color: #FFF;">Status</th>
                                                    <th style="width: 250px; background-color: #666; color: #FFF;">Request Date</th>
                                                    <th style="width: 100px; background-color: #666; color: #FFF;">Date Submitted</th>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                        
									</div>	
                                    
                                    <? } else { ?>
                                    
 									<div id="profileError" style="width: 100%; text-align: center; color: #CC0000;"></div>
									<div id="profileSuccess"></div> 
                                    
                                    <div style="width: 270px; padding: 5px; margin: 0 auto; text-align: center; background-color: #333; color: #FFF;">
                                    	Filter by: <span style="cursor: pointer; border-bottom: 1px dotted #CCC;" onClick="filterBy('Pending');">Pending</span> | 
                                        <span style="cursor: pointer; border-bottom: 1px dotted #CCC;" onClick="filterBy('Approved');">Approved</span> | 
                                        <span style="cursor: pointer; border-bottom: 1px dotted #CCC;" onClick="filterBy('Disapproved');">Disapproved</span>
                                    </div>
                                    
                                    <table id="tblUserMgmt" class="display" style="width: 100%;">
                                    	<thead>
                                        	<th style="width: 120px;">Date Submitted</th>
                                            <th>Username</th>
                                            <th>Date(s)</th>
                                            <th style="width: 100px;">Status</th>
                                            <th style="width: 150px;">Action</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                    
                                    <? } ?>			
									
								</section>
						</div>
					</div>
				</div>
			</div>
