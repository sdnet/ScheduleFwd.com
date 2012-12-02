<?php
require_once('mg_db.class.php');
require_once('mg_errors.class.php');
require_once('mg_module.class.php');
require_once('mg_utility.class.php');

/* Settings for connecting to Mongo!*/
define('MONGODB_NAME', 'medbase');
define('MONGODB_HOST', 'localhost');
define('MONGODB_USERNAME',	false);
define('MONGODB_PASSWORD',	false);
define('MONGODB_PORT', '27017');
define('MONGODB_REPLICAS',	false);
define('DEFAULT_COLLECTION', 'mongorilla');
?>