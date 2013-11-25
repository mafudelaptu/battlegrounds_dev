{if $data|count > 0 && $data|is_array}
<table class="table table-striped">
	<thead>
		<tr align="center">
			<th>Credits</th>
			<th>Player</th>
			<th>Points</th>
		</tr>
	</thead>
	<tbody>
		{foreach key=k item=v from=$data name=lastMatches_array}
		{if $v.TeamWonID == 1}
			{assign "teamWon" "The Radiant"}
		{elseif $v.TeamWonID == 2}
			{assign "teamWon" "The Dire"}
		{/if}
		<tr class="">
			<td>{include "prototypes/creditValue.tpl" creditValue=$v.Credits}</td>
			<td><img src="{$v.Avatar}">&nbsp;{include
				"prototypes/creditBasedName.tpl" name=$v.Name
				creditValue=$v.Credits playerSteamID=$v.SteamID}</td>
			<td>{$v.Points}</td>
		</tr>
		{/foreach}
	</tbody>
</table>

{else}
<div class="alert fade in">No Matches found!</div>
{/if}