{if $data|is_array && $data|count > 0}
<table class="table table-striped" id="warnHistoryTable">
<thead>
<tr>
	<th>Warned At</th>
	<th>In Prison Until</th>
	<th>Warn Expires At</th>
	<th>Warned By</th>
	<th>Reason</th>
	<th>Active</th>
</tr>
</thead>
<tbody>
	{foreach key=k item=v from=$data name=data_array}
	{if $v.Display==1}
			{assign "active" "Active"}
			{assign "activeClass" "success"}
		{else}
			{assign "active" ""}
			{assign "activeClass" "muted"}
		{/if}
	<tr class="{$activeClass}">
		
		<td>{$v.BannedAt|date_format:'%d.%m.%y - %H:%M'}</td>
		<td>{$v.BannedTill|date_format:'%d.%m.%y - %H:%M'}</td>
		<td>{$v.Expires|date_format:'%d.%m.%y - %H:%M'}</td>
		<td>{$v.ReasonName}
			{if $v.BannedBySteamID > 0}
			 - <a href="http://{$smarty.server.SERVER_NAME}/profile.php?ID={$v.BannedBySteamID}" target="_blank"><img src="{$v.BannedByAvatar}" alt="{$v.BannedByName}'s Avatar"> {$v.BannedByName}</a></td>
			{/if}</td>
		<td>{$v.BanReasonText}</td>
		<td>{$active}</td>
	</tr>
	{/foreach}
</tbody>
</table>
{else}
	no warns earned till now.
{/if}