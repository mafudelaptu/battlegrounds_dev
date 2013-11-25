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
<title>Dota2 Lone Wolf League</title>
<?php
require_once(DR.HOME_PATH_REL."/inc/inc_css_and_js.php");
?>
<script type="text/javascript" charset="utf-8">

	function test1fkt(){
		l("testsetest");
		return $.ajax({
			url : 'ajax.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "QueueLock",
				mode : "checkLock"
			}});
	}

	function test2fkt(){
		l("22222222222");
		return $.ajax({
			url : 'ajax.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "banlist",
				mode : "checkForBansOfPlayer"
			}});
	}

    $(document).ready(function(){
    	//initChat("match","123", "adminChat"); /* Start the inital request */
    	var test1 = $.ajax({
					url : 'ajax.php',
					type : "POST",
					dataType : 'json',
					data : {
						type : "QueueLock",
						mode : "checkLock"
					},
					success: function(data, text){
						l("TEXT:"+text);
						text = "error";
					}
					});
		var test2 = $.ajax({
					url : 'ajax.php',
					type : "POST",
					dataType : 'json',
					data : {
						type : "banlist",
						mode : "checkForBansOfPlayer"
					}});
		test1fkt().then(function(data){
					if(data.status == true){
						l("before");
						return test2fkt();
					}
			}).then(function(data2){
					l(data2);
			});
			
    });
    </script>
</head>

