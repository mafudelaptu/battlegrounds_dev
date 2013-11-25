	{assign "color" "whiteSmoke"}
{if $ranking == 1}
	{assign "borderColor" "gold"}
	{assign "color" "#333"}
{elseif $ranking == 2}
	{assign "borderColor" "silver"}

{elseif $ranking == 3}
	{assign "borderColor" "peru"}
{else}
	{assign "borderColor" "#333"}
{/if}

<div style="border: 2px solid {$borderColor}; border-radius: 5px; background-image:url({$avatarSrc}); background-position:center top;">
<!-- 	<img src="{$avatarSrc}" alt="Avatar"> -->

	{if $isPermaBanned==true}
		<div style="position: absolute; margin-top: 30px;margin-left: 30px; color: #b94a48;" class="t" title="Player is permanently banned from system.">
			<i class="icon-ban-circle icon-5x"></i>
		</div>
	{/if}

	<div
		style="margin-top: 140px; line-height:20px; background-color: {$borderColor}; font-size: 12px; text-align: center; color: {$color};">
		{if $ranking > 0}
		Ranking:&nbsp;<strong>{$ranking}.</strong>
		{else}
			not ranked yet
		{/if}
		</div>

</div>