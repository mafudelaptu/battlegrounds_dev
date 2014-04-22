@if($replayUploadActive=="Dota2")
<div class="box">
<div class="customH2 box_title">Game<span>Statistics</span> @include("icons.info", array("title"=>"data is comming from all uploaded replays on this site"))</div>
<div class="box_content">
@if(!empty($data) && count($data)>0)
<table class="table table-striped">
	<tr>
		<td>Game length</td>
		<td>{{$data->gamelength}}</td>
	</tr>
	<tr>
		<td>Kills</td>
		<td>{{$data->kills}}</td>
	</tr>
	<tr>
		<td>Deaths</td>
		<td>{{$data->deaths}}</td>
	</tr>
	<tr>
		<td>Assists</td>
		<td>{{$data->assists}}</td>
	</tr>
	<tr>
		<td>Creep Kills</td>
		<td>{{$data->creep_kills}}</td>
	</tr>
	<tr>
		<td>Creep Denies</td>
		<td>{{$data->creep_denies}}</td>
	</tr>
	<tr>
		<td>Gold/Minute</td>
		<td>{{$data->gold_minute}}</td>
	</tr>
	<tr>
		<td>Exp/Minute</td>
		<td>na</td>
	</tr>
</table>
@else
	<div class="alert alert-warning">no replays uploaded yet</div>
@endif
</div>
</div>
@endif
