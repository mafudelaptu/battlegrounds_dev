<div class="blackH2">Match<green>Stats</green> <i class="icon-question-sign t" data-original-title="data is comming from all uploaded replays on this site"></i></div>

<table class="table table-striped">
	<tr>
		<td>Max Kills</td>
		<td>{$data.MaxKills|string_format:"%d"}</td>
	</tr>
	<tr>
		<td>Max Deaths</td>
		<td>{$data.MaxDeaths|string_format:"%d"}</td>
	</tr>
	<tr>
		<td>Max Assists</td>
		<td>{$data.MaxAssists|string_format:"%d"}</td>
	</tr>
	<tr>
		<td>most Kills</td>
		<td>{$data.MostKills|string_format:"%d"}</td>
	</tr>
	<tr>
		<td>best Support</td>
		<td>{$data.MostSupports|string_format:"%d"}</td>
	</tr>
	<tr>
		<td>most Gold</td>
		<td>{$data.MostGold|string_format:"%d"}</td>
	</tr>
	<tr>
		<td>most Last Hits</td>
		<td>{$data.MostCS|string_format:"%d"}</td>
	</tr>
	<tr>
		<td>most Denies</td>
		<td>{$data.MostDenies|string_format:"%d"}</td>
	</tr>
</table>