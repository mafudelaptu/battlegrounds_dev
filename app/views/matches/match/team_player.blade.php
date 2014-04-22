{{-- set variables --}}
<?php
	$userHighlight = "";
	$hostIcon = "";
	if($iteration%2==0){
		$bg_iter = "matchRow1";
	}
	else{
		$bg_iter = "matchRow2";
	}

	if(Auth::user()->id == $playerdata['user_id']){
		$userHighlight = "highlight";
	}

	//dd($host);
	if($playerdata['leaver'] == true){
		$userHighlight = "highlight_leaver";
	}

	if($playerdata['user_id'] == $host->user_id){
		$userHighlight = "highlight_host";
		$hostIcon = "&nbsp;".View::make("icons.host")->render();
	}


?>
<div class="row {{$userHighlight}} matchRow {{$bg_iter}}">
	<div class="col-sm-6" style="width:258px">
		<span data-toggle="popover" title="" data-content="Wins: <span class='text-success'>{{$playerdata['stats']['Wins']}}</span> Losses: <span class='text-error'>{{$playerdata['stats']['Losses']}}</span> Winrate: <span class='text-warning'>{{$playerdata['stats']['WinRate']}}%</span> Leaves: {{$playerdata['stats']['Leaves']}}" data-original-title="User-Statistics" data-trigger="hover" data-html="true" data-placement="top" data-container="body">

		@include("prototypes.username", array("credits" => $playerdata['credits'],"username" => $playerdata['name'],"user_id" => $playerdata['user_id'],"truncateValue" => 0, "avatar" => $playerdata['avatar']))
		{{$hostIcon}}
	</span>
	</div>
	<div class="col-sm-2 text-right" style="width: 85px;">
		@include("matches.match.team_player_points", array("points" => $playerdata['points'], "winPoints" => $playerdata['winPoints'], "losePoints" => $playerdata['losePoints'], "pointsChange" => $playerdata['pointschange']))
	</div>
	<div class="col-sm-2 text-right" style="width:43px;">
		@if($inMatch && Auth::user()->id != $playerdata['user_id'])
		
		<div class="btn-group">
				  <a class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" href="#">
				    @include("icons.match_user_menu")
				    <span class="caret"></span>
				  </a>
				  <ul class="dropdown-menu" data-container="body">
				   @include("matches.match.team_player_button_playerinfo", array("user_id" => $playerdata['user_id']))
				  </ul>
		</div>

		@endif
	</div>


	<div class="col-sm-2 text-center" style="width:67px">
		
		@if($inMatch && Auth::user()->id != $playerdata['user_id'])
			{{--dd($userVotes)--}}
			@if(array_search($playerdata['user_id'], $userVotes) !== false)
			<?php 

							//var_dump((array_search($playerdata['user_id'], $userVotes)));
			 ?>
			@include("matches.match.team_player_vote_info", array("votestats" => $voteStats[$playerdata['user_id']]))
			
			@else
			<small>
			@include("matches.match.team_player_vote_buttons", array("user_id" => $playerdata['user_id']))
			</small>
			@endif
			
		@endif
		
	</div>
</div>