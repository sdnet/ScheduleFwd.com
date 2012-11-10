<? include("php_includes/protectedPage.php"); ?>
<html>
	<head>
		<title>MedSketch :: Medical scheduling software made easy</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<!--5grid--><script src="css/5grid/viewport.js"></script><!--[if lt IE 9]><script src="css/5grid/ie.js"></script><![endif]--><link rel="stylesheet" href="css/5grid/responsive.css" /><!--/5grid-->
		<link rel="stylesheet" href="css/style.css" />
		<!--[if lte IE 9]><link rel="stylesheet" href="css/style-ie9.css" /><![endif]-->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>

        <script src="js/jquery.dataTables.min.js" type="text/javascript"></script>

        <script language="javascript">
            function toUserMgmt() {
                alert("Redirect user to User Management page");
            }

            function toSchedMgmt() {
                alert("Redirect user to Schedule Management page");
            }

            function expandRow(divId) {
                $('#' + divId).css("padding","15px");
                $('#' + divId).toggle('fast');
            }

            function reduceRow(divId) {
                $('#' + divId).hide('slow');
            }
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
                        <div id="loggedInAs" />Logged in as: <?=$user?></div>
						<div class="4u-first" style="width: 100%;">

							<!-- Box #1 -->
								<section>
                                    <h2 id="dashboard">User Management</h2>
									<table id="tblUserMgmt" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Roles</th>
                                            <th style="width: 30px;">Edit</th>
                                            <th style="width: 30px;">Delete</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="gray"><img src="images/bullet_toggle_plus.png" onclick="expandRow('row1')" />Tiger</td>
                                            <td class="gray">Woods</td>
                                            <td class="gray">twoods@pga.com</td>
                                            <td class="gray">(324) 433.2223</td>
                                            <td class="gray">Admin</td>
                                            <td class="gray" style="text-align: center;"><img src="images/user_edit.png" alt="Edit" /></td>
                                            <td class="gray" style="text-align: center;"><img src="images/user_delete.png" alt="Edit" /></td>
                                        </tr>
                                        <tr id="row1" style="display: none; padding: 15px;">
                                            <td colspan="7">
                                                <span style="float: right; padding: 10px;"><a href="#" onclick="reduceRow('row1')"><img src="images/bullet_arrow_up.png" style="width: 20px; height: 20px;" /></a></span>
                                                <span style="float: left; padding: 10px;"><img src="images/thumbs/tiger.woods.jpg" style="width: 60px; height: 60px;" /></span>
                                                <span style="font-weight: bold; margin-top: 5px;">Quick view into Tiger Woods' schedule</span> <br />
                                                <span style="margin-left: 6px;">Hours scheduled: 125 | Hours worked: 100 | Hours remaining: 25 &nbsp; &nbsp; &nbsp; View user's: <a href="#">Monthly schedule</a> | <a href="#">Schedule preferences</a> | <a href="#">Time off requests</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><img src="images/bullet_toggle_plus.png" onclick="expandRow('row2')" />Phil</td>
                                            <td>Michelson</td>
                                            <td>pmichelson@pga.com</td>
                                            <td>(553) 223.5789</td>
                                            <td>User</td>
                                            <td style="text-align: center;"><img src="images/user_edit.png" alt="Edit" /></td>
                                            <td style="text-align: center;"><img src="images/user_delete.png" alt="Edit" /></td>
                                        </tr>
                                        <tr id="row2" style="display: none; padding: 15px;">
                                            <td colspan="7">
                                                <span style="float: right; padding: 10px;"><a href="#" onclick="reduceRow('row2')"><img src="images/bullet_arrow_up.png" style="width: 20px; height: 20px;" /></a></span>
                                                <span style="float: left; padding: 10px;"><img src="images/thumbs/phil.mickelson.jpg" style="width: 60px; height: 60px;" /></span>
                                                <span style="font-weight: bold; margin-top: 5px;">Quick view into Phil Michelson's schedule</span> <br />
                                                <span style="margin-left: 6px;">Hours scheduled: 135 | Hours worked: 100 | Hours remaining: 35 &nbsp; &nbsp; &nbsp; View user's: <a href="#">Monthly schedule</a> | <a href="#">Schedule preferences</a> | <a href="#">Time off requests</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="gray"><img src="images/bullet_toggle_plus.png" onclick="expandRow('row3')" />Adam</td>
                                            <td class="gray">Scott</td>
                                            <td class="gray">ascott@pga.com</td>
                                            <td class="gray">(678) 877.5564</td>
                                            <td class="gray">Admin</td>
                                            <td class="gray" style="text-align: center;"><img src="images/user_edit.png" alt="Edit" /></td>
                                            <td class="gray" style="text-align: center;"><img src="images/user_delete.png" alt="Edit" /></td>
                                        </tr>
                                        <tr id="row3" style="display: none; padding: 15px;">
                                            <td colspan="7">
                                                <span style="float: right; padding: 10px;"><a href="#" onclick="reduceRow('row3')"><img src="images/bullet_arrow_up.png" style="width: 20px; height: 20px;" /></a></span>
                                                <span style="float: left; padding: 10px;"><img src="images/thumbs/adam.scott.jpg" style="width: 60px; height: 60px;" /></span>
                                                <span style="font-weight: bold; margin-top: 5px;">Quick view into Adam Scott's schedule</span> <br />
                                                <span style="margin-left: 6px;">Hours scheduled: 115 | Hours worked: 100 | Hours remaining: 15 &nbsp; &nbsp; &nbsp; View user's: <a href="#">Monthly schedule</a> | <a href="#">Schedule preferences</a> | <a href="#">Time off requests</a>
                                            </td>
                                        </tr>
                                        </tbody>
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