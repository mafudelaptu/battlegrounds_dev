<?php

class Help extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'type' => 'required',
		'caption' => 'required',
		'content' => 'required',
		'order' => 'required',
		'active' => 'required'
	);

	public static function getAllData($type_id){
		return Help::where("type",$type_id)->orderBy("order", "ASC");
	}
}
