        <script language="javascript">			
			$(document).ready(function(){
				$('a[title]').qtip();
			});
			
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
			
            function toPDFSchedule() {
				var date = new Date();
				var d = date.getDate();
				m = date.getMonth();
				y = date.getFullYear();
				m++;
				if (m < 10) {
					m = "0" + m;	
				}

				var month = getParameterByName("month");
				var year = getParameterByName("year");
				
				var baseURL = "/toPDF/?type=mainschedule";
                window.location.href=baseURL + "&month=" + m + "&year=" + y + "&print=1";
            }
			
            function toPDFScheduleDay() {
				var date = new Date();
				var d = date.getDate();
				m = date.getMonth();
				y = date.getFullYear();
				m++;
				if (m < 10) {
					m = "0" + m;	
				}

				var baseURL = "/toPDF/?type=dayschedule";
                window.location.href=baseURL + "&day=" + d + "&month=" + m + "&year=" + y + "&print=1";
            }
			
            function toSchedule() {
                window.location.href="/YourCalendar/";
            }

		</script>
                                    
        <!-- CSS goes in the document HEAD or added to your external stylesheet -->
        <style type="text/css">
        table.hovertable {
			width: 95%;
            font-family: verdana,arial,sans-serif;
            font-size:11px;
            color:#333333;
            border-width: 1px;
            border-color: #A0B2C0;
            border-collapse: collapse;
        }
        table.hovertable th {
            background-color:#AFBECA;
            border-width: 1px;
            font-size: 1.8em;
			padding: 10px;
			font-weight: bold;
            border-style: solid;
            border-color: #a9c6c9;
        }
        table.hovertable tr {
            background-color:#ECF0F3;
        }
        table.hovertable td {
            border-width: 1px;
            padding: 8px;
            border-style: solid;
            border-color: #a9c6c9;
        }
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
                                    
                                    <h2 id="dashboard">Your Dashboard</h2>
                                   

                                    <!-- Table goes in the document BODY -->
                                    <table class="hovertable">
                                    <tr>
                                        <th>Your Printing Options</th>
                                    </tr>
                                    <tr onmouseover="this.style.backgroundColor='#DCE4E9';" onmouseout="this.style.backgroundColor='#ECF0F3';" title="Monthly downloadable PDF">
                                        <td onClick="toPDFSchedule()" style="cursor: pointer;">
                                        	<div style="font: 1.8em Tahoma, Geneva, sans-serif;">
                                            	<span style="font-weight: bold;">Monthly</span> -- 
                                            	Download a PDF of current month's schedule, all days, all shifts
                                            </div>
                                        </td>
                                    </tr>
                                    <tr onmouseover="this.style.backgroundColor='#DCE4E9';" onmouseout="this.style.backgroundColor='#ECF0F3';" title="Weekly downloadable PDF">
                                        <td onClick="toPDFScheduleWeek()" style="cursor: pointer;">
                                        	<div style="font: 1.8em Tahoma, Geneva, sans-serif;">
                                            	<span style="font-weight: bold;">Weekly</span> -- 
                                            	Download a PDF of current week's schedule, all shifts
                                            </div>
                                        </td>
                                    </tr>
                                    <tr onmouseover="this.style.backgroundColor='#DCE4E9';" onmouseout="this.style.backgroundColor='#ECF0F3';" title="Daily downloadable PDF">
                                        <td onClick="toPDFScheduleDay()" style="cursor: pointer;">
                                        	<div style="font: 1.8em Tahoma, Geneva, sans-serif;">
                                            	<span style="font-weight: bold;">Today</span> -- 
                                            	Download a PDF of today's schedule, all shifts
                                            </div>
                                        </td>
                                    </tr>
                                    <tr onmouseover="this.style.backgroundColor='#DCE4E9';" onmouseout="this.style.backgroundColor='#ECF0F3';" title="View online schedule">
                                        <td onClick="toSchedule()" style="cursor: pointer;">
                                        	<div style="font: 1.8em Tahoma, Geneva, sans-serif;">
                                            	<span style="font-weight: bold;">Schedule View</span> -- 
                                            	View online version of the current schedule
                                            </div>
                                        </td>
                                    </tr>
                                    </table>
												
								</section>
						</div>
					</div>
				</div>
			</div>