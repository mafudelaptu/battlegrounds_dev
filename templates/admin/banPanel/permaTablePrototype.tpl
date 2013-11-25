{if $data|count > 0 && $data|is_array}
<table id="{$TableID}"
	class="table table-striped LadderTable" cellpadding="0" cellspacing="0"
	border="0">
	<thead>
		<tr align="center">
			<th>SteamID</th>
			<th>Player</th>
			<th>Banned At</th>
			<th>Reason</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		{foreach key=k item=v from=$data name=data_array}

		<tr>
			<td>{$v.SteamID}</td>
			<td><a href="http://{$smarty.server.SERVER_NAME}/profile.php?ID={$v.SteamID}" target="_blank"><img src="{$v.Avatar}" alt="{$v.UserName}'s Avatar"> {$v.UserName}</a></td>
			<td>{$v.BannedAt|date_format:'%d.%m.%y - %H:%M:%S'}</td>
			<td>{$v.BanlistReason}</td>
			<td>
				<button class="btn btn-danger" onclick="deletePermaBan('{$v.SteamID}')">delete permaban</button>
			</td>
		</tr>

		{/foreach}
	</tbody>
</table>
<br> <!-- Scrollbalken workaround -->
{else} no Data, sorry {/if}
