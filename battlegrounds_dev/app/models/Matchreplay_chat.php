<?php

class Matchreplay_chat extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'matchreplay_id' => 'required',
		'match_id' => 'required',
		'name' => 'required',
		'time' => 'required',
		'msg' => 'required'
	);

	public $timestamps = false;

	public static function getChat($match_id){
		return Matchreplay_chat::leftJoin("users", "users.name", "=", "matchreplay_chats.name")->where("match_id", $match_id);
	}

}
