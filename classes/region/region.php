<?php
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class Region{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getAllRegions($select="*", $hideInactive=true){
		$DB = new DB();
		$con = $DB->conDB();
		$ret = array();
		if($hideInactive){
			$where = "WHERE Active = 1";
		}
		else{
			$where = "";
		}
			
		$sql = "SELECT ".$select."
							FROM Region
							".$where."
							";
		$ret = $DB->multiSelect($sql);
			
		return $ret;
	}
	
	function getRegionData($regionID){
		$DB = new DB();
		$con = $DB->conDB();
		$ret = array();
			
		$sql = "SELECT *
							FROM Region
							WHERE RegionID = ".(int) $regionID."
							";
		$ret = $DB->select($sql);
			
		return $ret;
	}
	
}

?>