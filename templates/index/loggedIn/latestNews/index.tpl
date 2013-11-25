<div class="blackH2">
	Latest<green>News</green>
</div>
{if $newsData|is_array && $newsData|count > 0}
	{foreach key=k item=v from=$newsData name=data_array}
		{include "index/loggedIn/latestNews/newsEntry.tpl" data=$v}
		
	{/foreach}
{else}
	no active News!
{/if}