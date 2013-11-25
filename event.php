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
<meta name="description" content="Dota2 League's start page of an Event. Here Players play a tournament until a final Team wins.">
<title>N-GAGE-TV - Event</title>
<?php
	require_once(DR.HOME_PATH_REL."/inc/inc_css_and_js.php");
?>
</head>
	<?php 
		$eventID = (int) $_GET['eventID'];
		$createdEventID = (int) $_GET['cEID'];
		
		$Event = new Event();
		$retEvent = $Event->getEventData($eventID);
		$retStatus = $Event->getPlayerStatus($eventID, $createdEventID);
		
		$EventTeams = new EventTeams();
		$retTeam = $EventTeams->getTeamOfUser($eventID, $createdEventID, 1);
			$retT1 = $EventTeams->getTeamMembers($eventID, $createdEventID, 1);
			$retT2 = $EventTeams->getTeamMembers($eventID, $createdEventID, 2);
			$retT3 = $EventTeams->getTeamMembers($eventID, $createdEventID, 3);
			$retT4 = $EventTeams->getTeamMembers($eventID, $createdEventID, 4);
			$retT5 = $EventTeams->getTeamMembers($eventID, $createdEventID, 5);
			$retT6 = $EventTeams->getTeamMembers($eventID, $createdEventID, 6);
		$EventMatches = new EventMatches();
		$retMatches = $EventMatches->getEventMatchesStatus($eventID, $createdEventID);
		
		$CreatedEvents = new CreatedEvents();
		$retCData = $CreatedEvents->getCreatedEventData($createdEventID);
		
			
		$smarty->assign('createdEventID', (int)$createdEventID);
		$smarty->assign('eventID', (int)$eventID);
		$smarty->assign('eventData', $retEvent['data']);
		$smarty->assign('teamOfPlayer', $retTeam['data']['EventTeamID']);
		$smarty->assign('team1Data', $retT1['data']);
		$smarty->assign('team2Data', $retT2['data']);
		$smarty->assign('team3Data', $retT3['data']);
		$smarty->assign('team4Data', $retT4['data']);
		$smarty->assign('team5Data', $retT5['data']);
		$smarty->assign('team6Data', $retT6['data']);
		$smarty->assign('playerStatus', $retStatus['data']);
		$smarty->assign('eventMatchesData', $retMatches['data']);
		$smarty->assign('createdEventData', $retCData['data']);
		
		// Chat handling
		$Chat = new Chat();
		//$Chat->createChatFile($eventID, "event", $createdEventID);
		
		$_SESSION['debug'] = p($retT1,1);
	?>

<body onload="setInterval('chat.update()', 2000);">
<script type="text/javascript">
	// kick off chat
	  var chat =  new Chat();

	  $(function() {
	     chat.getState("event"); 
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
</script>
	<div id="wrap">
    	<?php 
				$smarty->display('top_navi.tpl');
			?>
	<div id="globalBG">
      <div class="container">
  			<?php 
					$smarty->display('event/index.tpl');
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
    
</body>
</html>