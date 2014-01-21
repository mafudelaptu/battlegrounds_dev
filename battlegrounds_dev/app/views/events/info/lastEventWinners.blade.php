@if(!empty($lastEventWinner) && count($lastEventWinner) > 0)
<h3>Last Event Winners</h3>
<table class="table table-striped">
		<thead>
			<tr align="center">
				<th>Name</th>
			</tr>
		</thead>
		
		<tbody>
	<!-- TBODY zusammenbauen -->
		  @foreach($lastEventWinner as $k => $v)
		  	<tr>
				<td>
					@include("prototypes.username", array("username"=>$v->name, "credits"=>$v->credits, "user_id"=>$v->user_id, "avatar"=>$v->avatar))
				</td>
			</tr>
		  @endforeach
		</tbody>
</table>

@endif