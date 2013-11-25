<style type="text/css">

.prizeDiv{
	position:absolute;
	margin-top: 200px;
	margin-left: 780px;
	width:200px;
	border: gold 2px solid;
	border-radius: 5px;
	padding: 0 10px 10px 10px;
}
</style>

<div class="prizeDiv">
	<div class="page-header">
	  <h2>Prize</h2>
	</div>
	<div><img src="img/prizes/prizeTypeID_{$eventData.PrizeTypeID}.png" class="t" title="{$eventData.PrizeCount}x {$eventData.PrizeName} {if $eventData.PrizeCost > 0}
						(max Cost:  {$eventData.PrizeCost}&euro;
						{/if}">
	</div>
	<div align="center" style="
    margin-top: 5px;
">{$eventData.PrizeCount}x <strong>{$eventData.PrizeName}</strong> 
						{if $eventData.PrizeCost > 0}
						(max Cost:  {$eventData.PrizeCost}&euro;)
						{/if}
						</div>
</div>
