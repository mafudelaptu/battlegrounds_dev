<?php
//phpinfo();
define("HOME_PATH_REL", "/battlegrounds/");
 //error_reporting(E_ALL);
// ini_set("display_errors", 1);
require_once $_SERVER['DOCUMENT_ROOT']."/".HOME_PATH_REL.'inc/inc_base.php';

//p($_SESSION);
$Login = new Login();
//$_SESSION['sql']['queryCount'] = 0;

setcookie("loginHash", "", time()-3600);

$userLoggedIn = $Login->checkLogin3();
$smarty->assign('userLoggedIn',$userLoggedIn);

// BaseElo updaten
//$checkLogin = $_GET['checkLogin'];
$User = new User();

if($userLoggedIn){
	// Cookie verländern
	//setcookie("login[steamID]", $_SESSION['user']['steamID'], time()+36000);

	$userData = $User->getUserData($_SESSION['user']['steamID']);

	$_SESSION['user']['steamID'] = $userData['SteamID'];
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

	// get Notifications
	$Profile = new Profile();
	$notificationData = $Profile->getNotifications($_SESSION['user']['steamID']);
	
	$smarty->assign('notificationData', $notificationData['data']);
	$smarty->assign('notificationCount', $notificationData['count']);
	
	// check/set UserSkillBracket
	$SkillBracket = new SkillBracket();
	$retSB = $SkillBracket->checkAndSetSkillBracketOfUser();
	//p($retSB);
	
	// User Coins auslesen
	$UserCoins = new UserCoins();
	$retUC = $UserCoins->getCoinsOfUser();
	$smarty->assign('userCoins', (int) $retUC['data']);
	
	// Update Last Activity
	$retU = $User->setLastActivity();
	
	/**
	 * Region Bestimmung
	 */
	if($_GET['region']){
		setcookie("region", (int)$_GET['region'], time()+604800);
		header("Location: index.php");
	}
	elseif($_COOKIE['region'] == ""){
		switch($prefix){
			case "dota2rudev.":
			case "dota2ru.":
			case "ihlru.":
				setcookie("region", 5, time()+604800);
				header("Location: index.php");
				break;
			case "apdl.":
			case "apdldev.":
			default: setcookie("region", 6, time()+604800);
			header("Location: index.php");
			break;
			default: setcookie("region", 1, time()+604800);
			header("Location: index.php");
		}
	
	}
	//p($_SESSION['user']);
	// get All Regions
	$Region = new Region();
	$retR = $Region->getAllRegions("*", 1);
	$smarty->assign('regions', $retR);
	
	$retR2 = $Region->getRegionData($_COOKIE['region']);
	$smarty->assign('region', $retR2);
}

// Welche errors angezeigt werden sollen
if($_SESSION['user']['admin']){
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	//error_reporting(E_ALL);
}
else{
	//error_reporting(0);
}

// Redirect wenn nicht eingeloggt
$erlaubteOrte = array(HOME_PATH_REL."index.php",
		HOME_PATH_REL."ladder.php",
		HOME_PATH_REL."help.php",
		HOME_PATH_REL."event.php",
		HOME_PATH_REL."testEnv.php"
);
if(!$userLoggedIn && !in_array($_SERVER['PHP_SELF'], $erlaubteOrte)){
	//p($_SERVER['PHP_SELF']);
	//p($erlaubteOrte);
	header('Location: '.HOME_PATH."index.php");
}

?>