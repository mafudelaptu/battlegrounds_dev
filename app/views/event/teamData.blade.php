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
				<td>
					@include("prototypes.username", array("username"=> $v->name, "user_id"=> $v->user_id, "truncateValue"=>20, "width"=>"18px", "avatar"=>$v->avatar))
				</td>
				<td>{{$v->points}}</td>
			</tr>
		@endforeach
	</tbody>
</table>
@endif