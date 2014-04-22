<?php

class Tournamenttype extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'shortcut' => 'required',
		'active' => 'required'
	);
}
