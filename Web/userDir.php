<? require("php_includes/isLoggedIn.php"); ?>
<? require("php_includes/protectedPage.php"); ?>
<html>
	<head>
		<title>Schedule Forward :: Medical scheduling software made easy</title>
        <?php include("html_includes/adminMeta.php"); ?>
        <script language="javascript">	
		
			// Query for the users and translate the returned JSON into an array that DataTables can read
			$.ajaxSetup({async:false});
			$.post('ws/getUsers', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
				userObj = data.data;
			});
				
			userArray = new Array();
			var inc = 0;
			for (var user in userObj) {
				var id = userObj[user]._id.$id;
				var firstname = userObj[user].first_name;
				var lastname = userObj[user].last_name;
				var email = userObj[user].email;
				var phone = userObj[user].phone;
				var group = userObj[user].group;
				userArray[inc] = new Array(lastname,firstname,email,phone,group);
				inc++
			}
				
			$(document).ready(function(){
				$('div[title]').qtip();
				$('img[title]').qtip();
				loadAccountInfo();
				loadStats();
				
				$('#tblUserGrid').dataTable({
					"aoColumns": [
						null,
						null,
						null,
						null,
						null
					],
					/*"sDom": 'T<"clear">lfrtip',*/
					"bRetrieve": true,
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
                                
									<h2 id="dashboard">User Directory</h2>

                                    <div id="divUser">
                                        <table id="tblUserGrid" class="display" style="width: 100%;">
                                            <thead>
                                            <tr>
                                                <th>Last Name</th>
                                                <th>First Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Group</th>
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