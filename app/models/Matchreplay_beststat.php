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

	public static function calculateBestStats($players){
		$data = array();
		if(!empty($players) && count($players) > 0){
			$mostKills = 0;
			$bestTeamplayer = 0;
			$mostLastHits = 0;
			$mostDenies = 0;
			$mostXPM = 0;
			$mostGPM = 0;

			$mostKillsPlayers = array();
			$bestTeamplayerPlayers = array();
			$mostLastHitsPlayers = array();
			$mostDeniesPlayers = array();
			$mostXPMPlayers = array();
			$mostGPMPlayers = array();

			foreach ($players as $k => $v) {
				//mostKills":
				if($v->kills >= $mostKills){
					$mostKills = $v->kills;
				}

				//"bestTeamplayer":
				if($v->assists >= $bestTeamplayer){
					$bestTeamplayer = $v->assists;
				}

				//case "mostLastHits":
				if($v->cs >= $mostLastHits){
					$mostLastHits = $v->cs;
				}

				//case "mostDenies":
				if($v->denies >= $mostDenies){
					$mostDenies = $v->denies;
				}

				// highest Gold per Minute
				if($v->gpm >= $mostGPM){
					$mostGPM = $v->gpm;
				}

				// highest Experience per Minute
				if($v->xpm >= $mostXPM){
					$mostXPM = $v->xpm;
				}
			}

			// most werte bestimmt jetzt die player zuweisen
			foreach ($players as $k => $v) {
				//mostKills":
				if($v->kills == $mostKills){
					$mostKillsPlayers[] = $v;
				}

				//"bestTeamplayer":
				if($v->assists == $bestTeamplayer){
					$bestTeamplayerPlayers[] = $v;
				}

				//case "mostLastHits":
				if($v->cs == $mostLastHits){
					$mostLastHitsPlayers[] = $v;
				}

				//case "mostDenies":
				if($v->denies == $mostDenies){
					$mostDeniesPlayers[] = $v;
				}

				// highest Gold per Minute
				if($v->gpm == $mostGPM){
					$mostGPMPlayers[] = $v;
				}

				// highest Experience per Minute
				if($v->xpm == $mostXPM){
					$mostXPMPlayers[] = $v;
				}
			}

			$data = array(
				1 => array("val"=>$mostKills, "players"=>$mostKillsPlayers),
				4 => array("val"=>$mostDenies, "players"=>$mostDeniesPlayers),
				2 => array("val"=>$bestTeamplayer, "players"=>$bestTeamplayerPlayers),
				3 => array("val"=>$mostLastHits, "players"=>$mostLastHitsPlayers),
				11 => array("val"=>$mostGPM, "players"=>$mostGPMPlayers),
				12 => array("val"=>$mostXPM, "players"=>$mostXPMPlayers),
			);
		}
		return $data;
	}

}
