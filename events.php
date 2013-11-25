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
<meta name="description" content="Upcoming Events on Dota2 League">
<meta name="robots" content="index,follow">
<title>N-GAGE-TV - Events</title>
<?php
	// CSS und JS
	require_once(DR.HOME_PATH_REL."/inc/inc_css_and_js.php");
	
	// Google Stuff
	require_once(DR.HOME_PATH_REL."/inc/googleStuff.php");
?>
<?php
	// Get Next Event
	$Event = new Event();
	$retEvent = $Event->getNextEvent();
	$retIsEventActiv = $Event->isEventActive();
		// get EventSubmissions
		$EventSubmissions = new EventSubmissions();
		$eventID = $retEvent['data']['EventID'];
		$matchModeID = $retEvent['data']['MatchModeID'];
		$matchTypeID = $retEvent['data']['MatchTypeID'];
		$retES = $EventSubmissions->getPlayersSubmissionOfEvent($eventID, $matchModeID, $matchTypeID, false);
		$retES2 = $EventSubmissions->checkIfPlayerAlreadySignedIn($eventID);
		$retESCM = $EventSubmissions->getCreatedMatchOfPlayer($eventID);
		$retIsRdy = $EventSubmissions->isPlayerReadyForEvent($eventID);
		$retAllowed = $Event->checkRequirementsOfPlayer($steamID, $eventID);
		
		// EventType
		$EventType = new EventType();
		$allEventTypesRet = $EventType->getAllEventTypes();
		
		// EventStats
		$eventStatsRet = $Event->getGlobalEventStats();
		
		// Last Event Winners
		$EventWinner = new EventWinner();
		$eventWinnersRet = $EventWinner->getLastEventWinners();
		
		// Most Event Wins
		$mostEventWinsRet = $EventWinner->getUserWithMostEventWins();
	// get Last Events
	$lastEvents = $Event->getLastEvents("5");
		
	$smarty->assign('nextEvent', $retEvent['data']);
	$smarty->assign('isEventActiv', $retIsEventActiv['status']);
	$smarty->assign('eventSubsData', $retES['data']);
	$smarty->assign('eventSubsCount', $retES['count']);
	$smarty->assign('userAlreadyInEvent', $retES2['status']);
	$smarty->assign('createdEventID', $retESCM['data']);
	$smarty->assign('allEventTypes', $allEventTypesRet['data']);
	$smarty->assign('eventStats', $eventStatsRet['data']);
	$smarty->assign('lastEventWinner', $eventWinnersRet['data']);
	$smarty->assign('mostEventWins', $mostEventWinsRet['data']);
	$smarty->assign('lastEvents', $lastEvents['data']);
	$smarty->assign('allowedToJoin', $retAllowed['status']);
	$smarty->assign('isRdyForEvent', $retIsRdy['status']);
	$_SESSION['debug'] = p($retIsRdy,1);
?>



</head>

<body>
<div id="wrap">
  <?php 
	 			$smarty->display('top_navi.tpl');
			?>
			<div id="globalBG">
  <div class="container">
    <?php 
	 			$smarty->display('events/index.tpl');
				?>
</div>
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