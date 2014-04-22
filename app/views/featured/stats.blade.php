@section("stats")
<div class="arena_stats">
	<ul class="inLine">
<!-- 		<li>Unique Users: <strong>{{$uniqueUser}}</strong></li>
		<li>Matches played: <strong>{{$matchesPlayed}}</strong></li> -->
		<li style="margin-right:15px;"><i class="fa fa-users fa-1x t" title=""></i> In Queue: <strong>{{$usersInQueue}}</strong></li>

		<li><i class="fa fa-gamepad fa-1x t" title=""></i> Live Matches: <strong>{{$liveMatches}}</strong></li>
	</ul>
</div>
@show