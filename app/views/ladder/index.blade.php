@section('content')
<div class="box">
	<div class="box_title">
{{$heading}}
</div>
<div class="box_content">
<div class="alert alert-warning">
	<strong>Note:</strong> Only players, who reached the Bronze-League will be recorded in the Ladder. Players that once reached Bronze-League, will stay inside it	
</div>

@include("ladder.ladder_navi")

<hr />


<table id="ladderTable"
	class="table table-striped" cellpadding="0" cellspacing="0"
	border="0">
	<thead>
		<tr align="center">
			<th>#</th>
			<th></th>
			<th>D2L-Points</th>
			<th width="400">Player</th>
			<th>Points earned</th>
			<th>Wins</th>
			<th>Losses</th>
			<th>Win Rate</th>
			<th>Leaves</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
</div>
</div>
@stop