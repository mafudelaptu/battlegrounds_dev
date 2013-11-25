<?php 

$prefix = "";
$global_variables = array();
// Dota2Dev
if(strpos($_SERVER['SERVER_NAME'], "dev.") === 0 OR strpos(dirname(__FILE__), "dota2Dev/") > 0){
	$global_variables['DB_HOST'] = "localhost";
	$global_variables['DB_USERNAME'] = "dev_ihh_com";
	$global_variables['DB_PW'] = "RvP61JzmCqa67AE7";
	$global_variables['DB_NAME'] = "dev_ihh_com";
	$global_variables['DB_TABLE_PREFIX'] = "";
	$prefix = "dev."; // f�r region bestimmung
	
	$global_variables['DB_HOST_WP'] = "localhost";
	$global_variables['DB_USERNAME_WP'] = "dev_ihearthu_1";
	$global_variables['DB_PW_WP'] = "gI5TAT34RNGUIDtQ";
	$global_variables['DB_NAME_WP'] = "dev_ihearthu_1";
	$global_variables['DB_TABLE_PREFIX_WP'] = "";
	
	$global_variables['DB_HOST_FORUM'] = "localhost";
	$global_variables['DB_USERNAME_FORUM'] = "dev_ihearthu_2";
	$global_variables['DB_PW_FORUM'] = "LM7EFOokNTVJ5ETO";
	$global_variables['DB_NAME_FORUM'] = "dev_ihearthu_2";
	$global_variables['DB_TABLE_PREFIX_FORUM'] = "";
	
}
// Dota2 Live
elseif(strpos($_SERVER['SERVER_NAME'], "dota2.") === 0 OR strpos(dirname(__FILE__), "dota2/") > 0){
	$global_variables['DB_HOST'] = "localhost";
	$global_variables['DB_USERNAME'] = "root";
	$global_variables['DB_PW'] = "wpzhwLF4LBkK";
	$global_variables['DB_NAME'] = "dota2Live";
	$global_variables['DB_TABLE_PREFIX'] = "";
	$prefix = "dota2."; // f�r region bestimmung
}

$global_variables['DB_HOST_GLOBAL'] = "localhost";
$global_variables['DB_USERNAME_GLOBAL'] = "root";
$global_variables['DB_PW_GLOBAL'] = "wpzhwLF4LBkK";
$global_variables['DB_NAME_GLOBAL'] = "globalUser";
$global_variables['DB_TABLE_PREFIX_GLOBAL'] = "";

// Globale Variablen global definieren
foreach($global_variables as $k => $v){
	define($k, $v);
}
?>