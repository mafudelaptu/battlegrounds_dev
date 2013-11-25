<?php
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
*/
class User {
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function getName($steamID) {
		if ($steamID > 0) {
			$DB = new DB ();
			$con = $DB->conDB ();
			$sql = "SELECT Name
					FROM User
					WHERE SteamID = " . secureNumber ( $steamID ) . "
							";

			$ret = $DB->select ( $sql );

			return $ret ["Name"];
		} else {
			return "";
		}
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function getUserData($steamID, $select = "*") {
		$ret = array ();

		if ($steamID > 0) {
			$DB = new DB ();
			$con = $DB->conDB ();
			$sql = "SELECT " . $select . "
					FROM User
					WHERE SteamID = " . secureNumber ( $steamID ) . "
							";

			$ret = $DB->select ( $sql );
			return $ret;
		} else {
			return $ret;
		}
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function userAlreadyInDB($steamID) {
		if ($steamID > 0) {
			$DB = new DB ();
			$con = $DB->conDB ();
			$sql = "SELECT SteamID
					FROM User
					WHERE SteamID = " . secureNumber ( $steamID ) . "
							";

			$count = $DB->countRows ( $sql );

			if ($count > 0) {
				$ret = true;
			} else {
				$ret = false;
			}
		} else {
			$ret = "not logged in!";
		}

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function updateUser($steamID) {
		// Cookie verländern
		// setcookie("login[steamID]", $steamID, time()+36000);
		$Hash = new Hash ();
		$User = new User ();
		$hash = $_COOKIE ['loginHash2'];

		$check = $Hash->checkHash ( $hash );

		if ($check) {
			$_SESSION ['user'] ['steamID'] = $check;
		} else {
			$name = $User->getName ( $steamID );
			$Hash->updateHash ( $steamID, $name );
		}

		// Userdaten wie Name und Avatar
		$ret = $this->updateUserData ( $steamID );

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function getUserDataFromSteam($steamID) {
		$data = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" . STEAM_KEY . "&steamids=" . $steamID . "";
		$ch = curl_init ( $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		$json = curl_exec ( $ch );
		curl_close ( $ch );
		$dataSteam = json_decode ( $json, true );

		$personaname = $dataSteam ["response"] ["players"] [0] ["personaname"];
		$avatar = $dataSteam ["response"] ["players"] [0] ["avatar"];
		$avatarMed = $dataSteam ["response"] ["players"] [0] ["avatarmedium"];
		$avatarFull = $dataSteam ["response"] ["players"] [0] ["avatarfull"];
		$profileURL = $dataSteam ["response"] ["players"] [0] ["profileurl"];

		$data = array (
				"SteamID" => $steamID,
				"Name" => mysql_real_escape_string ( $personaname ),
				"Avatar" => mysql_real_escape_string ( $avatar ),
				"AvatarMed" => mysql_real_escape_string ( $avatarMed ),
				"AvatarFull" => mysql_real_escape_string ( $avatarFull ),
				"ProfileURL" => mysql_real_escape_string ( $profileURL )
		);
		return $data;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function updateUserData($steamID) {
		$ret = array ();

		if ($steamID > 0) {
			$DB = new DB ();
			$con = $DB->conDB ();

			$data = $this->getUserDataFromSteam ( $steamID );

			if (is_array ( $data ) && count ( $data ) > 0) {
				$set = "";
				foreach ( $data as $k => $v ) {
					if ($set != "") {
						$set .= ", ";
					}

					if ($v != "") {
						$set .= $k . "='" . mysql_real_escape_string ( $v ) . "'";
					}
				}
			}
			$ret ['debug'] .= "SET: " . $set;
			if ($set != "") {
				$sql = 'UPDATE User
						SET ' . $set . '
								WHERE SteamID = ' . secureNumber ( $steamID ) . '
										';
				$ret ['debug'] .= p ( $sql, 1 );
				$DB->update ( $sql, 0 );

				// BaseElo updaten
				$retBasePoints = $this->setBasePointsForPlayer ( $steamID );
				$ret ['debug'] .= p ( $retBasePoints, 1 );

				$ret ['status'] = true;
			} else {
				$ret ['status'] = false;
				$ret ['debug'] .= "Steam down";
			}
		} else {
			$ret ['debug'] .= "not logged in!";
		}
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function insertNewUser($steamID) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] = "";
		$ret ['debug'] .= "Start insertNewUser <br>\n";
		$Hash = new Hash ();

		// Daten von Steam asulesen (Avatar ect)
		$data = $this->getUserDataFromSteam ( $steamID );

		// Cookie Hash berechnen
		$hash = $Hash->createHash ( $steamID, $data ['Name'] );

		$data ['Hash'] = secureStringsNumbers ( $hash );
		$data['CreatedTimestamp'] = time();

		$ret = $DB->insert ( "User", $data );
		$_SESSION ['DEBUG'] .= $hash . "<br>";
		// cookie Setzen
		setcookie ( "loginHash2", $hash, time () + 36000 );

		// $ret['debug'] .= "End insertNewUser <br>\n";

		$_SESSION ['DEBUG'] .= p ( $ret, 1 );
		return $ret;
	}
	
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function insertNewUser2($userData) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] = "";
		$ret ['debug'] .= "Start insertNewUser <br>\n";
		$data['SteamID'] = (int) $userData['SteamID'];
		$data['Name'] = mysql_real_escape_string($userData['Name']);
		$avatar = "";
		if($userData['Avatar'] != ""){
			$avatar = "http://ihearthu.com/forums/uploads/".mysql_real_escape_string($userData['Avatar']);
		}
		else{
			$avatar = "http://ihearthu.com/forums/public/style_images/memory/profile/default_large.png";
		}
		
		$data['AvatarFull'] = $avatar;
		$data['AvatarMed'] = $avatar;
		$data['Avatar'] = $avatar;
		$data ['Hash'] = secureStringsNumbers ( $userData['Hash'] );
		$data['CreatedTimestamp'] = time();
		
		$ret = $DB->insert ( "User", $data );
		$_SESSION ['DEBUG'] .= $hash . "<br>";
		// cookie Setzen
		setcookie ( "loginHash2", $hash, time () + 36000 );
	
		// $ret['debug'] .= "End insertNewUser <br>\n";
	
		$_SESSION ['DEBUG'] .= p ( $ret, 1 );
		return $ret;
	}
	
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function isAdmin($steamID) {
		$ret = array ();

		if ($steamID > 0) {
			$DB = new DB ();
			$con = $DB->conDB ();
			$sql = "SELECT Admin
					FROM User
					WHERE SteamID = " . secureNumber ( $steamID ) . "
							";

			$ret = $DB->select ( $sql );
			if ($ret ['Admin'] == 1) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function getUserCount() {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();

		$sql = "SELECT SteamID
				FROM User
				";
		$count = $DB->countRows ( $sql );
		$ret ['count'] = $count;
		$ret ['status'] = true;

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function punishUserForLeave($steamID, $matchModeID, $matchTypeID, $matchID) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start punishUserForLeave <br>\n";
		if ($steamID > 0 && $matchID > 0) {

			$punishValue = 50; // Wie hoch die Elo strafe sein soll

			// UserElo f�r Matchmode und MatchType updaten
			// $sql = "UPDATE UserElo
			// SET Elo = Elo - ".$punishValue."
			// WHERE SteamID = ".secureNumber($steamID)." AND MatchModeID = ".(int) $matchModeID." AND MatchTypeID = ".(int) $matchTypeID."
			// ";
			// $retU = $DB->update($sql);
			// $ret['debug'] .= p("UserElo Update:".$retU,1);
			// Generellen Elo, LeaverCount anpassen
			$sql = "UPDATE UserStats
					SET Rank = Rank - " . $punishValue . ", Leaves = Leaves + 1
							WHERE SteamID = " . secureNumber ( $steamID ) . "
									";
			$retU = $DB->update ( $sql );
			$ret ['debug'] .= p ( "UserStats Update:" . $retU, 1 );

			// MatchDetails EloChange updaten
			$sql = "UPDATE `MatchDetails`
					SET EloChange = '-" . $punishValue . "'
							WHERE SteamID = " . secureNumber ( $steamID ) . " AND MatchID = " . ( int ) $matchID . "
									";
			$retU = $DB->update ( $sql );
			$ret ['debug'] .= p ( "MatchDetails Update:" . $retU, 1 );

			$ret ['status'] = true;
		} else {
			$ret ['status'] = "steamID = 0";
		}

		$ret ['debug'] .= "End punishUserForLeave <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function getAllUser($alsoSelf = true, $queryName = "", $forTypeAhead = "false", $limit = "*", $justYourLeague = false) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$steamID = $_SESSION ['user'] ['steamID'];

		$ret ['debug'] .= "Start getAllUser <br>\n";
		$ret ['debug'] .= p ( "alsoSelf:" . $alsoSelf, 1 );
		$where = "";
		if ($alsoSelf === "false" OR $alsoSelf !== true) {
			if ($steamID > 0) {
				$where = "WHERE u.SteamID != " . secureNumber ( $steamID );
			}
		} else {
			$ret ['debug'] .= p ( "self = true:" . ($alsoSelf), 1 );
		}
		if ($queryName != "") {
			if ($where != "") {
				$where .= " AND ";
			} else {
				$where .= "WHERE ";
			}
			$where .= "(u.Name LIKE '%" . secureStringsNumbers ( $queryName ) . "%' OR u.SteamID LIKE '%" . secureStringsNumbers ( $queryName ) . "%')";
		}

		if ($justYourLeague) {
			if ($where != "") {
				$where .= " AND ";
			} else {
				$where = "WHERE ";
			}
			// 			$UserLeague = new UserLeague ();
			// 			$retUL = $UserLeague->getLeagueOfUser ( $steamID );
			// 			$leagueTypeID = $retUL ['data'] ['LeagueTypeID'];
			$UserSkillBracket = new UserSkillBracket();
			$retUL = $UserSkillBracket->getSkillBracketOfUser( $steamID );
			$skillBracketTypeID = $retUL ['data'] ['SkillBracketTypeID'];
			if ($skillBracketTypeID > 0) {
				$where .= "usb.SkillBracketTypeID = " . ( int ) $skillBracketTypeID . "";
			}
		}

		$limitSQL = "";
		if ($limit != "*") {
			$limitSQL = "LIMIT " . ( int ) $limit;
		}
		if ($where == "WHERE ") {
			$where = "";
		}
		$sql = "SELECT u.Name, u.Avatar, u.SteamID
				FROM `User` u LEFT JOIN UserSkillBracket usb ON usb.SteamID = u.SteamID AND usb.MatchTypeID = 1
				" . $where . "
						" . $limitSQL . "
								";
		$ret ['debug'] .= p ( $sql, 1 );
		$data = $DB->multiSelect ( $sql );

		$ret ['debug'] .= p ( $forTypeAhead, 1 );

		if ($forTypeAhead == "true") {
			// Array f�r Typeahead umschreiben
			if (is_array ( $data ) && count ( $data ) > 0) {
				$retArray = array ();
				foreach ( $data as $k => $v ) {
					$retArray [] = $v ['Name'];
				}
			}
			$ret ['data'] = $retArray;
		} else {
			$ret ['data'] = $data;
		}

		$ret ['status'] = true;

		$ret ['debug'] .= "End getAllUser <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function fetchUserTable($data, $smarty) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start fetchUserTable <br>\n";

		$smarty->assign ( "data", $data );
		$smarty->assign ( "TableID", "selectDuoPartnerModalBodyTable" );
		$table = $smarty->fetch ( "find_match/modals/selectDuoPartnerModal.tablePrototype.tpl" );

		$ret ['data'] = $table;
		$ret ['status'] = true;

		$ret ['debug'] .= "End fetchUserTable <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function setBasePointsForPlayer($steamID = 0) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start setBasePointsForPlayer <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if ($steamID > 0) {
			if (DOTABUFFBASEPOINTS) {
				$retU = $this->getUserData ( $steamID );
				$basePointsUpdateTS = $retU ['BasePointsUpdatedTimestamp'];
				$basePoints = $v ['BasePoints'];

				$testTS = time () - 60 * 60 * 24; // 1 Tag
				$ret ['debug'] .= p ( "bpTS:" . $basePointsUpdateTS . " test:" . $testTS, 1 );
				if ($basePointsUpdateTS < $testTS || $basePointsUpdateTS == "0") {
					$UserPoints = new UserPoints ();
					$retUE = $UserPoints->calculateBasePointsOfUser ( $steamID );
					$baseElo = $retUE ['data'];
					if (( int ) $baseElo > 0) {
						$sql = "UPDATE User SET BasePoints = " . ( int ) $baseElo . ", BasePointsUpdatedTimestamp = " . time () . "
								WHERE SteamID = " . secureNumber ( $steamID ) . "
										";
						$ret ['debug'] .= p ( $sql, 1 );
						$data = $DB->update ( $sql );
						$ret ['status'] = $data;
					} else {
						$ret ['status'] = "BasePoints ausgelesen = 0";
					}
				} else {
					$ret ['status'] = "already updated!";
				}
			} else {
				$retU = $this->getUserData ( $steamID );
				$basePoints = $retU ['BasePoints'];

				if ($basePoints != STARTBASEPOINTS) {
					$baseElo = STARTBASEPOINTS;

					$sql = "UPDATE User SET BasePoints = " . ( int ) $baseElo . ", BasePointsUpdatedTimestamp = " . time () . "
							WHERE SteamID = " . secureNumber ( $steamID ) . "
									";
					$ret ['debug'] .= p ( $sql, 1 );
					$data = $DB->update ( $sql );
					$ret ['status'] = $data;
				} else {
					$ret ['status'] = "already updated!";
				}
			}
		} else {
			$ret ['status'] = "steamID = 0";
		}

		$ret ['debug'] .= "End setBasePointsForPlayer <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function getStatsOfUser($steamID = 0) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start getStatsOfUser <br>\n";

		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if ($steamID > 0) {

			$UserPoints = new UserPoints ();
			$retUP = $UserPoints->getGlobalPointsOfUser ( $steamID );
			$ret ['debug'] .= p ( $retUP, 1 );
			$points = $retUP ['data'];

			$retUP1vs1 = $UserPoints->getGlobalPointsOfUser ( $steamID, 8); // 1vs1
			$ret ['debug'] .= p ( $retUP, 1 );
			$points1vs1 = $retUP1vs1['data'];

			$retUP = $UserPoints->getGameStatsOfUser ( $steamID, 1);
			$gameStats = $retUP ['data'];

			$retUP2 = $UserPoints->getGameStatsOfUser ( $steamID, 8); // 1vs1 stats
			$gameStats1vs1 = $retUP2 ['data'];

			// 			$UserLeague = new UserLeague ();
			// 			$retUL = $UserLeague->getLeagueOfUser ( $steamID );
			// 			$leagueData = $retUL ['data'];

			// 			$retUL = $UserLeague->getLeagueLvl ( $steamID, $leagueData ['LeagueTypeID'], $points );
			// 			$ret ['debug'] .= p ( $retUL, 1 );
			// 			$leagueLvl = $retUL ['data'];

			$data ['Points'] = ( int ) $points;
			$data['Points1vs1'] = (int) $points1vs1;
			$data ['LeagueData'] = $leagueData;
			$data ['LeagueLvl'] = $leagueLvl;
			$data ['GameStats'] = $gameStats;
			$data['GameStats1vs1'] = $gameStats1vs1;
			$ret ['data'] = $data;
			$ret ['status'] = true;
		} else {
			$ret ['status'] = "steamID = 0";
		}

		$ret ['debug'] .= "End getStatsOfUser <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function getAllUser2($alsoSelf = true, $queryName = "", $forTypeAhead = "false") {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start createChatFile <br>\n";

		$sql = "SELECT * FROM `User`";
		$data = $DB->select ( $sql );
		$ret ['debug'] .= p ( $sql, 1 );

		$ret ['debug'] .= "End createChatFile <br>\n";
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function isActivePlayer($steamID = 0) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start isActivePlayer <br>\n";

		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if ($steamID > 0) {

			$startTimestamp = strtotime("-2 weeks");

			$sql = "SELECT *
					FROM `MatchDetails` md LEFT JOIN `Match` m ON m.MatchID = md.MatchID
					WHERE md.SteamID = " . secureNumber ( $steamID ) . " AND TimestampClosed >= ".(int)$startTimestamp." AND TimestampClosed <= ".time()."
							";
			$count = $DB->countRows( $sql );
			$ret ['debug'] .= p ( $sql, 1 );
			$ret['count'] = $count;
			if($count >= 10){
				$ret['data'] = true;
			}
			else{
				$ret['data'] = false;
			}
			$ret ['status'] = true;
		} else {
			$ret ['status'] = "steamID == 0";
		}
		$ret ['debug'] .= "End isActivePlayer <br>\n";
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-10-25
	*/
	function setLastActivity($steamID = 0) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start setLastActivity <br>\n";

		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if ($steamID > 0) {

			$sql = "UPDATE `User`
					SET LastActivityTimestamp = ".time()."
							WHERE SteamID = ".secureNumber($steamID)."
									";
			$data = $DB->update($sql);
			$ret['debug'] .= p($sql,1);
			$ret ['status'] = true;
		} else {
			$ret ['status'] = "steamID == 0";
		}
		$ret ['debug'] .= "End setLastActivity <br>\n";
		return $ret;
	}

}

?>
