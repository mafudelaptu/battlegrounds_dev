{if $data|count > 0 && $data|is_array}
<table id="{$TableID}"
	class="table table-striped LadderTable" cellpadding="0" cellspacing="0"
	border="0">
	<thead>
		<tr align="center">
			<th>SteamID</th>
			<th>Player</th>
			<th>Banned At</th>
			<th>Banned until</th>
			<th>Banned by</th>
			<th>Reason</th>
			<th>Active</th>
		</tr>
	</thead>
	<tbody>
		{foreach key=k item=v from=$data name=data_array}

		<tr>
			<td>{$v.SteamID}</td>
			<td><a href="http://{$smarty.server.SERVER_NAME}/profile.php?ID={$v.SteamID}" target="_blank"><img src="{$v.Avatar}" alt="{$v.UserName}'s Avatar"> {$v.UserName}</a></td>
			<td><span class="timeago"title="{$v.BannedAt|date_format:'%Y-%m-%d %H:%M:%S'}">{$v.BannedAt|date_format:'%Y-%m-%d %H:%M:%S'}</span></td>
			<td>{$v.BannedTill|date_format:'%d.%m.%y - %H:%M:%S'}</td>
			<td>{$v.ReasonName}
			{if $v.BannedBySteamID > 0}
			 - <a href="http://{$smarty.server.SERVER_NAME}/profile.php?ID={$v.BannedBySteamID}" target="_blank"><img src="{$v.BannedByAvatar}" alt="{$v.BannedBy}'s Avatar"> {$v.BannedBy}</a></td>
			{/if}
			<td>{$v.BanReasonText}</td>
			<td>{$v.Display}</td>
		</tr>

		{/foreach}
	</tbody>
</table>
<br> <!-- Scrollbalken workaround -->
{else} no Data, sorry {/if}
