{if $bestRankings|count > 0 && $bestRankings|is_array}
<table class="table table-striped">
	<thead>
		<tr align="center">
			<th>Rank</th>
			<th>Type</th>
			<th>Mode</th>
			<th>Points</th>
			<th>Wins</th>
			<th>Losses</th>
		</tr>
	</thead>
	<tbody>
		{foreach key=k item=v from=$bestRankings name=bestRankings_array}
		<tr class="{include file='prototypes/positionTrClass.tpl' position=$v.position}">
			<td><strong>{$v.position}.</strong></td>
			<td style="text-align:center">{include file="prototypes/matchTypeIcon.tpl" MatchTypeID=$v.MatchTypeID}</td>
			<td><span class="t" title="{$v.MatchMode}">{$v.MatchModeShortcut}</span></td>
			<td>{$v.Elo}</td>
			<td class="text-success">{$v.Wins}</td>
			<td class="text-error">{$v.Losses}</td>
		</tr>
    	{/foreach}
	</tbody>
</table>

{else}
<div class="alert fade in">No Rankings found!</div>
{/if}
<p>
<a href="ladder.php?ID={$steamID}">View All Rankings <i class="icon-double-angle-right"></i></a></p>