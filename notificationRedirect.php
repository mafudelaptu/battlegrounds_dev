<?php 
session_start();
//echo $_SERVER['DOCUMENT_ROOT']." DR:".dirname(__FILE__);
$pfadTiefe = str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__FILE__) ); // alle nötigen PHP funktionen einfügen
$slashCount = substr_count($pfadTiefe, '/'); // Zählen wieviele Slashes drinne sind
$ebeneTiefer = "";
for($i=0; $i<$slashCount; $i++){ // für jeden slash eine ebene tiefergehen
	$ebeneTiefer .= DIRECTORY_SEPARATOR . '..';
}
$pfad = dirname(__FILE__).$ebeneTiefer;
//require_once($pfad."/inc/inc_general_php_functions.php");
require_once(dirname(__FILE__)."/inc/inc_general_php_functions.php");

$DB = new DB();
$con = $DB->conDB();

switch($_GET['type']){
	case "global":
		$redirectLink = $_GET['redirectLink'];
		$gnid = (int)$_GET['gnid'];
		
		// als gesehen markieren
		$CheckedGlobalNotifications = new CheckedGlobalNotification();
		$retCGN = $CheckedGlobalNotifications->insertCheckedNotification($gnid);
		if($retCGN['status']){
			// redirect zum eigentlichen notification Link
			header('Location: '.$redirectLink);
		}
		break;
	case "raceNoPrize":
		$raceID = (int)$_GET['rID'];
		$steamID = $_SESSION['user']['steamID'];
		// RacePrize aktualisieren
		$sql = "UPDATE `RaceWinner`
							SET RacePrizeID = -1
							WHERE SteamID = ".secureNumber($steamID)." AND RaceID = ".(int) $raceID."
					";
		$data = $DB->update($sql);
		header('Location: index.php');
		break;
	default:
		$data = $_GET['data'];
		$cookieName = $_GET['name'];
		
		// Cookie setzen, damit beim nächsten mal nciht mehr angezeigt wird
		setcookie($cookieName, true, time()+360000000);
		
		// redirect zum eigentlichen notification Link
		header('Location: '.$data);
}


?>