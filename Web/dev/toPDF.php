<? require("php_includes/isLoggedIn.php"); ?>
<? require("php_includes/protectedPage.php"); ?>

			<?php 
                if (!isset($_GET['type'])) {
                    include("pdfTemplates/noType.php");
                } else {
                    $type = $_GET['type'];
                    if ($type == "preferences") {
                        $group = isset($_GET['group']) ? $_GET['group'] : "";
                        include("pdfTemplates/preferences.php");	
                    } else if ($type == "timeoff") {
                        $group = isset($_GET['group']) ? $_GET['group'] : "";
                        include("pdfTemplates/timeoff.php");
					}
					else if($type == "mainschedule") {
						$group = isset($_GET['group']) ? $_GET['group'] : "";
						include("pdfTemplates/mainschedule.php");
					}
					else if($type == "dayschedule") {
						$group = isset($_GET['group']) ? $_GET['group'] : "";
						include("pdfTemplates/dayschedule.php");
					}
					else if ($type == "yourschedule") {
						$group = isset($_GET['group']) ? $_GET['group'] : "";
						include("pdfTemplates/yourschedule.php");
					}
					else if ($type == "weekschedule") {
						$group = isset($_GET['group']) ? $_GET['group'] : "";
						include("pdfTemplates/weekschedule.php");
					}
                }
            ?>
            