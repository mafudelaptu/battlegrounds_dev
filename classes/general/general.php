<?php
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
function p($value, $asString=false){
	if(DEBUG == true OR $_SESSION['user']['admin'] == 1){
		if($asString){
			if(gettype($value) == "array"){
				return "<pre>".print_r($value,1)."</pre>\n";
			}
			else{
				return "<pre>".$value."</pre>\n";
			}
		}
		else{
			if(gettype($value) == "array"){
				echo "<pre>".print_r($value,1)."</pre>\n";
			}
			else{
				echo "<pre>".$value."</pre>\n";
			}
		}
	}
}

/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/

function search($array, $key, $value)
{
	$results = array();

	if (is_array($array))
	{
		if (isset($array[$key]) && $array[$key] == $value)
			$results[] = $array;

		foreach ($array as $subarray)
			$results = array_merge($results, search($subarray, $key, $value));
	}

	return $results;
}

/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
function secureStrings($s)	{
	$pattern = '/[0-9\?\.\+\/\\\*\(\)\[\]\{\}\^\'"!~,§\$%\&=;_#´`:\|<>°]+/isU';	// - Bindestrich zugelassen
	$replacement = '';
	return mysql_real_escape_string(preg_replace($pattern, $replacement, $s));
}
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
function secureStringsNumbers($sn)	{
	// $pattern = '/[^a-zA-Z0-9\.\- ]+/';
	$pattern = '/[\?\.\-\+\/\\\*\(\)\[\]\{\}\^\'"!~,§\$%\&=;_#´`:\|<>°]+/isU';
	$replacement = '';
	return mysql_real_escape_string(preg_replace($pattern, $replacement, $sn));
}
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
// interesting for BIG_INT
function secureNumber($steamID){
	if(is_numeric($steamID)){
		// nur Zahlen erlauben
		$wert = preg_replace('/[^0-9]/', '', $steamID);
		return mysql_real_escape_string($wert);
	}
	else return false;
}

// Sortiert ein 2 Dimensionales Array nach einem bestimmten Schl�ssel
// Bsp -> $processlist = orderArrayBy($processlist,'process_definition_responsible_name',SORT_DESC);
function orderArrayBy()	{
	$args = func_get_args();
	$data = array_shift($args);
	foreach ($args as $n => $field) {
		if (is_string($field)) {
			$tmp = array();
			foreach ($data as $key => $row) {
				$tmp[$key] = $row[$field];
			}
			$args[$n] = $tmp;
		}
	}
	$args[] = &$data;
	call_user_func_array('array_multisort', $args);
	return array_pop($args);
}

//  Nutzung des Ausgabepuffers um eine Datei "in einen String" einzubinden
function get_include_contents($filename) {
	if (is_file($filename)) {
		ob_start();
		include $filename;
		return ob_get_clean();
	}
	return false;
}

function recursive_array_search($needle,$haystack) {
	foreach($haystack as $key=>$value) {
		$current_key=$key;
		if($needle===$value OR (is_array($value) && recursive_array_search($needle,$value) !== false)) {
			return $current_key;
		}
	}
	return false;
}

function first($array) {
	if (!is_array($array)) return $array;
	if (!count($array)) return null;
	reset($array);
	return $array[key($array)];
}

function last($array) {
	if (!is_array($array)) return $array;
	if (!count($array)) return null;
	end($array);
	return $array[key($array)];
}

/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
function getMinAveEloOfTeam($team1, $team2){
	$ret = array();

	$sum1 = 0;
	$ave1 = 0;
	if(is_array($team1) && count($team1) > 0){

		foreach($team1 as $k =>$v){
			$sum1 += $v['Elo'];
		}
		$ave1 = $sum1/count($team1);
		$ret['dabug'] .= p($ave1,1);
	}

	$sum2 = 0;
	$ave2 = 0;
	if(is_array($team2) && count($team2) > 0){

		foreach($team2 as $k =>$v){
			$sum2 += $v['Elo'];
		}
		$ave2 = $sum2/count($team2);
		$ret['dabug'] .= p($ave2,1);
	}

	if($ave1 <= $ave2){
		$teamNumber = 1;
	}
	else{
		$teamNumber = 2;
	}

	$ret['data'] = $teamNumber;
	return $ret;
}

/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
function getNextWholeHour($date){

	$plus_one_hour = $date + 3600;

	$next_hour = floor($plus_one_hour / 3600) * 3600;

	return $next_hour;
}

?>