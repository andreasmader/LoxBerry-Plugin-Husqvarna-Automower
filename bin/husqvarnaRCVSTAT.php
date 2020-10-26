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


$activity = $mower->status->mowerStatus->activity;
$state = $mower->status->mowerStatus->state;

$timestamp = $mower->status->storedTimestamp/1000;
$interval  = round(time()-$timestamp,0);
$timestamp = round($timestamp-1230768000+2*60*60);	        	     // convert timestamp to loxone time

$nextStartTimestamp = $mower->status->nextStartTimestamp-1230768000; // convert timestamp to loxone time
if ($nextStartTimestamp == -1230768000) $nextStartTimestamp =0;

$connected = $mower->status->connected;

if ($connected) $activitynum=$session_husqvarna->automoweractivity[$activity]; else $activitynum=99;
$statenum=$session_husqvarna->automowerstate[$state];

if ($statenum==10) $statenum+=$mower->status->lastErrorCode;

$battery  = $mower->status->batteryPercent;
$charging = (($activitynum==6)||($activitynum==4));

// START calculate charging time between 45% up to 90% in order to get information on battery health
	//get locally stored values out of .txt file
	$dataarray = json_decode(file_get_contents('batteryinformation.txt', true), true);
	$battlow 			= $dataarray['battlow'];
	$counttime 			= $dataarray['counttime'];
	$starttime 			= $dataarray['starttime']; 
	$startbattval		= $dataarray['startbattval'];
	$lastchargingtime	= $dataarray['lastchargingtime'];
	
	// when battery level exeeds 90% calculate charging time from 0-100% based on collected information
	if ($counttime && ($battery>80))
	{
		$lastchargingtime = round((time()-$starttime) / ($battery-$startbattval) * 100); 
		$counttime = false;
		LOGOK("Battery Charging Measurement completed - Battery Charging Time :".$lastchargingtime."s");
	}
	elseif ($battery > 45) 
	{
	    // remember timestamp when battery level exeeds 45% and state is charging
	    if ($battlow && $charging) {$battlow= false; $counttime=true; $starttime = time(); $startbattval=$battery;} 
		// reset charging time measurement when state is not charging anymore (6 or 4)
		if ($counttime && (!$charging)) {$counttime=false; $starttime = 0; LOGERR("Charging stopped");}
		// Log Measurement in Logfile   
		if ($counttime) LOGOK("Measuring Battery Charging Time");
	}
	else $battlow= true; // register battery charging level below 45%
	
	//store important values back to local .txt file
	$dataarray['battlow']			= $battlow;
	$dataarray['counttime']			= $counttime;
	$dataarray['starttime']			= $starttime;
	$dataarray['startbattval']		= $startbattval;
	$dataarray['lastchargingtime']	= $lastchargingtime;
	file_put_contents ('batteryinformation.txt' , json_encode($dataarray));
// END calculate charging time

$result= array ("activitynum" => $activitynum, "statenum" => $statenum, "batteryPercent" => $battery, "interval" => $interval, "timestamp" => $timestamp, "nextStart" => $nextStartTimestamp);

if ($lastchargingtime>15) $result["lastchargingtime"] = $lastchargingtime;

$dataToSend = json_encode($result);
$session_husqvarna->logout();

//Tansfer Data
sendUDP($dataToSend, $miniserverIP, $husqvarnaCfg->get("HUSQVARNA","UDPPORT"));

LOGOK("Data sent to Miniserver:".$dataToSend);

LOGEND("Processing terminated");
?>