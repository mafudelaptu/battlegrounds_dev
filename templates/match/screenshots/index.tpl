<div class="page-header">
	<h2>
		<i class="icon-picture"></i>&nbsp;Uploaded Screenshots
	</h2>
</div>

{if $data|is_array && $data|count > 0}
	{foreach key=k item=v from=$data name=data_array}
		<a href="{$v}" target="_blank"><img src="{$v}" width="100"></a>
	{/foreach}
{/if}