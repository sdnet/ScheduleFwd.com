<link rel="stylesheet" href="/js/tabs/dhtmlx_custom.css" />
<script src="/js/tabs/codebase/dhtmlxcommon.js" type="text/javascript"></script>
<script src="/js/tabs/codebase/dhtmlxtabbar.js" type="text/javascript"></script>

<script>

tabbar = new dhtmlXTabBar("a_tabbar", "top");
tabbar.setSkin('dhx_skyblue');
tabbar.setImagePath("/js/tabs/codebase/imgs");
tabbar.addTab("a1", "Tab 1-1", "100px");
tabbar.addTab("a2", "Tab 1-2", "100px");
tabbar.addTab("a3", "Tab 1-3", "100px");
tabbar.setContent("a1", "html_1");
tabbar.setContent("a2", "html_2");
tabbar.setContentHTML("a3", "<br/>The content can be set as <b>HTML</b> node or as <b>HTML</b> text.");
tabbar.setTabActive("a1");

</script>

        <script language="javascript">			
			$(document).ready(function(){
				$('a[title]').qtip();
				loadWidgets();
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
			
			function loadWidgets() {
				// Latest time off requests
				$.post('ws/getTimeOff', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","status":"Pending","limit":"5"} , function(data) {
					if (data.message == "success") {
						var reqObj = data.data;
						for (var i = 0; i<reqObj.length; i++) {
							var date_created = new Date(reqObj[i].date_created.sec);
							var timeOff = reqObj[i].time_off;
							date_created = ISODateString(date_created);
							if (timeOff.length == 1) {
								displayTimeOff = timeOff;
							} else {
								dates = JSON.stringify(timeOff).toString().split(":");
								displayTimeOff = dates[0] + " through " + timeOff[timeOff.length - 1];
							}
							$('#latestDayOff > tbody').append('<tr><td>' + date_created +'</td><td>' + reqObj[i].user_name +'</td><td>' + displayTimeOff +'</td></tr>');
						}
					} else if (data.message == "noRecords") {
						$('#latestDayOff > tbody').append('<tr><td colspan="3">No pending day off requests</td></tr>');	
					}
				});	
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
						<div class="4u-first" style="width: 100%;">

							<!-- Box #1 -->
								<section style="height: 100%;">
                                    <h2 id="dashboard">This is your Schedule Forward administrative dashboard</h2>
									
									<div style="background-color: #F2F2F2; border-radius: 5px;">
									
									<div class="column">

										<div class="portlet">
											<div class="portlet-heading">Who is currently working?</div>
											<div class="portlet-content">
												<table id="currentlyWorking" class="dashboardTable">
													<thead>
														<tr>
															<th>User</th>
															<th style="width: 120px;">Shift</th>
															<th style="width: 120px;">Hours</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>Jim Jones</td>
															<td>Early shift</td>
															<td>12am - 12pm</td>
														</tr>
														<tr>
															<td>James Flanagan</td>
															<td>Mid shift</td>
															<td>10am - 10pm</td>
														</tr>
														<tr>
															<td>Tim Hayworth</td>
															<td>Mid shift 2</td>
															<td>11am - 11pm</td>
														</tr>
														<tr>
															<td>Sam Smith</td>
															<td>Mid shift 2</td>
															<td>11am - 11pm</td>
														</tr>
													</tbody>
												</table>
												<div style="width: 97%; text-align: right; padding: 5px;"><a href="#" style="font-size: 0.8em;">View today's schedule</a></div>
											</div>
                                            
                                            <div class="portlet-heading">Your Three Day Forecast</div>
                                            
                                            
                                            
										</div>
	
										<div class="portlet">
											<div class="portlet-heading">Latest traded shifts</div>
											<div class="portlet-content">
												<table id="latestTradedShifts" class="dashboardTable">
													<thead>
														<tr>
															<th style="width: 90px;">Date</th>
															<th>From</th>
															<th>To</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>2012/09/10</td>
															<td>James Flanagan</td>
															<td>George Corliss</td>
														</tr>
														<tr>
															<td>2012/09/02</td>
															<td>Tim Hayworth</td>
															<td>Alex Rivera</td>
														</tr>
														<tr>
															<td>2012/08/30</td>
															<td>Alex Ebadirad</td>
															<td>Andy Walker</td>
														</tr>
														<tr>
															<td>2012/08/29</td>
															<td>John Thomas</td>
															<td>Keven Lindroos</td>
														</tr>
													</tbody>
												</table>
												<div style="width: 97%; text-align: right; padding: 5px;"><a href="#" style="font-size: 0.8em;">View traded shift history</a></div>
											</div>
										</div>

									</div>

									<div class="column">

										<div class="portlet">
											<div class="portlet-heading">Latest inbox messages</div>
											<div class="portlet-content">
												<table id="latestInbox" class="dashboardTable">
													<thead>
														<tr>
															<th style="width: 90px;">Date</th>
															<th>From</th>
															<th>Excerpt</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>2012/09/10</td>
															<td>James Flanagan</td>
															<td>Hey there I am wondering...</td>
														</tr>
														<tr>
															<td>2012/09/02</td>
															<td>Tim Hayworth</td>
															<td>Thanks for switching my shift...</td>
														</tr>
														<tr>
															<td>2012/08/30</td>
															<td>George Corliss</td>
															<td>Don't think I can work Saturday...</td>
														</tr>
														<tr>
															<td>2012/08/29</td>
															<td>John Thomas</td>
															<td>Do you have time for lunch at...</td>
														</tr>
													</tbody>
												</table>
												<div style="width: 97%; text-align: right; padding: 5px;"><a href="#" style="font-size: 0.8em;">View your inbox</a></div>
											</div>
										</div>
	
										<div class="portlet">
											<div class="portlet-heading">5 Latest pending day off requests</div>
											<div class="portlet-content">
												<table id="latestDayOff" class="dashboardTable">
													<thead>
														<tr>
															<th style="width: 90px;">Date</th>
															<th>User</th>
															<th>For Date</th>
														</tr>
													</thead>
													<tbody>
													</tbody>
												</table>
												<div style="width: 97%; text-align: right; padding: 5px;"><a href="/timeoff" style="font-size: 0.8em;">View all day off requests</a></div>
											</div>
										</div>

									</div>
									
									</div>								
								</section>
                                
						</div>
					</div>
				</div>
			</div>