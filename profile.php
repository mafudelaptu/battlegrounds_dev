<?php
session_start();
require_once(dirname(__FILE__)."/inc/inc_general_php_functions.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="Profile page of <?php echo $_SESSION['user']['name'];?> with specific data about his account on Dota2 League">
<meta name="robots" content="index,follow">
<title>N-GAGE-TV - Profile of <?php echo $_SESSION['user']['name'];?></title>
<?php
	require_once(DR.HOME_PATH_REL."/inc/inc_css_and_js.php");
?>

<?php
	$id = $_GET['ID'];
	if($id > 0){
		if($id != $_SESSION['user']['steamID']){
			$besucher = true;
		}
		else{
			$besucher = false;
		}
		$steamID = $id;
	}
	else{
		$steamID = $_SESSION['user']['steamID'];
	}

	$_SESSION['tmp']['steamID'] = $steamID; // an ajax übergeben

	$MatchDetails = new MatchDetails();
	$User = new User();
	$UserStats = new UserStats();
	$MatchMode = new MatchMode();
	$MatchType = new MatchType();
	$UserLeague = new UserLeague();
	$LeagueType = new LeagueType();
	
	// User Details
	$userData = $User->getUserData($steamID);
	
	// User Stats auslesen
	$retStats = $User->getStatsOfUser($steamID);
	//$userStats = $UserStats->getUserStats($steamID);

	// League Data
// 	$retULLeague = $UserLeague->getLeagueOfUser($steamID);
// 	$retLeagueType = $LeagueType->getData($retULLeague['data']['LeagueTypeID']);
// 	$retULLeagueLvl = $UserLeague->getLeagueLvl($steamID, $retULLeague['data']['LeagueTypeID'], $retStats['data']['Points']);
// 	$retULLeagueName = $UserLeague->getLeagueName($retLeagueType['data']['Name'], $retULLeagueLvl['data']);
	
	// 	last 5 Matches
	$last5Matches = $MatchDetails->getLastMatchDetailsOfPlayer2($steamID, 5, "DESC");
	
	// User Credits auslesen
	$UserCredits = new UserCredits();
	$retCredits = $UserCredits->getCreditCountOfPlayer($steamID);
	
	// best Results auslesen
	$Ladder = new Ladder();
	$bestRankings = $Ladder->getBestRankings($steamID, 5);


	// get Notifications
	//$Profile = new Profile();
	//$notificationData = $Profile->getNotifications($steamID);

	// get All Matchmodes
	$matchmodes = $MatchMode->getAllMatchModes();
	
	// get all Matchtypes
	$matchtypes = $MatchType->getAllMatchTypes();
	
	// ProgressBar
	//$retLT = $LeagueType->getDataForProgressBar($steamID, $retStats['data']['LeagueData']['LeagueTypeID'], $retStats['data']['LeagueLvl'], $retStats['data']['Points']);

	// requirements For Next Bracket Data
	$SkillBracket = new SkillBracket();
	$retSB = $SkillBracket->getRequirementsForNextSkillBracket($steamID, 1); // single 5vs5
	$retSB1vs1 = $SkillBracket->getRequirementsForNextSkillBracket($steamID, 8); // 1vs1
// 	$retSB['data']['neededTotalGames'] = 42;
// 	$retSB['data']['neededWinRate'] = 2;
// 	$retSB['data']['neededCredits'] = 12;
// 	$retSB['data']['currentGames'] = 58;
// 	$retSB['data']['currentWinRate'] = 40;
// 	$retSB['data']['currentCredits'] = -11;
// 	$retSB['data']['nextTotalGames'] = 100;
// 	$retSB['data']['nextWinRate'] = 48;

	// get User Skill-Bracket
	$UserSkillBracket = new UserSkillBracket();
	$retUSB = $UserSkillBracket->getSkillBracketOfUser($steamID,1); // single 5vs5
	$retUSB1vs1 = $UserSkillBracket->getSkillBracketOfUser($steamID,8); // 1vs1
	/* 
	 * Teams
	*/
		// get DUO List
		$Group = new Group();
		$retDuoGr = $Group->getGroupsOfUser2(0, false, true);
		$retDuoGrOpen = $Group->getOpenTeams(0, false);
		$retInv = $Group->getAllInvitesOfUser();
		
	$Banlist = new Banlist();
	$retB1 = $Banlist->getAllBansOfPlayer($steamID);
	$retB2 = $Banlist->getAllBansOfPlayer($steamID, true);
	$retPB = $Banlist->isUserPermaBanned($steamID);
	
	// general ranking
	$generalRanking = $Ladder->getGeneralRankingSkillBracket($steamID, $retStats['data']['Points'],$retUSB['data']['SkillBracketTypeID'], 1);
	
	
	// Refer a Friend
	$ReferAFriend = new ReferAFriend();
	$retRAF = $ReferAFriend->generateLink();
	
	$retRAF2 = $ReferAFriend->getReferedCount();
	$smarty->assign('RAFCoinBonus', ReferAFriend::refererCoinBonus);
	$smarty->assign('RAFCountBorder', ReferAFriend::refererCountBorder);
	if(NEWPROFILE){
		$MatchMode = new MatchMode();
		$retMM = $MatchMode->getMostMatchmodesPlayedOfUser($steamID);
		
		$MatchDetailsReplay = new MatchDetailsReplay();
		$retMDR = $MatchDetailsReplay->getMostPlayedHeroesOfUser($steamID);
		
		$retMDR2 = $MatchDetailsReplay->getGameStatsOfUser($steamID);
		
		$retMDR3 = $MatchDetailsReplay->getMatchStatsOfUser($steamID);
		
		// 1vs1 ranking
		$ranking1vs1 = $Ladder->getGeneralRankingSkillBracket($steamID, $retStats['data']['Points'],$retUSB['data']['SkillBracketTypeID'], 8);
		
		if (!$besucher) {
			// Backpack Items
			$UserBackpack = new UserBackpack();
			$retUB = $UserBackpack->getAllBackpackItemsOfUser($steamID);
			
		}
		$smarty->assign('matchModesStatsData', $retMM['data']);
		$smarty->assign('heroesStatsData', $retMDR['data']);
		$smarty->assign('gameStats', $retMDR2['data']);
		$smarty->assign('matchStats', $retMDR3['data']);
		$smarty->assign('ranking1vs1', $ranking1vs1['position']);
		$smarty->assign('userBackpackItems', $retUB['data']);
	}
	
	// Assign Smarty Variables
	$smarty->assign('steamID', $steamID);
	$smarty->assign('userData', $userData);
	$smarty->assign('userStats', $retStats['data']);
	$smarty->assign('last5Matches', $last5Matches['data']);
	$smarty->assign('bestRankings', $bestRankings['data']);
// 	$smarty->assign('notificationData', $notificationData['data']);
// 	$smarty->assign('notificationCount', $notificationData['count']);
	$smarty->assign('ranking', $generalRanking['position']);
	$smarty->assign('matchmodes', $matchmodes);
	$smarty->assign('matchtypes', $matchtypes);
	$smarty->assign('userCredits', $retCredits['data']);
	$smarty->assign('progressBarData', $retLT['data']);
	$smarty->assign('leagueData', $retULLeague['data']);
	$smarty->assign('leagueName', $retUSB['data']['Name']);
	$smarty->assign('leagueName1vs1', $retUSB1vs1['data']['Name']);
	$smarty->assign('duoTeamListItems', $retDuoGr['data']);
	$smarty->assign('invites', $retInv['data']);
	$smarty->assign('openTeams', $retDuoGrOpen['data']);
	$smarty->assign('requirementsForNextBracketData', $retSB['data']);
	$smarty->assign('requirementsForNextBracketData1vs1', $retSB1vs1['data']);
	$smarty->assign('skillBracketTypeID', $retUSB['data']['SkillBracketTypeID']);
	$smarty->assign('skillBracketTypeID1vs1', $retUSB1vs1['data']['SkillBracketTypeID']);
	$smarty->assign('activeWarns', $retB2['data']);
	$smarty->assign('warnHistory', $retB1['data']);
	$smarty->assign('warnsCount', $retB1['count']);
	$smarty->assign('activeWarnsCount', $retB2['count']);
	$smarty->assign('isPermaBanned', $retPB['status']);
	$smarty->assign('refererLink', $retRAF['data']);
	$smarty->assign('referedCount', (int)$retRAF2['data']);
	$smarty->assign('inviteCount', (int)$retRAF2['inviteCount']);
	$smarty->assign('besucher', $besucher);
	// Debug
	$_SESSION['debug'] = "";
//	$_SESSION['debug'] .= p($notificationData,1);
// 	$_SESSION['debug'] .= p($bestRankings,1);
 	$_SESSION['debug'] = p($retMDR3,1);
// 	$_SESSION['debug'] .= p($retULLeagueLvl,1);
// 	$_SESSION['debug'] .= p($last5Matches,1);
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
	 			$smarty->display('profile/index.tpl');
		?>
		</div> <!-- /container -->
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