<body>
	<div id="wrap">
		<?php 
		$smarty->display('top_navi.tpl');
		?>
		<div class="container">

			<?php 

			$DB = new DB();
			$con = $DB->conDB();
			$matchID = 63;
			$teamWonID = 1;
			$steamID = 76561198047012055;
			$result = -1;
			$Match = new Match();
			$UserStats = new UserStats();
			$UserElo = new UserElo();

			// 	$ret = $Match->getPlayersWhoLeftTheMatch($matchID);

			// 	$ret = $UserStats->updateGeneralUserStats($steamID, $result, $matchID);
			// 	$ret = $UserElo->getAllEloValuesOfUser($steamID, true,true);
			p($ret);

			$array[0]['SteamID'] = 76561197998273695;
			$array[0]['Elo'] = 3459;
			$array[0]['MatchModeID'] = 1;
			$array[0]['Region'] = 1;
			$array[0]['MatchTypeID'] = 1;

			$array[1]['SteamID'] = 76561197985307740;
			$array[1]['Elo'] = 1364;
			$array[1]['MatchModeID'] = 1;
			$array[1]['Region'] = 1;
			$array[1]['MatchTypeID'] = 1;

			$array[2]['SteamID'] = 76561198017020628;
			$array[2]['Elo'] = 865;
			$array[2]['MatchModeID'] = 1;
			$array[2]['Region'] = 1;
			$array[2]['MatchTypeID'] = 1;

			$array[3]['SteamID'] = 76561198006011871;
			$array[3]['Elo'] = 1059;
			$array[3]['MatchModeID'] = 1;
			$array[3]['Region'] = 1;
			$array[3]['MatchTypeID'] = 1;
			$array[3]['GroupID'] = 15;

			$array[4]['SteamID'] = 76561198054646254;
			$array[4]['Elo'] = 755;
			$array[4]['MatchModeID'] = 1;
			$array[4]['Region'] = 1;
			$array[4]['MatchTypeID'] = 1;
		//	$array[4]['GroupID'] = 14;

			$array[5]['SteamID'] = 76561197981964972;
			$array[5]['Elo'] = 1003;
			$array[5]['MatchModeID'] = 1;
			$array[5]['Region'] = 1;
			$array[5]['MatchTypeID'] = 1;
			$array[5]['GroupID'] = 15;

			$array[6]['SteamID'] = 76561197975878655;
			$array[6]['Elo'] = 623;
			$array[6]['MatchModeID'] = 1;
			$array[6]['Region'] = 1;
			$array[6]['MatchTypeID'] = 1;
			//$array[6]['GroupID'] = 14;

			$array[7]['SteamID'] = 76561198013529151;
			$array[7]['Elo'] = 613;
			$array[7]['MatchModeID'] = 1;
			$array[7]['Region'] = 1;
			$array[7]['MatchTypeID'] = 1;

			$array[8]['SteamID'] = 76561198065806611;
			$array[8]['Elo'] = 583;
			$array[8]['MatchModeID'] = 1;
			$array[8]['Region'] = 1;
			$array[8]['MatchTypeID'] = 1;

			$array[9]['SteamID'] = 76561198063457962;
			$array[9]['Elo'] = 855;
			$array[9]['MatchModeID'] = 1;
			$array[9]['Region'] = 1;
			$array[9]['MatchTypeID'] = 1;

			// 	$array[10]['SteamID'] = 76561198056439830;
			// 	$array[10]['Elo'] = 1299;
			// 	$array[10]['MatchModeID'] = 1;
			// 	$array[10]['Region'] = 1;
			// 	$array[10]['MatchTypeID'] = 1;

			// 	$array[11]['SteamID'] = 76561198065060643;
			// 	$array[11]['Elo'] = 1215;
			// 	$array[11]['MatchModeID'] = 1;
			// 	$array[11]['Region'] = 1;
			// 	$array[11]['MatchTypeID'] = 1;

			// 	$array[12]['SteamID'] = 76561198075352540;
			// 	$array[12]['Elo'] = 1203;
			// 	$array[12]['MatchModeID'] = 1;
			// 	$array[12]['Region'] = 1;
			// 	$array[12]['MatchTypeID'] = 1;

			// 	$array[13]['SteamID'] = 76561198036113593;
			// 	$array[13]['Elo'] = 1204;
			// 	$array[13]['MatchModeID'] = 1;
			// 	$array[13]['Region'] = 1;
			// 	$array[13]['MatchTypeID'] = 1;
			// 	$array[13]['GroupID'] = 15;

			// 	$array[14]['SteamID'] = 76561198050039186;
			// 	$array[14]['Elo'] = 1167;
			// 	$array[14]['MatchModeID'] = 1;
			// 	$array[14]['Region'] = 1;
			// 	$array[14]['MatchTypeID'] = 1;
			// 	$array[14]['GroupID'] = 14;

			// 	$array[15]['SteamID'] = 76561198068803073;
			// 	$array[15]['Elo'] = 1280;
			// 	$array[15]['MatchModeID'] = 1;
			// 	$array[15]['Region'] = 1;
			// 	$array[15]['MatchTypeID'] = 1;
			// 	$array[15]['GroupID'] = 15;

			// 	$array[16]['SteamID'] = 76561198076067679;
			// 	$array[16]['Elo'] = 1260;
			// 	$array[16]['MatchModeID'] = 1;
			// 	$array[16]['Region'] = 1;
			// 	$array[16]['MatchTypeID'] = 1;
			// 	$array[16]['GroupID'] = 14;

			// 	$array[17]['SteamID'] = 76561198088047637;
			// 	$array[17]['Elo'] = 1200;
			// 	$array[17]['MatchModeID'] = 1;
			// 	$array[17]['Region'] = 1;
			// 	$array[17]['MatchTypeID'] = 1;

			// 	$array[18]['SteamID'] = 76561198053065928;
			// 	$array[18]['Elo'] = 1200;
			// 	$array[18]['MatchModeID'] = 1;
			// 	$array[18]['Region'] = 1;
			// 	$array[18]['MatchTypeID'] = 1;

			// 	$array[19]['SteamID'] = 76561198047012055;
			// 	$array[19]['Elo'] = 1160;
			// 	$array[19]['MatchModeID'] = 1;
			// 	$array[19]['Region'] = 1;
			// 	$array[19]['MatchTypeID'] = 1;

			// 	$array[0]['SteamID'] = 76561198056439830;
			// 	$array[0]['Elo'] = 1200;
			// 	$array[0]['MatchModeID'] = 1;
			// 	$array[0]['Region'] = 1;
			// 	$array[0]['MatchTypeID'] = 1;

			// 	$array[1]['SteamID'] = 76561198065060643;
			// 	$array[1]['Elo'] = 1200;
			// 	$array[1]['MatchModeID'] = 1;
			// 	$array[1]['Region'] = 1;
			// 	$array[1]['MatchTypeID'] = 1;

			// 	$array[2]['SteamID'] = 76561198075352540;
			// 	$array[2]['Elo'] = 1200;
			// 	$array[2]['MatchModeID'] = 1;
			// 	$array[2]['Region'] = 1;
			// 	$array[2]['MatchTypeID'] = 1;

			// 	$array[3]['SteamID'] = 76561198036113593;
			// 	$array[3]['Elo'] = 1200;
			// 	$array[3]['MatchModeID'] = 1;
			// 	$array[3]['Region'] = 1;
			// 	$array[3]['MatchTypeID'] = 1;
			// 	$array[3]['GroupID'] = 15;

			// 	$array[4]['SteamID'] = 76561198050039186;
			// 	$array[4]['Elo'] = 1200;
			// 	$array[4]['MatchModeID'] = 1;
			// 	$array[4]['Region'] = 1;
			// 	$array[4]['MatchTypeID'] = 1;
			// 	$array[4]['GroupID'] = 14;

			// 	$array[5]['SteamID'] = 76561198068803073;
			// 	$array[5]['Elo'] = 1200;
			// 	$array[5]['MatchModeID'] = 1;
			// 	$array[5]['Region'] = 1;
			// 	$array[5]['MatchTypeID'] = 1;
			// 	$array[5]['GroupID'] = 15;

			// 	$array[6]['SteamID'] = 76561198076067679;
			// 	$array[6]['Elo'] = 1200;
			// 	$array[6]['MatchModeID'] = 1;
			// 	$array[6]['Region'] = 1;
			// 	$array[6]['MatchTypeID'] = 1;
			// 	$array[6]['GroupID'] = 14;

			// 	$array[7]['SteamID'] = 76561198088047637;
			// 	$array[7]['Elo'] = 1200;
			// 	$array[7]['MatchModeID'] = 1;
			// 	$array[7]['Region'] = 1;
			// 	$array[7]['MatchTypeID'] = 1;
			// 	$array[7]['GroupID'] = 12;

			// 	$array[8]['SteamID'] = 76561198053065928;
			// 	$array[8]['Elo'] = 1200;
			// 	$array[8]['MatchModeID'] = 1;
			// 	$array[8]['Region'] = 1;
			// 	$array[8]['MatchTypeID'] = 1;

			// 	$array[9]['SteamID'] = 76561198047012055;
			// 	$array[9]['Elo'] = 1200;
			// 	$array[9]['MatchModeID'] = 1;
			// 	$array[9]['Region'] = 1;
			// 	$array[9]['MatchTypeID'] = 1;
			// 	$array[9]['GroupID'] = 12;

			// $BannedList = new Banlist();
			// $retB = $BannedList->getCurrentBanDataOfPlayer(76561197987480514);
			// p($retB);

			// $UserPoints = new UserPoints();
			// $retM = $UserPoints->insertPointChanges(201, 1);
			// p($retM);

