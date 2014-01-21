<?php

class General {
	public static function recursive_array_search($needle,$haystack) {
		foreach($haystack as $key=>$value) {
			$current_key=$key;
			if($needle===$value OR (is_array($value) && recursive_array_search($needle,$value) !== false)) {
				return $current_key;
			}
		}
		return false;
	}

	public static function getAvePointsOfTeam($array){
		$ret = array();
		$ret['debug'] = "";
		if(is_array($array) && count($array) > 0){
                                //$ret['debug'] .= p($array,1);
			$sum1 = 0; $sum2 = 0; $count1 = 0; $count2 = 0;
			foreach ($array as $k => $v) {
				$teamID = $v['team_id'];
				$points = $v['points'];
				if($teamID == 1){
					$sum1 += $points;
					$count1++;
				}
				else{
					$sum2 += $points;
					$count2++;
				}
			}

			$ave1 = ($count1 > 0 ? $sum1/$count1 : 0);
			$ave2 = ($count2 > 0 ? $sum2/$count2 : 0);
			$ret['debug'] .= "S:".$sum1." C:".$count1." S2:".$sum2." C2:".$count2." A:".$ave1." A2:".$ave2;
			$ret['ave1'] = $ave1;
			$ret['ave2'] = $ave2;
			if($ave1 <= $ave2){
				$ret['data'] = 1;
			}
			else{
				$ret['data'] = 2;
			}
			$ret['status'] = true;
		}
		else {
			$ret['data'] = 1;
			$ret['status'] = false;
		}
		return $ret;
	}

	public static function calculateWinRate($wins, $losses){
		$ret = 0;
		$wins = (int) $wins;
		$losses = (int) $losses;

		if ($wins+$losses > 0){
			$ret = round(($wins/(($wins+$losses)*100)),2);
		}
		return $ret;
	}

// Sortiert ein 2 Dimensionales Array nach einem bestimmten Schl�ssel
// Bsp -> $processlist = orderArrayBy($processlist,'process_definition_responsible_name',SORT_DESC);
	public static function orderArrayBy()	{
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

	/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
public static function getMinAvePointsOfTeam($team1, $team2){
	$ret = array();

	$sum1 = 0;
	$ave1 = 0;
	if(is_array($team1) && count($team1) > 0){

		foreach($team1 as $k =>$v){
			$sum1 += $v['points'];
		}
		$ave1 = $sum1/count($team1);
	}

	$sum2 = 0;
	$ave2 = 0;
	if(is_array($team2) && count($team2) > 0){

		foreach($team2 as $k =>$v){
			$sum2 += $v['points'];
		}
		$ave2 = $sum2/count($team2);
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

public static function first($array) {
	if (!is_array($array)) return $array;
	if (!count($array)) return null;
	reset($array);
	return $array[key($array)];
}

public static function last($array) {
	if (!is_array($array)) return $array;
	if (!count($array)) return null;
	end($array);
	return $array[key($array)];
}

public static function seperatePlayersIntoBalancedTeams($data, $team_size){
	$ret = array();
	switch ($team_size) {
		case 1:
		if(!empty($data)){
			$teams = array();
			foreach($data as $k => $v){
				$teams[] = $v; 
			}
		} 
		break;
			default: // 5
			$teamsRet = General::seperate20PlayersInto4BalancedTeams($data);
			$teams = $teamsRet['data'];
			break;
		}
		$ret['data'] = $teams;
		$ret['status'] = true;
		return $ret;
	}

	public static function seperate10PlayersInto2BalancedTeams($data) {
		$ret = array ();

		if (!empty($data) && count ( $data ) > 0) {
			$sortedArray = General::orderArrayBy ( $data, 'points', SORT_DESC );
			// p($sortedArray);
			$team = array (1=>"", 2=>"");
			$i = 0;
			while ( count ( $sortedArray ) !== 0 ) {

				// oberesten und untersten nehmen und in die gruppe packen
				$firstElem = General::first ( $sortedArray );
				$firstElemKey = array_search ( $firstElem, $sortedArray );
				$lastElem = General::last ( $sortedArray );
				$lastElemKey = array_search ( $lastElem, $sortedArray );

				// p($firstElemKey);
				// p($lastElemKey);

				// wenn eine runde rum dann erneut durchschnitte bereichnen
				if ($i % 2 === 0) {
					// in gruppe packen und aus array entfernen
					$minAveTeamNumberRet = General::getMinAvePointsOfTeam ( $team [1], $team [2] );
					$minAveTeamNumber = $minAveTeamNumberRet ['data'];

					// wenn es die letzten 2 sind
					if (count ( $sortedArray ) == 2 && count ( $data ) == 10) {
						$team [$minAveTeamNumber] [] = $firstElem;
						if ($minAveTeamNumber === 1) {
							$team [2] [] = $lastElem;
						} else {
							$team [1] [] = $lastElem;
						}
					} 					// sonst beide in selbe gruppe packen
					else {
						// in gruppe packen und aus array entfernen
						$team [$minAveTeamNumber] [] = $firstElem;
						$team [$minAveTeamNumber] [] = $lastElem;
					}
				} 				// sonst in die gefundenen in die andere gruppe packen
				else {
					// $ret['debug'] .= p($sortedArray,1);

					// wenn es die letzten 2 sind
					if (count ( $sortedArray ) == 2 && count ( $data ) == 10) {
						
						// in gruppe packen und aus array entfernen
						if ($lastChoosedTeam === 1) {
							$team [2] [] = $lastElem;
						} else {
							$team [1] [] = $lastElem;
						}
					} 					// sonst beide in selbe gruppe packen
					else {
						// in gruppe packen und aus array entfernen
						if ($lastChoosedTeam === 1) {
							$team [2] [] = $firstElem;
							$team [2] [] = $lastElem;
						} else {
							$team [1] [] = $firstElem;
							$team [1] [] = $lastElem;
						}
					}
				}

				unset ( $sortedArray [$firstElemKey] );
				unset ( $sortedArray [$lastElemKey] );

				$lastChoosedTeam = $minAveTeamNumber;
				$i ++;
			}

			$ret ['data'] = $team;
			$ret ['status'] = true;
		} else {

			$ret ['status'] = "Eingabe Array leer";
		}

		return $ret;
	}
/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
public static function seperate20PlayersInto4BalancedTeams($data) {
	$ret = array ();
	if (is_array ( $data ) && count ( $data ) > 0) {
			// in 10ner teams aufteilen
		$seperatedData = General::seperate10PlayersInto2BalancedTeams ( $data );
		$seperatedArrays = $seperatedData ['data'];
		if (is_array ( $seperatedArrays ) && count ( $seperatedArrays ) > 0) {
			$i = 0;
			$teams = array (1=>"", 2=>"", 3=>"", 4=>"");
			foreach ( $seperatedArrays as $k => $v ) {
				$doubleSeperatedData = array ();
				$doubleSeperatedData = General::seperate10PlayersInto2BalancedTeams ( $v );
				$doubleSeperatedArray = $doubleSeperatedData ['data'];

					// wenn erste iteration -> dann in team1 und 2 aufteilen
				if ($i === 0) {
					$teams [1] = $doubleSeperatedArray [1];
					$teams [2] = $doubleSeperatedArray [2];
					} 					// sonst team3 und 4
					else {
						$teams [3] = $doubleSeperatedArray [1];
						$teams [4] = $doubleSeperatedArray [2];
					}
					$i ++;
				}
				$ret ['data'] = $teams;
			}
		} else {
			$ret ['status'] = "Eingabe Array leer";
		}

		return $ret;
	}

}
