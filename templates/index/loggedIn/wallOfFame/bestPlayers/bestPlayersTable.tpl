{if $data|count > 0 && $data|is_array}
<table class="table table-striped">
	<thead>
		<tr align="center">
			<th>#</th>
			<th>Points</th>
			<th>Player</th>
			<th>Win Rate</th>
		</tr>
	</thead>
	<tbody>
		{foreach key=k item=v from=$data name=bestPlayers_array}
		<tr
			class="{include file='prototypes/positionTrClass.tpl' position=$smarty.foreach.bestPlayers_array.iteration}">
			<td><strong>{$smarty.foreach.bestPlayers_array.iteration}.</strong></td>
			<td>{$v.Rank}</td>
			<td><img src="{$v.Avatar}">&nbsp;{include
				"prototypes/creditBasedName.tpl" name=$v.Name
				creditValue=$v.Credits playerSteamID=$v.SteamID}</td>
			<td><span class="text-warning">{$v.WinRate}%</span></td>
		</tr>
		{/foreach}
	</tbody>
</table>

{else}
<div class="alert fade in">No Rankings found!</div>
{/if}
<p>
	<a href="ladder.php?ID={$steamID}">View All Rankings <i
		class="icon-double-angle-right"></i></a>
</p>