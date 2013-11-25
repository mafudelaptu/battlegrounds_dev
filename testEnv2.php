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
require_once (DR . HOME_PATH_REL . "/inc/inc_css_and_js.php");
?>

</head>

<body>
	<div class="container">
		<?php
		$DB = new DB ();
		$con = $DB->conDB ();
		
		$User = new User ();
		$retU = $User->getAllUser ();
		p ( $retU );
		$data = $retU ['data'];
		if (is_array ( $data ) && count ( $data ) > 0) {
			foreach ( $data as $k => $v ) {
				$steamID = $v['SteamID'];
// 				// $steamID = 76561198047012055;
// 				// $steamID = 76561198084503805;
// 				$SkillBracket = new SkillBracket ();
// 				$retSB = $SkillBracket->getSkillBracketOfUserByStats ( $steamID );
// 				p ( $retSB );
				
// 				$UserLeague = new UserLeague ();
// 				$retUL = $UserLeague->getLeagueOfUser ( $steamID );
// 				p ( $retUL );
// 				$userLeagueID = $retUL ['data'] ['LeagueTypeID'];
				
// 				$UserPoints = new UserPoints ();
// 				$retUP = $UserPoints->getGlobalPointsOfUser ( $steamID );
// 				$points = $retUP ['points'];
				
// 				switch ($userLeagueID) {
// 					case 1 :
// 						$correction = ((- 1) * $points);
// 						break;
// 					case 2 :
// 						$correction = ((- 1) * $points);
// 						break;
// 					case 3 :
// 						$correction = ((- 1) * $points) + 200;
// 						break;
// 					case 4 :
// 						$correction = ((- 1) * $points) + 400;
// 						break;
// 					case 5 :
// 						$correction = ((- 1) * $points) + 600;
// 						break;
// 					case 6 :
// 						$correction = ((- 1) * $points) + 800;
// 						break;
// 				}
// 				p ( $correction );
// 				$sql = "SELECT *
// 						FROM `UserPoints`
// 						WHERE SteamID = " . secureNumber ( $steamID ) . " AND PointsTypeID = 12
// 		";
// 				$count = $DB->countRows ( $sql );
// 				$ret ['debug'] .= p ( $sql, 1 );
// 				if ($count > 0) {
// 					p ( "schon eingetragen" );
// 				} else {
					
// 					$insertArray = array ();
// 					$insertArray ['SteamID'] = secureNumber ( $steamID );
// 					$insertArray ['MatchModeID'] = 0;
// 					$insertArray ['MatchTypeID'] = 0;
// 					$insertArray ['MatchID'] = 0;
// 					$insertArray ['PointsTypeID'] = 12; // Skill-Brackets-Adjustment
// 					$insertArray ['EventID'] = 0;
// 					$insertArray ['PointsChange'] = $correction;
// 					$insertArray ['Timestamp'] = time ();
					
// 					$data = $DB->insert ( "UserPoints", $insertArray );
// 					$ret ['debug'] .= p ( $sql, 1 );
// 				}
				
// 				$retU = $User->setBasePointsForPlayer ( $steamID );
// 				p ( $retU );
// 				$UserSkillBracket = new UserSkillBracket ();
// 				$retSBIns = $UserSkillBracket->insertFirstSkillBracketForUser ( $steamID );
// 				p ( $retSBIns );

				$SkillBracket = new SkillBracket();
				$retSB = $SkillBracket->checkAndSetSkillBracketOfUser($steamID);
			}
		}
		
		?>
	</div>
</body>
</html>