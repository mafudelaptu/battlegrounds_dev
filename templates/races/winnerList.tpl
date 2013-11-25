
<style type="text/css">
.table-striped tbody > tr:nth-child(odd) > td{
/* 	background-color: #cdff56; */
}
.table td{
/* 	background-color: #e7ffac; */
}
</style>
<div id="winnerList">
<!-- 	<h3>Winner-List till now</h3> -->
<div class="blackH2">WINNER<green>LIST</green></div>
<!-- <h3 style="margin-bottom:0"> -->
<!-- 	<img src="img/races/winnerlist.jpg"> -->
<!-- </h3> -->
{if $winnerList|count > 0 && $winnerList|is_array}
	<table class="table table-striped">
		<thead style="background-color: #E5E5E5">
			<tr align="center">
				<th>#</th>
				<th>Player</th>
				<th class="t" title="earned Points">EP</th>
				<th class="t" title="Coins-Reward">CR</th>
			</tr>
		</thead>

		<tbody>
			{foreach key=k item=v from=$winnerList name=data_array}
			{if $smarty.session.user.steamID == $v.SteamID}
				{assign "highlight" "info"}
			{else}
				{assign "highlight" ""}
			{/if}
				<tr class="{$highlight}">
					<td><strong>{$smarty.foreach.data_array.iteration}.</strong></td>
					<td><img src="{$v.Avatar}">&nbsp;{include
				"prototypes/creditBasedName.tpl" name=$v.Name
				creditValue=$v.Credits playerSteamID=$v.SteamID}</td>
					<td>{$v.EarnedPoints}</td>
					<td>{$v.CoinPrize}</td>
				</tr>
			{/foreach}
			
		</tbody>
	</table>
	{if $positionOfUser === false}
		<p class="alert alert-info" align="center">
			You have to play a match to participate
		</p>
	{else}
		{if $positionOfUser > $winnerList|count}
		<p class="alert alert-info" align="center">
			Your current placement is: <strong>{$positionOfUser}. </strong>
		</p>
		{/if}
	{/if}
{else}
<div class="alert">No active players yet.</div>
{/if}
</div>

