        <script language="javascript">			
			$(document).ready(function(){
				$('a[title]').qtip();
				loadDropdowns();
				loadCharts();
				getAlerts();
				highlightNavLink();
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
			
			function loadDropdowns() {
				<?
					// Count months between launch month and current month
					$d1 = Date("2012-08-01");
					$date = date("Y-m-d");
					$d2 = strtotime ( '+0 month' , strtotime ($date ));
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
						$d2 = strtotime ( '+0 month' , strtotime ($date ));
						$date = Date('Y-m-d',$d2);
						
						for ($i = 0; $i < $months; $i++) {
							$d2 = strtotime ( "-$i month" , strtotime ( $date ) );
							//$tmpDate = Date('Y-m-01',$d2);
							//$d2 = strtotime ( "+1 month" , strtotime ( $tmpDate ) );
							$d2Formatted = Date('Y-m-01',$d2);
							$tmpMonth = Date('F', $d2);
							$tmpMonth2 = Date('m', $d2);
							$tmpYear = Date('Y', $d2);
							$displayName = $tmpMonth . " " . $tmpYear;
							if ($passedInDate == $d2Formatted) {
								
								echo "prevMonthContent += \"<option value='$tmpMonth2,$tmpYear' selected>$displayName</option>\"; \n"; 
							} else {
								echo "prevMonthContent += \"<option value='$tmpMonth2,$tmpYear'>$displayName</option>\"; \n";
							}
						}
					?>	
				prevMonthContent += "</select>";
				$('#spnPrevMonth').html(prevMonthContent);
			}
			
			function processDataTable(data,dataKeys) {
				var dataArray = new Array();
				for (var i = 0; i < data.length; i++) {
					var tempArray = new Array();
					$.each(data[i], function(key, value) {
					   tempArray.push(value);
					});
					dataArray.push(tempArray);
				}
				return dataArray;
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

			function buildDataTableColumns(data) {
				retColJson = new Array();
				for (var i = 0; i < data.length; i++) {
					retColJson.push({ "sTitle" : data[i] });
				}

				return retColJson;
			}
			
			function changeDates() {
				loadCharts();	
			}
			
			function switchType() {
				loadCharts();	
			}
			
			function loadCharts() {
				var period = $('#selChangeDates').val();
				var type = $('#type').val();
				var period = period.split(",");
				var month = period[0];
				var year = period[1];
				
				$.ajaxSetup({async:false});
				$.post("/ws/getReport", {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","type":type,"month":month,"year":year},
                function (data) {
                    if (data.message == "Error") {
                       alert("There was an error getting this report. Please contact admin");
                    }else{
						var dataHolder = data.data.data;
						var dataDisplayKeys = data.data.keys;
						var dataKeys = data.data.dataKeys;
						var chartHolder = "";
						var chartType = "";
						
						if (oTable != undefined) {
							oTable.fnDestroy();
						}
						
						if (data.data.pie != null) {
							chartHolder = data.data.pie;
							chartType = "pie";	
						} else {
							chartHolder = data.data.bar;
							chartType = "bar";	
						}
						
						$('#reportTable').hide();
						$('#reportTable').empty();
						$('#reportTable').html('<table id="tblReport" class="display" style="width: 100%;"><thead></thead><tbody></tbody></table>');
						
						var dataArray = processDataTable(dataHolder,dataKeys);
						var dataColsArray = buildDataTableColumns(dataDisplayKeys);
						
						var oTable = $('#tblReport').dataTable({
							"aoColumns": dataColsArray,
							"sDom": 'T<"clear">lfrtip',
							"iDisplayLength": -1,
							"bStateSave": true,
							"bProcessing": true,
							"oTableTools": {
								"aButtons": [
									{
										"sExtends": "csv",
										"sButtonText": "CSV",
										"mColumns": [ 0, 1] 
									},
									{
										"sExtends": "xls",
										"sButtonText": "Excel",
										"mColumns": [ 0, 1 ]
									},
									{
										"sExtends": "pdf",
										"sButtonText": "PDF",
										"mColumns": [ 0, 1 ]
									},
								]
							},
							"aaData": dataArray
						});

					    $('#reportTable').show();
						if (chartType == 'bar') {
							$('#reportChart').html('<div id="chart1" style="width:100%; height:450px;"></div>');
							createChart(chartHolder);
						}
						if (chartType == 'pie') {
					    	$('#reportChart').html('<div id="chart1" style="width:100%; height:450px;"></div>');
							createPie(chartHolder);
						}
					}
                });
			}
			
			function createChart(data){
				var type = $('#type :selected').text();
				$.jqplot.config.enablePlugins = true;
				// var obj = jQuery.parseJSON(data);
				var s1 = eval(data.numbers);
				var ticks = eval(data.ticks);
			
				plot1 = $.jqplot('chart1', [s1], {
					title: type,
					seriesDefaults:{
						renderer:$.jqplot.BarRenderer,
						pointLabels: { show: true }
					},
					axesDefaults: {
						tickRenderer: $.jqplot.CanvasAxisTickRenderer,
						tickOptions: {
						  angle: -30,
						  fontSize: '10pt'
						}
					},
					axes: {
						xaxis: {
							renderer: $.jqplot.CategoryAxisRenderer,
							ticks: ticks
						}
					},
					highlighter: { show: true }
			   });
	    	}
			
			function createPie(data){
				var type = $('#type :selected').text();
				var s1 = eval(data);
				var plot8 = $.jqplot('chart1', [s1], {
					title: type,
					grid: {
						drawBorder: true, 
						drawGridlines: false,
						shadow:true
					},
					axesDefaults: {
				
					},
					seriesDefaults:{
						renderer:$.jqplot.PieRenderer,
						rendererOptions: {
							showDataLabels: true
						}
					},
					legend: {
						show: true,
						location: 'e'
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
                                    	Reporting Period: 
                                        <span id="spnPrevMonth"></span>
                                    </div>
				
                                    <h2 id="dashboard">Providers and Staff</h2>
                                    
                                    Select Report Type: 
                                    <select name="type" id="type" onChange="switchType()">
                                        <option value="usersByGroup"># of users by group</option>
                                        <option value="hoursByUser">Total hours by users</option>
                                        <option value="shiftByUser">Total shifts by user</option>
                                        <option value="externalProviderHours">External provider hours</option>
                                    </select>
                                    
                                    <div id="reportContainer" style="width: 100%;">
                                        
                                        <div id="reportChart">
                                        </div>
                                        
                                    	<div id="reportTable">
                                        	
                                        </div>
                                    
                                    </div>
                                    
                                    <!--
                                    
                                    <table style="width: 100%;">
                                    	<tr>
                                        	<td style="padding: 6px;">
                                                <div class="statBox" style="padding: 5px; background-color: #F2F2F2; border: 1px solid #E6E6E6; border-radius: 3px; width: 100%;">
                                                    <div style="background-color: #F2F2F2; text-align: center; font-weight: bold;">Stat title here</div>
                                                    <div style="background-color: #FFFFFF; padding: 5px;">Stat value</div>
                                                </div>
                                            </td>
                                        	<td style="padding: 6px;">
                                                <div class="statBox" style="padding: 5px; background-color: #F2F2F2; border: 1px solid #E6E6E6; border-radius: 3px; width: 100%;">
                                                    <div style="background-color: #F2F2F2; text-align: center; font-weight: bold;">Stat title here</div>
                                                    <div style="background-color: #FFFFFF; padding: 5px;">Stat value</div>
                                                </div>
                                            </td>
                                        	<td style="padding: 6px;">
                                                <div class="statBox" style="padding: 5px; background-color: #F2F2F2; border: 1px solid #E6E6E6; border-radius: 3px; width: 100%;">
                                                    <div style="background-color: #F2F2F2; text-align: center; font-weight: bold;">Stat title here</div>
                                                    <div style="background-color: #FFFFFF; padding: 5px;">Stat value</div>
                                                </div>
                                            </td>
                                        	<td style="padding: 6px;">
                                                <div class="statBox" style="padding: 5px; background-color: #F2F2F2; border: 1px solid #E6E6E6; border-radius: 3px; width: 100%;">
                                                    <div style="background-color: #F2F2F2; text-align: center; font-weight: bold;">Stat title here</div>
                                                    <div style="background-color: #FFFFFF; padding: 5px;">Stat value</div>
                                                </div>
                                            </td>
                                        </tr>
                                    	<tr>
                                        	<td style="padding: 6px;">
                                                <div class="statBox" style="padding: 5px; background-color: #F2F2F2; border: 1px solid #E6E6E6; border-radius: 3px; width: 100%;">
                                                    <div style="background-color: #F2F2F2; text-align: center; font-weight: bold;">Stat title here</div>
                                                    <div style="background-color: #FFFFFF; padding: 5px;">Stat value</div>
                                                </div>
                                            </td>
                                        	<td style="padding: 6px;">
                                                <div class="statBox" style="padding: 5px; background-color: #F2F2F2; border: 1px solid #E6E6E6; border-radius: 3px; width: 100%;">
                                                    <div style="background-color: #F2F2F2; text-align: center; font-weight: bold;">Stat title here</div>
                                                    <div style="background-color: #FFFFFF; padding: 5px;">Stat value</div>
                                                </div>
                                            </td>
                                        	<td style="padding: 6px;">
                                                <div class="statBox" style="padding: 5px; background-color: #F2F2F2; border: 1px solid #E6E6E6; border-radius: 3px; width: 100%;">
                                                    <div style="background-color: #F2F2F2; text-align: center; font-weight: bold;">Stat title here</div>
                                                    <div style="background-color: #FFFFFF; padding: 5px;">Stat value</div>
                                                </div>
                                            </td>
                                        	<td style="padding: 6px;">
                                                <div class="statBox" style="padding: 5px; background-color: #F2F2F2; border: 1px solid #E6E6E6; border-radius: 3px; width: 100%;">
                                                    <div style="background-color: #F2F2F2; text-align: center; font-weight: bold;">Stat title here</div>
                                                    <div style="background-color: #FFFFFF; padding: 5px;">Stat value</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    
                                    -->

								</section>
                                
						</div>
					</div>
				</div>
			</div>