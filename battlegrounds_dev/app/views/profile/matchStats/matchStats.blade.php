@if($replayUploadActive=="Dota2")
<div class="customH2">Match<span>Stats</span>  @include("icons.info", array("title"=>"data is comming from all uploaded replays on this site"))</div>
@if(!empty($data) && count($data)>0)
<table class="table table-striped">
	<tr>
		<td>Max Kills</td>
		<td>{{$data->MaxKills}}</td>
	</tr>
	<tr>
		<td>Max Deaths</td>
		<td>{{$data->MaxDeaths}}</td>
	</tr>
	<tr>
		<td>Max Assists</td>
		<td>{{$data->MaxAssists}}</td>
	</tr>
	<tr>
		<td>most Kills</td>
		<td>{{$data->MostKills}}</td>
	</tr>
	<tr>
		<td>best Support</td>
		<td>{{$data->MostSupports}}</td>
	</tr>
	<tr>
		<td>most Gold</td>
		<td>{{$data->MostGold}}</td>
	</tr>
	<tr>
		<td>most Last Hits</td>
		<td>{{$data->MostCS}}</td>
	</tr>
	<tr>
		<td>most Denies</td>
		<td>{{$data->MostDenies}}</td>
	</tr>
</table>
@else
	<div class="alert alert-warning">no replays uploaded yet</div>
@endif
@endif