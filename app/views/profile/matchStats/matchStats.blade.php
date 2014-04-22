@if($replayUploadActive=="Dota2")
<div class="box">
<div class="customH2 box_title">Match<span>Stats</span>  @include("icons.info", array("title"=>"data is comming from all uploaded replays on this site"))</div>
<div class="box_content">
@if(!empty($data) && count($data)>0)
<table class="table table-striped">
	<tr>
		<td>Max Kills</td>
		<td>{{(int)$data->MaxKills}}</td>
	</tr>
	<tr>
		<td>Max Deaths</td>
		<td>{{(int)$data->MaxDeaths}}</td>
	</tr>
	<tr>
		<td>Max Assists</td>
		<td>{{(int)$data->MaxAssists}}</td>
	</tr>
	<tr>
		<td>most Kills</td>
		<td>{{(int)$data->MostKills}}</td>
	</tr>
	<tr>
		<td>best Support</td>
		<td>{{(int)$data->MostSupports}}</td>
	</tr>
	<tr>
		<td>most Gold</td>
		<td>{{(int)$data->MostGold}}</td>
	</tr>
	<tr>
		<td>most Last Hits</td>
		<td>{{(int)$data->MostCS}}</td>
	</tr>
	<tr>
		<td>most Denies</td>
		<td>{{(int)$data->MostDenies}}</td>
	</tr>
</table>
@else
	<div class="alert alert-warning">no replays uploaded yet</div>
@endif
</div>
</div>
@endif