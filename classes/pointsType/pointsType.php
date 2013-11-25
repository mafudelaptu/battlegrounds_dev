<?php 

class PointsType{

	function getAllPointTypes(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getAllPointTypes <br>\n";

		$sql = "SELECT *
						FROM `PointsType` pt
						WHERE pt.Active=1
		";
		$data = $DB->multiSelect($sql); 
		$ret['debug'] .= p($sql,1);
		
		$ret['data'] = $data;
		$ret['status'] = true;

		$ret['debug'] .= "End getAllPointTypes <br>\n";
		return $ret;
	}

	function getAllPointTypesForSelectOptions(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getAllPointTypes <br>\n";
	
		$sql = "SELECT *
						FROM `PointsType` pt
						WHERE pt.Active=1
		";
		$data = $DB->multiSelect($sql);
		$ret['debug'] .= p($sql,1);
	
		if(is_array($data) && count($data) > 0){
			$tmpData = array();
		   foreach ($data as $k => $v) {
				$tmpData[$v['PointsTypeID']] = $v['Name'];
		  }
		  $ret['data'] = $tmpData;
		  $ret['status'] = true;
		}
		else{
			$ret['status'] = false;
		}
		$ret['debug'] .= "End getAllPointTypes <br>\n";
		return $ret;
	}
}

?>