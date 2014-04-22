@if($team_id == 1)
<?php $alertClass = "success"; ?>
@else
<?php $alertClass = "danger"; ?>
@endif

<?php 
$team_won_glow = "";
if($matchState == "closed" && $team_id == $matchData->team_won_id){
	$team_won_glow = "team_won_glow_";
	if($team_id == 1){
		$team_won_glow .= "green";
	}
	else{
		$team_won_glow .= "red";
	}
}
 ?>

<div class="box {{$team_won_glow}} mt_10" style="overflow:visible;">
	<div class="box_title team-title ">
		<div class="row">
			<div class="col-sm-1">
				{{HTML::image("img/dc/svun_small.png", "svun small")}}
			</div>
			<div class="col-sm-10 text-center">
				{{$team[$team_id]}} 

				@if($matchState == "closed" && $team_id == $matchData->team_won_id)
				@include("matches.match.middle_area_result_closed", array("team"=>$team, "team_won_id"=> $matchData->team_won_id))
				@endif

				<div class="label t" title="team-points">{{$teamStats['team_'.$team_id]}}</div>
				
			</div>
			<div class="col-sm-1 text-right">
				{{HTML::image("img/dc/svun_small_mirrored.png", "svun small")}}
			</div>
		</div>
	</div>
	<div class="box_content" style="padding-top:5px; padding-bottom: 5px; overflow:visible;">
		@foreach($data as $i=>$player)
		@include("matches.match.team_player", array('playerdata' => $player, "iteration"=>$i))

		@endforeach
	</div>
</div>