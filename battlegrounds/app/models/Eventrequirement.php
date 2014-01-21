<?php

class Eventrequirement extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'pointborder' => 'required',
		'skillbracketborder' => 'required',
		'winsborder' => 'required'
	);
}
