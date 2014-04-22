<?php

class Eventtype extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'matchmode_id' => 'required',
		'matchtype_id' => 'required',
		'tournamenttype_id' => 'required',
		'description' => 'required',
		'min_submissions' => 'required',
		'start_time' => 'required',
		'start_day' => 'required',
		'region_id' => 'required',
		'active' => 'required',
		'prizetype_id' => 'required',
		'eventrequirement_id' => 'required'
		);


	public static function getAllActiveEventtypes($data=false){
		if($data){
			return Eventtype::where("eventtypes.active",1)->join("matchmodes", "matchmodes.id", "=", "eventtypes.matchmode_id")
			->join("regions", "regions.id", "=", "eventtypes.region_id")
			->join("tournamenttypes", "tournamenttypes.id", "=", "eventtypes.tournamenttype_id")
			->join("eventrequirements", "eventrequirements.id", "=", "eventtypes.eventrequirement_id")
			->join("prizetypes", "prizetypes.id", "=", "eventtypes.prizetype_id")
			->leftJoin("skillbrackettypes", "skillbrackettypes.id", "=", "eventrequirements.skillbracketborder")
			->where("eventtypes.region_id", Auth::user()->region_id)
			->select(
				"eventtypes.*",
				"matchmodes.name as matchmode",
				"matchmodes.shortcut as mm_shortcut",
				"regions.name as region",
				"regions.shortcut as r_shortcut",
				"tournamenttypes.name as tournamenttype",
				"tournamenttypes.shortcut as tt_shortcut",
				"eventrequirements.pointborder as pointReq",
				"skillbrackettypes.id as skillbracketReq",
				"skillbrackettypes.name as skillbracket",
				"prizetypes.name as prize",
				"prizetypes.count as prizecount",
				"prizetypes.type as prizetype"
				);
		}
		else{
			return Eventtype::where("eventtypes.active",1);
		}
		
	}

	public static function getData($eventtype_id){
		return Eventtype::where("id", $eventtype_id);
	}

	public static function calculateNextStartTimestamp($start_time, $start_day, $region_id){
		if($start_time != "" && $start_day != ""){
			$timestamp = strtotime("next ".$start_day." ".$start_time);

			switch ($region_id) {
				case 1:
					# code...
				break;
				case 2:
					# code...
				break;
				case 3:
				# code...
				break;
				case 4:
				# code...
				break;
				case 5:
				# code...
				break;
				case 6:
					# code...
				break;
				case 7:
				# code...
				break;
				case 8:
				# code...
				break;
				case 9:
				# code...
				break;
				default:
					# code...
				break;
			}
			return $timestamp;
		}
		else{
			return "start_time || start_day == null";
		}
	}
}
