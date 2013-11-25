{if $data|count > 0 && $data|is_array}
<table class="table table-striped">
	<thead>
		<tr align="center">
			<th></th>
			<th>ID</th>
			<th>Mode</th>
			<th>Result</th>
		</tr>
	</thead>
	<tbody>
		{foreach key=k item=v from=$data name=lastMatches_array}
		{if $v.TeamWonID == 1}
			{assign "teamWon" "The Radiant"}
			{assign "labelClass" "success"}
		{elseif $v.TeamWonID == 2}
			{assign "teamWon" "The Dire"}
			{assign "labelClass" "important"}
		{/if}
		<tr class="">
			<td style="text-align:center">{include file="prototypes/matchTypeIcon.tpl" MatchTypeID="{$v.MatchTypeID}"}</td>
			<td><a href="match.php?matchID={$v.MatchID}">{$v.MatchID}</a></td>
			<td><span class="t" title="{$v.MatchModeName}">{$v.MatchModeShortcut}</span></td>
			<td><span class="label label-{$labelClass}">{$teamWon}</span></td>
		</tr>
		{/foreach}
	</tbody>
</table>

{else}
<div class="alert fade in">No Matches found!</div>
{/if}