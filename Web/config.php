<? require("php_includes/isLoggedIn.php"); ?>
<? require("php_includes/protectedPage.php"); ?>
<html>
	<head>
		<title>Schedule Forward :: Medical scheduling software made easy</title>
		<?php include("html_includes/adminMeta.php"); ?>

        <script language="javascript">
            function toUserMgmt() {
                window.location.href="/userMgmt";
            }

            function toSchedMgmt() {
                window.location.href="/schedules";
            }
			
            function toPrefMgmt() {
                alert("Redirect user to User Preference page");
            }
			
            function toSupport() {
                window.location.href="/support";
            }
			
			$(document).ready(function(){
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
						<div style="float: left;"><a href="/home">View Your Dashboard</a></div>						
                        <div id="loggedInAs" />Logged in as: <a href="/profile" title="Edit your profile"><?=$user?></a> &nbsp; | &nbsp; <a href="/logout">Logout</a></div>
						<div class="4u-first" style="width: 100%;">

							<!-- Box #1 -->
								<section style="height: 100%;">
                                    <? include("html_includes/adminHeader.php"); ?>
									
									<br />						
									
									<table style="width: 100%;">
                                        <tr>
                                            <td style="width: 50%; text-align: center;">
                                                <div id="btnUserMgmt" class="homeMainButton" onclick="toUserMgmt();">
                                                    <h2>User Management</h2>
                                                    <h3>Create, Edit and Delete System Users</h3>
                                                </div> <br />
                                            </td>
                                            <td style="width: 50%; text-align: center;">
                                                <div id="btnSchedMgmt" class="homeMainButton" onclick="toSchedMgmt();">
                                                    <h2>Schedule Management</h2>
                                                    <h3>Generate and View Monthly Schedules</h3>
                                                </div> <br />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50%; text-align: center;">
                                                <div id="btnUserMgmt" class="homeMainButton" onclick="toPrefMgmt();">
                                                    <h2>User Request Approvals</h2>
                                                    <h3>View Preference and Time Off Requests</h3>
                                                </div>
                                            </td>
                                            <td style="width: 50%; text-align: center;">
                                                <div id="btnSchedMgmt" class="homeMainButton" onclick="toSupport();">
                                                    <h2>Support</h2>
                                                    <h3>Create and Get Application Support</h3>
                                                </div>
                                            </td>
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