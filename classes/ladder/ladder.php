<?php
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class Ladder {
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getBestRankings($steamID, $count = "*") {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start getBestRankings <br>\n";

		if ($steamID == "" || $steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if ($steamID > 0) {
			$MatchMode = new MatchMode ();
			$MatchType = new MatchType ();
				
			$matchModes = $MatchMode->getAllMatchModes ();
			$matchTypes = $MatchType->getAllMatchTypes ();
				
			$data = array ();
			if (is_array ( $matchTypes ) && count ( $matchTypes ) > 0) {
				foreach ( $matchTypes as $k => $matchtype ) {
						
					if (is_array ( $matchModes ) && count ( $matchModes ) > 0) {

						foreach ( $matchModes as $k => $matchmode ) {
							$tmpData = array ();
								
							$whereMatchMode = "";
							if ($matchmode ['MatchModeID'] > 0) {
								$whereMatchMode = " AND MatchModeID = " . ( int ) $matchmode ['MatchModeID'];
							}
							$whereMatchType = "";
							if ($matchtype ['MatchTypeID'] > 0) {
								$whereMatchType = " AND MatchTypeID = " . ( int ) $matchtype ['MatchTypeID'];
							}
								
							$sqlWins = "SELECT COUNT(*)
									FROM `UserPoints`
									WHERE SteamID = up.SteamID AND PointsTypeID = 1
									" . $whereMatchMode . "
											" . $whereMatchType . "
													";
							$sqlLosses = "SELECT COUNT(*)
									FROM `UserPoints`
									WHERE SteamID = up.SteamID AND PointsTypeID = 2
									" . $whereMatchMode . "
											" . $whereMatchType . "
													";
							$sqlWinRate = "
									IF((" . $sqlWins . ")+(" . $sqlLosses . ") > 0,
											ROUND(((" . $sqlWins . ")/((" . $sqlWins . ")+(" . $sqlLosses . ")))*100,2)
													,0)";
							$sqlPointsEarned = "
									SELECT IF(SUM(PointsChange) > 0, SUM(PointsChange), 0)
									FROM `UserPoints`
									WHERE SteamID = up.SteamID
									" . $whereMatchMode . "
											" . $whereMatchType . "
													";
							$sql = "SELECT DISTINCT up.SteamID, (" . $sqlWins . ") as Wins,
									(" . $sqlLosses . ") as Losses,
											(" . $sqlPointsEarned . ") as PointsEarned,
													" . $sqlWinRate . " as WinRate
															FROM UserPoints up
															WHERE MatchModeID = " . ( int ) $matchmode ['MatchModeID'] . " AND MatchTypeID = " . ( int ) $matchtype ['MatchTypeID'] . "
																	HAVING PointsEarned > 0
																	ORDER BY PointsEarned DESC
																	";
							$tmpData = $DB->multiSelect ( $sql );
							$ret ['debug'] .= p ( $sql, 1 );
							// $ret['debug'] .= p($tmpData,1);
							if (is_array ( $tmpData ) && count ( $tmpData ) > 0) {
								$ret ['debug'] .= "NOT NULL";
								$key = recursive_array_search ( $steamID, $tmpData );
								$ret ['debug'] .= "KEY: " . $key;
								if ($key !== false) {
									$ret ['debug'] .= "ID:" . $steamID . " " . p ( $key, 1 );
									$tmpArray ['MatchTypeID'] = $matchtype ['MatchTypeID'];
									$tmpArray ['MatchModeID'] = $matchmode ['MatchModeID'];
									$tmpArray ['MatchType'] = $matchtype ['Name'];
									$tmpArray ['MatchMode'] = $matchmode ['Name'];
									$tmpArray ['MatchModeShortcut'] = $matchmode ['Shortcut'];
									$tmpArray ['position'] = ($key + 1);
									$tmpArray ['Elo'] = $tmpData [$key] ['PointsEarned'];
									$tmpArray ['Wins'] = $tmpData [$key] ['Wins'];
									$tmpArray ['Losses'] = $tmpData [$key] ['Losses'];
									$tmpArray ['WinRate'] = $tmpData [$key] ['WinRate'];
									$data [] = $tmpArray;
								}
							}
						}
					}
				}
			}
			// Ergebnisse nach position aufsteigend sortieren
			$orderedPositions = orderArrayBy ( $data, 'position', SORT_ASC );
				
			// je nachdem wieviele Ergebnisse zur�ckgegeben werden soll, die besten n zur�ckgeben
			if ($count != "*") {
				$count = ( int ) $count;

				for($i = 0; $i < $count; $i ++) {
					if ($orderedPositions [$i] ['position'] > 0) {
						$ret ['data'] [] = $orderedPositions [$i];
					}
				}
			}
			$ret ['debug'] .= p ( $ret ['data'], 1 );
				
			$ret ['status'] = true;
		} else {
			$ret ['status'] = "SteamID = 0";
		}

		$ret ['debug'] .= "End getBestRankings <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getGeneralRanking($steamID, $elo) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();

		if ($steamID > 0) {
			$tmpData = array ();
			// $sql = "SELECT SteamID, Rank
			// FROM UserStats
			// WHERE Rank >= ".(int) $elo."
			// AND (Wins > 0 OR Loses > 0)
			// ORDER BY Rank DESC
			// ";
			$sql = "SELECT IF(SUM(up.PointsChange)+u.BasePoints > 0, SUM(up.PointsChange)+u.BasePoints, 0) as Rank, u.SteamID
					FROM User u
					LEFT JOIN `UserPoints` up ON up.SteamID = u.SteamID
					LEFT JOIN UserLeague ul ON u.SteamID = ul.SteamID
					WHERE ul.LeagueTypeID > 1
					GROUP BY u.SteamID
					HAVING Rank > 0
					ORDER BY Rank DESC
					";
			$ret ['debug'] .= p ( $sql, 1 );
			$tmpData = $DB->multiSelect ( $sql );
				
			if (is_array ( $tmpData ) && count ( $tmpData ) > 0) {
				$position = 1;
				foreach ( $tmpData as $k => $v ) {
					if ($v ['SteamID'] == $steamID) {
						$ret ['position'] = $position;
					} else {
						$position ++;
					}
				}
			}
				
			$ret ['status'] = true;
		} else {
			$ret ['status'] = "SteamID 0";
		}
		return $ret;
	}
	
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getGeneralRankingSkillBracket($steamID, $points, $skillBracketTypeID, $matchTypeID=1) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
	
		if ($steamID > 0) {
			$tmpData = array ();
			
			switch($matchTypeID){
				case 1:
					$joinMatchTypeID = "usb.MatchTypeID = 1";
					$whereMatchTypeID = " (up.MatchTypeID = 1 OR up.MatchTypeID = 0)";
					break;
				default:
					$joinMatchTypeID = "usb.MatchTypeID = ".(int) $matchTypeID;
					$whereMatchTypeID = " up.MatchTypeID = ".(int) $matchTypeID; 
			}
			
			$sql = "SELECT up.SteamID, SUM(up.PointsChange)+u.BasePoints as Points, usb.SkillBracketTypeID
					FROM UserPoints up 
						JOIN `User` u ON u.SteamID = up.SteamID
						JOIN `UserSkillBracket` usb ON usb.SteamID = up.SteamID AND ".$joinMatchTypeID."
					WHERE ".$whereMatchTypeID." AND usb.SkillBracketTypeID >= ".(int)$skillBracketTypeID."
					GROUP BY up.SteamID
					HAVING Points >= ".(int) $points." OR usb.SkillBracketTypeID > ".(int)$skillBracketTypeID."
					ORDER BY usb.SkillBracketTypeID DESC, Points DESC
					";
			$ret ['debug'] .= p ( $sql, 1 );
			$count = $DB->countRows( $sql );
			
			$ret ['position'] = $count;
			$ret ['status'] = true;
		} else {
			$ret ['status'] = "SteamID 0";
		}
		return $ret;
	}
	
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getLadder($steamID = 0, $matchModeID = 0, $matchTypeID = 0, $limit = "") {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start getGeneralLadderPlayers <br>\n";

		// if($steamID == 0){
		// $steamID = $_SESSION['user']['steamID'];
		// }
		$whereMatchMode = "";
		if ($matchModeID > 0) {
			$whereMatchMode = " AND MatchModeID = " . ( int ) $matchModeID . " OR MatchModeID = 0)";
		}
		$whereMatchType = "";
		if ($matchTypeID > 0) {
			$whereMatchType = " AND (MatchTypeID = " . ( int ) $matchTypeID . " OR MatchTypeID = 0)";
		}

		if ($limit != "") {
			$limitSQL = "LIMIT " . ( int ) $limit;
		}

		$sqlWins = "SELECT COUNT(*)
				FROM `UserPoints`
				WHERE SteamID = u.SteamID AND PointsTypeID = 1
				" . $whereMatchMode . "
						" . $whereMatchType . "
								";
		$sqlLosses = "SELECT COUNT(*)
				FROM `UserPoints`
				WHERE SteamID = u.SteamID AND PointsTypeID = 2
				" . $whereMatchMode . "
						" . $whereMatchType . "
								";
		$sqlLeaves = "SELECT COUNT(*)
				FROM `UserPoints`
				WHERE SteamID = u.SteamID AND PointsTypeID = 5
				" . $whereMatchMode . "
						" . $whereMatchType . "
								";
		$sqlCredits = "
				SELECT SUM(Vote) as Credits
				FROM `UserCredits`
				WHERE SteamID = u.SteamID
				";
		$sqlWinRate = "
				IF((" . $sqlWins . ")+(" . $sqlLosses . ") > 0,
						ROUND(((" . $sqlWins . ")/((" . $sqlWins . ")+(" . $sqlLosses . ")))*100,2)
								,0)
								";
		if (! NOLEAGUES) {
			$whereLeagues = "WHERE ul.LeagueTypeID > 1 ";
		} else {
			$whereLeagues = "WHERE 1=1 ";
		}

		$sql = "SELECT u.Name, u.Avatar, u.SteamID, u.BasePoints,
				lt.Name as LeagueName,
				(" . $sqlWins . ") as Wins,
						(" . $sqlLosses . ") as Loses,
								(" . $sqlLeaves . ") as Leaves,
										(" . $sqlCredits . ") as Credits,
												" . $sqlWinRate . " as WinRate,

														IF(SUM(up.PointsChange)+u.BasePoints > 0, SUM(up.PointsChange)+u.BasePoints, 0) as Rank,
														SUM(up.PointsChange) as PointsEarned
														FROM User u
														LEFT JOIN `UserPoints` up ON up.SteamID = u.SteamID
														LEFT JOIN UserLeague ul ON u.SteamID = ul.SteamID
														LEFT JOIN LeagueType lt ON lt.LeagueTypeID = ul.LeagueTypeID
														" . $whereLeagues . "
																" . $whereMatchMode . "
																		" . $whereMatchType . "
																				GROUP BY u.SteamID
																				HAVING Rank > 0
																				ORDER BY Rank DESC, Wins DESC
																				" . $limitSQL . "
																						";
		$ret ['debug'] = p ( $sql, 1 );
		$data = $DB->multiSelect ( $sql );

		// StartNummber finden von Player
		$startNumber = $this->getStartDataNumber ( $data, $steamID );
		$ret ['startDataNumber'] = $startNumber;
		$ret ['data'] = $data;
		$ret ['debug'] .= $sql;

		$ret ['debug'] .= "End getGeneralLadderPlayers <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getStartDataNumber($data, $steamID) {
		if ($steamID > 0) {
			if (is_array ( $data ) && count ( $data ) > 0) {
				$startNumber = recursive_array_search ( $steamID, $data );

				$rest = $startNumber % 10;

				$startNumber = $startNumber - $rest;
			} else {
				$startNumber = 0;
			}
		} else {
			$startNumber = 0;
		}

		return $startNumber;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function loadLadderDataTable($steamID, $matchModeID, $matchTypeID, $section, $smarty) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start 	function loadLadderDataTable <br>\n";

		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		$retLadderData = $this->getLadder ( $steamID, $matchModeID, $matchTypeID );
		$ret ['debug'] .= p ( $retLadderData, 1 );
		$data = $retLadderData ['data'];
		$startNumber = $retLadderData ['startDataNumber'];

		// an Variable das Template fetchen
		$idTable = $section . "Tabs" . $matchModeID;
		$smarty->assign ( 'steamID', $steamID );
		$smarty->assign ( 'data', $data );
		$smarty->assign ( 'TableID', $idTable );
		$table = $smarty->fetch ( "ladder/tablePrototype.tpl" );
		$ret ['startDataNumber'] = $startNumber;
		$ret ["table"] = $table;
		$ret ['TableID'] = $idTable;
		$ret ['debug'] .= "End 	function loadLadderDataTable <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function loadLadderDataTable2($steamID, $matchModeID, $matchTypeID, $section, $smarty) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start 	function loadLadderDataTable <br>\n";

		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		$aColumns = array (
				'#',
				'LeagueName',
				'Rank',
				'Name',
				'PointsEarned',
				'BasePoints',
				'Wins',
				'Loses',
				'WinRate',
				'Leaves'
		);

		/*
		 * Paging
		*/
		$sLimit = "";
		if (isset ( $_GET ['iDisplayStart'] ) && $_GET ['iDisplayLength'] != '-1') {
			$sLimit = "LIMIT " . intval ( $_GET ['iDisplayStart'] ) . ", " . intval ( $_GET ['iDisplayLength'] );
		}

		/*
		 * Ordering
		*/
		// $sOrder = "ORDER BY Rank DESC, Wins DESC";
		// if ( isset( $_GET['iSortCol_0'] ) )
		// {
		// $sOrder = "ORDER BY ";
		// for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		// {
		// if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
		// {
		// $sOrder .= "`".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."` ".
		// ($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
		// }
		// }

		// $sOrder = substr_replace( $sOrder, "", -2 );
		// if ( $sOrder == "ORDER BY" )
		// {
		// $sOrder = "";
		// }
		// }

		/*
		 * Filtering NOTE this does not match the built-in DataTables filtering which does it word by word on any field. It's possible to do here, but concerned about efficiency on very large tables, and MySQL's regex functionality is very limited
		*/
		$sWhere = "";
		if (isset ( $_GET ['sSearch'] ) && $_GET ['sSearch'] != "") {
			$sWhere = "AND (";
			// 			for($i = 0; $i < count ( $aColumns ); $i ++) {
			// 				if($aColumns [$i])
				// 				$sWhere .= "`" . $aColumns [$i] . "` LIKE '%" . mysql_real_escape_string ( $_GET ['sSearch'] ) . "%' OR ";
			// 			}
			// 			$sWhere = substr_replace ( $sWhere, "", - 3 );
			// 			$sWhere .= "u.Name LIKE '%".mysql_real_escape_string ($_GET['sSearch'])."%' OR ";
			$sWhere .= "u.Name LIKE '%".mysql_real_escape_string ($_GET['sSearch'])."%' OR u.SteamID LIKE '%".mysql_real_escape_string ($_GET['sSearch'])."%'";
			$sWhere .= ')';
	}

	/*
	 * SQL queries Get data to display
	*/
	// $sQuery = "
	// SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."`
	// FROM $sTable
	// $sWhere
	// $sOrder
	// $sLimit
	// ";
	$whereMatchMode = "";
	if ($matchModeID > 0) {
		$whereMatchMode = " AND MatchModeID = " . ( int ) $matchModeID . " OR MatchModeID = 0)";
	}
	$whereMatchType = "";
	if ($matchTypeID > 0) {
		$whereMatchType = " AND (MatchTypeID = " . ( int ) $matchTypeID . " OR MatchTypeID = 0)";
	}

	$sqlWins = "SELECT COUNT(*)
			FROM `UserPoints`
			WHERE SteamID = u.SteamID AND PointsTypeID = 1
			" . $whereMatchMode . "
					" . $whereMatchType . "
							";
	$sqlLosses = "SELECT COUNT(*)
			FROM `UserPoints`
			WHERE SteamID = u.SteamID AND PointsTypeID = 2
			" . $whereMatchMode . "
					" . $whereMatchType . "
							";
	$sqlLeaves = "SELECT COUNT(*)
			FROM `UserPoints`
			WHERE SteamID = u.SteamID AND PointsTypeID = 5
			" . $whereMatchMode . "
					" . $whereMatchType . "
							";
	$sqlCredits = "
			SELECT SUM(Vote) as Credits
			FROM `UserCredits`
			WHERE SteamID = u.SteamID
			";
	$sqlWinRate = "
			IF((" . $sqlWins . ")+(" . $sqlLosses . ") > 0,
					ROUND(((" . $sqlWins . ")/((" . $sqlWins . ")+(" . $sqlLosses . ")))*100,2)
							,0)
							";
	if (! NOLEAGUES) {
		$whereLeagues = "WHERE ul.LeagueTypeID > 1 ";
	} else {
		$whereLeagues = "WHERE 1=1 ";
	}

	$sQuery = "SELECT u.Name, u.Avatar, u.SteamID, u.BasePoints,
			lt.Name as LeagueName,
			(" . $sqlWins . ") as Wins,
					(" . $sqlLosses . ") as Loses,
							(" . $sqlLeaves . ") as Leaves,
									(" . $sqlCredits . ") as Credits,
											" . $sqlWinRate . " as WinRate,

													IF(SUM(up.PointsChange)+u.BasePoints > 0, SUM(up.PointsChange)+u.BasePoints, 0) as Rank,
													SUM(up.PointsChange) as PointsEarned
													FROM User u
													LEFT JOIN `UserPoints` up ON up.SteamID = u.SteamID
													LEFT JOIN UserLeague ul ON u.SteamID = ul.SteamID
													LEFT JOIN LeagueType lt ON lt.LeagueTypeID = ul.LeagueTypeID
													" . $whereLeagues . "
															" . $whereMatchMode . "
																	" . $whereMatchType . "
																			" . $sWhere . "
																						
																					GROUP BY u.SteamID
																					HAVING Rank > 0
																					ORDER BY Rank DESC, Wins DESC
																					" . $sLimit . "
																							";
	$rResult = $DB->select( $sQuery, false);

	//p($sQuery);
	/* Data set length after filtering */
	$sQuery2 = "
			SELECT FOUND_ROWS()
			";
	$rResultFilterTotal =  $DB->select( $sQuery2, false);
	$aResultFilterTotal = mysql_fetch_array ( $rResultFilterTotal );
	$iFilteredTotal = $aResultFilterTotal [0];

	/* Total data set length */
	// $sQuery = "
	// SELECT COUNT(`".$sIndexColumn."`)
	// FROM $sTable
	// ";
	// $rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	// $aResultTotal = mysql_fetch_array($rResultTotal);
	// $iTotal = $aResultTotal[0];
	$sQuery3 = "SELECT u.Name, u.Avatar, u.SteamID, u.BasePoints,
			lt.Name as LeagueName,
			(" . $sqlWins . ") as Wins,
					(" . $sqlLosses . ") as Loses,
							(" . $sqlLeaves . ") as Leaves,
									(" . $sqlCredits . ") as Credits,
											" . $sqlWinRate . " as WinRate,

													IF(SUM(up.PointsChange)+u.BasePoints > 0, SUM(up.PointsChange)+u.BasePoints, 0) as Rank,
													SUM(up.PointsChange) as PointsEarned
													FROM User u
													LEFT JOIN `UserPoints` up ON up.SteamID = u.SteamID
													LEFT JOIN UserLeague ul ON u.SteamID = ul.SteamID
													LEFT JOIN LeagueType lt ON lt.LeagueTypeID = ul.LeagueTypeID
													" . $whereLeagues . "
															" . $whereMatchMode . "
																	" . $whereMatchType . "
																			" . $sWhere . "
																						
																					GROUP BY u.SteamID
																					HAVING Rank > 0
																					ORDER BY Rank DESC, Wins DESC";
	$iTotal = (String)$DB->countRows ( $sQuery3 );

	/*
	 * Output
	*/
	$output = array (
			"sEcho" => intval ( $_GET ['sEcho'] ),
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iTotal,
			"aaData" => array (),
			"debug" => $sQuery
	);
	$j = $_GET['iDisplayStart']+1;
	while ( $aRow = mysql_fetch_array ( $rResult ) ) {
			
		$row = array ();
		for($i = 0; $i < count ( $aColumns ); $i ++) {
			if ($aColumns [$i] == "#") {
				$row [] = "<strong>".$j.".<strong>";
			}
			elseif ($aColumns [$i] == "LeagueName"){
				if(NOLEAGUES == false){
					$smarty->assign ( 'leagueNameSimple', $aRow [$aColumns [$i]] );
					$html = $smarty->fetch ( "prototypes/medalIcon.tpl" );
					$row [] = $html;
				}
			}
			elseif ($aColumns [$i] == "Rank"){
				$row [] = "<strong>".$aRow [$aColumns [$i]]."</strong>";
			}
			elseif ($aColumns [$i] == "Name"){
				if($aRow ["SteamID"] == $steamID){
					$row["DT_RowClass"] ="info";
				}
				$row [] = '<a href="profile.php?ID='.$aRow ["SteamID"].'"><img src="'.$aRow['Avatar'].'" alt="'.$aRow['Name'].'\'s Avatar"> '.$aRow['Name'].'</a>';
			}
			elseif ($aColumns [$i] == "Wins"){
				$row [] = "<span class='text-success'>".$aRow [$aColumns [$i]]."</span>";
			}
			elseif ($aColumns [$i] == "Loses"){
				$row [] = "<span class='text-error'>".$aRow [$aColumns [$i]]."</span>";
			}
			elseif ($aColumns [$i] == "WinRate"){
				$row [] = "<span class='text-warning'>".$aRow [$aColumns [$i]]."%</span>";
			}
			else{
				/* General output */
				$row [] = $aRow [$aColumns [$i]];
			}
		}
		$output ['aaData'] [] = $row;
		$j++;
	}

	$ret ['debug'] .= "End 	function loadLadderDataTable <br>\n";

	return $output;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function loadLadderDataTableSkillBracket($steamID, $matchModeID, $matchTypeID, $section, $smarty) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start 	function loadLadderDataTable <br>\n";

		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		$aColumns = array (
				'#',
				'LeagueName',
				'Rank',
				'Name',
				'PointsEarned',
				'Wins',
				'Loses',
				'WinRate',
				'Leaves'
		);

		/*
		 * Paging
		*/
		$sLimit = "";
		if (isset ( $_GET ['iDisplayStart'] ) && $_GET ['iDisplayLength'] != '-1') {
			$sLimit = "LIMIT " . intval ( $_GET ['iDisplayStart'] ) . ", " . intval ( $_GET ['iDisplayLength'] );
		}

		/*
		 * Ordering
		*/
		// $sOrder = "ORDER BY Rank DESC, Wins DESC";
		// if ( isset( $_GET['iSortCol_0'] ) )
		// {
		// $sOrder = "ORDER BY ";
		// for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		// {
		// if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
		// {
		// $sOrder .= "`".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."` ".
		// ($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
		// }
		// }

		// $sOrder = substr_replace( $sOrder, "", -2 );
		// if ( $sOrder == "ORDER BY" )
		// {
		// $sOrder = "";
		// }
		// }

		/*
		 * Filtering NOTE this does not match the built-in DataTables filtering which does it word by word on any field. It's possible to do here, but concerned about efficiency on very large tables, and MySQL's regex functionality is very limited
		*/
		$sWhere = "";
		if (isset ( $_GET ['sSearch'] ) && $_GET ['sSearch'] != "") {
			$sWhere = "AND (";
			// 			for($i = 0; $i < count ( $aColumns ); $i ++) {
			// 				if($aColumns [$i])
			// 				$sWhere .= "`" . $aColumns [$i] . "` LIKE '%" . mysql_real_escape_string ( $_GET ['sSearch'] ) . "%' OR ";
			// 			}
			// 			$sWhere = substr_replace ( $sWhere, "", - 3 );
			// 			$sWhere .= "u.Name LIKE '%".mysql_real_escape_string ($_GET['sSearch'])."%' OR ";
			$sWhere .= "u.Name LIKE '%".mysql_real_escape_string ($_GET['sSearch'])."%' OR u.SteamID LIKE '%".mysql_real_escape_string ($_GET['sSearch'])."%'";
			$sWhere .= ')';
		}

		/*
		 * SQL queries Get data to display
		*/
		// $sQuery = "
		// SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."`
		// FROM $sTable
		// $sWhere
		// $sOrder
		// $sLimit
		// ";
		$whereMatchMode = "";
		if ($matchModeID > 0) {
			$whereMatchMode = " AND MatchModeID = " . ( int ) $matchModeID . " OR MatchModeID = 0)";
		}
		$whereMatchType = "";
		switch($matchTypeID){
			case "8":
				$whereMatchType = " AND MatchTypeID = " . ( int ) $matchTypeID . "";
				$joinUSBMatchType = " AND usb.MatchTypeID = ".(int)$matchTypeID."";
				$joinUPMatchType = " AND up.MatchTypeID = ".(int)$matchTypeID."";
				break;
			case "1":
				$whereMatchType = " AND (MatchTypeID = " . ( int ) $matchTypeID . " OR MatchTypeID = 0)";
				$joinUSBMatchType = " AND usb.MatchTypeID = ".(int)$matchTypeID."";
				$joinUPMatchType = " AND up.MatchTypeID = ".(int)$matchTypeID."";
				break;
			default:
				$whereMatchType = "";
				$joinUSBMatchType = "";
				$joinUPMatchType = "";
		}

		$sqlWins = "SELECT COUNT(*)
				FROM `UserPoints`
				WHERE SteamID = u.SteamID AND PointsTypeID = 1
				" . $whereMatchMode . "
						" . $whereMatchType . "
								";
		$sqlLosses = "SELECT COUNT(*)
				FROM `UserPoints`
				WHERE SteamID = u.SteamID AND PointsTypeID = 2
				" . $whereMatchMode . "
						" . $whereMatchType . "
								";
		$sqlLeaves = "SELECT COUNT(*)
				FROM `UserPoints`
				WHERE SteamID = u.SteamID AND PointsTypeID = 5
				" . $whereMatchMode . "
						" . $whereMatchType . "
								";
		$sqlCredits = "
				SELECT SUM(Vote) as Credits
				FROM `UserCredits`
				WHERE SteamID = u.SteamID
				";
		$sqlWinRate = "
				IF((" . $sqlWins . ")+(" . $sqlLosses . ") > 0,
						ROUND(((" . $sqlWins . ")/((" . $sqlWins . ")+(" . $sqlLosses . ")))*100,2)
								,0)
								";
		$sqlPoints = "
				SELECT SUM(PointsChange)
				FROM `UserPoints`
				WHERE SteamID = u.SteamID ".$whereMatchType."
						";
		if (! NOLEAGUES) {
			$whereSkillBracket = "WHERE usb.SkillBracketTypeID > 1 ";
		} else {
			$whereSkillBracket = "WHERE 1=1 ";
		}

		$sQuery = "SELECT u.Name, u.Avatar, u.SteamID,
				sbt.Name as SkillBracketName, usb.SkillBracketTypeID,
				(" . $sqlWins . ") as Wins,
						(" . $sqlLosses . ") as Loses,
								(" . $sqlLeaves . ") as Leaves,
										(" . $sqlCredits . ") as Credits,
												" . $sqlWinRate . " as WinRate,
														(".$sqlPoints.") as PointsEarned, u.BasePoints
																
																FROM User u
																		LEFT JOIN UserSkillBracket usb ON u.SteamID = usb.SteamID ".$joinUSBMatchType."
																				LEFT JOIN SkillBracketType sbt ON sbt.SkillBracketTypeID = usb.SkillBracketTypeID
																				" . $whereSkillBracket . "
																						" . $whereMatchMode . "
																								-- " . $whereMatchType . "
																										" . $sWhere . "

																												GROUP BY u.SteamID
																												HAVING PointsEarned+u.BasePoints > 0
																												ORDER BY usb.SkillBracketTypeID DESC, PointsEarned DESC, Wins DESC

																												";
		$rResult = $DB->select( $sQuery.$sLimit, false);

		//p($sQuery);
		/* Data set length after filtering */
		$sQuery2 = "
				SELECT FOUND_ROWS()
				";
		$rResultFilterTotal =  $DB->select( $sQuery2, false);
		$aResultFilterTotal = mysql_fetch_array ( $rResultFilterTotal );
		$iFilteredTotal = $aResultFilterTotal [0];

		$iTotal = (String)$DB->countRows ( $sQuery );

		/*
		 * Output
		*/
		$output = array (
				"sEcho" => intval ( $_GET ['sEcho'] ),
				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iTotal,
				"aaData" => array (),
				"debug" => $sQuery
		);
		$j = $_GET['iDisplayStart']+1;
		while ( $aRow = mysql_fetch_array ( $rResult ) ) {

			$row = array ();
			for($i = 0; $i < count ( $aColumns ); $i ++) {
				if ($aColumns [$i] == "#") {
					$row [] = "<strong>".$j.".<strong>";
				}
				elseif ($aColumns [$i] == "LeagueName"){
					if(NOLEAGUES == false){
						$smarty->assign ( 'skillBracketTypeID', $aRow ['SkillBracketTypeID'] );
						$smarty->assign ( 'skillBracketName', $aRow ['SkillBracketName'] );
						$html = $smarty->fetch ( "prototypes/skillBracketIcon.tpl" );
						$row [] = $html;
					}
				}
				// 				elseif ($aColumns [$i] == "Rank"){
				// 					$row [] = "<strong>".$aRow [$aColumns [$i]]."</strong>";
				// 				}
				elseif ($aColumns [$i] == "Rank"){
					$row [] = "<strong>".($aRow ["PointsEarned"]+$aRow ["BasePoints"])."</strong>";
				}

				elseif ($aColumns [$i] == "Name"){
					if($aRow ["SteamID"] == $steamID){
						$row["DT_RowClass"] ="info";
					}
					$row [] = '<a href="profile.php?ID='.$aRow ["SteamID"].'"><img src="'.$aRow['Avatar'].'" alt="'.$aRow['Name'].'\'s Avatar" width="32"> '.$aRow['Name'].'</a>';
				}
				elseif ($aColumns [$i] == "Wins"){
					$row [] = "<span class='text-success'>".$aRow [$aColumns [$i]]."</span>";
				}
				elseif ($aColumns [$i] == "Loses"){
					$row [] = "<span class='text-error'>".$aRow [$aColumns [$i]]."</span>";
				}
				elseif ($aColumns [$i] == "WinRate"){
					$row [] = "<span class='text-warning'>".$aRow [$aColumns [$i]]."%</span>";
				}
				else{
					/* General output */
					$row [] = $aRow [$aColumns [$i]];
				}
			}
			$output ['aaData'] [] = $row;
			$j++;
		}

		$ret ['debug'] .= "End 	function loadLadderDataTable <br>\n";

		return $output;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getBestPlayersFetchedData($smarty = null, $matchModeID = 0) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$steamID = $_SESSION ['user'] ['steamID'];

		$ret ['debug'] .= "Start getBestPlayersFetchedData <br>\n";

		$dataRet = $this->getLadder ( 0, $matchModeID, 0, 5 );
		$data = $dataRet ['data'];

		$ret ['debug'] .= p ( $dataRet, 1 );

		$smarty->assign ( 'data', $data );
		$smarty->assign ( 'steamID', $steamID );
		$table = $smarty->fetch ( "index/loggedIn/wallOfFame/bestPlayers/bestPlayersTable.tpl" );

		$ret ['data'] = $table;

		$ret ['status'] = true;

		$ret ['debug'] .= "End getBestPlayersFetchedData <br>\n";

		return $ret;
	}
}

?>