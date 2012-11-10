<? require("php_includes/isLoggedIn.php"); ?>
<? require("php_includes/protectedPage.php"); ?>
<html>
	<head>
		<title>Schedule Forward :: Medical scheduling software made easy</title>
		<?php include("html_includes/adminMeta.php"); ?>
        <link rel="stylesheet" href="/css/autoSuggest.css" />
        <script src="/js/jquery.autoSuggest.js" type="text/javascript"></script>

		<script>
			$(document).ready(function(){
				$('a[title]').qtip();
				$('img[title]').qtip();
				loadMessages();	
						
			});
			
			function ISODateString(secs) {
 				var t = new Date(1970,0,1);
				t.setSeconds(secs);
				var date = t.getDate();
				var month = t.getMonth() + 1;
				var year = t.getFullYear();
				var hour = t.getHours();
				var minute = t.getMinutes();
				var seconds = t.getSeconds();
				t = month + "/" + date + "/" + year + " " + hour + ":" + minute + ":" + seconds;
				return t;
			}
			
			function loadMessages(folder) {
				$('#showMessage').hide();
				$('#composeMessage').hide();
				$('#messages').show();
				$('#messageBody').html('');
				$('#messageDetails').html('');
				if (folder == undefined) {
					folder = "Inbox";	
				} else {
						
				}
				
				$( ".msgListContainer tbody tr" ).each( function(){
  					this.parentNode.removeChild( this ); 
				});

				$.ajaxSetup({async:false});
					$.post('ws/getMessages', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","folder":folder} , function(data) {
						messageObj = data.data;
						for (var i = 0; i<messageObj.length; i++) {
							tblMessageList(messageObj[i]._id.$id,messageObj[i].from.first_name,messageObj[i].from.last_name,messageObj[i].user_name,messageObj[i].subject,ISODateString(messageObj[i].date_created.sec),messageObj[i].read)
						}
					});	
			}

			function tblMessageList(id,last,first,user,subject,date,read) {
				if (read == 1) { read = "normal"; } else { read = "bold" }
				$('#holderOfMessages > tbody').append('<tr style="font-weight: ' + read + '" id="' + id + '" onmouseover="this.bgColor=\'#FFFFCC\'" onmouseout="this.bgColor=\'#FFF\'" onclick="loadMessage(\'' + id + '\');"><td style="width: 35px; text-align: center;"><input type="checkbox" class="selectMessage" value="' + id + '" /></td><td style="width: 150px;">' + last + ' ' + first +'</td><td style="width: 450px;">' + subject + '</td><td style="">' + date + '</td></tr>');
			}
			
			function showCompose(to,subject,body) {
				
				$('#t').empty();
				$('#t').append('<input type="text" name="txtTo" id="txtTo" style="width: 100%; font-size: 1.0em; line-height: 10px;" />');
				
				var arr = [];
				var selectedData = {};
				if (to != undefined) {
					var spltTo = to.split(",");
				}

				var data;
				$.post('ws/getSuggest', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>"} , function(data) {
					if (spltTo != undefined) {
						selectedData.items = [];
						for (var i = 0; i < spltTo.length; i++) {
							for(key in data.data) {
								var username = data.data[key].value;
								var displayname = data.data[key].name;
								if (username == spltTo[i]) {
									var name = displayname;
								}
							}
							
							if (((spltTo[i] != "null") && (name != "null")) && (spltTo[i] != "<?php echo $user;?>")) {
								arr.push({value:"" + spltTo[i] + "", name:"" + name + ""});	
							}
						}
					}
					selectedData = {items: arr};
					$("#txtTo").autoSuggest(data.data, {selectedItemProp: "name", searchObjProps: "name", preFill: selectedData.items});						
				});
				
				$('#profileError').html('');
				$('#profileSuccess').html('');
				$('#composeMessage').show(300);
				$('#messages').hide();
				$('#txtSubject').val(subject);
				$('#txtMessage').val(body);	
			}
			
			function showMessages() {
				$('#composeMessage').hide();
				$('#messages').show(300);
			}
			
			function highlightRow(row) {
				$('#' + row).css("background-color","#FF0000");
			}
			
			function loadMessage(id) {
				$( ".msgListContainer tbody tr" ).each( function(){
  					this.style.backgroundColor="#FFFFFF"; 
				});
				
				// Set message as read in data store
				$.post('ws/readMessage', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","messageId":id,"read":1} , function(data) {
					$('#'+id).css("font-weight","normal");
				});
				
				$.post('ws/getMessage', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","messageId":id} , function(data) {
					messageObj = data.data[0];
					$('#messageBody').html(messageObj.body.replace(/\n/g,'<br />'));
					var details = "From: " + messageObj.from.first_name + " " + messageObj.from.last_name + "<br />";
					var tmpToUsers = "";
					for (var i = 0; i < messageObj.to.length; i++) {
						tmpToUsers += '<a href="#" onclick="showCompose(\'' + messageObj.to[i].user_name + '\')">' + messageObj.to[i].first_name + " " + messageObj.to[i].last_name + "</a>, ";
					}
					tmpToUsers = tmpToUsers.substring(0,tmpToUsers.length-2);
					details = details + "To: " + tmpToUsers;
					$('#messageDetails').html(details);
					$('#' + id).css("background-color","#B3DAFF");
				});
				
				var reply = '<a href="#" onclick="setupReply(\'' + id + '\')"><img src="images/email_go.png" /> Reply</a> | <a href="#" onclick="setupReply(\'' + id + '\',\'all\')"><img src="images/email_go.png" /> Reply All</a> | <a href="#" onclick="setupReply(\'' + id + '\',\'forward\')"><img src="images/email_go.png" /> Forward</a>';
				
				var del = '<a href="#" onclick="deleteMessage(\'' + id + '\')"><img src="images/email_delete.png" /> Delete Email</a>';
				
				$('#showMessage').show();
				$('#replyDiv').html(reply);
				$('#deleteDiv').html(del);		
			}
			
			function submitMessage() {
				// var to = $('#txtTo').val();
				var to = $('.as-values').val();
				var from = "<?=$_SESSION['userName'];?>";
				var subject = $('#txtSubject').val();
				var message = $('#txtMessage').val();
				
				if (!(to) || !(subject) || !(message)) {
					$('#profileError').html('Messages need a "to", "subject" and a "message"; please try again...');
				} else {
					$.post('ws/addMessage', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","to":to,"from":from,"subject":subject,"body":message} , function(data) {
						if (data.message == "success") {
							$('#profileSuccess').html('Message successfully sent!');
							loadMessages('Inbox')
						}
					});
				}
			}
			
			function getCheckedIds() {
				var listOfIds = "";
				$( ".selectMessage" ).each( function(){
  					if (this.checked) {
						listOfIds += this.value + ",";
					}
				});
				listOfIds = listOfIds.substring(0,listOfIds.length-1);
				return listOfIds;				
			}

			function deleteMessage(id) {
				$.post('ws/moveMessage', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","messageId":id,"folder":"Deleted"} , function(data) {
					$('#' + id).remove();
					$('#showMessage').hide();
				});
			}
			
			function setupReply(id,type) {
				var tmpToUsers = "";
				var tmpSubject = "";
				var tmpBody = "";
				$.post('ws/getMessage', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","messageId":id} , function(data) {
					messageObj = data.data[0];
					if (type == "forward") { 
						tmpSubject = "Fwd: " + messageObj.subject; 
						tmpBody = "\n ---- Forwarded message ---- \n";
					} else { 
						tmpSubject = "Re: " + messageObj.subject; 
						tmpBody = "\n ---- Original message ---- \n";
						if (type == undefined) {
							tmpToUsers = messageObj.from.user_name + ",";	
						} else { 
							tmpToUsers = messageObj.from.user_name + ",";
							for (var i = 0; i < messageObj.to.length; i++) {
								tmpToUsers += messageObj.to[i].user_name + ",";
							}
						}
						tmpToUsers = tmpToUsers.substring(0,tmpToUsers.length-1);
					}
					tmpBody += messageObj.body.replace(/\n/g,'\n');
					
				});
				showCompose(tmpToUsers,tmpSubject,tmpBody);	
			}

			function moveMessage(folder) {
				var Ids = getCheckedIds();
				var splitIds = Ids.split(",");
				for (var i = 0; i < splitIds.length; i++) {
					// Set message as read in data store
					$.post('ws/moveMessage', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","messageId":splitIds[i],"folder":folder} , function(data) {
						$('#' + splitIds[i]).remove();
					});
				}
			}

			function markAsUnRead() {
				var Ids = getCheckedIds();
				var splitIds = Ids.split(",");
				for (var i = 0; i < splitIds.length; i++) {
					// Set message as read in data store
					$.post('ws/readMessage', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","messageId":splitIds[i],"read":0} , function(data) {
						$('#'+splitIds[i]).css("font-weight","bold");
					});
				}
			}
			
			function markAsRead() {
				var Ids = getCheckedIds();
				var splitIds = Ids.split(",");
				for (var i = 0; i < splitIds.length; i++) {
					// Set message as read in data store
					$.post('ws/readMessage', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","messageId":splitIds[i],"read":1} , function(data) {
						$('#'+splitIds[i]).css("font-weight","normal");
					});
				}
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
                        <? include("html_includes/loggedInAs.php"); ?>
						<div class="4u-first" style="width: 100%;">

							<!-- Box #1 -->
								<section>

								<table style="width: 100%;">
                                	<tr>
                                    	<td style="width: 100px;">
                                        	<ul>
                                            	<li><a href="#" onClick="loadMessages('Inbox')">Inbox</a></li>
                                                <li><a href="#" onClick="loadMessages('Sent')">Sent</a></li>
                                                <li><a href="#" onClick="loadMessages('Deleted')">Deleted</a></li>
                                                <li><a href="#" onClick="loadMessages('Archives')">Archives</a></li>
                                            </ul>
                                        </td>
                                        <td>
                                        	<div id="composeMessage">
												<div id="messageUtilsCompose">
                                                    <a href="#" onClick="showMessages()"><img src="images/email.png" /> View Inbox</a>
                                                </div>
                                                
                                                <div id="profileError" style="width: 100%; text-align: center; color: #CC0000;"></div>
                                                <div id="profileSuccess"></div>
                                                
                                                To: <br />
                                                <div id="t"><input type="text" name="txtTo" id="txtTo" style="width: 100%; font-size: 1.0em; line-height: 10px;" /> </div>
                                                
                                                Subject: <br />
                                                <input type="text" name="txtSubject" id="txtSubject" style="width: 100%; padding: 4px;" /> <br />
                                                
                                                Message: <br />
                                                <textarea name="txtMessage" id="txtMessage" style="width: 100%; height: 200px; padding: 4px;"></textarea> <br /><br />
                                                
                                                <input type="button" name="submitMessage" id="submitMessage" value="Send Message" onClick="submitMessage()" style="margin: 0 auto;" />
                                            </div>
											<div id="messages">
                                                <div id="messageUtilsCompose">
                                                    <a href="#" onClick="showCompose()"><img src="images/pencil.png" /> Compose</a> &nbsp; | &nbsp; 
                                                    <a href="#" onClick="markAsRead()"><img src="images/email_open_image.png" /> Mark as Read</a> &nbsp; | &nbsp;
                                                    <a href="#" onClick="markAsUnRead()"><img src="images/email_delete.png" /> Mark as Unread</a> &nbsp; | &nbsp;
                                                    <a href="#" onClick="moveMessage('Archives')"><img src="images/email_add.png" /> Move to Archives</a> &nbsp; | &nbsp;
                                                    <a href="#" onClick="moveMessage('Deleted')"><img src="images/pencil_delete.png" /> Delete</a>
                                                </div> 
    
                                                <table class="msgListContainer">
                                                    <thead>
                                                        <th style="width: 50px;">&nbsp;</th>
                                                        <th style="width: 150px;">From</th>
                                                        <th style="width: 450px;">Subject</th>
                                                        <th>Date</th>
                                                    </thead>
                                                </table>
                                                <div id="msgList">
                                                    <table id="holderOfMessages" class="msgListContainer">
														<tbody>
                                                        
                                                        </tbody>
                                                    </table>
                                                </div>
                                                
                                                <div id="showMessage" style="display:none;">
                                                    <div id="messageUtils">
                                                        <div id="deleteDiv" style="float: right; margin-top: 7px; margin-right; 10px;">
                                                            
                                                        </div>
                                                        <div id="replyDiv">
                                                        </div>
                                                    </div>
                                                    <div id="messageDetails">
                                                    </div> 
                                                    <div id="messageBody">
                                                         
                                                    </div>
                                                </div>
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