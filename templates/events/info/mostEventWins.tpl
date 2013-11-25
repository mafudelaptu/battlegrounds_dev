{if $mostEventWins|is_array && $mostEventWins|count > 0}


<!-- <h3>Most Event Wins</h3> -->
<h3><img src="img/events/mosteventwins.jpg"></h3>
<table class="table table-striped">
		<thead>
			<tr align="center">
				<th>Name</th>
				<th>Wins</th>
			</tr>
		</thead>
		
		<tbody>
	<!-- TBODY zusammenbauen -->
		  {foreach key=k item=v from=$mostEventWins name=mostEventWins_array}
		  	<tr>
				<td><img src="{$v.Avatar}">&nbsp;{include
				"prototypes/creditBasedName.tpl" name=$v.Name
				creditValue=$v.Credits playerSteamID=$v.SteamID}</td>
				<td>{$v.Wins}</td>
			</tr>
		  {/foreach}
		</tbody>
</table>
{/if}