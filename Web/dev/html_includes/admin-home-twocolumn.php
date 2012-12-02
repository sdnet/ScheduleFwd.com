<?php 
			if  (isset($_GET['view'])) {
				$view = $_GET['view'];
			} else {
				$view = "";	
			}
			if ($view == "cw") {
				include("html_includes/dashboard.currentlyWorking.php");	
			} elseif ($view == "t") {
				include("html_includes/dashboard.timeoff.php");	
			} elseif ($view == "u") {
				include("html_includes/dashboard.providers.php");	
			} elseif ($view == "a") {
				include("html_includes/dashboard.alerts.php");
			} else {
				include("html_includes/dashboard.currentlyWorking.php");	
			}
?>