<tr>
	<td><strong>{$data.Title}</strong></td>
	<td>{$data.CreateTimestamp|date_format:'%Y-%m-%d %H:%M:%S'}</td>
	<td>{$data.Order}</td>
	<td>{$data.ShowTimestamp|date_format:'%Y-%m-%d %H:%M:%S'}</td>
	<td>{$data.EndTimestamp|date_format:'%Y-%m-%d %H:%M:%S'}</td>
	<td>{if $data.Active==1} <span class="text-success">Active</span>
		{else} <span class="text-error">not Active</span> {/if}
	</td>
	<td>
		<button type="button" class="btn t" onclick="nPEditNews({$data.NewsID})" title="edit News">
			<i class="icon-pencil"></i>
		</button>
		<button type="button" class="btn btn-warning t" onclick="nPToggleActiveNews({$data.NewsID}, {$data.Active})" title="activate/deactivate News">
			<i class="icon-off"></i>
		</button>
		<button type="button" class="btn btn-danger t" onclick="nPDeleteNews({$data.NewsID})" title="delete News">
			<i class="icon-remove-sign"></i>
		</button>
	</td>
</tr>
