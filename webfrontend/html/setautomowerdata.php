<?php
require_once("inc/husqvarna_api.class.php");
$account = "andreas@mader.ws";
$passwd = "mozart1505";
$session_husqvarna = new husqvarna_api();
$session_husqvarna->login($account, $passwd);

$mowerlist=$session_husqvarna->get_robot();
$mower = $mowerlist[0];
$id= $mower->id;

//echo "<br>DEBUG: get_robot ".json_encode($mower)."<br>";

$cmd= $_GET["CMD"];

echo "Mower: ".$id." Command: ".$cmd;

switch ($cmd)
{
	case 1: $cmdTXT= 'park';  break;
	case 2: $cmdTXT= 'pause';  break;
	case 3: $cmdTXT= 'start3h'; break;
}
$res= $session_husqvarna->control($id, $cmdTXT);

echo "<br>Result:".json_encode($res);
//
//$fp = fopen('setcommand.txt', 'a');
//$dt = new DateTime();
//fwrite($fp, 'time:'.$dt->format('Y-m-d H:i:s'));
//fwrite($fp, ' empfangenes Kommando:'.$_GET["CMD"].' / '.$cmdTXT."\n");
//fclose($fp);

$session_husqvarna->logout();

?>