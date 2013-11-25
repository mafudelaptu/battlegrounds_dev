{if $data|count > 0 && $data|is_array}
<table class="table table-striped">
	<thead>
		<tr align="center">
			<th>#</th>
			<th>Points</th>
			<th>Name</th>
			<th>Win Rate</th>
		</tr>
	</thead>
	<tbody>
		{foreach key=k item=v from=$data name=bestPlayers_array}
		<tr
			class="{include file='prototypes/positionTrClass.tpl' position=$smarty.foreach.bestPlayers_array.iteration}">
			<td><strong>{$smarty.foreach.bestPlayers_array.iteration}.</strong></td>
			<td>{include file="prototypes/medalIcon.tpl"
				elo=$v.Rank}{$v.Rank}</td>
			<td><img src="{$v.Avatar}">&nbsp;{include
				"prototypes/creditBasedName.tpl" name=$v.Name
				creditValue=$v.Credits}</td>
			<td><span class="text-warning">{$v.WinLoseRatio}%</span></td>
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