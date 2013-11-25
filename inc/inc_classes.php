<?php
include DR.HOME_PATH_REL."/inc/simplehtmldom_1_5/simple_html_dom.php";

$alle = glob(DR.HOME_PATH_REL.'/classes/*/*.php');
//var_dump($alle);
if(count($alle)>0){
	foreach($alle as $datei) include $datei;
}

?>