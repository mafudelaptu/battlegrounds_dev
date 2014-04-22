@if(!empty($allEventTypes) && count($allEventTypes)>0)
<div class="box">
	<div class="box_title">
	Calendar
	</div>
	<div class="box_content">
		
<table class="table table-striped">
	<thead>
		<tr>
			<th>Day</th>
			<th>Prize</th>
			<th><span class="t" title="Requirements">Req</span</th>
			<th>Name</th>
			<th>Details</th>
		</tr>
	</thead>
	<tbody>
		@foreach($allEventTypes as $k => $v)
		<tr>
			<td><span class="t" title="every {{$v->start_day}}">{{$v->start_day}}<br>{{$v->start_time}} CET</span></td>
			<td>
				@include("icons.prizetype", array("prizetype_id"=>$v->prizetype_id, "prizecount"=>$v->prizecount, "prize"=>$v->prize, "width"=>50))
			</td>
			<td>
				@if($v->pointReq > 0 || $v->skillbracketReq != "")

					@if($v->pointReq > 0)
					<div class="pull-left">>= <span class="label t" title="minimum Points">{{$v->pointReq}}</span></div>
					@endif

					@if($v->skillbracketReq != "")
						<div class="pull-left t" title="minimum Skillbracket: {{$v->skillbracketReq}}">
							@include("icons/skillbracket", array("skillbracket_id"=>$v->skillbracketReq, "skillbracket"=>$v->skillbracket))
						</div>
					@endif
				@else
				no Req
				@endif
			</td>
			<td>{{$v->name}}</td>
			<td>
				<span class="t badge badge-info" data-original-title="{{$v->matchmode}}">{{$v->mm_shortcut}}</span>
				<span class="t badge badge-danger" data-original-title="{{$v->region}}">{{$v->r_shortcut}}</span>
				<span class="t badge badge-warning"
				data-original-title="{{$v->tournamenttype}}">{{$v->tt_shortcut}}</span> <span
				class="t label label-default" title="Minimum Players">{{$v->min_submissions}}</span>
			</td>
		</tr>
		@endforeach
	</tbody>
</table>
	</div>
</div>
@endif