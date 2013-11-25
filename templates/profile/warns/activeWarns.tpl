{if $data|is_array && $data|count > 0}
<div class="blackH2">ACTIVE<green>WARNS</green></div>
<table class="table table-striped">
<thead>
<tr>
	<th>Warned At</th>
	<th>In Prison Until</th>
	<th>Warned By</th>
	<th>Reason</th>
</tr>
</thead>
<tbody>
	{foreach key=k item=v from=$data name=data_array}
	<tr>
		<td>{$v.BannedAt|date_format:'%d.%m.%y - %H:%M:%S'}</td>
		<td>{$v.BannedTill|date_format:'%d.%m.%y - %H:%M:%S'}</td>
		<td>{$v.ReasonName}
			{if $v.BannedBySteamID > 0}
			 - <a href="http://{$smarty.server.SERVER_NAME}/profile.php?ID={$v.BannedBySteamID}" target="_blank"><img src="{$v.BannedByAvatar}" alt="{$v.BannedByName}'s Avatar"> {$v.BannedByName}</a></td>
			{/if}</td>
		<td>{$v.BanReasonText}</td>
	</tr>
	{/foreach}
</tbody>
</table>
{/if}