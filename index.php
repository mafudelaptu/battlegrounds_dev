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
<meta name="description" content="Starting Homepage of Dota2 League">
<meta name="robots" content="index,follow">
<title>N-GAGE.TV - Dota2</title>
<?php
// CSS und JS
require_once(DR.HOME_PATH_REL."/inc/inc_css_and_js.php");

// Google Stuff
require_once(DR.HOME_PATH_REL."/inc/googleStuff.php");
?>
<?php
$MatchDetails = new MatchDetails();
$User = new User();
$UserStats = new UserStats();
$Ladder = new Ladder();
$Match = new Match();
$Queue = new Queue();

// FirstLOgin
if(isset($_GET['firstLogin']) && $_GET['firstLogin'] == 1 && $_SESSION['user']['steamID'] > 0){
	// berechneten Elo ausrechnen
	$UserElo = new UserElo();
	$MatchMode = new MatchMode();
	$eloArray = $UserElo->getAllEloValuesOfUser($_SESSION['user']['steamID']);
	$modi = $MatchMode->getAllMatchModes();

	$smarty->assign('modi', $modi);
	$smarty->assign("eloArray",$eloArray);
	$smarty->assign("elo", $eloArray[1][1]['Elo']);
}
// statusBar
// User count
$userCount = $User->getUserCount();
$matchesCount = $Match->getMatchesPlayedCount();
$inQueueCount = $Queue->getInQueueCount();

// Refer A Friend
if($_GET['rid'] != ""){
	// set Session for login
	setcookie("referedID", $_GET['rid'], time()+3600);  /* verf�llt in 1 Stunde */
	header("Location: index.php");
}
// eingeloggt
if(isset($_SESSION['user']['steamID']) && $_SESSION['user']['steamID'] > 0){
	$steamID = $_SESSION['user']['steamID'];
	$UserPoints = new UserPoints();

	// User Details
	$userData = $User->getUserData($steamID);

	$userStats = $UserStats->getUserStats($steamID);

	// general ranking
	$generalRanking = $Ladder->getGeneralRanking($steamID, $userStats['Rank']);

	// get Notifications
	//$Profile = new Profile();
	//$notificationData = $Profile->getNotifications($steamID);

	// get Wall of Fame Data

	//$wallOfFameData = $UserPoints->getWallOfFameData();

	// User Credits auslesen
	$UserCredits = new UserCredits();
	$retCredits = $UserCredits->getCreditCountOfPlayer($steamID);
	$retHighestC =  $UserCredits->getHighestUserCredits();

	$SingleLadder = new SingleLadder();
	$bestPlayerRet = $SingleLadder->getGeneralLadderPlayers($steamID,0,null,5);

	$MatchMode = new MatchMode();
	$modi = $MatchMode->getAllMatchModes();

	$lastMatchesRet = $Match->getLastPlayedMatches(0, null);

	// Get Next Event
	$Event = new Event();
	$retEvent = $Event->getNextEvent();
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
	$retIsEventActiv = $Event->isEventActive();
	$retLost = $Event->playerLostEvent($eventID, $createdEventID,true);

	// StatsData auslesen
	$retStats = $User->getStatsOfUser($steamID);
	$retOpenSingle = $Match->getOpenMatches($steamID, true,1 );
	$retOpen1vs1 = $Match->getOpenMatches($steamID, true,8 );
	
	// League Data
// 	$UserLeague = new UserLeague();
// 	$LeagueType = new LeagueType();
// 	$retULLeague = $UserLeague->getLeagueOfUser($steamID);
// 	$retLeagueType = $LeagueType->getData($retULLeague['data']['LeagueTypeID']);
// 	$retULLeagueLvl = $UserLeague->getLeagueLvl($steamID, $retULLeague['data']['LeagueTypeID'], $retStats['data']['Points']);
// 	$retULLeagueName = $UserLeague->getLeagueName($retLeagueType['data']['Name'], $retULLeagueLvl['data']);

	// SkillBracket Data
	$UserSkillBracket = new UserSkillBracket();
	$SkillBracket = new SkillBracket();
	$retULLeague = $UserSkillBracket->getSkillBracketOfUser($steamID);
	$retLeagueType = $SkillBracket->getSkillBracketData($retULLeague['data']['SkillBracketTypeID']);
	$retULLeagueName = $retLeagueType['data']['Name'];
	
	
	// Streams
	$Streamer = new Streamer();
	$retStreamer = $Streamer->getStreamer();

	// News
	$News = new News();
	$retN = $News->getNewsForFrontend(5);
	
	
	// Smarty zuweisungen
	$smarty->assign('steamID', $steamID);
	$smarty->assign('userData', $userData);
	$smarty->assign('userStats', $userStats['data']);
	$smarty->assign('notificationData', $notificationData['data']);
	$smarty->assign('notificationCount', $notificationData['count']);
	$smarty->assign('ranking', $generalRanking['position']);
	$smarty->assign('wallOfFameData', $wallOfFameData['data']);
	$smarty->assign('userCredits', $retCredits['data']);
	$smarty->assign('bestPlayer', $bestPlayerRet['data']);
	$smarty->assign('modi', $modi);
	$smarty->assign('lastMatches', $lastMatchesRet['data']);
	$smarty->assign('highestCredits', $retHighestC['data']);
	$smarty->assign('nextEvent', $retEvent['data']);
	$smarty->assign('eventSubsData', $retES['data']);
	$smarty->assign('eventSubsCount', $retES['count']);
	$smarty->assign('userAlreadyInEvent', $retES2['status']);
	$smarty->assign('isEventActiv', $retIsEventActiv['status']);
	$smarty->assign('createdEventID', $retESCM['data']);
	$smarty->assign('allowedToJoin', $retAllowed['status']);
	$smarty->assign('stats', $retStats['data']);
	$smarty->assign('leagueData', $retULLeague['data']);
	$smarty->assign('leagueName', $retULLeagueName);
	$smarty->assign('liveMatches', (count($retOpenSingle['data'])+count($retOpen1vs1['data'])));
	$smarty->assign('isRdyForEvent', $retIsRdy['status']);
	$smarty->assign('streamerData', $retStreamer['data']);
	$smarty->assign('newsData', $retN['data']);
	$_SESSION['debug'] = p($retN,1);

	// Chat handling
	$Chat = new Chat();
	//$Chat->createChatFile(0, "shoutBox");

}

