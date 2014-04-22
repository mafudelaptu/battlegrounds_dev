<?php

class PermaBan extends Eloquent {
	protected $guarded = array();
	public $timestamps = false;
	protected $table = "permaBans";
	protected $primaryKey = 'user_id';

	public static $rules = array(
		'user_id' => 'required',
		'banlistreason_id' => 'required',
		'banned_at' => 'required'
	);

	public function banlistreasons(){
		return $this->hasMany("BanlistReason", "id");
	}

	public function user(){
		return $this->belongsTo("User", "id");
	}

	public static function isUserPermaBanned($user_id){
		$user = PermaBan::where("user_id", $user_id)->count();
		
		if($user > 0){
			return true;
		}
		else{
			return false;
		}
	}

	public static function addPermaBan($user_id, $reason, $bannedBy, $banlistreason_id=2){
		$insertArray = array(
			"user_id" => $user_id,
			"banlistreason_id" => $banlistreason_id,
			"reason" => $reason,
			"bannedBy" => $bannedBy,
			"banned_at" => new DateTime,
			);
		try{
			DB::table("permaBans")->insert($insertArray);
			return "user: ".$user_id." got successfully permabanned.";
		}
		catch(Exception $e){
			return "user: ".$user_id." already permabanned.";
		}
	}

	public static function getPermaBanOfUser($user_id){
		return PermaBan::join("users", "permaBans.bannedBy", "=", "users.id")
		->where("user_id", $user_id);
	}

	public static function removePermaBanOfUser($user_id){
		PermaBan::where("user_id", $user_id)->delete();
		return "user: ".$user_id." not permabanned anymore";
	}
}
