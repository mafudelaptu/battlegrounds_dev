<?php 

class RaceCoinPrizes{

	function getAllCoinPrizesData(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getAllCoinPrizesData <br>\n";

		$sql = "SELECT *
				FROM `RaceCoinPrizes`
				WHERE Active = 1
				";
		$data = $DB->multiSelect($sql);
		$ret['debug'] .= p($sql,1);

		$ret['data'] = $data;
		$ret['status'] = true;

		$ret['debug'] .= "End getAllCoinPrizesData <br>\n";
		return $ret;
	}

	function getCoinPrizeByPlacement($placement, $array=false){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getCoinPrizeByPlacement <br>\n";

		if($placement > 0){
			if($array){
				$array = orderArrayBy($array,'BottomBorder',SORT_DESC);
				if(is_array($array) && count($array) > 0){
				   foreach ($array as $k => $v) {
						if($v['BottomBorder'] <= $placement){
							$data = $v;
							break;
						}
				  }
				  $ret['status'] = true;
				}
				else{
					$ret['status'] = "array null";
				}
			}
			else{
				$sql = "SELECT *
					FROM `RaceCoinPrizes`
					WHERE Active = 1 AND BottomBorder <= ".(int) $placement."
							ORDER BY BottomBorder DESC
							LIMIT 1
				
							";
				$data = $DB->select($sql);
				$ret['debug'] .= p($sql,1);
				$ret['status'] = true;
			}
			
			$ret['data'] = $data;
			
		}
		else{
			$ret['status'] = "placement == 0";
		}
		$ret['debug'] .= "End getCoinPrizeByPlacement <br>\n";
		return $ret;
	}

}

?>