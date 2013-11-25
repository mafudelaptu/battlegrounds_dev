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
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description"
	content="Dota2 League's find match page, where you can select your Queue-type and get matched with random players of same skill.">
<meta name="robots" content="index,follow">
<title>N-GAGE-TV - Find Match</title>
<?php
require_once(DR.HOME_PATH_REL."/inc/inc_css_and_js.php");
?>
</head>
<?php 
if($_SESSION['user']['steamID']){
	$steamID = $_SESSION['user']['steamID'];
	$MatchMode = new MatchMode();
	$Region = new Region();
	$SingleQueue = new SingleQueue();
	$OneVsOneQueue = new OneVsOneQueue();
	$ThreeVsThreeQueue = new ThreeVsThreeQueue();

	$modi = $MatchMode->getAllMatchModes();
	$regions = $Region->getAllRegions();
	$singleQueueCount = $SingleQueue->getPlayersInQueue();
	$oneVsOneQueueCount = $OneVsOneQueue->getPlayersInQueue();
	$threeVsThreeQueueCount = $ThreeVsThreeQueue->getPlayersInQueue();

// 	$singleQueueInMatchCount = $SingleQueue->getPlayersInMatchCount();
// 	$oneVsOneQueueInMatchCount = $OneVsOneQueue->getPlayersInMatchCount();
// 	$threeVsThreeQueueInMatchCount = $ThreeVsThreeQueue->getPlayersInMatchCount();

	// hat user noch Matches nciht submitted -> dann join queue net erlauben
	$MatchDetails = new MatchDetails();
	$openSubmits = $MatchDetails->getOpenSubmitsOfPlayer($steamID);

	// 			$UserLeague = new UserLeague();
	// 			$retULLeague = $UserLeague->getLeagueOfUser($steamID);
	// 			$leagueData = $retULLeague['data'];

	// 			$retULPermissions = $UserLeague->getLeaguePermissionsForUser($leagueData['LeagueTypeID']);
	$UserSkillBracket = new UserSkillBracket();
	$retULLeague = $UserSkillBracket->getSkillBracketOfUser($steamID);
	$leagueData = $retULLeague['data'];

	$retULPermissions = $UserSkillBracket->getSkillBracketPermissionsForUser($leagueData['SkillBracketTypeID']);
	
	// MatchMode Player in Queue Counts bestimmen
	$matchModeCounts = $SingleQueue->getMatchModeCounts(true);
	$matchModeCounts1vs1 = $OneVsOneQueue->getMatchModeCounts(true);
	$matchModeCounts3vs3 = $ThreeVsThreeQueue->getMatchModeCounts(true);
	// region Player in Queue Counts bestimmen
	$regionCounts = $SingleQueue->getRegionCounts(true);
	$regionCounts1vs1 = $OneVsOneQueue->getRegionCounts(true);
	$regionCounts3vs3 = $ThreeVsThreeQueue->getRegionCounts(true);

	// bereits vorhandene Gruppen auslesen f�r DuoQueue
	$Group = new Group();
	$groupRet = $Group->getGroupsOfUser2();

	//auslesen ob Player gerade spielen (openMatches)
	$Match = new Match();
	$retOpenSingle = $Match->getOpenMatches($steamID, true,1 );
	$retOpen1vs1 = $Match->getOpenMatches($steamID, true,8 );
	$retOpen3vs3 = $Match->getOpenMatches($steamID, true,9 );

	// n�chste Volle Stunde
	$nextHour = getNextWholeHour(time());

	// is Permabanned?
	$Banlist = new Banlist();
	$retPB = $Banlist->isUserPermaBanned($steamID);
	
	$smarty->assign('openSubmitsLock', $openSubmits['openSubmits']);
	$smarty->assign('modi', $modi);
	$smarty->assign('regions', $regions);
	$smarty->assign('singleQueueCount', $singleQueueCount['count']);
	//$smarty->assign('singleQueueInMatchCount', $singleQueueInMatchCount['count']);
	$smarty->assign('oneVsOneQueueCount', $oneVsOneQueueCount['count']);
	//$smarty->assign('oneVsOneQueueInMatchCount', $oneVsOneQueueInMatchCount['count']);
	$smarty->assign('threeVsThreeQueueCount', $threeVsThreeQueueCount['count']);
	//$smarty->assign('threeVsThreeQueueInMatchCount', $threeVsThreeQueueInMatchCount['count']);

	$smarty->assign('matchModeCounts', $matchModeCounts['data']);
	$smarty->assign('matchModeStats', $matchModeCounts['statsData']);
	$smarty->assign('matchModeStatsMaxMM', $matchModeCounts['maxMM']);
	$smarty->assign('matchModeStatsMaxMMCount', $matchModeCounts['maxMMCount']);
	$smarty->assign('matchModeStatsMaxMM1vs1', $matchModeCounts1vs1['maxMM']);
	$smarty->assign('matchModeStatsMaxMMCount1vs1', $matchModeCounts1vs1['maxMMCount']);
	$smarty->assign('matchModeStatsMaxMM3vs3', $matchModeCounts3vs3['maxMM']);
	$smarty->assign('matchModeStatsMaxMMCount3vs3', $matchModeCounts3vs3['maxMMCount']);

	$smarty->assign('regionCounts', $regionCounts['data']);
	$smarty->assign('regionStats', $regionCounts['statsData']);
	$smarty->assign('regionStatsMaxR', $regionCounts['maxR']);
	$smarty->assign('regionStatsRCount', $regionCounts['maxRCount']);
	$smarty->assign('regionStatsMaxR1vs1', $regionCounts1vs1['maxR']);
	$smarty->assign('regionStatsRCount1vs1', $regionCounts1vs1['maxRCount']);
	$smarty->assign('regionStatsMaxR3vs3', $regionCounts3vs3['maxR']);
	$smarty->assign('regionStatsRCount3vs3', $regionCounts3vs3['maxRCount']);

	$smarty->assign('groupData', $groupRet['data']);
	$smarty->assign('openMatches', $retOpenSingle['data']);
	$smarty->assign('openMatches1vs1', $retOpen1vs1['data']);
	$smarty->assign('openMatches3vs3', $retOpen3vs3['data']);

	$smarty->assign('userPermissions', $retULPermissions['data']);
	$smarty->assign('leagueData', $leagueData);

	$smarty->assign('nextHour', $nextHour);
	
	$smarty->assign('isPermaBanned', $retPB['status']);

	if(is_array($modi) && count($modi) > 0){
					$UserElo = new UserElo();
					$eloArray = $UserElo->getAllEloValuesOfUser($steamID);
					$smarty->assign('eloArray', $eloArray);
					$halfCount = floor(count($modi)/2);
					$smarty->assign('halfCount', $halfCount);
					$smarty->assign('cookieMatchModes', $_COOKIE['singleQueue']);
			}
			if(is_array($regions) && count($regions) > 0){
				$halfCount = floor(count($regions)/2);
				$smarty->assign('halfCountRegion', $halfCount);
			}
			$_SESSION['debug'] = "";
			$_SESSION['debug'] .= p($_SESSION,1);
			//$_SESSION['debug'] .= p($retULPermissions,1);


			// Chat handling
			$Chat = new Chat();
			//$Chat->createChatFile(0, "singleQueue");
}
?>

