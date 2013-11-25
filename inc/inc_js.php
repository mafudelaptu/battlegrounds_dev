<?php 


// JS
echo "<!-- JS -->";
// Root
echo "<!-- ROOT -->";
$alle1 = glob(DR.HOME_PATH_REL.'/js/*.js');
$alle2 = glob(DR.HOME_PATH_REL.'/js/*/*.js');
$alle = array_merge($alle1,$alle2);
//p($alle);
if(count($alle)>0){
	foreach($alle as $datei){
		$tmp = explode(DR.HOME_PATH_REL."/", $datei);
		$url = HOME_PATH.$tmp[1];
		echo '<script type="text/javascript" src="'.$url.'"></script>';
	}
}

?>