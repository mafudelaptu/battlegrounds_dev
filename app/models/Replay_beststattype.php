<?php

class Replay_beststattype extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'active' => 'required'
	);
}
