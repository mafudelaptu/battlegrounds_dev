<div class="box">
	<div class="box_title">
		
	@include("icons.eventStats")
	Stats
	</div>
	<div class="box_content">
		
<dl class="dl-horizontal">
  <dt style="text-align: left">Events played:</dt>
  <dd><span class="">{{$eventStats['events_played']}}</span></dd>
  <dt style="text-align: left">Players signed-in:</dt>
  <dd><span class="">{{$eventStats['players_signed_in']}}</span></dd>
  <dt style="text-align: left">Players played:</dt>
  <dd><span class="">{{$eventStats['players_played']}}</span></dd>
</dl>
	</div>
</div>