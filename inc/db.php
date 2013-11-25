<?php 
class DB{
	// zur DB verbinden
	function conDB(){
		$server = DB_HOST;
		$username = DB_USERNAME;
		$pw = DB_PW;
		$db = DB_NAME;

		$link = mysql_connect($server, $username, $pw);
		mysql_select_db($db, $link);
		$this->con = $link;
		return $link;
	}
	function conDbGlobal(){
		$server = DB_HOST_GLOBAL;
		$username = DB_USERNAME_GLOBAL;
		$pw = DB_PW_GLOBAL;
		$db = DB_NAME_GLOBAL;
	
		$link = mysql_connect($server, $username, $pw);
		mysql_select_db($db, $link);
		return $link;
	}
	
	function conDbForum(){
		$server = DB_HOST_FORUM;
		$username = DB_USERNAME_FORUM;
		$pw = DB_PW_FORUM;
		$db = DB_NAME_FORUM;
	
		$link = mysql_connect($server, $username, $pw);
		mysql_select_db($db, $link);
		return $link;
	}
	
	function select($sql, $assoc=true, $debug=false) 	{
		//	$this->conDB($s);
		if($debug){
			echo "<pre>".$sql."</pre>";
		}
		$_SESSION['sql']['queryCount']++;
		if ($assoc) {
			$ret = mysql_fetch_assoc(mysql_query($sql));
			return $ret;
		} else {
			$ret = mysql_query($sql);
			return $ret;
		}
	}

	function delete($sql, $debug=false) 	{
		//	$this->conDB($s);
		if($debug){
			echo "<pre>".$sql."</pre>";
		}
		$_SESSION['sql']['queryCount']++;
		return mysql_query($sql);
	}

	function multiSelect($sql, $debug=false)	{
		//$this->conDB($s);
		$_SESSION['sql']['queryCount']++;
		$result = mysql_query($sql);
		if( $result != false )	{	// -fm für leere TBs ...
			$i=0;
			while ($ret = mysql_fetch_assoc($result)) {
				foreach($ret as $k => $v) {
					$ret_arr[$i][$k] = $v;
				}
				$i++;
			}
			if($debug){
				echo "<pre>".$sql." ".print_r($ret_arr,1)."</pre>";
			}

			return $ret_arr;
		}	else	{
			if($debug){
				echo "<pre>".$sql."</pre>";
			}
			return false;
		}
	}

	function multiSelectUnbuffered($sql, $debug=false)	{
		//$this->conDB($s);
		$_SESSION['sql']['queryCount']++;
		$result = mysql_unbuffered_query($sql);
		if( $result != false )	{	// -fm für leere TBs ...
			$i=0;
			while ($ret = mysql_fetch_assoc($result)) {
				foreach($ret as $k => $v) {
					$ret_arr[$i][$k] = $v;
				}
				$i++;
			}
			if($debug){
				echo "<pre>".$sql." ".print_r($ret_arr,1)."</pre>";
			}
	
			return $ret_arr;
		}	else	{
			if($debug){
				echo "<pre>".$sql."</pre>";
			}
			return false;
		}
	}
	
	function countRows($sql, $debug=false)	{
		if($debug){
			echo "<pre>".$sql."</pre>";
		}
		$_SESSION['sql']['queryCount']++;
		mysql_query($sql);
		return mysql_affected_rows();
	}
	//
	//	static function countRowsSelect($sql)	{
	//		DB::connect();
	//		return mysql_num_rows(mysql_query($sql));
	//	}
	//
	function update($sql, $debug=false)	{
		if($debug){
			echo "<pre>".$sql."</pre>";
		}
		$_SESSION['sql']['queryCount']++;
		return mysql_query($sql);
	}

