<?php 
//ini_set("display_errors", 1);
define("HOME_PATH_REL", "/battlegrounds/");
//unset($_SESSION);
require_once $_SERVER['DOCUMENT_ROOT'].HOME_PATH_REL.'inc/setGlobals.php';
// print_r($_SESSION);
require_once(DR.HOME_PATH_REL."inc/db.php");
// print_r($_SESSION);
require_once(DR.HOME_PATH_REL."inc/inc_classes.php");
// p($_SESSION);
// put full path to Smarty.class.php
$pfadTiefe = str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__FILE__) ); // alle nötigen PHP funktionen einfügen
$slashCount = substr_count($pfadTiefe, '/'); // Zählen wieviele Slashes drinne sind
$ebeneTiefer = "";
for($i=0; $i<$slashCount; $i++){ // für jeden slash eine ebene tiefergehen
	$ebeneTiefer .= DIRECTORY_SEPARATOR . '..';
}
$pfad = dirname(__FILE__).$ebeneTiefer;
//require($pfad.'/Smarty/Smarty.class.php');
require(DR.HOME_PATH_REL.'Smarty/Smarty.class.php');
$smarty = new Smarty();

// $smarty->setTemplateDir($pfad.'/templates');
// $smarty->setCompileDir($pfad.'/Smarty/templates_c');
// $smarty->setCacheDir($pfad.'/Smarty/cache');
// $smarty->setConfigDir($pfad.'/Smarty/configs');

$smarty->setTemplateDir(DR.HOME_PATH_REL.'templates');
$smarty->setCompileDir(DR.HOME_PATH_REL.'Smarty/templates_c');
$smarty->setCacheDir(DR.HOME_PATH_REL.'Smarty/cache');
$smarty->setConfigDir(DR.HOME_PATH_REL.'Smarty/configs');
?>