// 			// Gruppen-Typen vorbereiten
// 			$data = $array;
// 			$groupArray = array();
// 			p($data);
// 			foreach ($data as $k => $v){
// 				if($v['GroupID'] > 0){
// 					$tmpArray = array();
// 					$tmpArray['MatchID'] = (int) $matchID;
// 					$tmpArray['SteamID'] = secureNumber($v['SteamID']);
// 					$tmpArray['TeamID'] = (int) $teamID;
// 					$tmpArray['Elo'] = (int) $v['Elo'];
// 					$tmpArray['dataKey'] = (int) $k;
// 					$groupArray[$v['GroupID']][] = $tmpArray;
// 				}
// 			}
// 			p("GRROUUP ARRAAAAAAAAAAY");
// 			p($groupArray);

// 			$i=0;
// 			if(is_array($groupArray) && count($groupArray) > 0){
// 				$countTeam = array();
// 				foreach($groupArray as $k =>$v){
// 					if($i%2==0){
// 						$teamID = 2;
// 					}
// 					else{
// 						$teamID = 1;
// 					}
// 					p("ITERATION:".$i." Team:".$teamID);
// 					if(is_array($v) && count($v) > 0){
// 						foreach($v as $kk =>$vv){
// 							$tmpArray = array();
// 							$tmpArray['MatchID'] = (int) $matchID;
// 							$tmpArray['SteamID'] = secureNumber($vv['SteamID']);
// 							$tmpArray['TeamID'] = (int) $teamID;
// 							$tmpArray['Rank'] = (int) $vv['Elo'];
// 							$tmpArray['Ready'] = 0;

// 							$insertArray[] = $tmpArray;

// 							// aus Data entfernen
// 							unset($data[$vv['dataKey']]);
// 							$countTeam[$teamID]++;
// 						}
// 					}
// 					$i++;
// 				}
// 				$data = orderArrayBy($data,'Elo',SORT_DESC);
// 				p("�briges DATA ARRRRAAAAYY");
// 				p($data);
// 				p("Bereits gef�lltes Insert ARRAAYYY");
// 				p($insertArray);
// 				p("COUNT TEAMS");
// 				p($countTeam);
				
// 				foreach($data as $k => $v){
// 					$tmpArray = array();
// 					$retAve = getAvePointsOfTeam($insertArray);
// 					p($retAve);
// 					$teamID = $retAve['data'];
// 					p("Team with Ave Min :".$teamID);
// 					// 		if($i%2==0){
// 					// 			$teamID = 2;
// 					// 		}
// 					// 		else{
// 					// 			$teamID = 1;
// 					// 		}

// 					if($countTeam[$teamID] < 5){
// 						$tmpArray['MatchID'] = (int) $matchID;
// 						$tmpArray['SteamID'] = secureNumber($v['SteamID']);
// 						$tmpArray['TeamID'] = (int) $teamID;
// 						$tmpArray['Rank'] = (int) $v['Elo'];
// 						$tmpArray['Ready'] = 0;

// 						$insertArray[] = $tmpArray;
// 						$countTeam[$teamID]++;
// 					}
// 					else{
// 			if($teamID == 1){
// 				$teamID = 2;
// 			}
// 			else{
// 				$teamID = 1;
// 			}
// 			$tmpArray['MatchID'] = (int) $matchID;
// 			$tmpArray['SteamID'] = secureNumber($v['SteamID']);
// 			$tmpArray['TeamID'] = (int) $teamID;
// 			$tmpArray['Rank'] = (int) $v['Elo'];
// 			$tmpArray['Ready'] = 0;

// 			$insertArray[] = $tmpArray;
// 			$countTeam[$teamID]++;
// 		}
			
// 		$i++;
// 				}
// 			}
// 			p("INSERTARRAY");
// 			p($insertArray);
// 			$retAve = getAvePointsOfTeam($insertArray);
// 			p($retAve);
$Login = new Login();
$retL = $Login->checkUserFulfillRestrictions($steamID);
p($retL);
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
