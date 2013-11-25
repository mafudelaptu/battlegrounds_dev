<?php 

// Globale Variablen Definieren
$global_variables = array(

// Allgemeine Einstellungen     
	'PATH_TO_SCRIPT' => '/', // Wenn nicht im Root-verzeichnis, dann hier den Pfad angeben zum Script
	'STEAM_KEY' => "C086AB50054408B7A0FA42ABC467EAC9", // Steam public key für API
	//"HOME_PATH" => "http://".$_SERVER['SERVER_NAME']."/", // mit Slash "/" am ende
	"HOME_PATH_REL" => "/..",
	"START_ELO" => 1200,
		"DEBUG" => true,
	"DR" => dirname(__FILE__),
	"PROJECT_PATH" => dirname(__FILE__),
		"DIAMONDELO" => 1800,
		"GOLDELO" => 1500,
		"SILVERELO" => 1100,
		
		"MATCHCANCELBORDER" => 70,
		"RELEVANTCANCELCOUNT" => 6,
		"LEAVEMATCHPUNISHMENT" => 50,
	
		"MATCHMAKINGTIMING" => false,
		
		"POINTSMATCHMAKING" => false,
		"NOLEAGUES" => false,
		
		"VOTEBONUSFORMATCHES" => 5,
		"WINSTREAKBORDER" => 3,
		"EVENTBONUS" => 75,
		"DAILYCREDITSBORDERFORPUNISHMENT" => -30,
);

// Globale Variablen global definieren
foreach($global_variables as $k => $v){
	define($k, $v); 
}
require_once DR.HOME_PATH_REL."/inc/dbConnections.php";
require_once(DR."/db.php");
require_once(DR."/inc_classes.php");

require(DR.HOME_PATH_REL.'/Smarty/Smarty.class.php');
$smarty = new Smarty();

$smarty->setTemplateDir(DR.HOME_PATH_REL.'/templates');
$smarty->setCompileDir(DR.HOME_PATH_REL.'/Smarty/templates_c');
$smarty->setCacheDir(DR.HOME_PATH_REL.'/Smarty/cache');
$smarty->setConfigDir(DR.HOME_PATH_REL.'/Smarty/configs');

?>