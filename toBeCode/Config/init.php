<?php

use API\Application;

require_once "../Config/constants.php";
require_once "../Config/ApiInit.php";
require_once "../Config/Config.php";
require_once "../Config/Application.php";
require_once "../App/Models/DBConnection.php";
require_once "../App/Models/DBStaticUtil.php";


$api = new Application();
$initDb = $api->initDb();
