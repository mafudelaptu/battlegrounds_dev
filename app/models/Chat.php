<?php

class Chat extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'section' => 'required',
		'user_id' => 'required',
		'message' => 'required'
	);

	public static function insertChatMessage($section, $user_id, $message){
		$insertArray = array(
			"section"=>$section,
			"user_id"=>$user_id,
			"message"=>$message,
			"created_at"=>new DateTime,
			);
		try{

		Chat::insert($insertArray);
						}
						catch(Exception $e){
							return false;
						}
	}

	public static function getOlderMessages($section, $lastTimestamp){

		return Chat::where("chats.section", $section)
					->where("chats.created_at", "<", $lastTimestamp)
					->take(GlobalSetting::getChatHistoryCount());
	}
}
