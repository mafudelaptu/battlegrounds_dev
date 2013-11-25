<style type="text/css">
.s10{
	width:10%;
}
</style>
{if $data|count > 0 && $data|is_array}
<div class="row-fluid">
	<div class="span6">
		{foreach key=k item=v from=$data name=waitingForOtherPlayers_array}
			{if $v.TeamID == 1}
				{if $v.Ready == 1}
					{assign "rdy" "badge-success"}
					{assign "style" "background-color: #dff0d8;"}
				{else}
					{assign "rdy" ""}
					{assign "style" ""}
				{/if}
				
				<div style="{$style}">
					<img src="{$v.Avatar}">&nbsp;{$v.Name}<div class="badge {$rdy} pull-right" style="margin-top: 7px;">{$smarty.foreach.waitingForOtherPlayers_array.iteration}</div>
				</div>
			{/if}
			
	    {/foreach}
	</div>
	<div class="span6">
	{foreach key=k item=v from=$data name=waitingForOtherPlayers_array}
			{if $v.TeamID == 2}
				{if $v.Ready == 1}
					{assign "rdy" "badge-success"}
					{assign "style" "background-color: #dff0d8;"}
				{else}
					{assign "rdy" ""}
					{assign "style" ""}
				{/if}
				
				<div style="{$style}">
					<img src="{$v.Avatar}">&nbsp;{$v.Name}<div class="badge {$rdy} pull-right" style="margin-top: 7px;">{$smarty.foreach.waitingForOtherPlayers_array.iteration}</div>
				</div>
			{/if}
			
	    {/foreach}
	</div>
</div>
{/if}