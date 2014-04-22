
<div class="row">
	<div class="col-sm-3">
		@foreach($matchtypes as $key => $type)
		@if($key === 0)
		<?php $active = ""; ?>
		@else
		<?php $active = "hide"; ?>
		@endif
		<div class="{{$active}}" id="{{$type->id}}">
			@include("prototypes.skillbracketImage", array("skillbracket_id"=>$skillbracket[$type->id]['skillbrackettype_id'], "skillbracket"=>$skillbracket[$type->id]['skillbracket']))
		</div>
		@endforeach
	</div>
	<div class="col-sm-9">
		<div class="h4">
			<div class="pull-left">
				Credits:
				@include("prototypes.creditValue", array("creditValue"=> $credits))
			</div>
			<div class="pull-right tt_uc">
				<small>Warns: {{$activeBansCount}}<span class="t muted" title="total warns">({{$allBansCount}})</span></small>
			</div>
			<div class="clearer"></div>
		</div>
		@include("profile.userInfo.userItems")
		<table width="100%">
			<tr>
				<td>Acc created:</td>
				<td align="right">{{$userData->created_at}}</td>
			</tr>
			<tr>
				<td>Last activity:</td>
				<td align="right">{{$userData->updated_at}}</td>
			</tr>
		</table>
		<div class="pull-left">
			<a href="http://dotabuff.com/players/{{$userData->id}}" target="_blank"><i
				class=" icon-tasks"></i>DotaBuff-Profile</a>
			</div>
			<div class="pull-right" align="right">
				<a href="http://steamcommunity.com/profiles/{{$userData->id}}" target="_blank"><i
					class="icon-user"></i>Steam-Profile</a>
				</div>
			<div class="clearer"></div>
				@foreach($matchtypes as $key => $type)
				@if($key === 0)
				<?php $active = ""; ?>
				@else
				<?php $active = "hide"; ?>
				@endif
				<?php $data = $stats[$type->id]; ?>
				<div id="table_userstats_{{$type->id}}"class="row {{$active}} tt_uc table_userstats" align="center">
					<div class="col-sm-3">
						<div><strong>Wins</strong></div>
						<div><span class="text-success">{{$data['Wins']}}</span></div>
					</div>
					<div class="col-sm-3 p_0">
						<div><strong>Losses</strong></div>
						<div><span class="text-danger">{{$data['Losses']}}</span></div>
					</div>
					<div class="col-sm-3 p_0">
						<div><strong>Winrate</strong></div>
						<div><span class="text-warning">{{$data['WinRate']}}%</span></div>
					</div>
					<div class="col-sm-3 p_0">
						<div><strong>Leaves</strong></div>
						<div><span class="">{{$data['Leaves']}}</span></div>
					</div>
				</div>
				@endforeach
			</div>
		</div>

		