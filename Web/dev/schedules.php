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
								<section>
									<? include("html_includes/adminHeader.php"); ?>
									
									<br />
									
                                    <h2 id="dashboard">Schedule Management</h2>				
									
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