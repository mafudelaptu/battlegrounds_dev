<?php

class Matchreplay_beststat extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'matchreplay_id' => 'required',
		'match_id' => 'required',
		'user_id' => 'required',
		'replay_beststattype_id' => 'required',
		'value' => 'required'
		);

	public static function getBestStats($match_id){
		$ret = array();
		$data = Matchreplay_beststat::leftJoin("users", "users.id", "=", "matchreplay_beststats.user_id")
		->leftJoin("replay_beststattypes", "replay_beststattypes.id", "=", "matchreplay_beststats.replay_beststattype_id")
		->where("matchreplay_beststats.match_id", $match_id)
		->select("matchreplay_beststats.*", "users.*", "replay_beststattypes.name as beststatstype")
		->get();
		
		$data2 = array();
		if(!empty($data) && count($data) > 0){
			foreach ($data as $k => $v) {
				$type = $v['replay_beststattype_id'];
				$value = $v['value'];
				$label = $v['beststatstype'];
				$data2[$type]['general']['value'] = $value;
				$data2[$type]['general']['label'] = $label;
				$data2[$type]['players'][] = $data[$k];
			}
		}
		$ret['data'] = $data2;
		return $ret;
	}
}
