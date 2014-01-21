@if(!empty($replayData) && count($replayData) > 0)

<div class="page-header">
	<h2>
		<i class="icon-play-circle"></i>&nbsp;Replay-Data
	</h2>
</div>

<div class="row">
	<div class="col-sm-6">
		<h3>Stats</h3>
		@foreach($team as $teamID => $teamName)
		@if( $teamID==1)
		<?php 
		$tClass = "text-success";
		?>
		@else
		<?php 
		$tClass = "text-danger";
		?>
		@endif

		@include("matches.match.replay.teamTemplate", array("team"=>$teamName, "tClass"=>$tClass))
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Player</th>
					<th>Lvl</th>
					<th>Hero</th>
					<th>K</th>
					<th>D</th>
					<th>A</th>
					<th>Gold</th>
					<th>CS</th>
					<th>Denies</th>
				</tr>
			</thead>

			<tbody>
				@foreach($replayData as $k => $v)
				<?php 
					//dd($v);
				 ?>
				@if( $v->team_id==$teamID || $v->team_id == "")
				@include("matches.match.replay.statsTemplate", array("data"=>$v))
				@endif

				@endforeach
			</tbody>
		</table>
		@endforeach
	</div>
	<div class="col-sm-6">
		<h3>Best Players By Category</h3>
		@if( !empty($replayBestStats) && count($replayBestStats) > 0)
		<div align="center">
			@for($key=1; $key<=5; $key++ )
			@include("matches.match.replay.bestStatsTemplate", array("general"=>$replayBestStats[$key]['general'], "players"=>$replayBestStats[$key]['players'], "id"=>$key))
			@endfor
		</div>
		<div class="clearer"></div>
		<div align="center">
			@if(!empty($replayBestStats[$key]))
			@include("matches.match.replay.bestStatsTemplate", array("general"=>$replayBestStats[$key]['general'], "players"=>$replayBestStats[$key]['players'], "id"=>6))
			@endif
		</div>
		<div class="clearer"></div>
		<div align="center">
			@for($key=7; $key<=10; $key++)
			@if(!empty($replayBestStats[$key]))
				@include("matches.match.replay.bestStatsTemplate", array("general"=>$replayBestStats[$key]['general'], "players"=>$replayBestStats[$key]['players'], "id"=>$key))
			@endif
			@endfor
		</div>
		<div class="clearer"></div>
		@endif
	</div>
</div>
<h2>Replay-Chat</h2>
<div style="height:400px; overflow: auto;">
	@if(!empty($replayChat) && count($replayChat) > 0)
	@foreach($replayChat as $k=>$v)
	@if( $v->user_id > 0)
	<div class="row">
		<div class="col-sm-3" align="right">
			{$v->time} - <a href="profile/{{$v->user_id}}" target="_blank"><img src="{{$v->avatar}}" alt="{{$v->name}}'s Avatar"> {{$v->name}}:</a>
		</div>
		<div class="col-sm-9">
			{{$v->msg}}
		</div>
	</div>
	@else
	<div class="row">
		<div class="col-sm-3" align="right">
			{{$v->time}} - {{$v->name}}:
		</div>
		<div class="col-sm-9">
			{{$v->msg}}
		</div>
	</div>
	@endif
	@endforeach
	@endif
</div>



@endif