<?php 

class News{

	function createNewNews($title, $content, $order, $active=1, $showTimestamp=0, $endTimestamp=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start createNewNews <br>\n";

		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}
		$User = new User();
		$isAdmin = $User->isAdmin($steamID);
		if($isAdmin){
			$insertArray = array();
			$insertArray['Title'] = mysql_real_escape_string($title);
			$insertArray['Content'] = mysql_real_escape_string($content);
			$insertArray['CreateTimestamp'] = time();
			$insertArray['Order'] = (int) $order;
			$insertArray['ShowTimestamp'] = (int) strtotime($showTimestamp);
			$insertArray['EndTimestamp'] = (int)strtotime($endTimestamp);
			$insertArray['Active'] = (int) $active;
			$ret['debug'] .= p($insertArray,1);
			//$ret['debug'] .= p($DB->insert("News", $insertArray,$con,1),1);
			$retINs = $DB->insert("News", $insertArray);
			$ret['status'] = $retINs;
		}
		else{
			$ret['status'] = "noAdmin";
		}


		$ret['debug'] .= "End createNewNews <br>\n";
		return $ret;
	}


	function getNewsCount(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getNewsCount <br>\n";
		$sql = "SELECT *
				FROM `News`
				";
		$data = $DB->countRows($sql);
		$ret['debug'] .= p($sql,1);
		$ret['data'] = $data;
		$ret['status'] = true;

		$ret['debug'] .= "End getNewsCount <br>\n";
		return $ret;
	}

	function getAllNews($justActive=false){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getAllNews <br>\n";

		if($justActive){
			$where = "WHERE Active = 1";
		}
		else{
			$where = "";
		}

		$sql = "SELECT *
				FROM `News` n
				".$where."
						ORDER BY `Order` DESC

						";
		$data = $DB->multiSelect($sql);
		$ret['debug'] .= p($sql,1);
		$ret['data'] = $data;
		$ret['status'] = true;

		$ret['debug'] .= "End getAllNews <br>\n";
		return $ret;
	}

	function getDataOfNews($id){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getDataOfNews <br>\n";

		if($id > 0){
			$sql = "SELECT *
					FROM `News`
					WHERE NewsID = ".(int)($id)."
							";
			$data = $DB->select($sql);
			$ret['debug'] .= p($sql,1);
			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "id == 0";
		}
		$ret['debug'] .= "End getDataOfNews <br>\n";
		return $ret;
	}

	function updateNews($id, $title, $content, $order, $active, $showTimestamp=0, $endTimestamp=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start updateNews <br>\n";

		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}
		$User = new User();
		$isAdmin = $User->isAdmin($steamID);
		if($isAdmin){
			$sql = "UPDATE `News`
					SET Title='".mysql_real_escape_string($title)."',
							Content='".mysql_real_escape_string($content)."',
									`Order`=".(int)($order).",
											ShowTimestamp=".(int) strtotime($showTimestamp).",
													EndTimestamp=".(int) strtotime($endTimestamp).",
															Active = ".(int) $active."
																	WHERE NewsID = ".(int)($id)."
																			";
			$data = $DB->update($sql);
			$ret['debug'] .= p($sql,1);
			$ret['status'] = $data;
		}
		else{
			$ret['status'] = "noAdmin";
		}


		$ret['debug'] .= "End updateNews <br>\n";
		return $ret;
	}

	function toggleActiveNews($id, $active){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start toggleActiveNews <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($id > 0){
			$User = new User();
			$isAdmin = $User->isAdmin($steamID);
			if($isAdmin){
				if($active == "1"){
					$setActive = 0;
				}
				else{
					$setActive = 1;
				}
				$sql = "Update `News`
						SET `Active` = ".(int) $setActive."
								WHERE NewsID = ".(int)$id."
										";
				$data = $DB->update($sql);
				$ret['debug'] .= p($sql,1);
				$ret['status'] = $data;
			}
			else{
				$ret['status'] = "noAdmin";
			}

		}
		else{
			$ret['status'] = "id == 0";
		}
		$ret['debug'] .= "End toggleActiveNews <br>\n";
		return $ret;
	}

	function deleteNews($id){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start deleteNews <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($id > 0){
			$User = new User();
			$isAdmin = $User->isAdmin($steamID);
			if($isAdmin){
				$sql = "DELETE FROM `News`
						WHERE NewsID = ".(int)($id)."
								";
				$data = $DB->delete($sql);
				$ret['debug'] .= p($sql,1);
				$ret['status'] = $data;
			}
			else{
				$ret['status'] = "noAdmin";
			}

		}
		else{
			$ret['status'] = "id == 0";
		}
		$ret['debug'] .= "End deleteNews <br>\n";
		return $ret;
	}


	function getNewsForFrontend($limit=5){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getNewsForFrontend <br>\n";

		$sql = "SELECT *
				FROM `News`
				WHERE ShowTimestamp <= ".(int)time()." AND (EndTimestamp >= ".(int) time()." OR EndTimestamp = 0) AND Active = 1
				ORDER BY `Order` DESC
				LIMIT ".(int) $limit."
						";
		$data = $DB->multiSelect($sql);
		$ret['debug'] .= p($sql,1);

		$ret['data'] = $data;
		$ret['status'] = true;

		$ret['debug'] .= "End getNewsForFrontend <br>\n";
		return $ret;
	}
}

?>