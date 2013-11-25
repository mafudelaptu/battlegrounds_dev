<?php 

$DB = new DB();
$con = $DB->conDB();

p("START BanHandling");

p("Start PermaBann handling");
$sql = "SELECT b.SteamID, Count(*) as WarnCount
		FROM `Banlist` b
		WHERE NOT EXISTS (SELECT SteamID FROM PermaBanned WHERE SteamID = b.SteamID)
				AND Display = 1
		GROUP BY b.SteamID
		HAVING WarnCount >= 6
		";
$data = $DB->multiSelect($sql);
p($sql);

// jeder der gefunden wurde -> permabannen
if(is_array($data) && count($data) > 0){
	$insertArray = array();
   foreach ($data as $k => $v) {
		$steamID = $v['SteamID'];
		$tmp = array();
		$tmp['SteamID'] = secureNumber($steamID);
		$tmp['TimestampBanned'] = time();
		$tmp['BanlistReasonID'] = 3; // collected 6 active Warns
		$insertArray[] = $tmp;
  }
  $retIns = $DB->multiInsert("PermaBanned", $insertArray);
  p($retIns);
}

p("END PermaBann handling");

p("START Warn Verfall handling");
// alle aktiven Warns die älter als 20 tage sind auf inactiv setzen
$timeVerfall = 1728000; // 20 Tage
$sql = "SELECT b.BanlistID
				FROM `Banlist` b
				WHERE b.BannedAt <= ".(time()-$timeVerfall)." AND b.Display = 1
						AND NOT EXISTS (SELECT SteamID FROM PermaBanned WHERE SteamID = b.SteamID)
";
$data = $DB->multiSelect($sql); 
p($sql);
// jeden Bann der gefunden wurde auf inaktiv setzen
if(is_array($data) && count($data) > 0){
   foreach ($data as $k => $v) {
   		$banlistID = $v['BanlistID'];
		$sql = "UPDATE `Banlist`
				SET Display = 0
				WHERE BanlistID = ".(int)$banlistID."
		";
		$retUpdate = $DB->update($sql); 
		p($retUpdate);
  }
}
p("END Warn Verfall handling");

p("END BanHandling");
?>