	/**
	 * function insert
	 * fuegt Daten in eine Tabelle ein
	 *
	 * @author Frank Meyer <fr4nk235@googlemail.com>
	 *
	 * @param string $tb Tabelle
	 * @param array $data 2D-Datenarray, wobei Schlüssel = Spaltenname, Wert = Daten sind
	 *
	 * @return bool
	 *
	 */
	function insert( $tb, $data, $link=null, $debug=false)	{
		// Fehlerfall
		if( !(isset($tb) && isset($data)))	{
			return false;
		}

		// erweiterter Fehlerfall..Tabelle gibts nicht? Array falsche Daten und so? ...

		// letzter Key
		$v = end($data);
		$k = key($data);

		$insert_start = 'INSERT INTO `'.$tb.'` (';
		$insert_middle = ') VALUES (';
		$insert_end = ') ';


		// Allin-one foreach
		// Insert-string Spalten & Daten zusammen bauen
		$insert_keys = "";
		$insert_values = "";
		foreach($data as $key => $value) {
			// letzten Schluessel/Datum abfangen wegen Komma und so
			if( $key === $k )	{
				$insert_keys .= "`".$key."`";
				// es wird immer von einem String ausgegangen und nur überprueft obs nen interger is
				if( gettype($value) === 'integer' )	{
					$insert_values .= $value;
				}	else	{
					$insert_values .= "'$value'";
				}
			}
			else{
				$insert_keys .= "`".$key."`".', ';
				// es wird immer von einem String ausgegangen und nur überprueft obs nen interger is
				if( gettype($value) === 'integer' )	{
					$insert_values .= $value.', ';
				}	else	{
					$insert_values .= "'$value', ";
				}
			}
		}

		// Insert-String komplett bauen^^
		$insert = $insert_start.$insert_keys.$insert_middle.$insert_values.$insert_end;
		if($debug){
			echo "<pre>".$insert."</pre>";
		}
		// in die DB schreiben
		$_SESSION['sql']['queryCount']++;
		if(empty($link))	{
			//connect();
			return mysql_query($insert);
		}	else	{
			return mysql_query($insert, $link);
		}
	}

	/**
	 * function insert
	 * fuegt Daten in eine Tabelle ein
	 *
	 * @author Frank Meyer <fr4nk235@googlemail.com>
	 *
	 * @param string $tb Tabelle
	 * @param array $data 2D-Datenarray, wobei Schlüssel = Spaltenname, Wert = Daten sind
	 *
	 * @return bool
	 *
	 */
	function multiInsert( $tb, $data, $debug=false, $link=null)	{
		// Fehlerfall
		if( !(isset($tb) && isset($data)))	{
			return false;
		}

		// erweiterter Fehlerfall..Tabelle gibts nicht? Array falsche Daten und so? ...

		// letzter Key
		$v = end($data);
		$k = key($data);

		$insert_start = 'INSERT INTO `'.$tb.'` (';
		$insert_middle = ') VALUES ';


		if(is_array($data) && count($data) > 0){
			$insertKeys = "";
			$insertValues = "";
			$i = 0;
			foreach($data as $k =>$v){
				//p($v);
				if(is_array($v) && count($v) > 0){

					if($insertValue != "") $insertValue .= ", ";

					foreach($v as $kk =>$vv){
						if($i == 0){
							if($insertKeys != "") $insertKeys .= ", ";
							$insertKeys .= $kk;
						}

						if($insertValues != "") $insertValues .= ", ";
						if( gettype($vv) === 'integer' OR is_numeric($vv))	{
							$insertValues .= $vv;
						}
						else{
							$insertValues .= "'".$vv."'";
						}

					}
					$insertValue .= "(".$insertValues.")";
					$insertValues = "";
				}


				$i++;
			}
		}

		//echo $insertKeys."\n<br>";
		//echo $insertValue;

		// zusammenbauen
		$insert = $insert_start.$insertKeys.$insert_middle.$insertValue;
		
		if($debug){
			return "<pre>".$insert."</pre>";
		}
		else{
// 			if(empty($link))	{
// 				//connect();
// 				return mysql_query($insert);
// 			}	else	{
// 				return mysql_query($insert, $link);
// 			}
			$_SESSION['sql']['queryCount']++;
			return mysql_query($insert);
		}
		
	}

}

?>