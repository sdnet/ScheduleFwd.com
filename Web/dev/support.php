<? require("php_includes/isLoggedIn.php"); ?>
<? require("php_includes/protectedPage.php"); ?>
<html>
	<head>
		<title>Schedule Forward :: Medical scheduling software made easy</title>
		<?php include("html_includes/adminMeta.php"); ?>

        <script language="javascript">
            function toUserMgmt() {
                window.location.href="userMgmt";
            }

            function toSchedMgmt() {
                alert("Redirect user to Schedule Management page");
            }
			
			function sendSupport() {
				alert("Send support to be implemented later");
			}
        </script>
		
		<script>
			$(function() {
				$( "#tabs" ).tabs();
			});
			
			$(document).ready(function(){

				$('#tblTicketsOpen').dataTable({
					"aoColumns": [
						null,
						null,
					],"aoColumnDefs": [
					  { "sWidth": "50%", "aTargets": [ 0 ] }
					],
					"bStateSave": true,
				});
				
				$('#tblTicketsClosed').dataTable({
					"aoColumns": [
						null,
						null,
					],"aoColumnDefs": [
					  { "sWidth": "50%", "aTargets": [ 0 ] }
					],
					"bStateSave": true,
				});
				
				$('a[title]').qtip();
				
			});
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

                                    <h2 id="dashboard">Contact Support</h2>
									<div class="demo">
										<div id="tabs">
											<ul>
												<li><a href="#tabs-1" style="color: #000000;" style="font-size: smaller;">New Support Request</a></li>
												<li><a href="#tabs-2" style="color: #000000;" style="font-size: smaller;">Open Support Requests</a></li>
												<li><a href="#tabs-3" style="color: #000000;" style="font-size: smaller;">Archived Support Requests</a></li>
											</ul>
											<div id="tabs-1">
												<div id="supportContainer">
										
													<p>Please use the following form to request support or ask a question regarding Schedule Forward.</p>
										
													<table id="tblSupport">
														<tr>
															<td class="left">Subject: </td>
															<td>
																<select name="ddlSubject" id="ddlSubject">
																	<option value="Support">Technical Support (Problem)</option>
																	<option value="Feature">Feature Request</option>
																	<option value="Question">General Question</option>
																</select>
															</td>
														</tr>
														<tr>
															<td class="left" valign="top">Message: </td>
															<td><textarea name="txtBody" id="txtBody"></textarea></td>
														</tr>
														<tr>
															<td class="left">Your Email: </td>
															<td><input type="text" name="txtEmail" id="txtEmail" value="<?=$email ?>" /></td>
														</tr>
														<tr>
															<td colspan="2" style="text-align: center;"><input type="button" name="btnContactSubmit" id="btnContactSubmit" value="Submit Message" onClick="sendSupport()" /></td>
														</tr>
													</table>
										
												</div>
											</div>
											<div id="tabs-2">
												<table id="tblTicketsOpen" class="display" style="width: 100%;">
													<thead>
														<tr>
															<th>Date</th>
															<th>Subject of ticket</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>25Aug, 2012</td>
															<td>Here is another example of a support ticket</td>
														</tr>
														<tr>
															<td>24Aug, 2012</td>
															<td>This is my subject for the ticket</td>
														</tr>
														<tr>
															<td>23Aug, 2012</td>
															<td>This is another cool subject for the ticket</td>
														</tr>
														<tr>
															<td>22Aug, 2012</td>
															<td>I am having trouble logging into the system</td>
														</tr>
														<tr>
															<td>22Aug, 2012</td>
															<td>How can I create a new schedule for every weekend day?</td>
														</tr>
													</tbody>
												</table> <br />
											</div>
											<div id="tabs-3">
												<table id="tblTicketsClosed" class="display" style="width: 100%;">
													<thead>
														<tr>
															<th>Date</th>
															<th>Subject of ticket</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>24Aug, 2012</td>
															<td>This is my subject for the ticket</td>
														</tr>
														<tr>
															<td>23Aug, 2012</td>
															<td>This is another cool subject for the ticket</td>
														</tr>
														<tr>
															<td>22Aug, 2012</td>
															<td>I am having trouble logging into the system</td>
														</tr>
														<tr>
															<td>20Aug, 2012</td>
															<td>Weird problem after logging into the system</td>
														</tr>
													</tbody>
												</table> <br />
											</div>											
											
											
														
											<!--
												<div id="supportContainer">
													<div class="ticket">
														<div class="tblTicketsMisc">
															Date Submitted: August 26th, 2012
														</div>
														<div class="tblTicketsSubject">
															This is the subject of a sample ticket
														</div>
														<div class="tblTicketsBody">
															Sample sample sample Sample sample sample Sample sample sample Sample sample sample Sample sample sample Sample sample sample 
															Sample sample sample Sample sample sample Sample sample sample Sample sample sample Sample sample sample Sample sample sample 
															Sample sample sample Sample sample sample Sample sample sample Sample sample sample Sample sample sample Sample sample sample  
														</div>
														<div class="tblTicketsResponse">
															<div class="tblTicketsResponseMisc">
																<div style="float: right;">August 26th, 2012 at 12:32pm</div>
																Submitted by Kathy Miles
															</div>
															<div class="tblTicketsResponseBody">
																Response to Sample sample sample Sample sample sample Sample sample sample Sample
															</div>
														</div> <br />
														<div class="tblTicketsResponse">
															<div class="tblTicketsResponseMisc">
																<div style="float: right;">August 26th, 2012 at 12:30pm</div>
																Submitted by Support
															</div>
															<div class="tblTicketsResponseBody">
																Sample sample sample Sample sample sample Sample sample sample Sample
															</div>
														</div>
													</div>
													<div class="ticket">
														<div class="tblTicketsMisc">
															Date Submitted: August 25th, 2012
														</div>
														<div class="tblTicketsSubject">
															This is the subject of a sample ticket
														</div>
														<div class="tblticketsBody">
															Sample sample sample Sample sample sample Sample sample sample Sample sample sample Sample sample sample Sample sample sample 
															Sample sample sample Sample sample sample Sample sample sample Sample sample sample Sample sample sample Sample sample sample 
															Sample sample sample Sample sample sample Sample sample sample Sample sample sample Sample sample sample Sample sample sample  
														</div>
													</div>
												</div>
												-->
										</div>
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