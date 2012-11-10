<? session_start(); ?>
<?
    if ($_SESSION['userName']) {
        header("Location: home");
    }
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Schedule Forward :: Reset Schedule</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<!--5grid--><script src="css/5grid/viewport.js"></script><!--[if lt IE 9]><script src="css/5grid/ie.js"></script><![endif]--><link rel="stylesheet" href="css/5grid/responsive.css" /><!--/5grid-->
		<link rel="stylesheet" href="css/style.css" />
		<!--[if lte IE 9]><link rel="stylesheet" href="css/style-ie9.css" /><![endif]-->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
		<script type="text/javascript">
	
	function displayAlert(id,text) {
		$('#' + id + '').html(''+text+'');
	}
	
    function displayLogin() {
        $('#basic-modal-content').modal({position: ["15%",""]}); // jQuery object - this demo
    }

	function processReset() {
        var password = document.getElementById('password').value;
        var password2 = document.getElementById('password2').value;
		var guid = '<?php echo $_GET['guid'];?>';
		var grpcode = '<?php echo $_GET['c'];?>';
        $.post("ws/resetPassword.php", { submit: "true", password2: password2, password: password, guid:guid, grpcode:grpcode},
        function (data) {
            if (data.message == "success") {
			displayAlert('alert','<div class="alert alert-error"><img src="images/accept.png" alt="Success" /> Password Changed Successfully!');
                window.location.href = "/index";
            } else if (data == "invalidGUID") {
				displayAlert('alert','<div class="alert alert-error"><img src="images/stop.png" alt="Error" /> Your reset ID link is invalid. Please request another one using the forgot password link.</div>');
			} else if (data == "notMatch") {
			displayAlert('alert','<div class="alert alert-error"><div class="alert alert-error"><img src="images/stop.png" alt="Error" /> Your passwords do not match</div>');
			} else {
			displayAlert('alert','<div class="alert"><div class="alert alert-error"><img src="images/stop.png" alt="Error" /> Please fill out both fields</div>');
			}
        });							
	}
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
						<div class="4u-first" style="width: 100%; text-align: center;">

							<!-- Box #1 -->
								<section>
									<header>
										<h2>Please Reset Your Password</h2>
									</header>
                                    <div id="alert" style="margin-bottom: 10px;"></div>
									<div class="box" style="margin-bottom:20px">
					    <table style="margin: 0 auto;" class="none">
					        <tr>
					            <td style="padding: 5px;">
					                Password: <br />
					                <input type="password" id="password" name="password" value="" />
					            </td>
								</tr>
								<tr>
					            <td style="padding: 5px;">
					                Password confirm: <br />
					                <input type="password" id="password2" name="password2" value="" /> 
					            </td>
					        </tr>
							<tr>
								<td>
									<div class="btn-group pull-left">
										<input type="button" name="button" value="Reset Password" onclick="processReset();" />
									</div>
								</td>
								
							</tr>
					    </table>
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