<? session_start(); ?>
<?
    if ($_SESSION['userName']) {
        header("Location: home");
    }
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Schedule Forward :: Medical scheduling software made easy</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<!--5grid--><script src="css/5grid/viewport.js"></script><!--[if lt IE 9]><script src="css/5grid/ie.js"></script><![endif]--><link rel="stylesheet" href="css/5grid/responsive.css" /><!--/5grid-->
		<link rel="stylesheet" href="css/style.css" />
		<!--[if lte IE 9]><link rel="stylesheet" href="css/style-ie9.css" /><![endif]-->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>

        <script language="javascript">
            function processLogin() {
                var username = document.getElementById('username').value;
                var password = document.getElementById('password').value;
                var hospital = $("#hospital").val();
                $.post("ws/LoginUser", { submit: "true", username: username, password: password, grpcode: hospital },
                    function (data) {
                        if (data.message == "success") {
                            displayAlert('alert', 'Login Successful, please wait...');
                            window.location.href = "/home";
                        } else if (data.message == "authFailure") {
                            displayAlert('alert', 'Email/Password combination was invalid.');
                        } else if (data.message == "notActive") {
                            displayNotActive();
                        } else {
                            displayAlert('alert', 'Please fill out both fields');
                        }
                });
                }

                function displayAlert(id, text) {
                    $('#' + id + '').html('' + text + '');
                    $('#' + id + '').show('show');
                }
				
				$(document).ready(function() {
				  $.get("ws/getGroupCodes",
                    function (data) {
						var select = '<select name="hospital" id="hospital">';
						var temparray = data["data"];
						for(i=0; i<temparray.length; i++) {
							select = select + '<option value="' + temparray[i]['groupcode'] + '">' + temparray[i]['name'] + '</option>';
						}
						select = select + "</select>";
						$('#hospital-select').html(""); //clear old options
						$('#hospital-select').html(select);
					});
				});
        </script>
	</head>
	<body>

		<!-- Header -->
			<div id="header-wrapper">
				<? include("html_includes/header.php"); ?>
                <div id="banner">
					<div class="5grid 5grid-alt">
						<div class="6u-first" style="width: 100%; text-align: center;">
						
							<!-- Banner Copy -->
								<p style="color: #FFF; font-weight: bold;">We make scheduling software quick, easy and painless.</p>

						</div>
					</div>
				</div>
                <div class="5grid-clear"></div>
			</div>

		<!-- Content -->
			<div id="content-wrapper">

                <div id="nologin">
                    <? if ($_GET['nologin'] == "1") {
                        echo '<img src="images/stop.png" /> Please login before attempting to access a protected page';
                    } ?>
                    <? if ($_GET['logout'] == "1") {
                    	echo '<img src="images/stop.png" /> You have been successfully logged out';
                    } ?>      
                </div>

				<div id="content">
					<div class="5grid">
						<div class="4u-first" style="width: 50%;">

							<!-- Box #1 -->
								<section>
									<header>
										<h2>Login to your Schedule Forward account below</h2>
									</header>
                                    <div id="alert" style="margin-bottom: 10px;"></div>
									<table id="tblLogin">
                                        <tr>
                                            <td>Username: </td>
                                            <td><input type="text" id="username" name="username" value="" /></td>
                                        </tr>
                                        <tr>
                                            <td>Password: </td>
                                            <td><input type="password" id="password" name="password" value="" /></td>
                                        </tr>
                                        <tr>
                                            <td>Select hospital: </td>
                                            <td>
                                                <div id="hospital-select">
												</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: center;" colspan="2"><input type="button" name="button" value="Login" onclick="processLogin();" /></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right;" colspan="2"><a href="#"><span style="font-size: smaller;">Forgot your password?</span></a></td>
                                        </tr>
                                    </table>
								</section>

						</div>
						<div class="4u" style="width: 47%;">

							<!-- Box #2 -->
								<section>
									<header>
										<h2>What MedSketch Does For Your Hospital</h2>
									</header>
									<ul class="check-list">
										<li>View your weekly schedule with custom filtering options</li>
										<li>Generate monthly schedules at a click of a button</li>
										<li>Specify shift preferences and weights based on user roles</li>
										<li>Switch shifts with others quickly straight from your phone</li>
									</ul>
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