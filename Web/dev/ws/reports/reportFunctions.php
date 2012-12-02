<?php
function pieIt($array){
	$chart = "[";
	if (count($array) > 0){
			foreach($array as $key=>$value){
						$chart .= "['" . $key ."'," . $value . "],";	
				}
			$chart = substr($chart,0,-1);
		}else{
			$chart .= "['No Records Found',0]";	
		}	
			$chart .= "]";	
			
	return $chart;
}

function barIt($array){
	$chart = "[";
	$ticks = "[";
		foreach($array as $key=>$value){
					$chart .= "" . $value . ",";
					$ticks .= "'" . $key . "',";	
				}	
	$chart = substr($chart,0,-1);		
	$ticks = substr($ticks,0,-1);	
	$chart .= "]";
	$ticks .= "]";	
	$barArray = array("numbers" => $chart, "ticks" => $ticks);
	return $barArray;
}
?>