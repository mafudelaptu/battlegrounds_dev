<div class="custom2H2">
	Highest Credits
</div>
@if(count($data) > 0)
<div id="highestCreditsTable">
<table class="table table-striped">
	<thead>
		<tr align="center">
			<th>#</th>
			<th>Credits</th>
			<th>Player</th>
		</tr>
	</thead>
	<tbody>
		@foreach($data as $k=>$v)
		
		<tr>
			<td>
				<strong>{{($k+1)}}</strong>
			</td>
			<td>
				{{$v->credits}}
			</td>
			<td>
				@include("prototypes/username", array("username"=>$v->name, "avatar"=>$v->avatar, "credits"=>$v->credits, "user_id"=>$v->id, "truncateValue"=>15, "avatarWidth"=>18))
			</td>
		</tr>
		@endforeach
	</tbody>
</table>

@else
<div class="alert alert-warning">No Players found!</div>
@endif
