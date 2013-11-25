
{if $count > 0} 
		{if $data|count > 0 && $data|is_array}
			<li>
	 			 {foreach key=k item=v from=$data name=modi_array}
	 			 	<a tabindex="-1" href="{$v.href}">
	 			 		<span class="label label-info">{$v.count}</span>&nbsp;{$v.message}
	 			 	</a>
	 			 {/foreach}
	 		</li>
	 	{/if}
	{else}
		<li><a tabindex="-1">No notifications</a></li>
{/if} 



