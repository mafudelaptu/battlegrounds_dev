{if $data|count > 0 && $data|is_array}
<table id="{$TableID}"
	class="table table-striped LadderTable" cellpadding="0" cellspacing="0"
	border="0">
	<thead>
		<tr align="center">
			<th>#</th>
			{if $smarty.const.NOLEAGUES == false}
				<th></th>
			{/if}
			<th>D2L-Points</th>
			<th>Player</th>
			<th>Points earned</th>
			<th>Base-Points <a href="help.php#BasePoints" target="_blank"><i class="icon-question-sign"></i></a></th>
			<th>Wins</th>
			<th>Losses</th>
			<th>Win Rate</th>
			<th>Leaves</th>
		</tr>
	</thead>
	<tbody>
		{foreach key=k item=v from=$data name=data_array}

		{assign "elo" $v.Rank}
		{assign "winRate" $v.WinRate}

		{if $steamID == $v.SteamID}
			{assign "highlightUser" "info"}
		{else}
			{assign "highlightUser" ""}
		{/if}
		<tr class="{$highlightUser} {include file='prototypes/positionTrClass.tpl' position=$smarty.foreach.data_array.iteration}">
			<td>{$smarty.foreach.data_array.iteration}.</td>
			{if $smarty.const.NOLEAGUES == false}
				<td>{include file="prototypes/medalIcon.tpl" leagueNameSimple=$v.LeagueName}</td>
			{/if}
			<td style="font-weight:bold">{$elo}</td>
			<td><a href="profile.php?ID={$v.SteamID}"><img src="{$v.Avatar}" alt="{$v.Name}'s Avatar"> {$v.Name}</a></td>
			
			<td>{$v.PointsEarned}</td>
			<td>{$v.BasePoints}</td>
			<td class="text-success">{$v.Wins}</td>
			<td class="text-error">{$v.Loses}</td>
			<td class="text-warning">{$winRate} %</td>
			<td>{$v.Leaves}</td>
		</tr>

		{/foreach}
	</tbody>
</table>
<br> <!-- Scrollbalken workaround -->
{else} no Data, sorry {/if}
