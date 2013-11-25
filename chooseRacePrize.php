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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="Choose your Prize for Winning a Race on Dota2 League">
<meta name="robots" content="no-follow">
<title>N-GAGE-TV - Choose Race Prize</title>
<?php
	// CSS und JS
	require_once(DR.HOME_PATH_REL."/inc/inc_css_and_js.php");
	
	// Google Stuff
	require_once(DR.HOME_PATH_REL."/inc/googleStuff.php");
?>
<?php
	// get Winner data
	$raceID = $_GET['rID'];
	$RaceWinner = new RaceWinner();
	$retRW = $RaceWinner->getWinnerData($raceID);
	$winnerData = $retRW['data'];
	
	$raceTypeID = $winnerData['RaceID'];
	
	// RaceData
	$Race = new Race();
	$RaceType = new RaceType();
	$retRT = $Race->getRaceData($raceID);
	
	$RacePrizes = new RacePrizes();
	$retRP = $RacePrizes->getRacePrizes($raceID);
	
	// get WinnerList
	$UserPoints = new UserPoints();
	$retUP = $UserPoints->getRaceWinnerList($retRT['data']['StartTimestamp'], $retRT['data']['EndTimestamp'], $retRT['data']['WinnerCount'], $retRT['data']['WinnerCountType']);
	
	// getPickPosition
	$retRWPick = $RaceWinner->getPickPositionOfRace($raceID);
	
	$smarty->assign('winnerData', $winnerData);
	$smarty->assign('raceData', $retRT['data']);
	$smarty->assign('racePrizesData', $retRP['data']);
	$smarty->assign('winnerList', $retUP['data']);
	$smarty->assign('positionOfUser', $retUP['PositionOfUser']);
	$smarty->assign('pickPosition', $retRWPick['data']);
	$_SESSION['debug'] = p($retRWPick,1);
?>



</head>

<body>
<div id="wrap">
  <?php 
	 			$smarty->display('top_navi.tpl');
			?>
  <div class="container">
    <?php 
	 			$smarty->display('chooseRacePrize/index.tpl');
				?>
</div>
  <!-- /container -->
</div>
<div id="footer">
  <div class="container">
    <?php $smarty->display('footer.tpl'); ?>
  </div>
</div>
<?php $smarty->display('general_stuff.tpl');?>
</body>
</html>