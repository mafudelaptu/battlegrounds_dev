<div class="box">
	<div class="box_title">
			<div class="inline-block strong tt_none">
				
				@include("prototypes.username", array("credits" => 0,"username" => $userData->name,"user_id" => $userData->user_id, "truncateValue" => 0, "avatar" => $userData->avatar, "link"=>false))

				@if(GlobalSetting::isSteamGame() && $visitor ==false)
					<i class="fa fa-refresh t pointer" title="force syncing data (username, avatar) with Steam" id="syncWithSteam"></i>
				@endif

			</div>
			<div class="inline-block ml_20">
				<small>
					@foreach($matchtypes as $key => $type)
					@if($key === 0)
					<?php $active = ""; ?>
					@else
					<?php $active = "hide"; ?>
					@endif

					@if(  $stats[$type->id]['Ranking'] == 0)
					<?php $stats[$type->id]['Ranking'] = "unranked";?>
					@else
					<?php $stats[$type->id]['Ranking'] .= ".";?>
					@endif
					<div id="userstats_{{$type->id}}" class="profile_userstats {{$active}}">
						<span>
							Points:&nbsp;<strong class="text-success">
							{{$points[$type->id]}}</strong>
						</span>
						<span>
							Ranking:&nbsp;
							<strong class="text-info">{{$stats[$type->id]['Ranking']}}</strong>
						</span>
					</div>
					@endforeach
				</small>
			</div>
			<div class="inline-block pull-right">
				@foreach($matchtypes as $key => $type)
				@if($key === 0)
				<?php $active = "alert-info"; ?>
				@else
				<?php $active = "btn-link"; ?>
				@endif
				<span class="{{$active}} switchMatchtype p_8 mt_n7 mb_n7 tt_none inline-block" data-id="{{$type->id}}">{{$type->name}}</span>&nbsp;
				@endforeach
			</div>
			<div class="clearer"></div>
	
	</div>
	<div class="box_content p_5">
		<div class="row">
			<div class="col-sm-5">
				@include("profile.userInfo.index")

				@foreach($matchtypes as $key => $type)
				@if($key === 0)
				<?php $active = ""; ?>
				@else
				<?php $active = "hide"; ?>
				@endif
				<div class="{{$active}} mt_10 lvlup_user" id="lvlup_user_{{$type->id}}">
					@include("profile.userInfo.lvlUpInfo", array("data"=>$nextSkillbracket[$type->id]))
				</div>
				@endforeach
			</div>
			<div class="col-sm-3">
				@include("profile.userStats.heroes", array("data"=>$heroesStatsData))
			</div>
			<div class="col-sm-4">
				@include("profile.userStats.matchmodes", array("data"=>$matchModesStatsData))
				@include("profile.awards.index")
			</div>
		</div>
		
	</div>
</div>



