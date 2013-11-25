<style type="text/css">

.backpackItem{
	width:120px; 
	padding:12px;
}

</style>
<div class="blackH2">Your<green>Backpack</green></div>
{if $data|is_array && $data|count > 0}
	{foreach key=k item=v from=$data name=data_array}
		<div class="pull-left">
			{include "profile/backpack/backpackItem.tpl" data=$v}
		</div>
	{/foreach}
{/if}