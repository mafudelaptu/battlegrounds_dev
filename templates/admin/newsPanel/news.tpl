<div class="page-header">
	<h2>News</h2>
</div>
{if $news|is_array && $news|count > 0}
	<table class="table table-striped">
		<thead>
			<tr>
			<th class="span2">Title</th>
			<th class="span2">Created At</th>
			<th class="span1">Order</th>
			<th class="span2">Show At</th>
			<th class="span2">End At</th>
			<th class="span1">Active</th>
			<th class="span2"></th>
			</tr>
		</thead>
	<tbody>
	{foreach key=k item=v from=$news name=data_array}
		{include "admin/newsPanel/newsEntry.tpl" data=$v}
	{/foreach}
	</tbody>
	</table>
{else}
	no news created yet
{/if}
