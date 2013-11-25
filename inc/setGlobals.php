<?php 

// Globale Variablen Definieren
$global_variables = array(

// Allgemeine Einstellungen
		'PATH_TO_SCRIPT' => '/', // Wenn nicht im Root-verzeichnis, dann hier den Pfad angeben zum Script
		'STEAM_KEY' => "C086AB50054408B7A0FA42ABC467EAC9", // Steam public key für API
		"HOME_PATH" => "http://".$_SERVER['SERVER_NAME']."/battlegrounds/", // mit Slash "/" am ende
		"START_ELO" => 1200,
		"DR" => $_SERVER['DOCUMENT_ROOT'],
		"PROJECT_PATH" => dirname(__FILE__),

		"DIAMONDELO" => 1800,
		"GOLDELO" => 1500,
		"SILVERELO" => 1100,

		"BRONZECREDITBORDER" => 50,
		"SILVERCREDITBORDER" => 125,
		"GOLDCREDITBORDER" => 250,


		"RELEVANTCANCELCOUNT" => 6,
		"MATCHCANCELBORDER" => 70,
		"LEAVEMATCHPUNISHMENT" => 50,

		"WINSBORDER" => 300,
		
		"VOTEBONUSFORMATCHES" => 5,
		"WINSTREAKBORDER" => 3,
		"EVENTBONUS" => 75,
		
		"DAILYCREDITSBORDERFORPUNISHMENT" => -30,
		
		"TEAMSACTIVE" => true,
		"GBACTIVE" => false,
		
		"DUOQUEUE" => true,
		"ONEVSONEQUEUE" => true,
		
		

);
// Globale Variablen global definieren
foreach($global_variables as $k => $v){
	define($k, $v);
}

require_once DR.HOME_PATH_REL."inc/dbConnections.php";
require_once DR.HOME_PATH_REL."inc/customGlobals.php";
require_once DR.HOME_PATH_REL."inc/setDebug.php";

?>
