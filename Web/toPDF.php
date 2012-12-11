<? require("php_includes/isLoggedIn.php"); ?>
<? require("php_includes/protectedPage.php"); ?>

<?php

if (!isset($_GET['type'])) {
    include("pdfTemplates/noType.php");
} else {
    
    //update schedule
    $db = new MONGORILLA_DB;
    $arg = array('col' => $_SESSION['groupcode'], 'type' => 'tempShift', 'where' => array('year' => $_GET['year'],'month' => $_GET['month'], 'day' => array('$ne' => '00')));
    $results = $db->find($arg);
    $schedule = array();
    $scheduleId = "";
    foreach ($results as $shift) {
        $schedule[$shift['id']] = $shift;
        $scheduleId = $shift['scheduleId'];
    }


    $obj = array('schedule' => $schedule);
    $arg = array('col' => $_SESSION['groupcode'], 'type' => 'schedule', 'obj' => $obj, 'where' => array(), 'id' => $scheduleId);
    $results = $db->upsert($arg);
    
    
    
    
    $type = $_GET['type'];
    if ($type == "preferences") {
        $group = isset($_GET['group']) ? $_GET['group'] : "";
        include("pdfTemplates/preferences.php");
    } else if ($type == "timeoff") {
        $group = isset($_GET['group']) ? $_GET['group'] : "";
        include("pdfTemplates/timeoff.php");
    } else if ($type == "mainschedule") {
        $group = isset($_GET['group']) ? $_GET['group'] : "";
        include("pdfTemplates/mainschedule.php");
    } else if ($type == "dayschedule") {
        $group = isset($_GET['group']) ? $_GET['group'] : "";
        include("pdfTemplates/dayschedule.php");
    } else if ($type == "yourschedule") {
        $group = isset($_GET['group']) ? $_GET['group'] : "";
        include("pdfTemplates/yourschedule.php");
    } else if ($type == "weekschedule") {
        $group = isset($_GET['group']) ? $_GET['group'] : "";
        include("pdfTemplates/weekschedule.php");
    }
}
?>
            