<?php

require('setup.php');


$newHospitalName = "";
$newGroupCode = "";
$newHospitalPass = "";
$newHospitalConfirmPass = "";

$firstName = "";
$lastName = "";
$hospitalAccountUserName = "";
$hospitalAccountPass = "";
$confirmHospitalAccountPass = "";

clearFields();

if(isset($_POST["newHospitalName"])) {
	$newHospitalName = $_POST["newHospitalName"];
}

if(isset($_POST["newGroupCode"])) {
	$newGroupCode = $_POST['newGroupCode'];
}

if(isset($_POST["newHospitalPass"])) {
	$newHospitalPass = $_POST["newHospitalPass"];
}

if(isset($_POST["newHospitalConfirmPass"])) {
	$newHospitalConfirmPass = $_POST["newHospitalConfirmPass"];
}

if(isset($_POST["firstName"])) {
	$firstName = $_POST["firstName"];
}

if(isset($_POST["lastName"])) {
	$lastName = $_POST["lastName"];
}

if(isset($_POST["hospitalAccountUserName"])) {
	$hospitalAccountUserName = $_POST["hospitalAccountUserName"];
}

if(isset($_POST["hospitalAccountPass"])) {
	$hospitalAccountPass = $_POST["hospitalAccountPass"];
}

if(isset($_POST["confirmHospitalAccountPass"])) {
	$confirmHospitalAccountPass = $_POST["confirmHospitalAccountPass"];	
}


$completeAccountInfoFields1 = (!empty($_POST["newHospitalName"]) && !empty($_POST["newGroupCode"]) && !empty($_POST["newHospitalPass"]) && !empty($_POST["newHospitalConfirmPass"]));

$completeAccountInfoFields2 = (!empty($_POST["firstName"]) && !empty($_POST["lastName"]) && !empty($_POST["hospitalAccountUserName"]) && !empty($_POST["hospitalAccountPass"]) && !empty($_POST["confirmHospitalAccountPass"]));

if($completeAccountInfoFields1 & $completeAccountInfoFields2) {
	$newHospitalName = $_POST["newHospitalName"];
	$newGroupCode = $_POST['newGroupCode'];
	$newHospitalPass = $_POST["newHospitalPass"];
	$newHospitalConfirmPass = $_POST["newHospitalConfirmPass"];
	
	if($newHospitalPass == $newHospitalConfirmPass) {
		createNewHospital($newHospitalName, $newGroupCode, $newHospitalPass);
		clearFields();	
			
	}
	else {
		echo "Your passwords do not match. Try again.";
	}
}
else {
	echo "Please complete all fields.";
}

function createNewHospital($name, $newGroupCode, $pass) {
	$newHospital = new setup($newGroupCode, $name);
	$newHospital->setUsers($pass);
}

function clearFields() {
	$newHospitalName = "";
	$newGroupCode = "";
	$newHospitalPass = "";
	$newHospitalConfirmPass = "";

	$firstName = "";
	$lastName = "";
	$hospitalAccountUserName = "";
	$hospitalAccountPass = "";
	$confirmHospitalAccountPass = "";
}


$htmlBody = "<html>
<head></head>

<body>
<h3>Hospital Account Info</h3>
<form action=\"testsetup.php\" method=\"post\">
Enter hospital name: <input type=\"text\" name=\"newHospitalName\" value=\"".$newHospitalName."\"><br>
Enter group code: <input type=\"text\" name=\"newGroupCode\" value=\"".$newGroupCode."\"><br>
Enter password: <input type=\"text\" name=\"newHospitalPass\" value=\"".$newHospitalPass."\"><br>
Confirm password: <input type=\"text\" name=\"newHospitalConfirmPass\" value=\"".$newHospitalConfirmPass."\"><br>
<hr>
<h3>admin contact info</h3>
First Name: <input type=\"text\" name=\"firstName\" value=\"".$firstName."\"><br>
Last Name: <input type=\"text\" name=\"lastName\" value=\"".$lastName."\"><br>
Admin User Name: <input type=\"text\" name=\"hospitalAccountUserName\" value=\"".$hospitalAccountUserName."\"><br>
Enter password: <input type=\"text\" name=\"hospitalAccountPass\" value=\"".$hospitalAccountPass."\"><br>
Confirm password: <input type=\"text\" name=\"confirmHospitalAccountPass\" value=\"".$confirmHospitalAccountPass."\"><br>
<input type=\"submit\" value=\"Submit\" onclick=\"\"><br>
</form>
</body>
</html>";

echo($htmlBody);


?>

