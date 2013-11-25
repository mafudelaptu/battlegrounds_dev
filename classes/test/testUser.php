<?php
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class TestUser{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getRandomSteamID(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$sql = "SELECT SteamID, Hash FROM User ORDER BY RAND() LIMIT 1 ";
		
		$data = $DB->select($sql);
	
// 		$ret =  array(
// 			"76561198007292295", // Kethner - fakeQueue drin
// 			"76561198046737906", // BajoZeko - fakeQueue drin
// 			"76561198062751593", // MickeyMouse - fakeQueue drin
// 			"76561198068803073", // [kZd] Anubpwnz - fakeQueue drin
// 			"76561197997437341", // harphield - fakeQueue drin
// 			"76561198053918825", // Onizuka - fakeQueue drin
// 			"76561198065060643", // June15th - fakeQueue drin
// 			"76561198065095821", // Tobijusimus
// 			"76561198036113593", // Mr Cupcake - fakeQueue drin
// 			"76561198034911234", // g4m3 - fakeQueue drin
// 			"76561197995156810", // LionsHeart
// 			"76561198075352540"  // murs
// 	);
// 		$key = array_rand($ret,1);
		
	//	return $ret[$key];
	return $data;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getFakeUsers(){
		$ret =  array(
			"76561198007292295", // Kethner - fakeQueue drin
			"76561198046737906", // BajoZeko - fakeQueue drin
			"76561198062751593", // MickeyMouse - fakeQueue drin
			"76561198068803073", // [kZd] Anubpwnz - fakeQueue drin
			"76561197997437341", // harphield - fakeQueue drin
			"76561198053918825", // Onizuka - fakeQueue drin
			"76561198065060643", // June15th - fakeQueue drin
			"76561198065095821", // Tobijusimus
			"76561198036113593", // Mr Cupcake - fakeQueue drin
			"76561198034911234", // g4m3 - fakeQueue drin
			"76561197995156810", // LionsHeart
			"76561198075352540"  // murs
	);
		return $ret;
	}
	
}

?>