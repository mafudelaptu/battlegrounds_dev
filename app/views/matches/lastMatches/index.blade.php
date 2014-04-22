@section("content")
<div class="box">
	
<div class="page-header box_title">
	<h1>{{$heading}}</h1>
</div>
<div class="box_content">
	
@if (!empty($data))
<table id="lastMatchesTable"
	class="table table-striped lastMatchesTable" cellpadding="0" cellspacing="0"
	border="0">
	<thead>
		<tr align="center">
			<th>Type</th>
			<th>Date</th>
			<th>MatchID</th>
			<th>Mode</th>
		</tr>
	</thead>
	<tbody>
		@foreach($data as $k => $v)
		<tr>
			<td>
				@include("icons.matchtype", array("matchtype_id"=>$v->matchtype_id, "matchtype"=>$v->matchtype))
			</td>
			<td><span class="timeago" title="{{$v->created_at}}" datasort="{{$v->created_at}}">{{$v->created_at}}</span></td>
			<td><a href="{{URL::to('match/'.$v->id)}}">{{$v->id}}</a></td>
			<td><span class="t badge badge-info" title="{{$v->matchmode}}">{{$v->mm_shortcut}}</span></td>
		</tr>

		@endforeach
	</tbody>
</table>
<br> <!-- Scrollbalken workaround -->
@else
<div class="alert alert-warning"> 
		no last matches
</div>
@endif
</div>
</div>
@stop