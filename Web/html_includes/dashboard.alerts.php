        <script language="javascript">	
			$(document).ready(function(){
				loadAlerts();
				highlightNavLink();
				$('a[title]').qtip();
			});
			
			function markAlertAsRead(id) {
				$.post('ws/deleteAlerts', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","alertId":id} , function(data) {});
				$('.'+id).remove();	
			}
			
			function loadAlerts() {
				inc = 0;
				var alertsArray = new Array();
				$.ajaxSetup({async:false});
				$.post('ws/getAlerts', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
					if (data.message == "success") {
						alertsObj = data.data.alerts;
						if (alertsObj != null) {
							for(var i = 0; i < alertsObj.length; i++) {
								var id = alertsObj[i]._id.$id;
								var severity = alertsObj[i].severity;
								var message = alertsObj[i].message;
								
								var content = '';
								if (severity == "Error") {
									content = '<div id="eError" class="' + id + '" onclick="markAlertAsRead(\'' + id + '\')">';
								} else if (severity == "Alert") {
									content = '<div id="eNotification" class="' + id + '" onclick="markAlertAsRead(\'' + id + '\')">';
								} else {
									content = '<div id="eInfo" class="' + id + '" onclick="markAlertAsRead(\'' + id + '\')">';
								}
									content += '<div style="float: right; margin-top: 6px;"><img src="/images/cancel.png" /></div> &nbsp;';
									content += message;
                                content += '</div>';
								
								var currentHTML = $('#alertContainer').html();
								$('#alertContainer').html(currentHTML + content);
							}
						}
					}
				});
				return alertsArray;
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

                                    <h2 id="dashboard">Alerts and Notifications</h2>
                                    
                                    <div id="alertContainer">
                                    
                                    </div>
                                    
                                    <!-- 
 									<table id="tblAlerts" class="display" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th style="width: 80px;">Severity</th>
                                            <th>Message</th>
                                            <th style="width: 30px;">Read</th>
                                        </tr>
                                        </thead>
										<tbody>

										</tbody>
                                    </table>
                                    -->	
 
								</section>
                                
						</div>
					</div>
				</div>
			</div>