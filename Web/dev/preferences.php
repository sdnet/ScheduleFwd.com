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
				$( "#changePassword" ).dialog({
					autoOpen: false,
					modal: true,
					width: 400
				});
			
				$( ".openChangePassword" ).click(function() {
					$( "#changePassword" ).dialog( "open" );
					return false;
				});
			});
			
			$(document).ready(function(){
				getFormData();
				$("#phone").mask("(999) 999-9999? x99999");
				$('a[title]').qtip();
				
				$(".defaultText").focus(function(srcc)
				{
					if ($(this).val() == $(this)[0].title)
					{
						$(this).removeClass("defaultTextActive");
						$(this).val("");
					}
				});
    
				$(".defaultText").blur(function()
				{
					if ($(this).val() == "")
					{
						$(this).addClass("defaultTextActive");
						$(this).val($(this)[0].title);
					}
				});
    
				$(".defaultText").blur(); 
			});
			
			function getFormData() {
				var userObj;
				$.ajaxSetup({async:false});
				$.post('ws/getUser', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","id":"<?=$_SESSION['_id']?>","format":"dt"} , function(data) {
					userObj = data.data[0];
				});
			
				$('#userId').val(userObj._id.$id);
				$('#username').val(userObj.user_name);
				$('#username').attr("readonly", true)
				   .qtip({
						content: 'Usernames cannot be edited',
						show: 'mouseover',
						hide: 'mouseout',
						position: { corner: { target: 'topRight', tooltip: 'bottomLeft'} },
						style: {
							name: 'light'
						}
				   });
				$('#firstname').val(userObj.first_name);
				$('#lastname').val(userObj.last_name);
				$('#email').val(userObj.email);
				$('#phone').val(userObj.phone);
				$('#role').val(userObj.role);
				$('#role').attr("readonly", true)
				   .qtip({
						content: 'Sorry, you cannot edit your own role',
						show: 'mouseover',
						hide: 'mouseout',
						position: { corner: { target: 'topRight', tooltip: 'bottomLeft'} },
						style: {
							name: 'light'
						}
				});
			
				$.post("ws/getGroups", {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"}, 
					function (data) {
						var userGroup = userObj.group;
						var select = '<select name="group" id="group">';
						var temparray = data["data"];
						select = select + '<option value="">-- Select Below --</option>';
						for(i=0; i<temparray.length; i++) {
							select = select + '<option value="' + temparray[i]['name'] + '" ' + (userGroup == temparray[i]['name'] ? "SELECTED" : "") + ' disabled="disabled">' + temparray[i]['name'] + '</option>';
						}
						select = select + "</select>";
						$('.group-select').html(""); //clear old options
						$('.group-select').html(select);
				});
				
				$('#group').attr("readonly", true)
				   .qtip({
						content: 'Sorry, you cannot edit your own group',
						show: 'mouseover',
						hide: 'mouseout',
						position: { corner: { target: 'topRight', tooltip: 'bottomLeft'} },
						style: {
							name: 'light'
						}
				});
			}
				
				function displayChangePasswordStuff() {
					$('#changePasswordLink').html('<img src="images/text_signature.png" alt="Change Password Below" title="Change Password Below" /> Change Password Below');
					$('#frmChangePassword').show();
				}
				
				function processUser() {
					// Clear error div of any error messages
					$('#profileError').html('');
					$('#profileSuccess').hide('');
				
					// Collect form data
					var username = $('#username').val();
					var currPass = $('#currPassword').val();
					var newPass = $('#newPassword').val();
					var newPass2 = $('#newPassword2').val();
					var firstname = $('#firstname').val();
					var lastname = $('#lastname').val();
					var email = $('#email').val();
					var phone = $('#phone').val();
					var group = $('#group').val();
					var role = $('#role').val();
					var userId = $('#userId').val();
					var stopImage = "<img src=\"images/stop.png\" alt=\"Error\" />";
					
					// Check to see if user is attempting to change password
					if (currPass == "") { 
							$('#profileError').html(stopImage + ' Please enter your current password to modify your profile.');
							return;
					} else {
						if (((newPass != "") || (newPass2 != "")) && (newPass != newPass2)) {
							$('#profileError').html(stopImage + ' Your new passwords do not match; please try again.');
							return;
						}
					}
					
					// Form the json object to hold the user data
					var jsonUser = {
						"sessionId":"<?=$sessionId;?>",
						"username": "" + username + "",
						"original": "" + currPass + "",
						"password": "" + newPass + "",
						"password2": "" + newPass2 + "",
						"id": "" + userId + "",
						"firstname": "" + firstname + "",
						"lastname": "" + lastname + "",
						"email": "" + email + "",
						"phone": "" + phone + "",
						"group": "" + group + "",
						"role": "" + role + "",
						"grpcode": "<?=$_SESSION['grpcode'];?>"
					};
					
					var stopImage = "<img src=\"images/stop.png\" alt=\"Error\" />";
					var goImage = "<img src=\"images/accept.png\" alt=\"Success\" />";
					
					$.post("ws/editProfile", jsonUser,
						function (data) {
							if (data.message == "emailInvalid") {
								$('#profileError').html(stopImage + ' Email address is invalid.');
							}
							if (data.message == "emailExists") {
								$('#profileError').html(stopImage + ' Email already exists; please choose a unique email address.');
							}
							if (data.message == "userExists") {
								$('#profileError').html(stopImage + ' Username already exists; please choose a unique username.');
							}
							if (data.message == "emptyFields") {
								$('#profileError').html(stopImage + ' Please fill out all fields.');
							}
							if (data.message == "passwordInvalid") {
								$('#profileError').html(stopImage + ' Incorrect current password; please try again.');
							}
							if (data.message == "success") {
								$('#profileSuccess').html(goImage + ' Your profile has been successfully edited!');
								$('#profileSuccess').fadeIn();
								setInterval(function(){$('#profileSuccess').fadeOut();},2000);
							}
					});
				}
				
				function asteriskInput() {
					alert("adf");
					if (($this).val().length > 13 )
						$(this).val('*');
				}
				
		</script>
		
		<style media="screen" type="text/css">
			.defaultTextActive { color: #E0E0E0; font-style: italic; }
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
								<section>
									<? include("html_includes/adminHeader.php"); ?>
									
									<br />
									
                                    <h2 id="dashboard">Edit Your Schedule Preferences Below</h2>

									<div id="profileError" style="width: 100%; text-align: center; color: #CC0000;"></div>
									<div id="profileSuccess"></div>

									<div id="newUser" style="text-align: center; background-color: #E6E6E6; padding: 20px; border-radius: 4px; border: 1px solid #D9D9D9;">
										<table cellspacing="10" style="margin: 0 auto;">
											<tr id="frmChangePassword" style="background-color: #D9D9D9; border: 1px solid #CCCCCC;">
												<td colspan="4" style="text-align: center;">
													<table style="width: 100%;">
														<tr>
															<td style="background-color: #CCCCCC; padding-left: 10px;" colspan="2">Please specify shift preferences below:</td>
														</tr>
														<tr>
															<td style="text-align: center; font-weight: bold; font: 0.9em Arial; padding-top: 5px;">You prefer to work:</td>
															<td style="text-align: center; font-weight: bold; font: 0.9em Arial; padding-top: 5px;">You want to work on:</td>
														</tr>
														<tr>
															<td style="text-align: center; width: 40%;">
																<select>
																	<option>Mornings</option>
																	<option>Afternoons</option>
																	<option>Evenings</option>
																</select>
															</td>
															<td style="text-align: center; width: 40%;">
																<select>
																	<option>Sundays</option>
																	<option>Mondays</option>
																	<option>Tuesdays</option>
																	<option>Wednesday</option>
																	<option>Thursdays</option>
																	<option>Fridays</option>
																	<option>Saturdays</option>
																</select>
															</td>
														</tr>
													</table>
												</td>	
											</tr>
											<tr>
												<td colspan="4" style="text-align: center; margin-top: 10px;">
													<br />
													<input name="userId" id="userId" type="hidden" value="">
													<input type="button" name="btnSubmitNewUser" id="btnSubmitNewUser" value=" Edit Your Profile " onClick="processUser();" />
													<div id="pleaseWait" style="display: none;"><img src="images/ajax-loader.gif" /> Please wait, reloading page...</a>
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