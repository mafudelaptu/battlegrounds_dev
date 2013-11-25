<div class="blackH2">PRIZE<green>POOL</green></div>
<!-- <h3>Prize-Pool</h3> -->
<!-- <h3><img src="img/races/prizepool.jpg"></h3> -->
<!-- <iframe src="http://steamcommunity.com/profiles/76561198047012055/inventory/#570" style="width:100%; height:600px;" onload="totop()"></iframe> -->
<div style="overflow:auto; height:600px;">
{if $racePrizesData|count > 0 && $racePrizesData|is_array}
	{foreach key=k item=v from=$racePrizesData name=data_array}
		{assign "rarity" $k}
		{if $rarity == 6}
			{assign "rarityName" "N-Gage-Items"}
		
		{elseif $rarity == 5}
			{assign "rarityName" "Legendary"}
		{elseif $rarity == 4}
			{assign "rarityName" "Mythical"}
		{elseif $rarity == 3}
			{assign "rarityName" "Rare"}
		{elseif $rarity == 2}
			{assign "rarityName" "Uncommon"}
		{elseif $rarity == 1}
			{assign "rarityName" "Common"}
		{/if}


		<div class="page-header">
			<strong>{$rarityName} <small>({$v|count})</small></strong>
		</div>
		{if $v|count > 0 && $v|is_array}
			{foreach key=kItem item=item from=$v name=dataItem_array}
				{if $item.BackpackItemID > 0}
					{$item.Name = $item.BackpackName}
					{$item.Img = $item.BackpackImage}
					{$item.ItemType = "N-Gage-Item"}
				{/if}
				<div class="pull-left" style="margin:0px 15px 10px 0px; width:120px;" align="center">
					{if $choose == "true"}
						<input type="radio" name="prizePick" value="{$item.RacePrizeID}" onclick="$('button.saveSelection').show()">
					{/if}
					<img src="{$item.Img}" class="img-polaroid">
					<div class="muted"><small>{$item.ItemType}</small></div>
					<div><small>{$item.Name}</small></div>
				</div>
				{if $smarty.foreach.dataItem_array.iteration % 6 == 0}
					<div class="clearer"></div>
				{/if}
		    {/foreach}
		{/if}
		<div class="clearer"></div>
		
		
    {/foreach}
{else}
	<div class="alert">no prizes available anymore</div>
{/if}
</div>
