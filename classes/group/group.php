<?php
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class Group {
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function createDuoGroup($partnerSteamID, $name = "") {

		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();

		$ret ['debug'] .= "Start createDuoGroup <br>\n";
		$ret ['debug'] .= p ( "Name:" . $name, 1 );
		$steamID = $_SESSION ['user'] ['steamID'];
		if(TEAMSACTIVE){
			if ($partnerSteamID > 0 && $steamID > 0) {
				$checkRet = $this->checkIfGroupAlreadyCreated ( $partnerSteamID );
				if (! $checkRet ['existing']) {
					$UserLeague = new UserLeague();
					$retUL = $UserLeague->getLeagueOfUser($steamID);
					$retUL2 = $UserLeague->getLeagueOfUser($partnerSteamID);
					$leagueTypeID = $retUL['data']['LeagueTypeID'];
					$leagueTypeIDPartner = $retUL2['data']['LeagueTypeID'];
					$ret['debug'] .= p("L1:".$leagueTypeID." L2:".$leagueTypeIDPartner,1);
					if($leagueTypeIDPartner == $leagueTypeID){
						$insertArray = array ();
						
						$insertArray ['CreatedTimestamp'] = time ();
						$insertArray ['Name'] = mysql_real_escape_string ( $name );
						// $ret['debug'] .= $DB->insert("Group", $insertArray,null,1);
						$insRet = $DB->insert ( "Group", $insertArray );
						
						$groupID = mysql_insert_id ();
						// wenn erfolgreich Gruppe angelegt, dann noch mitglieder hinzuf�gen
						if ($insRet) {
							$insertArray = array ();
							$insertArray [0] ['GroupID'] = ( int ) $groupID;
							$insertArray [0] ['SteamID'] = secureNumber ( $steamID );
							$insertArray [0] ['Accepted'] = 1;
							$insertArray [1] ['GroupID'] = ( int ) $groupID;
							$insertArray [1] ['SteamID'] = secureNumber ( $partnerSteamID );
							$insertArray [1] ['Accepted'] = 0;
							$ret ['debug'] .= $DB->multiInsert ( "GroupMembers", $insertArray, 1 );
							$mInsRet = $DB->multiInsert ( "GroupMembers", $insertArray );
							$ret ['status'] = $mInsRet;
							$ret ['data'] = ( int ) $groupID;
						} else {
							$ret ['status'] = "Team creation failed!";
						}
					}
					else{
						$ret ['status'] = "Team creation failed! (Skill-Bracket)";
					}
					
				} else {
					$ret ['data'] = ( int ) $checkRet ['GroupID'];
					$ret ['status'] = "Team already exists!";
				}
			} else {
				$ret ['status'] = "seach for a duo-partner first!";
			}
		}


		$ret ['debug'] .= "End createDuoGroup <br>\n";

		return $ret;
	}
	
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function createDuoGroupSkillBracket($partnerSteamID, $name = "") {
	
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
	
		$ret ['debug'] .= "Start createDuoGroup <br>\n";
		$ret ['debug'] .= p ( "Name:" . $name, 1 );
		$steamID = $_SESSION ['user'] ['steamID'];
		if(TEAMSACTIVE){
			if ($partnerSteamID > 0 && $steamID > 0) {
				$checkRet = $this->checkIfGroupAlreadyCreated ( $partnerSteamID );
				if (! $checkRet ['existing']) {
					$UserSkillBracket = new UserSkillBracket();
					$retUL = $UserSkillBracket->getSkillBracketOfUser($steamID);
					$retUL2 = $UserSkillBracket->getSkillBracketOfUser($partnerSteamID);
					$leagueTypeID = $retUL['data']['SkillBracketTypeID'];
					$leagueTypeIDPartner = $retUL2['data']['SkillBracketTypeID'];
					$ret['debug'] .= p("L1:".$leagueTypeID." L2:".$leagueTypeIDPartner,1);
					if($leagueTypeIDPartner == $leagueTypeID){
						$insertArray = array ();
	
						$insertArray ['CreatedTimestamp'] = time ();
						$insertArray ['Name'] = mysql_real_escape_string ( $name );
						// $ret['debug'] .= $DB->insert("Group", $insertArray,null,1);
						$insRet = $DB->insert ( "Group", $insertArray );
	
						$groupID = mysql_insert_id ();
						// wenn erfolgreich Gruppe angelegt, dann noch mitglieder hinzuf�gen
						if ($insRet) {
							$insertArray = array ();
							$insertArray [0] ['GroupID'] = ( int ) $groupID;
							$insertArray [0] ['SteamID'] = secureNumber ( $steamID );
							$insertArray [0] ['Accepted'] = 1;
							$insertArray [1] ['GroupID'] = ( int ) $groupID;
							$insertArray [1] ['SteamID'] = secureNumber ( $partnerSteamID );
							$insertArray [1] ['Accepted'] = 0;
							$ret ['debug'] .= $DB->multiInsert ( "GroupMembers", $insertArray, 1 );
							$mInsRet = $DB->multiInsert ( "GroupMembers", $insertArray );
							$ret ['status'] = $mInsRet;
							$ret ['data'] = ( int ) $groupID;
						} else {
							$ret ['status'] = "Team creation failed!";
						}
					}
					else{
						$ret ['status'] = "Team creation failed! (Skill-Bracket ".$leagueTypeID." ".$leagueTypeIDPartner;
					}
						
				} else {
					$ret ['data'] = ( int ) $checkRet ['GroupID'];
					$ret ['status'] = "Team already exists!";
				}
			} else {
				$ret ['status'] = "seach for a duo-partner first!";
			}
		}
	
	
		$ret ['debug'] .= "End createDuoGroup <br>\n";
	
		return $ret;
	}
	
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getGroupMembers($groupID) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start getGroupMembers <br>\n";
		if ($groupID > 0) {
			$sql = "SELECT *
					FROM `GroupMembers`
					WHERE GroupID = " . secureNumber ( $groupID ) . "
							";
			$ret ['debug'] .= p ( $sql, 1 );
			$data = $DB->multiSelect ( $sql );

			$ret ['data'] = $data;

			$ret ['status'] = true;
		} else {
			$ret ['status'] = "groupID = 0";
		}

		$ret ['debug'] .= "End getGroupMembers <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getGroupsOfUser($steamID = 0) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();

		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		$ret ['debug'] .= "Start getGroupsOfUser <br>\n";
		if ($steamID > 0) {
			// $sql = "SELECT g.GroupID, gm.SteamID, u.Avatar, u.Name
			// FROM `Group` g JOIN `GroupMembers` gm ON g.GroupID = gm.GroupID
			// JOIN User u ON u.SteamID = gm.SteamID
			// WHERE EXISTS (
			// SELECT GroupID
			// FROM GroupMembers
			// WHERE SteamID = ".secureNumber($steamID)."
			// )
			// ";
			$sql = "SELECT GroupID
					FROM `GroupMembers`
					WHERE SteamID = " . secureNumber ( $steamID ) . "
							";

			$ret ['debug'] .= p ( $sql, 1 );
			$data = $DB->multiSelect ( $sql );
			if (is_array ( $data ) && count ( $data ) > 0) {
				$data2 = array ();
				foreach ( $data as $k => $v ) {
					$sql = "SELECT gm.GroupID, gm.SteamID, u.Avatar, u.Name
							FROM `GroupMembers` gm JOIN User u ON u.SteamID = gm.SteamID
							WHERE GroupID = " . ( int ) $v ['GroupID'] . "
									";
					// $ret['debug'] .= p($sql,1);
					$tmp = $DB->multiSelect ( $sql );
					// $ret['debug'] .= p($tmp,1);
					if (is_array ( $tmp ) && count ( $tmp ) > 0) {
						$array = array ();
						$dataTest = array ();
						foreach ( $tmp as $k => $v ) {
							$userArray ['Name'] = $v ['Name'];
							$userArray ['Avatar'] = $v ['Avatar'];
							$userArray ['SteamID'] = $v ['SteamID'];

							$data2 [$v ['GroupID']] ['members'] [] = $userArray;
							$data2 [$v ['GroupID']] ['data'] ['count'] ++;
						}
					}
				}
			}
			// $ret['debug'] .= p($data2,1);

			$ret ['data'] = $data2;
			$ret ['status'] = true;
		} else {
			$ret ['status'] = "steamID = 0";
		}

		$ret ['debug'] .= "End getGroupsOfUser <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getGroupsOfUser2($steamID = 0, $smarty = false, $checkInvites = false) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();

		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		$ret ['debug'] .= "Start getGroupsOfUser <br>\n";
		if ($steamID > 0) {
			// $sql = "SELECT g.GroupID, gm.SteamID, u.Avatar, u.Name
			// FROM `Group` g JOIN `GroupMembers` gm ON g.GroupID = gm.GroupID
			// JOIN User u ON u.SteamID = gm.SteamID
			// WHERE EXISTS (
			// SELECT GroupID
			// FROM GroupMembers
			// WHERE SteamID = ".secureNumber($steamID)."
			// )
			// ";
			$sql = "SELECT *, Count(*) as Count, (SELECT Count(*) FROM GroupMembers tmp WHERE Accepted = 1 AND tmp.GroupID = gm.GroupID) as CountAccepted
					FROM `GroupMembers` gm LEFT JOIN `Group` g ON g.GroupID = gm.GroupID
					WHERE SteamID = ".secureNumber($steamID)."
							GROUP BY gm.GroupID
							HAVING CountAccepted = 2
							";

			$ret ['debug'] .= p ( $sql, 1 );
			$data = $DB->multiSelect ( $sql );
			if (is_array ( $data ) && count ( $data ) > 0) {
				$data2 = array ();
				foreach ( $data as $k => $v ) {
					$count = $v ['Count'];
					if($checkInvites){
						if ($count == 2) {
							$show = true;
						}
						else{
							$show = false;
						}
					}
					else{
						$show = true;
					}
					$show = true;
					if ($show) {
						if ($v ['Name'] == "") {
							$data2 [$v ['GroupID']] ['Name'] = "Duo-Team";
						} else {
							$data2 [$v ['GroupID']] ['Name'] = $v ['Name'];
						}

						$sql = "SELECT gm.GroupID, gm.SteamID, u.Avatar, u.Name, gm.Accepted, usb.SkillBracketTypeID
								FROM `GroupMembers` gm 
									JOIN User u ON u.SteamID = gm.SteamID
									LEFT JOIN UserSkillBracket usb ON usb.SteamID = gm.SteamID AND usb.MatchTypeID = 1
								WHERE GroupID = " . ( int ) $v ['GroupID'] . "
										";
						$ret['debug'] .= p($sql,1);
						$tmp = $DB->multiSelect ( $sql );
						// $ret['debug'] .= p($tmp,1);
						if (is_array ( $tmp ) && count ( $tmp ) > 0) {
							$array = array ();
							$dataTest = array ();
							foreach ( $tmp as $k2 => $v2 ) {
								$userArray ['Name'] = $v2 ['Name'];
								$userArray ['Avatar'] = $v2 ['Avatar'];
								$userArray ['SteamID'] = $v2 ['SteamID'];
								$userArray ['Accepted'] = $v2 ['Accepted'];
								$userArray ['SkillBracketTypeID'] = $v2 ['SkillBracketTypeID'];
								$data2 [$v ['GroupID']] ['members'] [] = $userArray;
								$data2 [$v ['GroupID']] ['data'] ['count'] ++;
								$data2 [$v ['GroupID']] ['data'] ['GroupID'] = $v ['GroupID'];
							}
							
							// check if SkillBracket the same
							if(is_array($data2) && count($data2) > 0){
								foreach ($data2 as $k3 => $v3) {
									$groupID = $k3;
									$members = $v3['members'];
									$skillBracket = 0;
									if(is_array($members) && count($members) > 0){
									   foreach ($members as $kk3 => $vv3) {
									   	if($skillBracket == 0){
									   		$skillBracket = $vv3['SkillBracketTypeID'];
									   	}
									   	else{
									   		if($skillBracket == $vv3['SkillBracketTypeID']){
									   			$data2 [$groupID] ['data']['allowed'] = true;
									   		}
									   		else{
									   			$data2 [$groupID] ['data']['allowed'] = false;
									   		}
									   	}
									  }
									}
							  }
							}
							
							$earnedPoints = 0;
							$wins = 0;
							$losses = 0;
							$winRate = 0;
							switch (count ( $data2 [$v ['GroupID']] ['members'] )) {
								case "2" :
									$steamID1 = secureNumber ( $data2 [$v ['GroupID']] ['members'] [0] ['SteamID'] );
									$steamID2 = secureNumber ( $data2 [$v ['GroupID']] ['members'] [1] ['SteamID'] );
									$sql = "SELECT *, (SELECT SUM(PointsChange) FROM UserPoints WHERE SteamID = " . $steamID1 . " AND MatchID = up.MatchID  AND PointsTypeID IN (1,2,8)) as p1,
											(SELECT SUM(PointsChange) FROM UserPoints WHERE SteamID = " . $steamID2 . " AND MatchID = up.MatchID  AND PointsTypeID IN (1,2,8)) as p2
													FROM UserPoints up
													WHERE SteamID = " . $steamID1 . " OR SteamID = " . $steamID2 . " GROUP BY up.MatchID
															HAVING (p1 > 0 AND p2 > 0) OR (p1 < 0 AND p2 < 0);
															";
									$statsData = $DB->multiSelect ( $sql );
									if (is_array ( $statsData ) && count ( $statsData ) > 0) {
										$earnedPoints = 0;
										$wins = 0;
										$losses = 0;
										$winRate = 0;
										foreach ( $statsData as $kk => $vv ) {
											if ($vv ['p1'] < 0 && $vv ['p1'] < $vv ['p2']) {
												$earnedPoints += ( int ) $vv ['p1'];
											} else {
												$earnedPoints += ( int ) $vv ['p2'];
											}
											if ($vv ['p1'] > 0 && $vv ['p1'] > $vv ['p2']) {
												$earnedPoints += ( int ) $vv ['p1'];
											} else {
												$earnedPoints += ( int ) $vv ['p2'];
											}
											$pointsType = $vv ['PointsTypeID'];
											switch ($pointsType) {
												case "1" :
													$wins ++;
													break;
												case "2" :
													$losses ++;
													break;
											}
										}
									}
									$earnedPoints = ( int ) $earnedPoints / 2;
									break;
							}

							$total = $wins + $losses;
							if ($total > 0) {
								$winRate = round ( $wins / $total * 100, 2 );
							} else {
								$winRate = 0;
							}
							$ret ['debug'] .= p ( "P:" . $earnedPoints . " W:" . $wins . " L:" . $losses . " WR:" . $winRate, 1 );
							$data2 [$v ['GroupID']] ['stats'] ['Points'] = ( int ) $earnedPoints;
							$data2 [$v ['GroupID']] ['stats'] ['Wins'] = ( int ) $wins;
							$data2 [$v ['GroupID']] ['stats'] ['Losses'] = ( int ) $losses;
							$data2 [$v ['GroupID']] ['stats'] ['WinRate'] = $winRate;
							// $ret['debug'] .= p($sql,1);
						}
					} else {
						$ret ['debug'] .= p ( "nicht alle invite akzeptiert!", 1 );
					}
				}
				// $ret['debug'] .= p($data2,1);
				if ($smarty) {
					$smarty->assign ( 'data', $data2 );
					$table = $smarty->fetch ( "profile/teams/duoTeamListItems.tpl" );
					$ret ['html'] = $table;
				}
				$ret ['data'] = $data2;
				$ret ['status'] = true;
			}
		} else {
			$ret ['status'] = "steamID = 0";
		}

		$ret ['debug'] .= "End getGroupsOfUser <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkIfGroupAlreadyCreated($partnerID) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();

		$steamID = $_SESSION ['user'] ['steamID'];

		$ret ['debug'] .= "Start checkIfGroupAlreadyCreated <br>\n";
		if ($steamID > 0) {

			$sql = "SELECT count(g.GroupID) as Count, g.GroupID
					FROM `Group` g JOIN GroupMembers gm ON g.GroupID = gm.GroupID
					WHERE EXISTS(
					SELECT *
					FROM GroupMembers
					WHERE SteamID = " . secureNumber ( $steamID ) . "  AND GroupID = g.GroupID
							)
							AND
							EXISTS(
							SELECT *
							FROM GroupMembers
							WHERE SteamID = " . secureNumber ( $partnerID ) . "  AND GroupID = g.GroupID
									)
									GROUP BY g.GroupID
									HAVING Count = 2
									";
			$ret ['debug'] .= p ( $sql, 1 );
			$data = $DB->select ( $sql );
			if (is_array ( $data ) && count ( $data ) > 0) {
				$ret ['existing'] = true;
				$ret ['GroupID'] = $data ['GroupID'];
			}
			if ($count > 0) {
				$ret ['existing'] = false;
			} else {
				$ret ['data'] = false;
			}
			$ret ['status'] = true;
		} else {
			$ret ['status'] = "steamID = 0";
		}

		$ret ['debug'] .= "End checkIfGroupAlreadyCreated <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function deleteTeamOfUser($groupID, $steamID = 0) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start deleteTeam <br>\n";

		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if ($groupID > 0) {

			$retCheck = $this->checkIfPlayerIsInGroup ( $groupID, $steamID );

			if ($retCheck ['status'] == true) {

				$sql = "DELETE FROM `Group`
						WHERE GroupID = " . ( int ) $groupID . "
								";
				$data = $DB->delete ( $sql );
				$ret ['debug'] .= p ( $sql, 1 );

				$sql = "DELETE FROM `GroupMembers`
						WHERE GroupID = " . ( int ) $groupID . "
								";
				$data = $DB->delete ( $sql );
				$ret ['debug'] .= p ( $sql, 1 );

				$ret ['status'] = $data;
			} else {
				$ret ['debug'] .= p ( $retCheck, 1 );
				$ret ['status'] = false;
			}
		} else {
			$ret ['status'] = "groupID == 0";
		}
		$ret ['debug'] .= "End deleteTeam <br>\n";
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkIfPlayerIsInGroup($groupID, $steamID = 0) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start checkIfPlayerIsInGroup <br>\n";

		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if ($groupID > 0) {

			$sql = "SELECT *
					FROM `GroupMembers` gm
					WHERE SteamID = " . secureNumber ( $steamID ) . " AND GroupID = " . ( int ) $groupID . "
							";
			$count = $DB->countRows ( $sql );
			$ret ['debug'] .= p ( $sql, 1 );

			if ($count > 0) {
				$ret ['status'] = true;
			} else {
				$ret ['status'] = false;
			}
		} else {
			$ret ['status'] = "groupID == 0";
		}
		$ret ['debug'] .= "End checkIfPlayerIsInGroup <br>\n";
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function editTeamName($groupID, $name) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start editTeamName <br>\n";

		if ($groupID > 0) {
			$retCheck = $this->checkIfPlayerIsInGroup ( $groupID );
			if ($retCheck ['status']) {
				$sql = "UPDATE `Group`
						SET Name = '" . mysql_real_escape_string ( $name ) . "'
								WHERE GroupID = " . ( int ) $groupID . "
										";
				$data = $DB->update ( $sql );
				$ret ['debug'] .= p ( $sql, 1 );
				$ret ['status'] = $data;
			} else {
				$ret ['status'] = false;
			}
		} else {
			$ret ['status'] = "groupID == 0";
		}
		$ret ['debug'] .= "End editTeamName <br>\n";
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getAllInvitesOfUser($steamID = 0, $smarty=null) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start getAllInvitesOfUser <br>\n";

		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if ($steamID > 0) {
			$sql = "SELECT gm.*, g.Name, u.Name as Creator, u.Avatar as CreatorAvatar
					FROM GroupMembers gm
					LEFT JOIN `Group` g ON g.GroupID = gm.GroupID
					LEFT JOIN `User` u ON u.SteamID = gm.SteamID
					WHERE gm.SteamID = " . secureNumber ( $steamID ) . " AND gm.Accepted = 0
							";
			$ret ['debug'] .= $sql;
			$data = $DB->multiSelect ( $sql );
			if (is_array ( $data ) && count ( $data ) > 0) {
				foreach ( $data as $k => $v ) {
					$groupID = $v['GroupID'];
					$sql = "SELECT gm.SteamID, u.Avatar, u.Name, gm.Accepted
							FROM `GroupMembers` gm JOIN User u ON u.SteamID = gm.SteamID
							WHERE GroupID = " . ( int ) $groupID . "
									";
					$ret ['debug'] .= $sql;
					$tmp = $DB->multiSelect ( $sql );

					$data[$k]['members'] = $tmp;
				}
			}
			$ret ['data'] = $data;

			if($smarty != null){
				$smarty->assign ( 'data', $data );
				$table = $smarty->fetch ( "profile/teams/inviteItems.tpl" );
				$ret ['html'] = $table;
			}

			$ret ['debug'] .= p ( $sql, 1 );
			$ret ['status'] = true;
		} else {
			$ret ['status'] = "steamID == 0";
		}
		$ret ['debug'] .= "End getAllInvitesOfUser <br>\n";
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getOpenTeams($steamID = 0, $smarty=false){
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start getOpenTeams <br>\n";

		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if ($steamID > 0) {
			$sql = "SELECT (SELECT COUNT(*)
					FROM GroupMembers tmp
					WHERE tmp.GroupID = gm.GroupID AND tmp.SteamID = ".secureNumber($steamID).") as Count, Accepted, gm.GroupID, gm.SteamID, g.Name
							FROM `GroupMembers` gm LEFT JOIN `Group` g ON g.GroupID = gm.GroupID
							WHERE SteamID != ".secureNumber($steamID)." AND Accepted != 1
									HAVING Count > 0
									";

			$ret ['debug'] .= p ( $sql, 1 );
			$data = $DB->multiSelect ( $sql );
			if (is_array ( $data ) && count ( $data ) > 0) {
				$data2 = array ();
				foreach ( $data as $k => $v ) {

					if ($v ['Name'] == "") {
						$data2 [$v ['GroupID']] ['Name'] = "Duo-Team";
					} else {
						$data2 [$v ['GroupID']] ['Name'] = $v ['Name'];
					}

					$sql = "SELECT gm.GroupID, gm.SteamID, u.Avatar, u.Name, gm.Accepted
							FROM `GroupMembers` gm JOIN User u ON u.SteamID = gm.SteamID
							WHERE GroupID = " . ( int ) $v ['GroupID'] . "
									";
					$ret['debug'] .= p($sql,1);
					$tmp = $DB->multiSelect ( $sql );
					// $ret['debug'] .= p($tmp,1);
					if (is_array ( $tmp ) && count ( $tmp ) > 0) {
						$array = array ();
						$dataTest = array ();
						foreach ( $tmp as $k2 => $v2 ) {
							$userArray ['Name'] = $v2 ['Name'];
							$userArray ['Avatar'] = $v2 ['Avatar'];
							$userArray ['SteamID'] = $v2 ['SteamID'];
							$userArray ['Accepted'] = $v2 ['Accepted'];
							$data2 [$v ['GroupID']] ['members'] [] = $userArray;
							$data2 [$v ['GroupID']] ['data'] ['count'] ++;
							$data2 [$v ['GroupID']] ['data'] ['GroupID'] = $v ['GroupID'];
						}
					}
					// $ret['debug'] .= p($data2,1);
					if ($smarty) {
						$smarty->assign ( 'data', $data2 );
						$table = $smarty->fetch ( "profile/teams/openTeamItems.tpl" );
						$ret ['html'] = $table;
					}
					$ret ['data'] = $data2;
					$ret ['status'] = true;
				}
			} else {
				$ret ['status'] = "steamID = 0";
			}
		} else {
			$ret ['status'] = "steamID == 0";
		}
		$ret ['debug'] .= "End getOpenTeams <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function changeAcceptedOfGroup($groupID, $value, $steamID = 0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start changeAcceptedOfGroup <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}
		$ret['debug'] .= p("VAL:".$value,1);
		if($groupID > 0){
			$retCheck = $this->checkIfPlayerIsInGroup ( $groupID );
			if ($retCheck ['status']) {
				$sql = "UPDATE GroupMembers
						SET Accepted = ".(int)$value."
								WHERE SteamID = ".secureNumber($steamID)." AND GroupID = ".(int)$groupID."
										";
				$data = $DB->update($sql);
				$ret['debug'] .= p($sql,1);
				$ret['data'] = $data;

				// wenn decline dann auch aus db hauen
				if($value == -1){
					$sql = "DELETE FROM `Group`
							WHERE GroupID = ".(int)($groupID)."
									";
					$data = $DB->delete($sql);
					$ret['debug'] .= p($sql,1);

					$sql = "DELETE FROM `GroupMembers`
							WHERE GroupID = ".(int)($groupID)."
									";
					$data = $DB->delete($sql);
					$ret['debug'] .= p($sql,1);

				}

				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
			}

		}
		else{
			$ret['status'] = "matchID == 0";
		}
		$ret['debug'] .= "End changeAcceptedOfGroup <br>\n";
		return $ret;
	}

	function getGroupData($groupID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getGroupData <br>\n";

		if($groupID > 0){
				
			$sql = "SELECT *
					FROM `Group` g
					WHERE g.GroupID = ".(int)($groupID)."
							";
			$data = $DB->select($sql);
				
			$ret['debug'] .= p($sql,1);
				
			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "groupID == 0";
		}
		$ret['debug'] .= "End getGroupData <br>\n";
		return $ret;
	}

	function getDuoPartnerOfGroupOfUser($steamID, $groupID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getDuoPartnerOfGroupOfUser <br>\n";

		if($steamID > 0 && $groupID > 0){
			$sql = "SELECT *
							FROM `GroupMembers` gm LEFT JOIN User u ON u.SteamID = gm.SteamID
							WHERE gm.SteamID != ".secureNumber($steamID)." AND gm.GroupID = ".(int) $groupID."
									LIMIT 1
			";
			$data = $DB->select($sql); 
			$ret['debug'] .= p($sql,1);
			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID == 0 && groupID == 0";
		}
		$ret['debug'] .= "End getDuoPartnerOfGroupOfUser <br>\n";
		return $ret;
	}
}

?>