<div class="row-fluid" align="center" style="padding-top:7px;">
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
			<div class="t" title="{$v.HeroName}"><img src="{$v.Src}" alt="{$v.HeroName}" /></div>
			<div><strong>{$v.Value}%</strong></div>
		</div>
	{/foreach}
{else}
	<div class="alert">no matches played or replayes uploaded yet!</div>
{/if}
</div>
