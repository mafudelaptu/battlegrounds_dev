<div class="blackH2">Game<green>Statistics</green> <i class="icon-question-sign t" data-original-title="data is comming from all uploaded replays on this site"></i></div>
<table class="table table-striped">
	<tr>
		<td>Game length</td>
		<td>{$data.GameLength}</td>
	</tr>
	<tr>
		<td>Kills</td>
		<td>{$data.Kills|string_format:"%.2f"}</td>
	</tr>
	<tr>
		<td>Deaths</td>
		<td>{$data.Deaths|string_format:"%.2f"}</td>
	</tr>
	<tr>
		<td>Assists</td>
		<td>{$data.Assists|string_format:"%.2f"}</td>
	</tr>
	<tr>
		<td>Creep Kills</td>
		<td>{$data.CreepKills|string_format:"%.2f"}</td>
	</tr>
	<tr>
		<td>Creep Denies</td>
		<td>{$data.CreepDenies|string_format:"%.2f"}</td>
	</tr>
	<tr>
		<td>Gold/Minute</td>
		<td>{$data.GoldMinute|string_format:"%d"}</td>
	</tr>
</table>
