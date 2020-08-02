#!/usr/bin/php
<?php
require_once "loxberry_system.php";
require_once "loxberry_log.php";
require_once "Config/Lite.php";
require_once "$lbphtmldir/husqvarna_api.class.php";
require_once "$lbphtmldir/functions.inc.php";

$miniserverIP = "";
$log = Null;
$husqvarnaCfg = Null;
$Husqvarna = Null;
$msArray = Null;
$msID = 0;

// Creates a log object, automatically assigned to your plugin, with the group name "GardenaLog"
$log = LBLog::newLog( [ "name" => "HusqvarnaLog", "package" => $lbpplugindir, "logdir" => $lbplogdir, "loglevel" => 6] );
// After log object is created, logging is started with LOGSTART
// A start timestamp and other information is added automatically
LOGSTART("Husqvarna rcvstatus started");


$husqvarnaCfg = new Config_Lite("$lbpconfigdir/husqvarna.cfg",LOCK_EX,INI_SCANNER_RAW);

if ($husqvarnaCfg == Null){
	LOGCRIT("Unable to read config file, terminating");
	LOGEND("Processing terminated");
	exit;
}
else {
	LOGOK("Reading config file successfull");
}

if ($husqvarnaCfg->get("HUSQVARNA","ENABLED")){
	LOGOK("Plugin is enabled");
} else{
	LOGOK("Plugin is disabled");
	LOGEND("Processing terminated");
	exit;
}

$msArray = LBSystem::get_miniservers();
$msID = $husqvarnaCfg->get("HUSQVARNA","MINISERVER");
$miniserverIP = $msArray[$msID]['IPAddress'];

//Neues Husqvarna Objekt anlegen. Username und Passwort werden aus cfg Datei gelesen.
$session_husqvarna = new husqvarna_api();
$session_husqvarna->login($husqvarnaCfg->get("HUSQVARNA","USERNAME"), $husqvarnaCfg->get("HUSQVARNA","PASSWORD"));

$mowerlist=$session_husqvarna->get_robot();
//LOGOK("Data received from Husqvarna Connect API:".json_encode($mowerlist));
$mower = $mowerlist[0];
$mowerID= $mower->id;

$battery = $mower->status->batteryPercent;
$activity = $mower->status->mowerStatus->activity;
$state = $mower->status->mowerStatus->state;

$timestamp = $mower->status->storedTimestamp;
$interval = round(time()-$timestamp/1000,0);

$connected = $mower->status->connected;

if ($connected) $activitynum=$session_husqvarna->automoweractivity[$activity]; else $activitynum=99;
$statenum=$session_husqvarna->automowerstate[$state];

if ($statenum==10) $statenum+=$mower->status->lastErrorCode;

$result= array ("activitynum" => $activitynum, "statenum" => $statenum, "batteryPercent" => $battery, "interval" => $interval);

$dataToSend = json_encode($result);
$session_husqvarna->logout();

//Tansfer Data
sendUDP($dataToSend, $miniserverIP, $husqvarnaCfg->get("HUSQVARNA","UDPPORT"));

LOGOK("Data sent to Miniserver:".$dataToSend);

LOGEND("Processing terminated");
?>