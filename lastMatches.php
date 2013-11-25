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
<meta name="description" content="Least played matches of a player.">
<meta name="robots" content="index,follow">
<title>N-GAGE-TV - Last Matches</title>
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
	$User = new User();
	$userData = $User->getUserData($steamID);
	
	$MatchDetails = new MatchDetails();
	$lastMatches = $MatchDetails->getLastMatchDetailsOfPlayer($steamID);
	// Assign Smarty Variables
	$smarty->assign('lastMatches', $lastMatches['data']);
	$smarty->assign('userData', $userData);
	
	$_SESSION['debug'] = p($lastMatches,1);
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
	 			$smarty->display('lastMatches/index.tpl');
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