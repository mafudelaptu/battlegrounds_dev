<div class="page-header">
	<h2>Notifications</h2>
</div>
{if $notifications|is_array && $notifications|count > 0}
<table class="table table-striped" id="nPTable">
	<thead>
		<tr>
			<th>Type</th>
			<th>Text</th>
			<th>Link</th>
			<th nowrap="nowrap">Created By</th>
			<th>Active</th>
			<th></th>
		</tr>
	</thead>
	<tbody>{foreach key=k item=v from=$notifications name=data_array}
		{include "admin/notificationPanel/notificationEntry.tpl" data=$v}
		{/foreach}
	</tbody>
</table>
{else} no news created yet {/if}
