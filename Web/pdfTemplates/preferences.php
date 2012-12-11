<?php

if(isset($_GET['month']) && isset($_GET['year'])) {
	$month = $_GET['month'];
	$year = $_GET['year'];
}
else {
	$date = date("Y-m-d");// current date
	$advanceDate = strtotime(date("Y-m-d", strtotime($date)) . " +2 month");
	$month = Date("m",$advanceDate);
	$year = Date("Y",$advanceDate);
}

$print = $_GET['print'];

$passedInUser = $_SESSION['_id'];
$gpCode = $_SESSION['grpcode'];
$sessionId = session_id();

$group = $_GET['group'];

$shiftList = getShiftList($sessionId, $gpCode, $group);
$htmlBody = "";
$htmlBody = $htmlBody."<html id=\"pageHTML\">
	<head>
		<title>Schedule Forward :: Medical scheduling software made easy</title>
        <link rel=\"stylesheet\" href=\"/css/pdf.css\" />
        <script src=\"//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js\" type=\"text/javascript\"></script>
        <script type=\"text/javascript\">
			$(document).ready(function() {
				onPageLoad();
			});
		</script>
    </head>
    <body>
        
			<div id=\"wrapper\">
				<div id=\"header\">Schedule Forward :: Automated Scheduling Made Easy</div>
			</div>
            
            <!-- Content -->
            <div id=\"content\">

            
    <form id=\"frmTimeOff\" action=\"/ws/createPDF.php\" method=\"post\">
    <input type=\"hidden\" id=\"contentId\" name=\"content\" value=\"\" />
    <input type=\"hidden\" id=\"sessionId\" name=\"sessionId\" value=".$sessionId." />
    <input type=\"hidden\" id=\"grpcode\" name=\"grpcode\" value=".$_SESSION['grpcode']." />
    <input type=\"hidden\" id=\"type\" name=\"type\" value=\"preferences\" />
    
    <div id=\"createPDF\"><a onclick=\"sendDivToPDF('toPDFDiv')\">Create PDF</a></div>

<div id=\"toPDFDiv\">";

