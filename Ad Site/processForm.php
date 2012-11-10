<?php

if ($_POST['submit']) {
	$link = mysql_connect('localhost', 'root', 'admin1!2!3!');
	$db_selected = mysql_select_db('beta', $link);
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	
	$query = "INSERT INTO names (fname,lname,email,phone) VALUES (\"" . $fname . "\", \"" . $lname . "\", \"" . $email . "\", \"" . $phone . "\")";
	
	
	
	if (mysql_query($query)) {
		echo "Success";
	} else {
		echo mysql_error();
	}
	
	mysql_close($link);
}

?>