{if $userLoggedIn == "true"}
	{if $smarty.const.ONLYONEREGION==false}
		<ul class="nav pull-right">
			<li id="region-menu" class="dropdown">
		
		           <a href="#" id="drop3" role="button" class="dropdown-toggle" data-toggle="dropdown">
		           		
		           		
		           		{$region.Shortcut}
		           		
		           		
		           	<b class="caret"></b>
		           </a>
		           <ul class="dropdown-menu" role="menu" aria-labelledby="drop3">
		             {if $regions|is_array && $regions|count > 0}
						{foreach from=$regions item=v key=k}
							 <li><a tabindex="-1" href="?region={$v.RegionID}">{$v.Shortcut}</a></li>
						{/foreach}		             
{/if}
		           </ul>
			</li>
		</ul>
	{/if}

{/if}