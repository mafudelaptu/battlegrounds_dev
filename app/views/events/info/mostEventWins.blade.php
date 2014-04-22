@if(!empty($mostEventWins) && count($mostEventWins) > 0)
<div class="box">
	<div class="box_title">
	Most Event Wins
	</div>
	<div class="box_content">
		
<table class="table table-striped">
		<thead>
			<tr align="center">
				<th>Name</th>
				<th>Wins</th>
			</tr>
		</thead>
		
		<tbody>
	<!-- TBODY zusammenbauen -->
		  @foreach($mostEventWins as $k => $v)
		  	<tr>
				<td>
					@include("prototypes.username", array("username"=>$v->name, "credits"=>$v->credits, "user_id"=>$v->user_id, "avatar"=>$v->avatar))
				</td>
				<td>
					{{$v->Wins}}
				</td>
			</tr>
		  @endforeach
		</tbody>
</table>
	</div>

</div>
@endif