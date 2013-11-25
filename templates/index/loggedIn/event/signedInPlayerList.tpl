{if $data|count > 0 && $data|is_array}


<table id="{$TableID}"
	class="table table-striped" cellpadding="0" cellspacing="0"
	border="0">
	<thead>
		<tr align="center">
			<th>#</th>
			<th>Player</th>
			<th>Points</th>
		</tr>
	</thead>
	<tbody>
	
	{assign "vielfaches" $eventSubsCount/$nextEvent.MinSubmissions}
	{if $vielfaches|floor == 0}
		{assign "vielfaches" 1}
	{else}
		{assign "vielfaches" $vielfaches|floor}
	{/if}
	
	{assign "grenzeRot" $vielfaches*$nextEvent.MinSubmissions}
	
	{foreach key=k item=v from=$data name=data_array}
	
		{if $smarty.session.user.steamID == $v.SteamID}
				{assign "trClass" "info"}
		{elseif $v.rowNumber > $grenzeRot}
				{assign "trClass" "error"}
		{else}
				{assign "trClass" "success"}
		{/if}
	
		<tr class="{$trClass}">
			<td><strong>{$v.rowNumber}.</strong></td>
			<td><a href="profile.php?ID={$v.SteamID}" target="_blank"><img src="{$v.Avatar}" alt="{$v.Name}'s Avatar"> {$v.Name}</a></td>
			<td>{$v.Elo}</td>
		</tr>
    {/foreach}
    </tbody>
</table>
{else}
	no Data
{/if}