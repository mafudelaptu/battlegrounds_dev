<div align="center" class="backpackItem">
	<div class="t" title="{$data.Descr}">
		<div>
		<img alt="{$data.Name} Icon" src="{$data.Image}" class="img-polaroid">
		</div>
		<div> {$data.Name}</div>
	</div>
	
	{if $smarty.const.ITEMSACTIVATABLE == 1}
		{if $data.Activatable == 1}
			<button class="btn btn-success btn-mini">use Item</button>
		{/if}
	{else}
		{if $data.Activatable == 1}
			<button class="btn btn-success btn-mini disabled t" title="coming soon">use Item</button>
		{/if}
	{/if}
	
</div>