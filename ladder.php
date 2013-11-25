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
<meta name="description" content="Overview-page of all ranking of players in Dota2 League">
<meta name="robots" content="index,follow">
<title>N-GAGE-TV - Ladder</title>
<?php
	require_once(DR.HOME_PATH_REL."/inc/inc_css_and_js.php");
?>
<?php
	if($_GET['ID'] > 0){
		$steamID = $_GET['ID'];
	}
	else{
		$steamID = $_SESSION['user']['steamID'];
	}
	
	
	$MatchMode = new MatchMode();
	$matchModes = $MatchMode->getAllMatchModes();
	$smarty->assign('matchModes', $matchModes);
	
	$activeSingleLadder = "";
	$activeTeamLadder = "";
	if($_GET['ladder'] == "Single5vs5Ladder"){
		$activeSingleLadder = "active";
		$section = "Single5vs5Ladder";
		// SingleLadder General Data auslesen
		$matchTypeID = 1;
	}
// 	elseif ($_GET['ladder'] == "GlobalLadder"){
// 		$activeGlobalLadder = "active";
// 		$section = "GlobalLadder";
// 		// SingleLadder General Data auslesen
// 		$Ladder = new Ladder();
// 		$generalData = $Ladder->getLadder($steamID);
// 		$matchTypeID = 0;
// 	}
	elseif ($_GET['ladder'] == "TeamLadder"){
		$activeTeamLadder = "active";
		$section = "TeamLadder";
	}
	elseif ($_GET['ladder'] == "1vs1Ladder"){
		$active1vs1Ladder = "active";
		$section = "1vs1Ladder";
		$matchTypeID = 8;
		
	}
	elseif ($_GET['ladder'] == "3vs3Ladder"){
		$active3vs3Ladder = "active";
		$section = "3vs3Ladder";
		$matchTypeID = 9;
	}
	else{
		$active1vs1Ladder = "active";
		$section = "1vs1Ladder";
		$matchTypeID = 8;
	}
	
	$smarty->assign('steamID', $steamID);
	$smarty->assign('activeGlobalLadder', $activeGlobalLadder);
	$smarty->assign('activeSingleLadder', $activeSingleLadder);
	$smarty->assign('activeTeamLadder', $activeTeamLadder);
	$smarty->assign('active1vs1Ladder', $active1vs1Ladder);
	$smarty->assign('active3vs3Ladder', $active3vs3Ladder);
	$smarty->assign('section', $section);
	$smarty->assign('generalData', $generalData['data']);
	$smarty->assign('matchTypeID', $matchTypeID);
	
	$_SESSION['debug'] = p($generalData,1);

?>
<script type="text/javascript">

$(document).ready(function() {
	initTable(<?php echo $generalData['startDataNumber'];?>);
} );

</script>
</head>
<body>
  <div id="wrap">
   <?php 
	 			$smarty->display('top_navi.tpl');
			?>
	<div id="globalBG">
      <div class="container">
      	<?php 
	 			$smarty->display('ladder/index.tpl');
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