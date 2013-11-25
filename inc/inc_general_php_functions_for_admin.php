<?php 
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
// Globale Variablen Definieren
$global_variables = array(

// Allgemeine Einstellungen     
	'PATH_TO_SCRIPT' => '/', // Wenn nicht im Root-verzeichnis, dann hier den Pfad angeben zum Script
	'STEAM_KEY' => "C086AB50054408B7A0FA42ABC467EAC9", // Steam public key für API
	"HOME_PATH" => "http://".$_SERVER['SERVER_NAME']."/battlegrounds/", // mit Slash "/" am ende
	"HOME_PATH_REL" => "/battlegrounds/",
	"START_ELO" => 1200,
		"DEBUG" => true,
	"DR" => $_SERVER['DOCUMENT_ROOT'],
	"PROJECT_PATH" => dirname(__FILE__),
	
	"MATCHCANCELBORDER" => 70,
	"RELEVANTCANCELCOUNT" => 6,
	"LEAVEMATCHPUNISHMENT" => 50
);

// Globale Variablen global definieren
foreach($global_variables as $k => $v){
	define($k, $v); 
}
require_once DR.HOME_PATH_REL."/inc/dbConnections.php";
require_once(DR.HOME_PATH_REL."/inc/db.php");
require_once(DR.HOME_PATH_REL."/inc/inc_classes.php");

// put full path to Smarty.class.php
$pfadTiefe = str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__FILE__) ); // alle nötigen PHP funktionen einfügen
$slashCount = substr_count($pfadTiefe, '/'); // Zählen wieviele Slashes drinne sind
$ebeneTiefer = ""; 
for($i=0; $i<$slashCount; $i++){ // für jeden slash eine ebene tiefergehen
	$ebeneTiefer .= DIRECTORY_SEPARATOR . '..';
}
$pfad ="..";
require($pfad.'/Smarty/Smarty.class.php');
$smarty = new Smarty();

$smarty->setTemplateDir($pfad.'/templates');
$smarty->setCompileDir($pfad.'/Smarty/templates_c');
$smarty->setCacheDir($pfad.'/Smarty/cache');
$smarty->setConfigDir($pfad.'/Smarty/configs');


$Login = new Login();
$userLoggedIn = $Login->checkLogin();
$smarty->assign('userLoggedIn',$userLoggedIn);

	if($_POST['logout']){
			$Login->handleLogout();
	}
	else{
			$Login->handleLogin();
	}

if($userLoggedIn){
		// Cookie verländern
		//setcookie("login[steamID]", $_SESSION['user']['steamID'], time()+36000);
		
		$User = new User();
		
		$userData = $User->getUserData($_SESSION['user']['steamID']);
		
		$_SESSION['user']['name'] = $userData['Name'];
		$_SESSION['user']['avatar'] = $userData['Avatar'];
		$_SESSION['user']['avatarMed'] = $userData['AvatarMed'];
		$_SESSION['user']['avatarFull'] = $userData['AvatarFull'];
		$_SESSION['user']['admin'] = $userData['Admin'];
		
		$smarty->assign('userName',$userData['Name']);
		$smarty->assign('userAvatar',$userData['Avatar']);
		$smarty->assign('userAvatarMed',$userData['AvatarMed']);
		$smarty->assign('userAvatarFull',$userData['AvatarFull']);
		$smarty->assign('steamID',$_SESSION['user']['steamID']);
		$smarty->assign('isAdmin',$_SESSION['user']['admin']);
		
		$_SESSION['user']['Region'] = $userData['Region'];
}
else{
	header('Location: '.HOME_PATH);
}

// Wenn nciht Admin -> weiterleitung
if(!$User->isAdmin($_SESSION['user']['steamID'])){
	header('Location: '.HOME_PATH);
}

// Match Redirect
// $Match = new Match();
// if($_COOKIE['matchFound'] > 0 && !$_GET['matchID'] && $Match->isPlayerInMatch($_COOKIE['matchFound']) ){
// 	$_SESSION['matchLock'] = true;
// 	header('Location: '.HOME_PATH."match.php?matchID=".$_COOKIE['matchFound']);
// }
// else{
// 	$_SESSION['matchLock'] = false;
// }
?>