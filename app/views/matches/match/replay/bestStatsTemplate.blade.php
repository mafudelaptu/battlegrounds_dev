@if($general['value'] > 0)
<div class="bSBox pull-left">
<div class="bSTitle" align="center">{{$general['label']}}</div>
<div style="background-image: url({{URL::to('img/match/replay/'.$id.'.png')}}); background-repeat: no-repeat;" class="bSIcon">
	<div class="bSValue" align="center">
		{{$general['value']}}
	</div>
</div>
<div class="bSPlayers">PLAYERS</div>
<div class="bSPlayersBox">
	@if(!empty($players) && count($players) > 0)
		@foreach($players as $k=>$v)
			<div align="center"><img alt="{{$v->name}}" src="{{$v->avatar}}">&nbsp;{{$v->name}}</div>
		@endforeach
	@endif
</div>
</div>

@else

@endif
