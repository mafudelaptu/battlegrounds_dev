{if $general.Value > 0}
<div class="bSBox pull-left">
<div class="bSTitle" align="center">{$general.Label}</div>
<div style="background-image: url(img/match/replay/{$id}.png); background-repeat: no-repeat;" class="bSIcon">
	<div class="bSValue" align="center">
		{$general.Value}
	</div>
</div>
<div class="bSPlayers">PLAYERS</div>
<div class="bSPlayersBox">
	{if $players|is_array && $players|count > 0}
		{foreach key=k item=v from=$players name=data_array}
			<div align="center"><img alt="{$v.Name}" src="{$v.Avatar}">&nbsp;{$v.Name}</div>
		{/foreach}
	{/if}
</div>
</div>

{else}

{/if}
