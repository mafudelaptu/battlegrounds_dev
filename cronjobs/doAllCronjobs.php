<?php
session_start();
ini_set("display_errors", 1);
require_once(dirname(__FILE__)."/../inc/inc_general_php_functions_for_cronjobs.php");

?>

<?php 
// Alle cronjobs einbinden
$alle = glob(dirname(__FILE__).'/*/*.php');
//var_dump($alle);
$_SESSION['sql']['queryCount'] = 0;
p($_SESSION);
if(count($alle)>0){
	foreach($alle as $datei) include $datei;
}
p($_SESSION);
?>