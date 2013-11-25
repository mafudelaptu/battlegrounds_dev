<div class="row-fluid" align="center" style="padding-bottom:7px;">
{if $data|is_array && $data|count > 0}
	
	{foreach key=k item=v from=$data name=data_array}
		{if $smarty.foreach.data_array.iteration==1}
			{if $data.count==1}
				{assign "offset" "offset5"}
			{elseif $data.count==2}
				{assign "offset" "offset4"}
			{elseif $data.count==3}
				{assign "offset" "offset3"}
			{elseif $data.count==4}
				{assign "offset" "offset2"}
			{elseif $data.count==5}
				{assign "offset" "offset1"}
			{elseif $data.count==6}
				{assign "offset" ""}
			{/if}
		{else}
			{assign "offset" ""}
		{/if}
		<div class="span2 {$offset}">
			<h4 class="t" title="{$v.MatchModeName}"><strong>{$v.MMShortcut}</strong></h4>
			<div>{$v.Value}%</div>
		</div>
	{/foreach}
{else}
	<div class="alert">no matches played yet!</div>
{/if}
</div>
