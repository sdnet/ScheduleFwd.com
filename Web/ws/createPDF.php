<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include('cws.php');
require_once("classes/dompdf/dompdf_config.inc.php");
$sessionId = $_POST['sessionId'];
$groupcode = $_POST['grpcode'];
if(isset($_POST['id'])){
	$userId = $_POST['id'];
}else{
	$userId = $_SESSION['_id'];
}
$type = $_POST['type'];
$content = $_POST['content'];
// check session
if(VerifySession($sessionId,$groupcode,$userId,'User') == true){
	if($groupcode == "" || $userId == null) {
		$message =  "emptyFields";
	} else {
	
		$html = str_replace('<div id="createPDF"><a onclick="sendDivToPDF(\'toPDFDiv\')">Create PDF</a></div>','',$content);
		if($type == 'preferences'){
			$l = 'L';	
			$fontsize = 12;
		}else{
			$l = 'P';	
			$fontsize = 7;
		}
		include("classes/mpdf/mpdf.php");
		$mpdf=new mPDF('',array(210,297),$fontsize,'',1,1,0,0,1,1,'' . $l . ''); 
		//$mpdf->SetDisplayMode('fullpage');
		// LOAD a stylesheet
		$stylesheet = file_get_contents('http://www.schedulefwd.com/css/style.css');
		$mpdf->WriteHTML($stylesheet,1); 
		$stylesheet = file_get_contents('http://www.schedulefwd.com/css/pdf.css');
		$mpdf->WriteHTML($stylesheet,1); 
		$mpdf->WriteHTML($html);
			$filename = "" . $type . "-" . date('F', strtotime('+2 month')) . ".pdf"; 
		//$fp = fopen($filename, "a"); 
		//fwrite($fp, $pdfoutput); 
		//fclose($fp); 
	//	$data = array('url' => 'http://www.schedulefwd.com/ws/calendar.pdf');
		header('Content-Type: application/pdf');
		header('Content-Disposition: inline; filename="' . $filename . '";');
		echo $mpdf->Output('' . $filename . '','D');
	}				
		}else{
	//return auth failure
	echo "Authentication Failure, please try again.";	
}



?>