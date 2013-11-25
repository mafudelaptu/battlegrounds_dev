<?php
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class UserStats{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
        function getRank($steamID){

                if($steamID > 0){
                        $DB = new DB();
                        $con = $DB->conDB();
                        $sql = "SELECT Rank
                                        FROM UserStats
                                        WHERE SteamID = ".secureNumber($steamID)."
                                                        ";
                        $ret = $DB->select($sql);

                        return $ret["Rank"];
                }
                else{
                        return 0;
                }
        }
        /*
         * Copyright 2013 Artur Leinweber
        * Date: 2013-01-01
        */
        function updateUserStatsData($steamID){
                $DB = new DB();
                $con = $DB->conDB();

                $DotaBuff = new DotaBuff();
                $data = $DotaBuff->getStats($steamID);

                $sql = 'UPDATE UserStats
                                SET WinLoseRatio="'.$data["ratio"].'", Lvl="'.$data["lvl"].'", Wins="'.$data["wins"].'", Loses="'.$data["loses"].'"
                                                WHERE SteamID = '.secureNumber($steamID).'
                                                                ';
                $DB->update($sql, 0);
        }
        /*
         * Copyright 2013 Artur Leinweber
        * Date: 2013-01-01
        */
        function checkUserHaveStats($steamID){
                $DB = new DB();
                $con = $DB->conDB();
                $sql = "SELECT DISTINCT SteamID
                                FROM UserStats
                                WHERE SteamID = ".secureNumber($steamID)."
                                                ";
                $count = $DB->countRows($sql,0);
                //p($count);
                if($count > 0){
                        return true;
                }
                else{
                        return false;
                }
        }
        /*
         * Copyright 2013 Artur Leinweber
        * Date: 2013-01-01
        */
        function getUserStats($steamID, $select="*"){
                $ret = array();
                if($steamID > 0){
                        $DB = new DB();
                        $con = $DB->conDB();

                        $sql = "SELECT DISTINCT ".$select."
                                        FROM UserStats
                                        WHERE SteamID = ".secureNumber($steamID)."
                                                        ";
                        $data = $DB->select($sql);
                        $ret['debug'] = $sql;
                        $ret['data'] = $data;
                }
                return $ret;
        }
        /*
         * Copyright 2013 Artur Leinweber
        * Date: 2013-01-01
        */
        function updateGeneralUserStats($steamID, $result, $matchID){
                $ret = array();
                if($steamID > 0 && $matchID > 0){
                        $DB = new DB();
                        $con = $DB->conDB();
                        $Match = new Match();
                        
                        $leaver = $Match->playerLeftTheMatch($steamID, $matchID);
                        $playersLeftMatch = $Match->getPlayersWhoLeftTheMatch($matchID);
                        $countPlayersLeft = (int) count($playersLeftMatch['data']);
                        
                        switch($result){
                                // WIN
                                case 1:
                                        $set = "SET Wins = Wins+1
                                                        ";
                                        break;
                                        // LOSE
                                case -1:
                                		// nicht wirklich sinnvoll    erstma disabled
                                        //if($countPlayersLeft === 0){
                                                $set = "SET Loses = Loses+1
                                                        ";
                                        //}
                                        break;
                                default: $set = "SET Wins = Wins";
                        }

                        // Leaver handling
                        if($leaver['left']){
                                if($set != ""){
                                        $set .= ", Leaves = Leaves+1";
                                }
                                else{
                                        $set = "SET Leaves = Leaves+1";
                                }
                        }
                        // neue WinRate updaten
                        if($set != ""){
                        	$set .= ", WinLoseRatio = (Wins/(Wins+Loses))*100";
                        	
                        	$sql = "UPDATE UserStats
                                        ".$set."
                                                        WHERE SteamID = ".secureNumber($steamID)."
                                                                        ";
                        	$DB->update($sql);
                        	$ret['debug'] = $sql;
                        	$ret['status'] = true;
                        }
                        else{
                        	$ret['status'] = false;
                        }

                       
                       
                }
                return $ret;

        }
        /*
         * Copyright 2013 Artur Leinweber
        * Date: 2013-01-01
        */
        function updateRank($steamID, $rank){
                $ret = array();
                $DB = new DB();
                $con = $DB->conDB();

                if($steamID > 0){
                        $sql = "UPDATE `UserStats`
                                        SET Rank = ".(int) $rank."
                                                        WHERE SteamID = ".secureNumber($steamID)."";
                        $DB->update($sql);
                        $ret['status'] = true;
                }
                else{
                        $ret['status'] = "SteamID 0";
                }
                return $ret;
        }

        /*
         * Copyright 2013 Artur Leinweber
        * Date: 2013-01-01
        */
        
        function updateUserStatsElo($steamID, $eloChange, $result){
                $ret = array();
                $DB = new DB();
                $con = $DB->conDB();
                $ret['debug'] .= "Start updateUserStatsElo <br>\n";
                if($steamID > 0){
                	switch ($result) {
                		case "1":
                			$set = "Rank = Rank + ".(int)$eloChange;
                		break;
                		
                		case "-1":
                			$set = "Rank = Rank - ".(int)$eloChange;
                			break;
                		
                		default:
                			;
                		break;
                	}
                        // WON
//                         if($result == 1){
//                                 $set = "Rank = Rank + ".(int)$eloChange;
//                         }
//                         else{
//                                 $set = "Rank = Rank - ".(int)$eloChange;
//                         }
        
                        $sql = "UPDATE UserStats
                                        SET ".$set."
                                        WHERE SteamID = ".secureNumber($steamID)."
                                        ";
                        $ret['debug'] .= p($sql,1);
                        $DB->update($sql);
                        $ret['status'] = true;
                }
                else{
                        $ret['status'] = "SteamID = 0";
                }
        
                $ret['debug'] .= "End updateUserStatsElo <br>\n";
        
                return $ret;
        }
        /*
         * Copyright 2013 Artur Leinweber
        * Date: 2013-01-01
        */
        function changeUserRank($steamID, $eloChange, $positive=true){
        	$ret = array();
        	$DB = new DB();
        	$con = $DB->conDB();
        	$ret['debug'] .= "Start changeUserRank <br>\n";
        
        	if($steamID > 0 && $eloChange > 0){
        			
        		if($positive){
        			$zeichen = "+";
        		}
        		else{
        			$zeichen = "-";
        		}
        			
        		$sql = "UPDATE `UserStats`
					SET Rank = Rank ".$zeichen." ".(int)$eloChange."
					WHERE SteamID = ".secureNumber($steamID)."
			
					";
        		$ret['debug'] .= p($sql,1);
        		$retUpdate = $DB->update($sql);
        			
        		$ret['status'] = $retUpdate;
        	}
        	else{
        		$ret['status'] = "eventID = 0 | createdEventID = 0 | teamID = 0";
        	}
        
        
        	$ret['debug'] .= "End changeUserRank <br>\n";
        
        	return $ret;
        }
}

?>