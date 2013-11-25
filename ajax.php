<?php
session_start ();

$pfadTiefe = str_replace ( $_SERVER ['DOCUMENT_ROOT'], "", dirname ( __FILE__ ) ); // alle nötigen PHP funktionen einfügen
$slashCount = substr_count ( $pfadTiefe, '/' ); // Zählen wieviele Slashes drinne sind
$ebeneTiefer = "";
for($i = 0; $i < $slashCount; $i ++) { // für jeden slash eine ebene tiefergehen
	$ebeneTiefer .= DIRECTORY_SEPARATOR . '..';
}

$pfad = dirname ( __FILE__ ) . $ebeneTiefer;
require_once ("inc/inc_general_php_functions_for_ajax.php");
$ret = array();
switch ($_POST ['type']) {
	case "ladder" :
		switch ($_POST ['mode']) {
			case "loadLadderDataTable" :
				$Ladder = new Ladder ();
				$ret = $Ladder->loadLadderDataTable ( $_POST ['ID'], $_POST ['matchModeID'], $_POST ['matchTypeID'], $_POST ['section'], $smarty );
				break;
					
			case "loadLadderDataTable1vs1Ladder" :
				$OneVsOneLadder = new OneVsOneLadder ();
				$ret = $OneVsOneLadder->loadLadderDataTable ( $_POST ['ID'], $_POST ['MatchModeID'], $smarty );
				break;
			case "loadLadderDataTable3vs3Ladder" :
				$ThreeVsThreeLadder = new ThreeVsThreeLadder ();
				$ret = $ThreeVsThreeLadder->loadLadderDataTable ( $_POST ['ID'], $_POST ['MatchModeID'], $smarty );
				break;
			case "getGeneralLadderPlayers" :
				$SingleLadder = new SingleLadder ();
				$ret = $SingleLadder->getGeneralLadderPlayers ( $_POST ['ID'], $_POST ['reload'], $smarty );
				break;
			case "getBestPlayersFetchedData" :
				$Ladder = new Ladder ();
				$ret = $Ladder->getBestPlayersFetchedData ( $smarty, $_POST ['mmID'] );
				break;
		}

	case "test" :
		switch ($_POST ['mode']) {

			case "handleFakeLogin" :
				$TestLogin = new TestLogin ();
				$ret = $TestLogin->handleFakeLogin ( $_POST ['ID'] );
				break;
		}
		break;
	case "matchTeams" :
		switch ($_POST ['mode']) {

			case "cleanMatchTeamsOfPlayer" :
				$MatchTeams = new MatchTeams ();
				$ret = $MatchTeams->cleanMatchTeamsOfPlayer ();
				break;
			case "checkAlreadyInMatchTeams" :
				$MatchTeams = new MatchTeams ();
				$ret = $MatchTeams->checkIfPlayerAlreadyInMatchTeams ();
				break;
		}
		break;
	case "login" :
		switch ($_POST ['mode']) {

			case "handleLogout" :
				$Login = new Login ();
				$ret = $Login->handleLogout2();
				break;
		}
		break;
	case "user" :
		switch ($_POST ['mode']) {

			case "addRegion" :
				$User = new User ();
				$ret = $User->addRegion ( $_POST ['region'] );
				break;
			case "getAllUser" :
				$User = new User ();
				$ret = $User->getAllUser ( $_POST ['alsoSelf'], $_POST ['query'], $_POST ['typeahead'], 30, $_POST['justYourLeague']);
				break;
			case "fetchUserTable" :
				$User = new User ();
				$ret = $User->fetchUserTable ( $_POST ['data'], $smarty );
				break;
		}
		break;
	case "queue" :
		switch ($_POST ['mode']) {

			case "joinSingleQueue" :
				$SingleQueue = new SingleQueue ();
				$ret = $SingleQueue->joinQueue ();
				break;
			case "joinSingleQueue2" :
				$SingleQueue = new SingleQueue ();
				$ret = $SingleQueue->joinQueue2 ( $_POST ['modi'], $_POST ['region'] );
				break;
			case "leaveQueue" :
				$Queue = new Queue ();
				$ret = $Queue->leaveQueue ();
				break;
			case "leaveQueue2" :
				$Queue = new Queue ();
				$ret = $Queue->leaveQueue2 ();
				break;
			case "updateForceSearch" :
				$Queue = new Queue ();
				$ret = $Queue->updateForceSearch ( $_POST ['forceSearch']);
				break;
			case "kickAllPlayersOutOfQueue" :
				$Queue = new Queue ();
				$ret = $Queue->kickAllPlayersOutOfQueue ( $_POST ['ID'], $_POST ['RS'] );
				break;
			case "join1vs1Queue" :
				$OneVsOneQueue = new OneVsOneQueue ();
				$ret = $OneVsOneQueue->joinQueue ( $_POST ['modi'], $_POST ['region'] );
				break;
			case "join3vs3Queue" :
				$ThreeVsThreeQueue = new ThreeVsThreeQueue ();
				$ret = $ThreeVsThreeQueue->joinQueue ( $_POST ['modi'], $_POST ['region'] );
				break;
			case "inQueue" :
				$Queue = new Queue ();
				$ret = $Queue->inQueue ();
				break;
			case "checkJoinQueue" :
				$Queue = new Queue ();
				$ret = $Queue->checkJoinQueue();
				break;
		}

		break;
	case "matchmaking" :
		switch ($_POST ['mode']) {
			case "search" :
				$Matchmaking = new Matchmaking ();
				$ret = $Matchmaking->search ( $_POST ['nr'], $_POST ['leave'] );
				break;
			case "singleQueueSearch" :
				$Matchmaking = new Matchmaking ();
				$ret = $Matchmaking->singleQueueSearch ( $_POST ['modi'], $_POST ['region'], $_POST ["forceSearch"] );
				break;
			case "singleQueueSearch2" :
				$Matchmaking = new Matchmaking ();
				$ret = $Matchmaking->singleQueueSearch2 ( $_POST ['modi'], $_POST ['region'], $_POST ["forceSearch"] );
				break;
			case "oneVsOneQueueSearch" :
				$Matchmaking = new Matchmaking ();
				$ret = $Matchmaking->oneVsOneQueueSearch ( $_POST ['modi'], $_POST ['region'], $_POST ["forceSearch"] );
				break;
			case "threeVsThreeQueueSearch" :
				$Matchmaking = new Matchmaking ();
				$ret = $Matchmaking->threeVsThreeQueueSearch ( $_POST ['modi'], $_POST ['region'], $_POST ["forceSearch"] );
				break;
			case "cleanDBMatchmaking" :
				$Matchmaking = new Matchmaking ();
				$ret = $Matchmaking->cleanDBMatchmaking ( $_POST ['ID'] );
				break;
			case "cleanEverything":
				$Matchmaking = new Matchmaking ();
				$ret = $Matchmaking->cleanEverything($_POST['matchID'], $_POST['groupID'], $_POST['reason']);
				break;
		}
		break;

	case "match" :
		switch ($_POST ['mode']) {
			case "accept" :
				$Match = new Match ();
				$ret = $Match->acceptMatch ( $_POST ['ID'] );
				break;
			case "decline" :
				$Match = new Match ();
				$ret = $Match->declineMatch ( $_POST ['ID'] );
				break;
			case "checkAllReadyForMatch" :
				$Match = new Match ();
				$ret = $Match->checkAllReadyForMatch ( $_POST ['ID'] );
				$SmartyFetch = new SmartyFetch ();
				$htmlRet = $SmartyFetch->fetchWaitingForOtherPlayersTPL ( $smarty, $ret ['data'] );
				$ret ['html'] = $htmlRet ['data'];
				break;
			case "deleteCreatedMatch" :
				$Match = new Match ();
				$ret = $Match->deleteCreatedMatch ( $_POST ['ID'] );
				break;
			case "submitResult" :
				$Match = new Match ();
				$ret = $Match->submitResult ( $_POST ['value'], $_POST ['ID'], $_POST ['screenshot'], $_POST ['leaver'] );
				break;
			case "saveMatchDetails" :
				$Match = new Match ();
				$ret = $Match->saveMatchDetails ( $_POST ['ID'] );
				break;
			case "cancelMatch" :
				$Match = new Match ();
				$ret = $Match->cancelMatch ( $_POST ['ID'], $_POST ['array'], $_POST ['reason'] );
				break;
			case "cancelMatchHard" :
				$Match = new Match ();
				$ret = $Match->cancelMatchHard ( $_POST ['ID'] );
				break;
			case "getLastPlayedMatches" :
				$Match = new Match ();
				$ret = $Match->getLastPlayedMatches ( $_POST ['mmID'], $smarty );
				break;
		}
		break;
	case "matchDetails" :
		switch ($_POST ['mode']) {
			case "checkForStrangeSubmissions" :
				$MatchDetails = new MatchDetails ();
				$ret = $MatchDetails->checkForStrangeSubmissions ( $_POST ['ID'] );
				break;
			case "getGeneralWinRateTrendData" :
				$MatchDetails = new MatchDetails ();
				$ret = $MatchDetails->getGeneralWinRateTrendData ( $_SESSION ['tmp'] ['steamID'] );
				break;
			case "getFullGeneralWinRateTrendData" :
				$MatchDetails = new MatchDetails ();
				$ret = $MatchDetails->getGeneralWinRateTrendData ( $_SESSION ['tmp'] ['steamID'], "*" );
				break;
			case "getEloHistoryData" :
				$MatchDetails = new MatchDetails ();
				$ret = $MatchDetails->getEloHistoryData ( $_POST ['matchmode'], $_POST ['matchtype'], $_POST ['count'], $_POST ['ID'] );
				break;
			case "deleteMatchDetails" :
				$MatchDetails = new MatchDetails ();
				$ret = $MatchDetails->deleteMatchDetails ( $_POST ['ID'] );
				break;
		}
		break;
	case "uploads" :
		switch ($_POST ['mode']) {
			case "moveScreenshotFile" :
				$Uploads = new Uploads ();
				$steamID = $_SESSION ['user'] ["steamID"];
				$ret = $Uploads->moveScreenshotFile ( $_POST ['ID'], $steamID, $_POST ['fileName'] );
				break;
			case "moveReplayFile" :
				$Uploads = new Uploads ();
				$steamID = $_SESSION ['user'] ["steamID"];
				$ret = $Uploads->moveReplayFile ( $_POST ['ID'], $steamID, $_POST ['fileName'] );
				break;
		}

		break;
	case "chat" :
		switch ($_POST ['mode']) {
			case "matchChat" :
				$Chat = new Chat ();
				$ret = $Chat->handleChat ( $_POST ['function'], $_POST ['state'], $_POST ['text'], $_POST ['file'], $_POST ['message'], $smarty );
				break;
			case "updateChat" :
				$Chat = new Chat2 ( $_POST ['section'], $_POST ['special'] );
				$ret = $Chat->updateChat ( $smarty, $_POST ['lines'] );
				break;
			case "postComment" :
				$Chat = new Chat2 ( $_POST ['section'], $_POST ['special'] );
				$ret = $Chat->postComment2 ( $_POST ['msg'] );
				break;
		}

		break;
	case "userElo" :
		switch ($_POST ['mode']) {
			case "getEloRoseData" :
				$UserElo = new UserElo ();
				$ret = $UserElo->getEloRoseData ( $_POST ['sID'] );
				break;
			case "insertFirstEloForMatchType" :
				$UserElo = new UserElo ();
				$ret = $UserElo->insertFirstEloForMatchType ( $_POST ['ID'] );
				break;
			case "checkIfUserHaveEloForMatchType" :
				$UserElo = new UserElo ();
				$ret = $UserElo->checkIfUserHaveEloForMatchType ( $_POST ['ID'], $_POST ['steamID'] );
				break;
		}
		break;
	case "userPoints" :
		switch ($_POST ['mode']) {
			case "getPointRoseData" :
				$UserPoints = new UserPoints ();
				$ret = $UserPoints->getPointRoseData ( $_POST ['sID'] );
				break;
			case "getPointsHistoryData" :
				$UserPoints = new UserPoints ();
				$ret = $UserPoints->getPointsHistoryData ( $_POST ['matchmode'], $_POST ['matchtype'], $_POST ['count'], $_POST ['ID'] );
				break;
		}
		break;
	case "MatchDetailsHostLobby" :
		switch ($_POST ['mode']) {
			case "setLobbyHostForMatch" :
				$MatchDetailsHostLobby = new MatchDetailsHostLobby ();
				$ret = $MatchDetailsHostLobby->setLobbyHostForMatch ( $_POST ['ID'] );
				break;
			case "cleanHostForMatch" :
				$MatchDetailsHostLobby = new MatchDetailsHostLobby ();
				$ret = $MatchDetailsHostLobby->cleanHostForMatch ( $_POST ['ID'] );
				break;
		}

		break;
	case "UserVotes" :
		switch ($_POST ['mode']) {
			case "insertVote" :
				$UserVotes = new UserVotes ();
				$ret = $UserVotes->insertVote ( $_POST ['sID'], $_POST ['vtID'], $_POST ['mID'], $_POST ['t'] );
				break;
		}
		break;
	case "banlist" :
		switch ($_POST ['mode']) {
			case "checkForBansOfPlayer" :
				$Banlist = new Banlist ();
				$ret = $Banlist->checkForBansOfPlayer ();
				break;
			case "getCurrentBanDataOfPlayer" :
				$Banlist = new Banlist ();
				$ret = $Banlist->getCurrentBanDataOfPlayer ();
				break;
			case "cleanBansOfPlayer" :
				$Banlist = new Banlist ();
				$ret = $Banlist->cleanBansOfPlayer ();
				break;
		}
		break;
	case "group" :
		switch ($_POST ['mode']) {
			case "createDuoGroup" :
				$Group = new Group ();
				$ret = $Group->createDuoGroupSkillBracket( $_POST ['partnerID'], $_POST ['name'] );
				break;
			case "getGroupsOfUser2" :
				$Group = new Group ();
				$ret = $Group->getGroupsOfUser2 ( 0, $smarty, true);
				break;
			case "getOpenTeams" :
				$Group = new Group ();
				$ret = $Group->getOpenTeams( 0, $smarty);
				break;
			case "deleteTeam" :
				$Group = new Group ();
				$ret = $Group->deleteTeamOfUser ( $_POST ['ID'] );
				break;
			case "editTeamName" :
				$Group = new Group ();
				$ret = $Group->editTeamName ( $_POST ['ID'], $_POST ['name'] );
				break;
			case "acceptTeamInvite" :
				$Group = new Group ();
				$ret = $Group->changeAcceptedOfGroup( $_POST ['ID'], 1 );
				break;
			case "declineTeamInvite" :
				$Group = new Group ();
				$ret = $Group->changeAcceptedOfGroup( $_POST ['ID'], -1 );
				break;
			case "loadTeamInvites" :
				$Group = new Group ();
				$ret = $Group->getAllInvitesOfUser(0,$smarty);
				break;
		}
		break;
	case "QueueGroup" :
		switch ($_POST ['mode']) {
			case "joinQueueAsGroup" :
				$SingleQueueGroup = new SingleQueueGroup ();
				$ret = $SingleQueueGroup->joinQueueAsGroup ( $_POST ['ID'], $_POST ['MM'], $_POST ['regions'] );
				break;
			case "cleanQueueGroup" :
				$SingleQueueGroup = new SingleQueueGroup ();
				$ret = $SingleQueueGroup->cleanQueueGroup ( $_POST ['ID'] );
				break;
			case "checkIfAlreadyInQueueWithGroup" :
				$SingleQueueGroup = new SingleQueueGroup ();
				$ret = $SingleQueueGroup->checkIfAlreadyInQueueWithGroup ();
				break;
			case "checkAbleToJoinDuo" :
				$SingleQueueGroup = new SingleQueueGroup ();
				$ret = $SingleQueueGroup->checkAbleToJoinDuo($_POST['groupID']);
				break;
		}
		break;
	case "EventSubmissions" :
		switch ($_POST ['mode']) {
			case "joinEvent" :
				$EventSubmissions = new EventSubmissions ();
				$ret = $EventSubmissions->joinEvent ( $_POST ['ID'] );
				break;
			case "singOutPlayerOfEvent" :
				$EventSubmissions = new EventSubmissions ();
				$ret = $EventSubmissions->singOutPlayerOfEvent ( $_POST ['ID'] );
				break;
			case "setReadyStatus" :
				$EventSubmissions = new EventSubmissions ();
				$ret = $EventSubmissions->setReadyStatus ( $_POST ['event'], $_POST ['status'], $_POST ['steamID'] );
				break;
		}
		break;
	case "RaceWinner" :
		switch ($_POST ['mode']) {
			case "setSelectedPrize" :
				$RaceWinner = new RaceWinner ();
				$ret = $RaceWinner->setSelectedPrize ( $_POST ['ID'], $_POST['raceID']);
				break;
		}
		break;
	case "UserLeague" :
		switch ($_POST ['mode']) {
			case "getLeagueOfUser" :
				$UserLeague = new UserLeague ();
				$ret = $UserLeague->getLeagueOfUser ();
				break;
		}
		break;
	case "QueueLock" :
		switch ($_POST ['mode']) {
			case "insertLock" :
				$QueueLock = new QueueLock ();
				$ret = $QueueLock->insertLock ();
				break;
			case "checkLock" :
				$QueueLock = new QueueLock ();
				$ret = $QueueLock->checkLock ();
				break;
		}
		break;
	case "userNotification" :
		switch ($_POST ['mode']) {
			case "setUserNotificationAsChecked" :
				$UserNotification = new UserNotification();
				$ret = $UserNotification->setUserNotificationAsChecked($_POST['ID']);
				break;
			case "sendNotificationToDuoPartner" :
				$UserNotification = new UserNotification();
				$ret = $UserNotification->sendNotificationToDuoPartner($_POST['groupID'], $smarty);
				break;
			case "sendPingNotification":
				$UserNotification = new UserNotification();
				$ret = $UserNotification->sendPingNotification($_POST['steamID'], $_POST['matchID']);
				break;
		}
		break;
	case "Notification" :
		switch ($_POST ['mode']) {
			case "getAllNotificationsForUser" :
				$Notification = new Notification();
				$ret = $Notification->getAllNotificationsForUser();
				break;
		}
		break;
	case "constant" :
		switch ($_POST ['mode']) {
			case "justCM" :
				$ret['data'] = JUSTCM;
				break;
		}
		break;
}

switch ($_GET ['type']) {
	case "ladder" :
		switch ($_GET ['mode']) {
			case "loadLadderDataTable2" :
				$Ladder = new Ladder ();
				$ret = $Ladder->loadLadderDataTable2( $_GET ['ID'], $_GET['MMID'], $_GET ['MTID'], $_GET ['section'], $smarty);
				break;
			case "loadLadderDataTableSkillBracket" :
				$Ladder = new Ladder ();
				$ret = $Ladder->loadLadderDataTableSkillBracket( $_GET ['ID'], $_GET['MMID'], $_GET ['MTID'], $_GET ['section'], $smarty);
				break;
		}
		break;
}

if (! DEBUG) {
	unset ( $ret ['debug'] );
}

echo json_encode ( $ret );
?>