$htmlBody = $htmlBody."<h1>Shift Preferences for ".$group." </h1>";
$htmlBody = $htmlBody."<p>Fill out the sheet below to specify your working preferences.</p>";
$htmlBody = $htmlBody."<table style=\"width: 100%;\">
        <tr>
            <td style=\"width: 40%; font-weight: bold; text-align: center; padding-right: 10px; background-color: #F2F2F2;\">
                <h3>Rank shifts in order of preference, 1 being best</h3>
            </td>
            <td style=\"font-weight: bold; text-align: center; padding-right: 10px; background-color: #F2F2F2;\">
                <h3>Select your working preferences</h3>
            </td>
        </tr>
        <tr>
            <td valign=\"top\" style=\"padding: 5px;\">
                <table id=\"tbaleShifts\" style=\"width: 100%;\">
                    <thead>
                        <th style=\"width: 50px; background-color: #F2F2F2;\">Rank</th>
                        <th style=\"background-color: #F2F2F2;\">Shift Name</th>
                    </thead>
                    <tbody>";
                    $htmlBody = $htmlBody.$shiftList;
                    $htmlBody = $htmlBody."
                    </tbody>
                </table>
            </td>
            <td style=\"padding: 5px;\">
                    <div style=\"background-color: #FFFFFF; padding: 5px; width: 99%\">
                        Select your block working day preferences: (Circle one for each)<br />
                        <span style=\"font-weight: bold;\">Weekends</span>:
                        Yes &nbsp; | &nbsp; No   &nbsp; &nbsp; &nbsp;
                        <span style=\"font-weight: bold;\">Weekdays</span>:  
                        Yes &nbsp; | &nbsp; No
                    </div>
                    <hr />
                    <div style=\"background-color: #FFFFFF; padding: 5px; width: 99%\">
                        Would you like to abide by circadian rhythm?  (Circle one)
                        Yes &nbsp; | &nbsp; No
                    </div>
                    <hr />
                    <div style=\"background-color: #FFFFFF; padding: 5px; width: 99%\">
                    Which days do you prefer to work?  (Circle days) <br />
                    Monday &nbsp; | &nbsp; Tuesday &nbsp; | &nbsp; Wednesday &nbsp; | &nbsp; Thursday &nbsp; | &nbsp; Friday &nbsp; | &nbsp; Saturday &nbsp; | &nbsp;  Sunday
                    </div>
                    <hr />
                    <div style=\"background-color: #FFFFFF; padding: 5px; width: 99%\">
                        <div style=\"line-height: 18px; margin-bottom: 6px;\">After a night shift (ie: <span style=\"font-weight: bold;\">ending at 7am Tuesday</span>), when is the 
                        earliest that you would prefer to work next? (Circle one)</div>
                        Wednesday, 7am &nbsp; | &nbsp; Wednesday, 12pm &nbsp; | &nbsp; Wednesday, 7pm &nbsp; | &nbsp; Thursday, 7am
                        </div>
                    <hr />
                    <div style=\"background-color: #FFFFFF; padding: 5px; width: 99%\">
                    Number of consecutive <span style=\"font-weight: bold;\">night</span> shifts: (Circle one)<br />
                    <span style=\"font-weight: bold;\">Max:</span> 1 night &nbsp; | &nbsp; 2 nights &nbsp; | &nbsp; 3 nights &nbsp; | &nbsp; 4 nights &nbsp; | &nbsp; 5 nights &nbsp; | &nbsp; 6 nights
                    <br />
                    <span style=\"font-weight: bold;\">Desired:</span> 1 night &nbsp; | &nbsp; 2 nights &nbsp; | &nbsp; 3 nights &nbsp; | &nbsp; 4 nights &nbsp; | &nbsp; 5 nights &nbsp; | &nbsp; 6 nights
                    <br /><br />
                    Number of consecutive <span style=\"font-weight: bold;\">day</span> shifts: (Circle one)<br />
                    <span style=\"font-weight: bold;\">Max:</span> 1 night &nbsp; | &nbsp; 2 nights &nbsp; | &nbsp; 3 nights &nbsp; | &nbsp; 4 nights &nbsp; | &nbsp; 5 nights &nbsp; | &nbsp; 6 nights
                    <br />
                    <span style=\"font-weight: bold;\">Desired:</span> 1 night &nbsp; | &nbsp; 2 nights &nbsp; | &nbsp; 3 nights &nbsp; | &nbsp; 4 nights &nbsp; | &nbsp; 5 nights &nbsp; | &nbsp; 6 nights
                    <br />
                </div>
            </td>
        </tr>
    </table></div></body></html>";
    
    if($print == 1) {
	    toPdf($htmlBody, $sessionId, $gpCode);
    }
    
    
    echo($htmlBody);
    
    
        
    function getShiftList($sessId, $gpCode, $gp){
	    $data_array =array('sessionId'=>$sessId,'grpcode'=>$gpCode,'group'=>$gp, 'id'=>$_SESSION['_id']);
	    $data = http_build_query($data_array);
	    
	    $response = do_post_request('http://schedulefwd.com/ws/getShifts', $data);
	    
	    $response = json_decode($response, true);
	    $dataArray = $response['data'];
	    
	    
	    $returnString = "";
	    
	    foreach($dataArray as $shift) {
	    	$start = $shift['start'];
	    	$end = $shift['end'];
	    	
	    	if ($start < 1200) {
				if ($start < 1000) {
					$start = substr($start, 1, strlen($start));
				}
				if ($start == "000") {
					$start = "1200";	
				}
				$start = $start."am";
			}
			else {
				$start = intval($start) - 1200;
				
				if ($start == "0") {
					$start = "1200";	
				}
				
				$start = $start . "am";	
			}
			
			if ($end < 1200) {
				if ($end < 1000) {
					$end = substr($end, 1, strlen($end));
				}
				if ($end == "0") {
					$end = "1200";	
				}
				$end = $end."am";
			}
			else {
				$end = intval($end) - 1200;
				
				if ($end == "0") {
					$end = "1200";	
				}
				
				$end = $end . "pm";
			}
			
			$start = substr($start, 0, strlen($start) - 4) . ":" . substr($start, (strlen($start)-4), strlen($start));
			$end = substr($end, 0, strlen($end) - 4) . ":" . substr($end, (strlen($end)-4), strlen($end));
			
		    $returnString = $returnString."<tr><td style=\"padding: 4px; width:25px; border: 1px solid #666666;\"> &nbsp; </td><td> &nbsp;".$shift['name']." (".$start." - ".$end.")</td>";
	    }
	    
	    return $returnString;
	}
	
	function do_post_request($url, $data, $optional_headers = null) {
     $params = array('http' => array(
                  'method' => 'POST',
                  'content' => $data,
                  'header'=> "Content-type: application/x-www-form-urlencoded\r\n"
                . "Content-Length: " . strlen($data) . "\r\n",
               ));
     if ($optional_headers !== null) {
        $params['http']['header'] = $optional_headers;
     }
     $ctx = stream_context_create($params);
     $fp = fopen($url, 'rb', false, $ctx);
     if (!$fp) {
        throw new Exception("Problem with $url, $php_errormsg");
     }
     $response = @stream_get_contents($fp);
     if ($response === false) {
        throw new Exception("Problem reading data from $url, $php_errormsg");
     }
     	return $response;
     }
     
     function toPdf($stringContent, $sessId, $grpCode) {
	     $data_array = array('id'=>$_SESSION['_id'],'sessionId'=>$sessId,'grpcode'=>$grpCode, 'content'=>$stringContent, 'type'=>'timeoff');
	     $data = http_build_query($data_array);
	     
	     
	     $response = do_post_request('http://schedulefwd.com/ws/createPDF', $data);
	    
	    header('Content-disposition: attachment; filename=schedule.pdf');
	    header('Content-type: application/pdf');
	    echo $response; 
     }
    
?>





<script type="text/javascript">

function sendDivToPDF(divToPDF) {
	var content = $('#pageHTML').html();
	$('#contentId').val(content);
	$('#frmTimeOff').submit();
	/*
	var prefObj;
	var div = $('#'+divToPDF).html();
	$.post('/ws/createPDF', {"sessionId":"<?=$sessionId;?>","grpcode":"<?=$_SESSION['grpcode'];?>","content":div,"type":"preferences"} , function(data) {
		prefObj = data.data;
	});
	*/	
}
</script>