$smarty->assign('userCount', $userCount['count']);
$smarty->assign('matchesCount', $matchesCount['count']);
$smarty->assign('inQueueCount', $inQueueCount['count']);
?>

<script type="text/javascript">
	// kick off chat
	  var chat =  new Chat();

	  $(function() {
	     chat.getState("shoutBox"); 
	     // watch textarea for key presses
	     $("#sendie").keydown(function(event) {  
	     
	         var key = event.which;  
	   
	         //all keys including return.  
	         if (key >= 33) {
	           
	             var maxLength = $(this).attr("maxlength");  
	             var length = this.value.length;  
	             
	             // don't allow new content if length is maxed out
	             if (length >= maxLength) {  
	                 event.preventDefault();  
	             }  
	         }  
	                                                                                                     });
	     // watch textarea for release of key press
	     $('#sendie').keyup(function(e) {  
	                
	        if (e.keyCode == 13) { 
	        
	              var text = $(this).val();
	              var maxLength = $(this).attr("maxlength");  
	              var length = text.length; 
	               
	              // send 
	              if (length <= maxLength + 1) { 
	                chat.send(text, name);  
	                $(this).val("");
	              } else {
	                $(this).val(text.substring(0, maxLength));
	              }  
	        }
	     });
	  });
</script>

</head>
<?php 
if(CLOSED == false){
	if((ISIHL && !$userLoggedIn)){
	?>
	<body style="background-image: url(img/ihl/<?php echo IHLNAME; ?>.png); background-position: top center; background-attachment: fixed; background-repeat: no-repeat;">

	</body>
	<?php
	}
	
	else{
	?>
	<body
		onload="//$(document).ready(function() {setInterval('chat.update()', 5000)});">
		<div id="wrap">
			<?php 
			$smarty->display('top_navi.tpl');
			?>
			<div id="globalBG">
				<?php 
				$smarty->display('index/index.tpl');
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
	<?php 
	}
}
else{
	?>
	<?php $smarty->display('index/indexClosed.tpl'); ?>
	
	<?php 
}
?>

</html>
