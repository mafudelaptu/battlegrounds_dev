<?php
session_start();
$pfadTiefe = str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__FILE__) ); // alle nötigen PHP funktionen einfügen
$slashCount = substr_count($pfadTiefe, '/'); // Zählen wieviele Slashes drinne sind
$ebeneTiefer = "";
for($i=0; $i<$slashCount; $i++){ // für jeden slash eine ebene tiefergehen
	$ebeneTiefer .= DIRECTORY_SEPARATOR . '..';
}

$pfad = dirname(__FILE__).$ebeneTiefer;
require_once("inc/inc_general_php_functions_for_ajax.php");
$ret = array();
switch($_POST['type']){
	case "fakeUser":
		switch($_POST['mode']){
			case "fakeSubmittsSimulieren":
				$FakeUser = new FakeUser();
				$ret = $FakeUser->fakeSubmittsSimulieren($_POST['ID'], $_POST['teamWon']);
				break;
			case "resetSubmissions":
				$FakeUser = new FakeUser();
				$ret = $FakeUser->resetSubmissions($_POST['ID']);
				break;
			case "insertUser":
				$FakeUser = new FakeUser();
				$ret = $FakeUser->insertUser($_POST['ID']);
				break;
			case "insertRandomUserinQueue":
				$FakeUser = new FakeUser();
				$ret = $FakeUser->insertRandomUserinQueue($_POST['ID']);
				break;
			case "submitAllMatchAccept":
				$FakeUser = new FakeUser();
				$ret = $FakeUser->submitAllMatchAccept($_POST['ID']);
				break;
		}
		break;
	case "cronjobs":
		switch($_POST['mode']){
			case "cronjobMatchResultsSubmit":
				$ret = get_include_contents('cronjobs/match/matchResultHandling.php');
				break;
			case "cronjobDoAllCronjobs":
				$ret = get_include_contents('cronjobs/doAllCronjobs.php');
				break;
		}
		break;


	case "fakeMatches":
		switch($_POST['mode']){
			case "unitTestMatches":
				$FakeMatches = new FakeMatches();
				$ret = $FakeMatches->unitTestMatches();
				break;
			case "insertRandomTestMatches":
				$FakeMatches = new FakeMatches();
				$ret = $FakeMatches->insertRandomTestMatches();
				break;
		}
		break;

	case "banlist":
		switch($_POST['mode']){
			case "insertBan":
				$Banlist = new Banlist();
				$ret = $Banlist->insertBan($_POST['ID'], 2, $_POST['reason']);
				break;
			case "disableLastBan":
				$Banlist = new Banlist();
				$ret = $Banlist->cleanBansOfPlayer($_POST['ID']);
				break;
			case "removeLastBan":
				$Banlist = new Banlist();
				$ret = $Banlist->removeLastBan($_POST['ID']);
				break;
			case "getBannedPlayers":
				$Banlist = new Banlist();
				$ret = $Banlist->getBannedPlayers(true, $smarty);
				break;
			case "deletePermaBan":
				$Banlist = new Banlist();
				$ret = $Banlist->deletePermaBan($_POST['steamID']);
				break;
		}
		break;
	case "chat" :
		switch ($_POST ['mode']) {
			case "updateChat" :
				$Chat = new Chat2 ( $_POST ['section'], $_POST ['special'] );
				$ret = $Chat->updateChat ( $smarty, $_POST ['lines'] );
				break;
			case "postComment" :
				$Chat = new Chat2 ( $_POST ['section'], $_POST ['special'] );
				$ret = $Chat->postComment ( $smarty, $_POST ['msg'] );
				break;
		}
	case "UserPoints":
		switch ($_POST ['mode']) {
			case "getUserPointsData" :
				$UserPoints = new UserPoints();
				$ret = $UserPoints->getAllPointsEntriesOfUserForMatch($_POST['matchID'], $_POST['steamID']);
				$SmartyFetch = new SmartyFetch();
				$retSF = $SmartyFetch->fetchEditUserPointsTPL($smarty, $ret['data']);
				$ret['html'] = $retSF['data'];
				break;
			case "saveUserPointChanges" :
				$UserPoints = new UserPoints();
				$ret = $UserPoints->saveUserPointChanges($_POST['matchID'], $_POST['steamID'], $_POST['data']);
				break;
			case "deleteAllUserPoints" :
				$UserPoints = new UserPoints();
				$ret = $UserPoints->deleteAllUserPoints($_POST['matchID'], $_POST['steamID']);
				break;
		}
		break;
	case "SmartyFetch":
		switch ($_POST ['mode']) {
			case "fetchPointsTypeSelectTPL" :
				$SmartyFetch = new SmartyFetch();
				$ret = $SmartyFetch->fetchPointsTypeSelectTPL($smarty);
				break;
		}
		break;
	case "MatchDetailsLeaverVotes":
		switch ($_POST ['mode']) {
			case "markUserAsLeaver" :
				$MatchDetailsLeaverVotes = new MatchDetailsLeaverVotes();
				$ret1 = $MatchDetailsLeaverVotes->insertLeaverVote( $_POST['matchID'], $_POST['steamID'], $_SESSION['user']['steamID']);
				$ret2 = $MatchDetailsLeaverVotes->insertLeaverVote( $_POST['matchID'], $_POST['steamID'], 1);
				$ret3 = $MatchDetailsLeaverVotes->insertLeaverVote( $_POST['matchID'], $_POST['steamID'], 2);
				$ret4 = $MatchDetailsLeaverVotes->insertLeaverVote( $_POST['matchID'], $_POST['steamID'], 3);
				$ret5 = $MatchDetailsLeaverVotes->insertLeaverVote( $_POST['matchID'], $_POST['steamID'], 4);
				$ret6 = $MatchDetailsLeaverVotes->insertLeaverVote( $_POST['matchID'], $_POST['steamID'], 5);
				$ret['debug'] .= p($ret6,1);

				$tmpArray = array(
						array(
								"value" => 0,
								"pointsType" => 5
						)
				);

				$UserPoints = new UserPoints();
				$retUP = $UserPoints->insertLeave($_POST['matchID'], $_POST['steamID']);
				$ret['debug'] .= p($retUP,1);

				$ret['status'] = true;
				break;
			case "demarkUserAsLeaver" :
				$MatchDetailsLeaverVotes = new MatchDetailsLeaverVotes();
				$retMDLV = $MatchDetailsLeaverVotes->deleteAllVotesOfLeaver($_POST['matchID'], $_POST['steamID']);
				$ret['debug'] .= p($retMDLV,1);

				$UserPoints = new UserPoints();
				$retUP = $UserPoints->deletePointsTypeOfMatchOfUser($_POST['matchID'], $_POST['steamID'], 5);
				$ret['debug'] .= p($retUP,1);

				$ret['status'] = true;
				break;
		}
		break;
	case "news":
		switch ($_POST ['mode']) {
			case "createNewNews" :
				$News = new News();
				$ret = $News->createNewNews($_POST['title'], $_POST['content'], $_POST['order'],$_POST['active'], $_POST['showDate'], $_POST['endDate']);
				break;
			case "editNews" :
				$SmartyFetch = new SmartyFetch();
				$ret = $SmartyFetch->fetchEditNewsTPL($smarty, $_POST['id']);
				break;
			case "updateNews" :
				$News = new News();
				$ret = $News->updateNews( $_POST['id'], $_POST['title'], $_POST['content'], $_POST['order'],$_POST['active'], $_POST['showDate'], $_POST['endDate']);
				break;
			case "toggleActiveNews" :
				$News = new News();
				$ret = $News->toggleActiveNews($_POST['id'], $_POST['active']);
				break;
			case "deleteNews" :
				$News = new News();
				$ret = $News->deleteNews($_POST['id']);
				break;

		}
		
		break;
		case "notification":
			switch ($_POST ['mode']) {
				case "createNewNotification" :
					$GloabalNotification = new GlobalNotification();
					$ret = $GloabalNotification->createNewNotification($_POST['notificationTypeID'], $_POST['text'], $_POST['link'], $_POST['active']);
					break;
					case "editNotification" :
						$SmartyFetch = new SmartyFetch();
						$ret = $SmartyFetch->fetchEditNotificationTPL($smarty, $_POST['id']);
						break;
					case "updateNotification" :
						$GlobalNotification = new GlobalNotification();
						$ret = $GlobalNotification->updateNotification( $_POST['id'], $_POST['notificationTypeID'], $_POST['text'], $_POST['link'], $_POST['active']);
						break;
					case "toggleActiveNotification" :
						$GlobalNotification = new GlobalNotification();
						$ret = $GlobalNotification->toggleActiveNotification($_POST['id'], $_POST['active']);
						break;
					case "deleteNotification" :
						$GlobalNotification = new GlobalNotification();
						$ret = $GlobalNotification->deleteNotification($_POST['id']);
						break;
			}
			break;
}
echo json_encode($ret);
?>