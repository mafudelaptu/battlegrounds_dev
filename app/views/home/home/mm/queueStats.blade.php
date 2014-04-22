@if(!empty($data))
<div class="row" align="center">
	<div class="col-sm-6">
		<i class="fa fa-users fa-2x t" title="players in queue" style="color: #3a87ad"></i>
		<div class="">{{$data->queueCount}}</div>
	</div>

	<div class="col-sm-6">
		<i class="fa fa-gamepad fa-2x t" title="players in match" style="color: #b94a48"></i>
		<div class="">
			@if(!empty($data->openMatches))
			<a href="openMatches">{$data->openMatches} (live)</a> 
			@else
			0
			@endif
		</div>			
	</div>
</div>
@endif