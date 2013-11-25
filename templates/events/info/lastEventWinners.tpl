{if $lastEventWinner|is_array && $lastEventWinner|count > 0}
<!-- <h3>Last Event Winners</h3> -->
<h3><img src="img/events/lasteventwinners.jpg"></h3>
<table class="table table-striped">
		<thead>
			<tr align="center">
				<th>Name</th>
				<th>Points</th>
			</tr>
		</thead>
		
		<tbody>
	<!-- TBODY zusammenbauen -->
		  {foreach key=k item=v from=$lastEventWinner name=lastEventWinners_array}
		  	<tr>
				<td><img src="{$v.Avatar}">&nbsp;{include
				"prototypes/creditBasedName.tpl" name=$v.Name
				creditValue=$v.Credits playerSteamID=$v.SteamID}</td>
				<td>
				{$v.Rank}
				</td>
			</tr>
		  {/foreach}
		</tbody>
</table>

{/if}