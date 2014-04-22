<h3>{{$title}}</h3>

@if(!empty($data))
<table class="table table-striped" id="{{$id}}">
<thead>
<tr>
	<th>Warned At</th>
	<th>In Prison Until</th>
	<th>Warn Expires At</th>
	<th>Warned By</th>
	<th>Reason</th>
</tr>
</thead>

<tbody>
	@foreach($data as $k => $v)
	<tr>
		<td>{{$v->created_at}}</td>
		<td>{{$v->banned_until}}</td>
		<td>{{$v->expires}}</td>
		<td>{{$v->reason}}
			@if($v->bannedBy > 0)
			 - <a href="{{URL::to('profile/$v->bannedBy')}}" target="_blank"><img src="{{$v->bannedByAvatar}}" alt="{{$v->bannedByName}}'s Avatar">{{$v->bannedByName}}</a>
			@endif
		</td>
		<td>{{$v->banReasonText}}</td>
	</tr>
	@endforeach
</tbody>
</table>
@else
<div class="alert alert-warning">
	no bans found
</div>
@endif