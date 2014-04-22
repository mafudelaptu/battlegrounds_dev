@foreach($modes as $mode)
<div class="row mmDetails">
	<div class="col-sm-6">
		{{ $mode->name }}({{$mode->shortcut}})
	</div>
	<div class="col-sm-6 stats">
		<div>Player(s) in Queue: <span class="badge badge-info t" id="labelPlayers{{$mode->matchmode_id}}">1</span></div>
		<div class="positionInQueue">Your Position in Queue is: <span class="label label-default" id="labelPosition{{$mode->matchmode_id}}">1</span></div>
	</div>
</div>
@endforeach