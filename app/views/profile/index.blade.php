@section("content")

@include("profile.overview")

<ul class="nav nav-tabs">
	<li class="active"><a href="#overview" data-toggle="tab">Overview</a></li>
	@if(GlobalSetting::getTeamsActive())
	<li class=""><a href="#teams" data-toggle="tab">Teams</a></li>
	@endif
	@if(!$visitor)
	<li class=""><a href="#warns" data-toggle="tab">Warns</a></li>
<!-- 	<li class=""><a href="#backpack" data-toggle="tab">Backpack</a></li>
	<li class=""><a href="#refer-a-friend" data-toggle="tab">Refer-A-Friend</a></li> -->
	@endif
</ul>
<div class="tab-content">
	<div class="tab-pane active" id="overview">
		<div class="row">
			<div class="col-sm-4">
				@include("profile.matchStats.last_matches")
			</div>
			<div class="col-sm-4">
						@include("profile.matchStats.matchStats", array("data"=>$matchStats))
					</div>
			<div class="col-sm-4">
						@include("profile.matchStats.gameStats", array("data"=>$gameStats))
			</div>
		</div>


		<div class="row" id="graphArea">
			<div class="col-sm-4">
				@include("profile/graphs/pointRose")
			</div>
			<div class="col-sm-8">
				@include("profile/graphs/pointHistory")
			</div>
		</div>
	</div>
	@if(GlobalSetting::getTeamsActive())
	<div class="tab-pane" id="teams">
		@include("profile.teams")
	</div>
	@endif
	@if(!$visitor)
	<div class="tab-pane" id="warns">
		@include("profile.warns.index", array("data"=>$bansData, "permaban" => $permaban))
	</div>
<!-- 	<div class="tab-pane" id="backpack">
		@include("profile.backpack")
	</div>
	<div class="tab-pane" id="refer-a-friend">
		@include("profile.refer-a-friend")
	</div> -->
	@endif

</div>
@stop