<?php 

// Globale Variablen Definieren
$global_variables = array(

// Allgemeine Einstellungen     
	'PATH_TO_SCRIPT' => '/', // Wenn nicht im Root-verzeichnis, dann hier den Pfad angeben zum Script
	'STEAM_KEY' => "C086AB50054408B7A0FA42ABC467EAC9", // Steam public key für API
	"HOME_PATH" => "http://".$_SERVER['SERVER_NAME']."/battlegrounds/", // mit Slash "/" am ende
	"HOME_PATH_REL" => "/battlegrounds",
	"START_ELO" => 1200,
		"DEBUG" => true,
	"DR" => $_SERVER['DOCUMENT_ROOT'],
	"PROJECT_PATH" => dirname(__FILE__),
		"DIAMONDELO" => 1800,
		"GOLDELO" => 1500,
		"SILVERELO" => 1100,
		
		"MATCHCANCELBORDER" => 70,
		"RELEVANTCANCELCOUNT" => 6,
		"LEAVEMATCHPUNISHMENT" => 50,
	

		"MATCHMAKINGTIMING" => false,

		"POINTSMATCHMAKING" => false
);

// Globale Variablen global definieren
foreach($global_variables as $k => $v){
	define($k, $v); 
}

require_once DR.HOME_PATH_REL."/inc/dbConnections.php";
require_once(DR.HOME_PATH_REL."/inc/db.php");
require_once DR.HOME_PATH_REL."/inc/customGlobals.php";
require_once(DR.HOME_PATH_REL."/inc/inc_classes.php");

$pfad = "";

require($pfad.'Smarty/Smarty.class.php');
$smarty = new Smarty();

$smarty->setTemplateDir($pfad.'templates');
$smarty->setCompileDir($pfad.'Smarty/templates_c');
$smarty->setCacheDir($pfad.'Smarty/cache');
$smarty->setConfigDir($pfad.'Smarty/configs');

?>