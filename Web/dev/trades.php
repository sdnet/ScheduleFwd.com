<? require("php_includes/isLoggedIn.php"); ?>
<? require("php_includes/protectedPage.php"); ?>
<html>
	<head>
		<title>Schedule Forward :: Medical scheduling software made easy</title>
        <?php include("html_includes/adminMeta.php"); ?>
        <script language="javascript">	
		
			// Query for the users and translate the returned JSON into an array that DataTables can read
			$.ajaxSetup({async:false});
			
			// Get all trade requests
			$.post('ws/getTrades', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
				tradeObj = data.data;
			});
			
			// Get admin approval for traded shift config property
			var adminTradeApproval = false;
			$.post('ws/getConfigByKey', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","key":"tradeApproval"} , function(data) {
				configObj = data.data.tradeApproval;
				if (configObj != null) {
					if (configObj == "true") {
						adminTradeApproval = true;	
					}
				}
			});
				
			// Build pending requests array
			tradesArray = new Array();
			var inc = 0;
			for (var trade in tradeObj.Pending) {
				var id = tradeObj.Pending[trade].id;
				
				// Determine name to display in 'Trade With' column
				var target_user = "";
				
				<?php if ($role == "Admin") { ?>
					orig = tradeObj.Pending[trade].original_user.first_name + " " + tradeObj.Pending[trade].original_user.last_name + " (" + tradeObj.Pending[trade].original_shift.shiftName + " at " + tradeObj.Pending[trade].original_shift.start + ") ";
					if (tradeObj.Pending[trade].target_shift != null) {
						target = tradeObj.Pending[trade].target_user.first_name + " " + tradeObj.Pending[trade].target_user.last_name + " (" + tradeObj.Pending[trade].target_shift.shiftName + " at " + tradeObj.Pending[trade].target_shift.start + ") ";
					} else {
						target = "";	
					}
					tradeInfo = orig + " traded with <br />" + target;
					
					var approve = '<span style="cursor: pointer; width: 90%; margin: 0 auto; padding: 4px; background-color: #33CFFF; border: 1px solid #009CCC;" onclick="approveTrade(\'' + id + '\',\'Accepted\')">Approve</span> &nbsp; <span style="cursor: pointer; width: 90%; margin: 0 auto; padding: 4px; background-color: #FF531F; border: 1px solid #B82B00;" onclick="approveTrade(\'' + id + '\',\'Rejected\')">Reject</span>';
					
					var date = tradeObj.Pending[trade].date_created;
					var status = tradeObj.Pending[trade].status;
					tradesArray[inc] = new Array(date,tradeInfo,status,approve);
					inc++
					
				<? } else { ?>
					if (tradeObj.Pending[trade].original_user.user_name == "<?php echo $user; ?>") {
						target_user = tradeObj.Pending[trade].target_user.first_name + " " + tradeObj.Pending[trade].target_user.last_name;	
						var target_shift = (tradeObj.Pending[trade].target_shift != null ? "<span style=\"font-weight: bold;\">" + tradeObj.Pending[trade].target_shift.shiftName + "</span> at " + tradeObj.Pending[trade].target_shift.start : "No shift");
					} else {
						target_user = tradeObj.Pending[trade].original_user.first_name + " " + tradeObj.Pending[trade].original_user.last_name;
						var target_shift = (tradeObj.Pending[trade].original_shift != null ? "<span style=\"font-weight: bold;\">" + tradeObj.Pending[trade].original_shift.shiftName + "</span> at " + tradeObj.Pending[trade].original_shift.start : "No shift");
					}
					var approve = '<span style="cursor: pointer; width: 90%; margin: 0 auto; padding: 4px; background-color: #33CFFF; border: 1px solid #009CCC;" onclick="approveTrade(\'' + id + '\',\'Accepted\')">Approve</span> &nbsp; <span style="cursor: pointer; width: 90%; margin: 0 auto; padding: 4px; background-color: #FF531F; border: 1px solid #B82B00;" onclick="approveTrade(\'' + id + '\',\'Rejected\')">Reject</span>';
					if ((tradeObj.Pending[trade].original_user.user_name == "<?php echo $user; ?>") || (status == "Admin Approval")) {
						approve = '<span style="cursor: pointer; width: 90%; margin: 0 auto; padding: 4px; background-color: #CC9600; border: 1px solid #664B00;" onclick="cancelTrade(\'' + id + '\',\'Accepted\')">Cancel Request</span>';	
					}
					
					var date = tradeObj.Pending[trade].date_created;
					var status = tradeObj.Pending[trade].status;
					tradesArray[inc] = new Array(date,target_user,target_shift,status,approve);
					inc++
				<? } ?>
				
			}
			
			// Build pending requests array
			completedTradesArray = new Array();
			var inc = 0;
			for (var trade in tradeObj.Completed) {
				var id = tradeObj.Completed[trade].id;

				// Determine name to display in 'Trade With' column
				<?php if ($role == "Admin") { ?>
					orig = tradeObj.Completed[trade].target_user.first_name + " " + tradeObj.Completed[trade].target_user.last_name;
					target = tradeObj.Completed[trade].original_user.first_name + " " + tradeObj.Completed[trade].original_user.last_name;
					target_user = orig + " traded with " + target;
				<? } else { ?>
					if (tradeObj.Completed[trade].original_user.user_name == "<?php echo $user; ?>") {
						target_user = tradeObj.Completed[trade].target_user.first_name + " " + tradeObj.Completed[trade].target_user.last_name;	
					} else {
						target_user = tradeObj.Completed[trade].original_user.first_name + " " + tradeObj.Completed[trade].original_user.last_name;
					}
				<? } ?>

				var target_shift = (tradeObj.Completed[trade].target_shift != null ? "<span style=\"font-weight: bold;\">" + tradeObj.Completed[trade].target_shift.shiftName + "</span> at " + tradeObj.Completed[trade].target_shift.start : "No shift");
				var date = tradeObj.Completed[trade].date_created;
				var status = tradeObj.Completed[trade].status;
				if (status == "Accepted") {
					status = '<span style="color: #009CCC;">Approved</span>';
				} else if (status == "Cancelled") {
					status = '<span style="color: #CC0036;">Cancelled</span>';
				} else if (status == "Admin Approval") {
					status = '<span style="color: #006618;">Admin Approval</span>';
				} else {
					status = '<span style="color: #CC0036;">Rejected</span>';
				}
				completedTradesArray[inc] = new Array(date,target_user,target_shift,status);
				inc++
			}
				
			$(document).ready(function(){
				$('div[title]').qtip();
				$('img[title]').qtip();
				loadAccountInfo();
				loadStats();
				
				$('#tblUserGrid').dataTable({
					<?php if ($role == "Admin") { ?>
					"aoColumns": [
						null,
						null,
						{ "bSortable": false },
						{ "bSortable": false }
					],
					<?php } else { ?>
					"aoColumns": [
						null,
						null,
						null,
						{ "bSortable": false },
						{ "bSortable": false }
					],
					<?php } ?>
					/*"sDom": 'T<"clear">lfrtip',*/
					"bRetrieve": true,
					"bStateSave": true,
					"bProcessing": true,
					"iDisplayLength": -1,
					/*
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
					},*/
					"aaData": tradesArray
				});
				
				$('#tblUserMgmt2').dataTable({
					"aoColumns": [
						null,
						null,
						null,
						{ "bSortable": false }
					],
					/*"sDom": 'T<"clear">lfrtip',*/
					"bRetrieve": true,
					"bStateSave": true,
					"bProcessing": true,
					"iDisplayLength": -1,
					/*
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
					*/
					"aaData": completedTradesArray
				});

			function loadStats() {
				var statsObj;
				$.post('ws/getMyStats', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
						statsObj = data.data; 
				});
				
				var hours = "";
				var remaining = "";
				var traded = "";
				var totalshifts = "";
				var shifts = "";
				var total = "";
				
				if (statsObj != null) {
					hours = statsObj.hours;
					remaining = statsObj.remaining;
					traded = statsObj.traded;
					totalshifts = statsObj.totalshifts;
					shifts = statsObj.shifts;
					total = statsObj.total;
				} else {
					hours = "N/A";
					remaining = "N/A";
					traded = "N/A";
					totalshifts = "N/A";
					shifts = "N/A";
					total = "N/A";
				}
				
				$('#hours').html(hours);
				$('#remaining').html(remaining);
				$('#traded').html(traded);
				$('#totalshifts').html(totalshifts);
				$('#shifts').html(shifts);
				$('#yearlyhours').html(total);
			}
			
			function loadAccountInfo() {
				$.ajaxSetup({async:false});
				var userObj;
				$.post('ws/getUser', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":"<?=$_SESSION['_id']?>"} , function(data) {
						userObj = data.data[0];
				});
			
				$('#pflName').html(userObj.first_name + ' ' + userObj.last_name);
				$('#pflGroup').html(userObj.group);
				$('#pflPhone').html(userObj.phone);
				$('#pflEmail').html(userObj.email);
			}

			});
			
			function approveTrade(id,type) {
				var wordInConfirm = "reject";
				if (type == "Accepted") {
					wordInConfirm = "approve";
				}
				
				if (confirm("Are you sure you would like to " + wordInConfirm + " this trade?")) { 
					$.post('ws/acceptTrade', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","tradeId":id,"status":type} , function(data) {
						tradeObj = data;
						if (tradeObj.message == "success") {
								location.reload();
						}
					});
				}	
			}
			
			function cancelTrade(id) {
				if (confirm("Are you sure you would like to cancel this trade request?")) { 
					$.post('ws/deleteTrade', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","tradeId":id} , function(data) {
						tradeObj = data;
						if (tradeObj.message == "success") {
								location.reload();
						}
					});
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



		<div id="freeow" class="freeow freeow-top-right"></div>

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
						<div class="9u-first">

							<!-- Box #1 -->
								<section>
                                
									<h2 id="dashboard">Submitted Trade Requests</h2>
                                    
                                    <h3 style="font-size: 1.6em; margin-bottom: 20px;">These trade requests require your attention</h3>

                                    <div id="divTrade">
                                        <table id="tblUserGrid" class="display" style="width: 100%;">
                                            <thead>
                                            <tr>
                                            	<?php if ($role == "Admin") { ?>
                                                    <th style="width: 100px;">Date submitted</th>
                                                    <th>Trade Details</th>
                                                    <th style="width: 75px;">Status</th>
                                                    <th style="width: 100px;">Action</th>
                                                <?php } else { ?>
                                                    <th style="width: 100px;">Date submitted</th>
                                                    <th style="width: 130px;">Trading With</th>
                                                    <th>Your New Shift</th>
                                                    <th style="width: 75px;">Status</th>
                                                    <th style="width: 100px;">Action</th>
                                                <?php } ?>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <br /><br /><hr /><br />
                                    
                                    <h3 style="font-size: 1.6em; margin-bottom: 20px;">Historical trade requests</h3>

                                    <div id="divTrade2">
                                        <table id="tblUserMgmt2" class="display" style="width: 100%;">
                                            <thead>
                                            <tr>
                                                <th style="width: 100px;">Date submitted</th>
                                                <th style="width: 130px;">Trading With</th>
                                                <th>Your New Shift</th>
                                                <th style="width: 75px;">Status</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            
                                            </tbody>
                                        </table>
                                    </div>

								</section>
						</div>
                        
						<div class="3u">
							
							<!-- Sidebar -->
								<section style="line-height: 25px;">
									<header>
										<h2>Your account</h2>
									</header>
                                    
                                    <img src="http://forwardintel.com/images/tom.smallwood.jpg" style="float: left; padding-right: 5px; padding-bottom: 5px;" />
                                    
                                    <span id="pflName" style="font-weight: bold; font-size: larger;"></span> <br />
                                    <span id="pflGroup" style="font-size: smaller;"></span> <br />
                                    <span id="pflPhone" style="font-size: smaller;"></span> <br />
                                    <span id="pflEmail" style="font-size: smaller;"></span> <br />
                                    
                                    <div style="width: 100%; text-align: center;"><a href="/profile">Edit your profile</a></div>
								</section>
						</div>
                        
						<div class="3u">
							
							<!-- Sidebar -->
								<section style="line-height: 25px;">
									<header>
										<h2>Your statistics</h2>
                                        <div style="margin: 0 auto; font-style: italic;">Through today</div>
									</header>
                                    
                                    <table style="width: 100%;">
                                    	<tr>
                                        	<td>Hours worked:</td>
                                            <td style="width: 30px;"><div id="hours"></div></td>
                                        </tr>
                                    	<tr>
                                        	<td>Hours remaining:</td>
                                            <td style="width: 30px;"><div id="remaining"></div></td>
                                        </tr>
                                    	<tr>
                                        	<td>Traded shifts:</td>
                                            <td style="width: 30px;"<div id="traded"></div></td>
                                        </tr>
                                    	<tr>
                                        	<td>Total monthly shifts:</td>
                                            <td style="width: 30px;"<div id="totalshifts"></div></td>
                                        </tr>
                                    	<tr>
                                        	<td>Total yearly shifts:</td>
                                            <td style="width: 30px;"<div id="shifts"></div></td>
                                        </tr>
                                   	<tr>
                                        	<td>Total yearly hours:</td>
                                            <td style="width: 30px;"<div id="yearlyhours"></div></td>
                                        </tr>
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

	</body>
</html>