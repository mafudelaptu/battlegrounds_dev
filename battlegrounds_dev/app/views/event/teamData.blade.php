@if(count($data) > 0 && !empty($data))
<table
	class="table table-striped" cellpadding="0" cellspacing="0"
	border="0">
	<thead>
		<tr align="center">
			<th>Player</th>
			<th>Points</th>
		</tr>
	</thead>
	<tbody>
		@foreach($data as $k => $v)
			<?php 
				$highlight = "";
				if($v->user_id == Auth::user()->id){
					$highlight = "info";
				}
			 ?>
			<tr class="{{$highlight}}">
				<td><a href="profile/{{$v->user_id}}" target="_blank"><img src="{{$v->avatar}}" alt="{{$v->name}}'s Avatar"> {{$v->name}}</a></td>
				<td>{{$v->points}}</td>
			</tr>
		@endforeach
	</tbody>
</table>
@endif