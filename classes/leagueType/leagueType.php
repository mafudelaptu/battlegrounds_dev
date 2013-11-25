<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class LeagueType{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getData($leagueTypeID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getData <br>\n";
		if($leagueTypeID > 0){
			$sql = "SELECT *
					FROM `LeagueType`
					WHERE LeagueTypeID = ".(int)$leagueTypeID."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);

			$ret['data']  =$data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "leagueTypeID = 0";
		}

		$ret['debug'] .= "End getData <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getDataForProgressBar($steamID, $leagueTypeID, $leagueLvl, $points){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getDataForProgressBar <br>\n";
		if($leagueTypeID > 0){

			$UserLeague = new UserLeague();
			$leagueData = $this->getData($leagueTypeID);
			$leagueName = $leagueData['data']['Name'];
			// 			$leagueTypeID = 3;
			// 			$leagueLvl = 3;
				
			$retleagueName = $UserLeague->getLeagueName($leagueName, $leagueLvl);
			$leagueName = $retleagueName['data'];
				
			switch($leagueTypeID){
				case "1":
					$startLabel = "";
					$endLabel = "Bronze";
					$leagueData = $this->getData($leagueTypeID);
					$startBorder = 0;
						
					$leagueDrunterOG = $leagueData['data']['PointBorderTop']+1;
					$endBorder = (int) $leagueDrunterOG;
					break;
				case "6":
						
					$endLabel = "";
					$endBorder = "";
						
					$leagueData = $this->getData($leagueTypeID-1);
					$name = $leagueData['data']['Name'];
					$leagueDrunterOG = $leagueData['data']['PointBorderTop'];
						
					$startLabel = $name." Lvl ".(UserLeague::totalLeagueLvl-1);
					$startBorder = (int) $leagueDrunterOG+1;
						
					break;
				case "2":
					switch($leagueLvl){
						case "":
							$leagueData = $this->getData($leagueTypeID-1);
							
							$leagueDrunterOG = 0;
							$leagueData = $this->getData($leagueTypeID);
							$name = $leagueData['data']['Name'];

							$startLabel = "";

							$retUL = $UserLeague->getBorderLvlPoints($leagueTypeID, $leagueLvl);
							$ret['debug'] .= p($retUL,1);
							$startBorder =  0;
							$endPoints = $retUL['data']['EndPoints'];
							$endLabel = $name." Lvl 1";
							$endBorder = $endPoints;
							break;
						case "1":

							$leagueData = $this->getData($leagueTypeID);
							$name = $leagueData['data']['Name'];
							$retUL = $UserLeague->getBorderLvlPoints($leagueTypeID, $leagueLvl);
							$startPoints = $retUL['data']['StartPoints'];
							$endPoints = $retUL['data']['EndPoints'];
								
							$startLabel = $name;
							$startBorder = $startPoints;
								
							$endLabel = $name." Lvl 2";
							$endBorder = $endPoints;
							break;
						case (UserLeague::totalLeagueLvl-1):
							$leagueData = $this->getData($leagueTypeID+1);
							$leagueUG = $leagueData['data']['PointBorderBottom'];
							$nameEnd = $leagueData['data']['Name'];

							$leagueData = $this->getData($leagueTypeID);
							$nameStart = $leagueData['data']['Name'];
							$retUL = $UserLeague->getBorderLvlPoints($leagueTypeID, $leagueLvl);
							$startPoints = $retUL['data']['StartPoints'];

							$startLabel = $nameStart." Lvl ".($leagueLvl-1);
							$startBorder = $startPoints;

							$endLabel = $nameEnd;
							$endBorder = $leagueUG;
							break;
						default:
							$leagueData = $this->getData($leagueTypeID);
							$retUL = $UserLeague->getBorderLvlPoints($leagueTypeID, $leagueLvl);
							$startPoints = $retUL['data']['StartPoints'];
							$endPoints = $retUL['data']['EndPoints'];
							$nameStart = $leagueData['data']['Name'];

							$startLabel = $nameStart." Lvl ".($leagueLvl-1);
							$startBorder = $startPoints;

							$endLabel = $nameStart." Lvl ".($leagueLvl+1);
							$endBorder = $endPoints;
					}
					break;
				default:
					switch($leagueLvl){
						case "":
							$leagueData = $this->getData($leagueTypeID-1);
							$leagueDrunterOG = $leagueData['data']['PointBorderTop'];
							$nameStart = $leagueData['data']['Name'];

							$leagueData = $this->getData($leagueTypeID);
							$nameEnd = $leagueData['data']['Name'];

							$startLabel = $nameStart." Lvl ".(UserLeague::totalLeagueLvl-1);
							$startBorder = ((int) $leagueDrunterOG+1);

							$retUL = $UserLeague->getBorderLvlPoints($leagueTypeID, $leagueLvl);
							$endLabel = $nameEnd." Lvl 1";
							$endBorder = $retUL['data']['EndPoints']+1;
							break;
						case "1":

							$leagueData = $this->getData($leagueTypeID);
							$name = $leagueData['data']['Name'];
							$retUL = $UserLeague->getBorderLvlPoints($leagueTypeID, $leagueLvl);
							$startPoints = $retUL['data']['StartPoints'];
							$endPoints = $retUL['data']['EndPoints'];

							$startLabel = $name;
							$startBorder = $startPoints;

							$endLabel = $name." Lvl 2";
							$endBorder = $endPoints+1;
							break;
						case (UserLeague::totalLeagueLvl-1):
							$leagueData = $this->getData($leagueTypeID+1);
							$leagueUG = $leagueData['data']['PointBorderBottom'];
							$nameEnd = $leagueData['data']['Name'];
								
							$leagueData = $this->getData($leagueTypeID);
							$nameStart = $leagueData['data']['Name'];
							$retUL = $UserLeague->getBorderLvlPoints($leagueTypeID, $leagueLvl);
							$startPoints = $retUL['data']['StartPoints'];
								
							$startLabel = $nameStart." Lvl ".($leagueLvl-1);
							$startBorder = $startPoints+1;
								
							$endLabel = $nameEnd;
							$endBorder = $leagueUG;
							break;
						case 1:
							$leagueData = $this->getData($leagueTypeID+1);
							$leagueUG = $leagueData['data']['PointBorderBottom'];
							$nameEnd = $leagueData['data']['Name'];

							$leagueData = $this->getData($leagueTypeID);
							$nameStart = $leagueData['data']['Name'];
							$retUL = $UserLeague->getBorderLvlPoints($leagueTypeID, $leagueLvl);
							$startPoints = $retUL['data']['StartPoints'];

							$startLabel = $nameStart;
							$startBorder = $startPoints;

							$endLabel = $nameEnd;
							$endBorder = $leagueUG;
							break;
						default:
							$leagueData = $this->getData($leagueTypeID);
							$retUL = $UserLeague->getBorderLvlPoints($leagueTypeID, $leagueLvl);
							$startPoints = $retUL['data']['StartPoints'];
							$endPoints = $retUL['data']['EndPoints'];
							$nameStart = $leagueData['data']['Name'];
								
							$startLabel = $nameStart." Lvl ".($leagueLvl-1);
							$startBorder = $startPoints+1;
								
							$endLabel = $nameStart." Lvl ".($leagueLvl+1);
							$endBorder = $endPoints+1;
								
					}
			}
				
			$data['StartLabel'] = $startLabel;
			$data['EndLabel'] = $endLabel;
			$data['StartBorder'] = (int) $startBorder;
			$data['EndBorder'] = $endBorder;
			$data['CurrentLeague'] = $leagueName;
				
			$ret['debug'] .= p("P:".$points,1);
			if($leagueTypeID != 6){
				// 				$normalizedPoints = $points-$startPoints;
				// 				$normalizedTotal = $endBorder-$startBorder;
				// 				$width = round($normalizedPoints/$normalizedTotal*100,0);
				$value = 100/($endBorder-$startBorder) * ($points-$startBorder);
				$width = round($value,0);
			}
			else{
				$width = 100;
			}
				
			$data['Width'] = $width;
				
			$ret['data']  = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "leagueTypeID = 0";
		}

		$ret['debug'] .= "End getDataForProgressBar <br>\n";

		return $ret;
	}

}

?>