<body
	onload="//$(document).ready(function() {setInterval('chat.update()', 5000)});">
	<script type="text/javascript">
	/**
	// kick off chat
	  var chat =  new Chat();

	  $(function() {
	     chat.getState("singleQueue"); 
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
	
	$(document).ready(function() {
		 
		  // Rejoin Queue after AutoKick
		  var rejoin = getParameterByName("rejoin");
		  var joinType = getParameterByName("joinType");
		  l($("#hiddenInputOpenSubmitsLock").val() == "");
		  if(rejoin == "true" && $("#hiddenInputOpenSubmitsLock").val() == ""){
			  	l("joinType:"+joinType);
			  	
				switch(joinType){
					case "singleQueueJoin":
							joinSingleQueue();
						break;
					case "singleQueueQuickJoin":
							joinSingleQueue(true);
						break;
					case "duoQueueJoin":
						groupID = getParameterByName("gid");
						l("Start justCM");
						$.ajax({
							url : 'ajax.php',
							type : "POST",
							dataType : 'json',
							data : {
								type : "constant",
								mode : "justCM"
							},
							success : function(result) {
								l("justCM success");
								l(result);
								var justCM = result.data; 
								joinSingleQueueAsGroup2(groupID, false, justCM);
							}
						});
						l("End justCM");
						
					break;
					case "1vs1QueueJoin":
						join1vs1Queue2();
						break;
				}  
			}
		  else{
				l(rejoin+"-"+$("#hiddenInputOpenSubmitsLock").val()+"-"+joinType);
			  	
		}
	});
	 
</script>
	<div id="wrap">
		<?php 
		//
		$smarty->display('top_navi.tpl');
		?>
		<div id="globalBG">
			<div class="container">
				<?php 
				$smarty->display('find_match/index.tpl');
				?>
			</div>
			<!-- /container -->
		</div>
	</div>
	<div id="footer">
		<div class="container">
			<?php $smarty->display('footer.tpl'); ?>
		</div>
	</div>
	<?php $smarty->display('general_stuff.tpl');?>
	<!-- Modal -->
	<!--		<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Join Single Queue</h3>
      </div>
      <div class="modal-body">
        <p>Do you realy want to join the SingleQueue?</p>
      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button class="btn btn-primary" onclick="joinSingleQueue()">Join Queue!</button>
  	</div>
    </div>-->

</body>
</html>
