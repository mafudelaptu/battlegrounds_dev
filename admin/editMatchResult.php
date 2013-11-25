<?php
session_start();
require_once("../inc/inc_general_php_functions_for_admin.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Dota2 Lone Wolf League</title>
<?php
	require_once(DR.HOME_PATH_REL."/inc/inc_css_and_js.php");
?>
<?php

$matchID = $_GET['matchID'];

if($matchID > 0){
	/*
	 * Form handling
	*/
	
	if($_POST['submitChanges']){
		$DB = new DB();
		$con = $DB->conDB();
		// Matchresult Handling
		$selection = (int) $_POST['mRMatchResultRadio'];
		switch($selection){
			case "cancelMatch":
				$sql = "UPDATE `Match`
								SET TeamWonID = -1, Canceled = 1, `ManuallyCheck` = 0, TimestampClosed = ".time()."
								WHERE MatchID = ".(int)$matchID."
					";
				break;
			default:
				$sql = "UPDATE `Match`
								SET TeamWonID = ".(int) $selection.", Canceled = 0, `ManuallyCheck` = 0, TimestampClosed = ".time()."
								WHERE MatchID = ".(int)$matchID."
					";
		}
		$retUp = $DB->update($sql);
		$msg = "Matchresult successfully updated";
	}
	
	$Match = new Match();
	$MatchDetails = new MatchDetails();
	$MatchDetailsLeaverVotes = new MatchDetailsLeaverVotes();
	
	// general MatchData
	$retM = $Match->getMatchDataForChangeMatchResults($matchID); 
	
	// leaverVotes
	$retMDLV = $MatchDetailsLeaverVotes->getLeaverData($matchID,true);
	
	// cancelLeaverVotes
	$retMDCLV = $MatchDetailsLeaverVotes->getCancelLeaverData($matchID,true);
	
	
	$smarty->assign('data', $retM['data']);
	$smarty->assign('generalMatchData', $retM['generalMatchData']);
	$smarty->assign('leaverVotes', $retMDLV['data']);
	$smarty->assign('cancelLeaverVotes', $retMDCLV['data']);
	$smarty->assign('screenshots', $retM['screenshots']);
	
}
else{
	p("ERROR: no matchID!");
	exit();
}

$_SESSION['debug'] = p($retMDLV,1);
	
	
?>
</head>

<body>
  <div id="wrap">
   <?php 
	 			$smarty->display('admin/top_navi.tpl');
			?>
	<div id="globalBG">
      <div class="container">
      
      	<?php 
      			if ($msg) {
      				p($msg);
      			}
	 			$smarty->display('admin/editMatchResult/index.tpl');
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