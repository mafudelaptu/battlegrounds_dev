<tr>
	<td><strong>{$data.TypeName}</strong></td>
	<td>{$data.Text}</td>
	<td><a href="{$data.Href}" target="_blank">{$data.Href}</a></td>
	<td><img src="{$data.Avatar}" alt="" />{include "prototypes/creditBasedName.tpl" playerSteamID=$data.CreatedBy name=$data.UserName}</td>
	<td>{if $data.Active==1} <span class="text-success">Active</span>
		{else} <span class="text-error">not Active</span> {/if}
	</td>
	<td>
		<button type="button" class="btn t" onclick="nPEditNotification({$data.GlobalNotificationID})" title="edit Notification">
			<i class="icon-pencil"></i>
		</button>
		<button type="button" class="btn btn-warning t" onclick="nPToggleActiveNotification({$data.GlobalNotificationID}, {$data.Active})" title="activate/deactivate Notification">
			<i class="icon-off"></i>
		</button>
		<button type="button" class="btn btn-danger t" onclick="nPDeleteNotification({$data.GlobalNotificationID})" title="delete Notification">
			<i class="icon-remove-sign"></i>
		</button>
	</td>
</tr>
