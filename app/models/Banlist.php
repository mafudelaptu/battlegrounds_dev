<?php

class Banlist extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'user_id' => 'required',
		'banned_at' => 'required',
		'banned_until' => 'required',
		'banlistreason_id' => 'required',
		'display' => 'required',
		'bannedBy' => 'required',
		'reason' => 'required'
		);

	public static function checkForBansOfUser($user_id){
		$ret = array();
		$data = Banlist::leftJoin("users", "banlists.bannedBy", "=", "users.id")
		->join("banlistreasons", "banlistreasons.id", "=", "banlists.banlistreason_id")
		->where("banlists.user_id", $user_id)
		->where("banlists.display", 1)
		//->where("banned_until", ">", new DateTime)
		->orderBy("banlists.banned_until", "DESC")
		->select("banlists.*", "users.avatar", "users.name", "banlistreasons.name as blr_banned_by");
		$banData = $data->first();
		$count = $data->count();

		if($count > 0){
			$ret['data']['banned_until'] = $banData->banned_until;
			$ret['data']['created_at'] = $banData->created_at->format("Y-m-d H:i:s");
			$ret['data']['avatar'] = $banData->avatar;
			$ret['data']['name'] = $banData->name;
			$ret['data']['reason'] = $banData->reason;
			$ret['data']['bannedBy'] = $banData->bannedBy;
			$ret['data']['blr_banned_by'] = $banData->blr_banned_by;

			$ret['banCount'] = $count;
			$ret['banned'] = false;
			$banned_until_ts = strtotime($banData->banned_until);
			$now = new DateTime;
			$now = strtotime($now->format("U"));
			//dd($banned_until_ts." ".$now);
			if($count > 1 && $banned_until_ts > $now){
				$ret['banned'] = true;
			}
			$ret['status'] = true;
		}
		else{
			$ret['status'] = false;
		}

		return $ret;
	}

	public static function getAllActiveBans($user_id){
		if($user_id > 0){
			$data = DB::table("banlists")->where("user_id", $user_id)
			->where("display", 1);
			
			return $data;
		}
		else{
			return  "user_id == 0";
		}
	}

	public static function getAllBans($user_id){
		if($user_id > 0){
			$data = DB::table("banlists")->where("user_id", $user_id);
			
			return $data;
		}
		else{
			return  "user_id == 0";
		}
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	public static function calculateBannedTill($user_id){
		$ret = array();

		if($user_id > 0){
			// bereits vorhandene BanCount auslesen
			$count = Banlist::getAllActiveBans($user_id)->count();

			switch($count){
				// Verwarnung
				case "0":
				$bannedTill = time();
				break;
				// Queue Ban
				case "1":
					// 24h ban
				$bannedTill = time() + (24 * 60 * 60);
				break;
				// Queue Ban
				case "2":
					// 3 Tage ban
				$bannedTill = time() + (24 * 60 * 60*3);
				break;
				// Queue Ban
				case "3":
					// 7 Tage ban
				$bannedTill = time() + (24 * 60 * 60*7);
				break;	
				case "4":
				// 21 Tage Ban
				$bannedTill = time() + (24 * 60 * 60*21);
				break;
				// Queue Ban
				case "5":
					// Perma ban
				$bannedTill = 99999999999;
				break;
			}
			$ret['data'] = $bannedTill;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "SteamID = 0";
		}

		return $ret;
	}

	public static function insertBan($user_id, $banlistreason_id, $reasonText="", $banned_by=0){
			// banned till
			$retTill = Banlist::calculateBannedTill($user_id);
			$bannedTill = $retTill['data'];
			$date = new DateTime;
			$date->setTimestamp($bannedTill);

			$insertArray = array(
				"user_id"=>$user_id,
				"banned_until"=>$date,
				"banlistreason_id"=>$banlistreason_id,
				"display"=>1,
				"bannedBy" => $banned_by,
				"reason"=>$reasonText,
				"created_at"=>new DateTime,
				);
		try{
				Banlist::insert($insertArray);
				return $date;
			}
			catch(Exception $e){
				return false;
			}
	}

	public static function getAllUsersWhoHaveToMuchActiveBans(){
		return Banlist::where("banlists.display", 1)
		->leftJoin("permaBans", "permaBans.user_id", "=", "banlists.user_id")
		->where("permaBans.banlistreason_id", null)
		->groupBy("banlists.user_id")
		->having("warnCount", ">=", 6)
		->select("banlists.user_id",
			DB::raw("COUNT(banlists.user_id) as warnCount"));
	}

	public static function getAllUsersThatHaveOldActiveBans($timeDecay){
		$date = new DateTime;
		$date->setTimestamp(time()-$timeDecay);
		return Banlist::where("banlists.display", 1)
		->where("created_at", "<=", $date)
		->leftJoin("permaBans", "permaBans.user_id", "=", "banlists.user_id")
		->where("permaBans.banlistreason_id", null)
		->select("banlists.*");
	}

	public static function removeLastBanOfUser($user_id){
		$data = Banlist::getAllActiveBans($user_id)
		->orderBy("banned_until", "desc")
		->take(1);

		$count = count($data->get());
		if($count == 1){
			$sql = "DELETE FROM banlists WHERE user_id = ".$user_id." ORDER BY banned_until DESC LIMIT 1";
			DB::select(DB::raw($sql));
			return true;
		}
		else{
			return false;
		}
	}
}
