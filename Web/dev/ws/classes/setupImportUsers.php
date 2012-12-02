<?php  
require_once('' . $_SERVER['DOCUMENT_ROOT'] . '/core/mg_base.class.php');

ini_set('auto_detect_line_endings',TRUE);


if ($_FILES['csv']['size'] > 0) { 
    $file = $_FILES['csv']['tmp_name']; 
    $handle = fopen($file,"r"); 
    
    createUser($handle);
         
    
}

function createUser($handle) {
	if($db == null) {
		
		$db = new MONGORILLA_DB;
		echo "it is null";
	}
	
	while($user=fgets($handle)){
    	$theUserArray = explode(',', $user);
    	
    	$prefArray = array('days' => array('0' => 'Monday', '1'▼ => 'Tuesday', '2' => 'Wednesday', '3' => 'Thursday', '4' => 'Saturday', '5' => 'Sunday',), 'shifts' => '', 'block_days' => '1', 'block_weekend' => '1', 'max_days' => '2', 'max_nights' => '4', 'desired_nights' => '4', 'desired_days' => '2', 'circadian' => '1', 'afterNightShift' => 'Wed7am');

		$obj = array('first _name' => $theUserArray[0], 'last_name' => $theUserArray[1], 'email' => $theUserArray[2], 'group' => $theUserArray[3], 'docType' => 'user', 'preferences' => $prefArray, 'active' => 1, 'password' => 'thatpassword', 'phone' => '', 'picture' => null, 'priority' => '5', 'role' => 'Attending');
		$arg = array('col' => 'TOH', 'type' => 'user', 'obj' => $obj);
		$results = $db->upsert($arg);
    }
	
	
}

?> 

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> 
<title>Import Users</title> 
</head> 

<body> 

<?php if (!empty($_GET[success])) { echo "<b>Your file has been imported.</b><br><br>"; } ?> 

<form action="setupImportUsers.php" method="post" enctype="multipart/form-data" name="form1" id="form1"> 
  Choose your file: <br /> 
  <input name="csv" type="file" id="csv" /> 
  <input type="submit" name="Submit" value="Submit" /> 
</form>

</body> 
</html> 