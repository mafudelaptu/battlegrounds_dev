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
	
if($_SESSION['user']['steamID']){
	$User = new User();
	$User->updateUser($_SESSION['user']['steamID']);
	
		$User = new User();
		$steamID = $_SESSION['user']['steamID'];
		$userName = $User->getName($_SESSION['user']['steamID']);
		$matchID = (int) $_GET['matchID'];
		$Match = new Match();
		$MatchDetails = new MatchDetails();
		$smarty->assign('matchID', $matchID);
		
		
		
		$matchSubmittedRet = $Match->matchSubmitted($matchID);
		$matchSubmitted = $matchSubmittedRet['submitted'];
		$smarty->assign('matchSubmitted', $matchSubmitted);
		
		$teamOfUser = $MatchDetails->getMatchDetailsDataOfPlayer($matchID, $steamID, $select="TeamID");
		$smarty->assign('userTeam', $teamOfUser['data']['TeamID']);
		
		// MatchData
		$matchData = $Match->getMatchData($matchID);
		 
		// Player in Match?
		$isPlayerInMatch = $Match->isPlayerInMatch($matchID);
		
		// Ready to Play? - gegen doofe MM abuser
		$isReadyToPlayRet = $Match->isMatchReadyToPlay($matchID, $steamID);
		$isReadyToPlay = $isReadyToPlayRet['status'];
		
		$smarty->assign('isPlayerInMatch', $isPlayerInMatch);
		$smarty->assign('matchData', $matchData['data']);
		
		$retState = $Match->getMatchStateForUser($matchID);
		$smarty->assign('matchStatus', $retState['data']);
		
		// initCredits
		$UserCredits = new UserCredits();
		$retCredits = $UserCredits->initCreditsOfPlayer();
		if($matchID > 0){
			if($isPlayerInMatch){
				// wenn sp�ter auf die Seite kommt, dann erneuten submit verhindern
				$matchAlreadyCanceled = $Match->matchAlreadyCanceled($matchID);
				$matchSubmittedByPlayer = $MatchDetails->getMatchDetailsDataOfPlayer($matchID, $steamID, "Submitted");
					
				if($matchSubmitted['submitted'] ||  $matchSubmittedByPlayer['data']['Submitted'] == 1){
					$_COOKIE['matchFoundResultSubmitted'] = $matchID;
					$smarty->assign('submitLock', true);
				}
				else{
					$_COOKIE['matchFoundResultSubmitted'] = 0;
					$smarty->assign('submitLock', false);
				}
					
				// VoteType auslesen
				$VoteType = new VoteType();
				$retVT = $VoteType->getVoteTypes();
					
				// bereits abgegebene UserVotes auslesen
				$UserVotes = new UserVotes();
				$retUV = $UserVotes->getUserVotesForMatch($matchID);
				$retUVU = $UserVotes->getUserVotesForMatchOfUser($matchID);
			
				// Allowed to Vote
				$UserVoteCounts = new UserVoteCounts();
				$retAllowed = $UserVoteCounts->userAllowToVote();
					
				$teamWonID = $Match->getTeamWonIDOfMatch($matchID);
				$smarty->assign('teamWonID', $teamWonID['TeamWonID']);
					
				// MatchReplayData
				$MatchDetailsReplay = new MatchDetailsReplay();
				$ReplayParser = new ReplayParser();
				$retRepData = $MatchDetailsReplay->getReplayData($matchID);
				$retRepDataBestStats = $MatchDetailsReplay->getReplayDataBestStats($matchID);
				$retRepSubmitted = $ReplayParser->replayInDB($matchID, 0);
				$retRepDataChat = $MatchDetailsReplay->getReplayChat($matchID);
					
				$data = $Match->getReadyMatchData($matchID);
			
				$canceled = $Match->playerVotedForCancelingMatch($steamID, $matchID);
					
				$smarty->assign('data', $data);
				$smarty->assign('canceled', $canceled['canceled']);
				$smarty->assign('matchAlreadyCanceled', $matchAlreadyCanceled['canceled']);
				$smarty->assign('playerSubmitted', $matchSubmittedByPlayer['data']['Submitted']);
				$smarty->assign('voteTypes', $retVT['data']);
				$smarty->assign('userVotes', $retUV['data']);
				$smarty->assign('userVotesUser', $retUVU['data']);
				$smarty->assign('userVotesAllowed', $retAllowed['data']);
				$smarty->assign('replayData', $retRepData['data']);
				$smarty->assign('replayDataBestStats', $retRepDataBestStats['data']);
				$smarty->assign('replaySubmitted', $retRepSubmitted);
				$smarty->assign('replayChat', $retRepDataChat['data']);
					
				// Chat handling
// 				$Chat = new Chat();
// 				$Chat->createChatFile($matchID, "match");
					
			}
			//elseif($matchSubmitted){
			else{
				// BesucherModus
				$teamWonID = $Match->getTeamWonIDOfMatch($matchID);
				$data = $Match->getReadyMatchData($matchID);
					
				// VoteType auslesen
				$VoteType = new VoteType();
				$retVT = $VoteType->getVoteTypes();
			
				// bereits abgegebene UserVotes auslesen
				$UserVotes = new UserVotes();
				$retUV = $UserVotes->getUserVotesForMatch($matchID);
				$retUVU = $UserVotes->getUserVotesForMatchOfUser($matchID);
					
				// Allowed to Vote
				$UserVoteCounts = new UserVoteCounts();
				$retAllowed = $UserVoteCounts->userAllowToVote();
					
				// MatchReplayData
				$MatchDetailsReplay = new MatchDetailsReplay();
				$ReplayParser = new ReplayParser();
				$retRepData = $MatchDetailsReplay->getReplayData($matchID);
				$retRepDataBestStats = $MatchDetailsReplay->getReplayDataBestStats($matchID);
				$retRepSubmitted = $ReplayParser->replayInDB($matchID, 0);
				$retRepDataChat = $MatchDetailsReplay->getReplayChat($matchID);
					
				$matchAlreadyCanceled = $Match->matchAlreadyCanceled($matchID);
					
				$smarty->assign('teamWonID', $teamWonID['TeamWonID']);
				$smarty->assign('data', $data);
				$smarty->assign('besucher', true);
				$smarty->assign('matchAlreadyCanceled', $matchAlreadyCanceled['canceled']);
				$smarty->assign('voteTypes', $retVT['data']);
				$smarty->assign('userVotes', $retUV['data']);
				$smarty->assign('userVotesUser', $retUVU['data']);
				$smarty->assign('userVotesAllowed', $retAllowed['data']);
				$smarty->assign('replayData', $retRepData['data']);
				$smarty->assign('replayDataBestStats', $retRepDataBestStats['data']);
				$smarty->assign('replaySubmitted', $retRepSubmitted);
				$smarty->assign('replayChat', $retRepDataChat['data']);
			}
		}
		else{
			unset($_COOKIE['matchFound']);
			header('Location: '.HOME_PATH);		
		}
		
		// wenn anderer Spieler der nicht im match war anguckt


		$_SESSION['debug'] = p($isReadyToPlayRet,1);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="Found Match of players who joined the same queue.">
<meta name="robots" content="index,follow">
<title>N-GAGE-TV - Match</title>
<?php
	require_once(DR.HOME_PATH_REL."/inc/inc_css_and_js.php");
?>

<script type="text/javascript">
	  /**
	  // kick off chat
	  var chat =  new Chat();

	  $(function() {
	     chat.getState("match"); 
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
*/
</script>

</head>

<body onload="//setInterval('chat.update()', 1000)">
	<div id="wrap">
   		<?php 
				$smarty->display('top_navi.tpl');
			?>
			<div id="globalBG">
      <div class="container">
        <?php 
				$smarty->display('match/index.tpl');
			?>
		</div>
	</div>
    </div>
    <div id="footer">
      <div class="container">
        <?php $smarty->display('footer.tpl'); ?>
      </div>
    </div>
    <?php $smarty->display('general_stuff.tpl');?>
</body>
</html>