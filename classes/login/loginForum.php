<?php 

class LoginForum{

	function checkLogin($userID, $pass){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDBForum();
		$ret['debug'] .= "Start checkLogin <br>\n";

		if($userID > 0 && $pass != ""){
			$sql = "SELECT *
							FROM `members` m LEFT JOIN `profile_portal` pp ON pp.pp_member_id = m.member_id
							WHERE m.member_id = ".(int)$userID." AND m.member_login_key = '".mysql_real_escape_string($pass)."'
					LIMIT 1
			";
			$data = $DB->select($sql); 
			$ret['data'] = $data;
			$ret['debug'] .= p($sql,1);
			if($data['member_id'] > 0){
				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
			}
			
			
		}
		else{
			$ret['status'] = "userID == 0 || pass == ''";
		}
		$ret['debug'] .= "End checkLogin <br>\n";
		return $ret;
	}

}

?>