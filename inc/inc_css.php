<?php 
// CSS
echo "<!-- CSS -->";
// Root
echo "<!-- Root -->";
$alle1 = glob(DR.HOME_PATH_REL.'/css/*.css');
$alle2 = glob(DR.HOME_PATH_REL.'/css/*/*.css', GLOB_NOSORT);
$alle = array_merge($alle1,$alle2);
//p($alle);
if(count($alle)>0){
	foreach($alle as $datei){
		$tmp = explode(DR.HOME_PATH_REL."/", $datei);
		$url = HOME_PATH.$tmp[1];
		echo '<link rel="stylesheet" type="text/css" href="'.$url.'"/>';
	}
}

?>
