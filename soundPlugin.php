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
<meta name="description"
	content="Profile page of <?php echo $_SESSION['user']['name'];?> with specific data about his account on Dota2 League">
<meta name="robots" content="index,follow">
<title>N-GAGE-TV - Sound Plugin </title>
<?php
require_once(DR.HOME_PATH_REL."/inc/inc_css_and_js.php");
?>


<script type="text/javascript">
$(document).ready(function() {
	//match found Sound abspielen
	//$.playSound("files/sound/matchReadySound.mp3");
	l("Start playMatchFoundSound");
	var mySound = new buzz.sound([
	                              "files/sound/matchReadySound.ogg",
	                              "files/sound/matchReadySound.mp3",
	                              "files/sound/matchReadySound.wav",
	                              "files/sound/matchReadySound.aac"
	                          ]);
	mySound.play();
	mySound.setVolume( 100 );
	l(mySound.getVolume());
	l("End playMatchFoundSound");
	if (!mySound.isSupported()) {
	    alert("Your browser is too old and cant play audio files, time to update!");
	}
	});


</script>


<body>
	<div class="container">
	<br><br><br><br><br><br>
		<div class="alert alert-info">Now you should hear a sound! If not, your browser is too old. Please update your browser or choose an other one!</div>
	</div>
</body>