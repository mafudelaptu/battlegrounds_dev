<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class MatchType{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getAllMatchTypes($select="*", $hideInactive=true){
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
							FROM MatchType
							".$where."
							";
		$ret = $DB->multiSelect($sql);
			
		return $ret;
	}
	
}

?>