{if $skillBracket == 1}
{assign var="imgSrc" value="img/league/qualifying.png"}
{assign var="eloClass" value="qualifying"}
{assign var="eloAltDesc" value="Prison"}

{elseif $skillBracket == 2}
{assign var="imgSrc" value="img/league/bronze.png"}
{assign var="eloClass" value="bronze"}
{assign var="eloAltDesc" value="Trainee"}

{elseif $skillBracket == 3}
{assign var="imgSrc" value="img/league/silver.png"}
{assign var="eloClass" value="silver"}
{assign var="eloAltDesc" value="Amateur"}

{elseif $skillBracket == 4}
{assign var="imgSrc" value="img/league/gold.png"}
{assign var="eloClass" value="gold"}
{assign var="eloAltDesc" value="Skilled"}

{elseif $skillBracket == 5}
{assign var="imgSrc" value="img/league/platinum.png"}
{assign var="eloClass" value="platinum"}
{assign var="eloAltDesc" value="Expert"}

{elseif $skillBracket == 6}
{assign var="imgSrc" value="img/league/diamond.png"}
{assign var="eloClass" value="diamond"}
{assign var="eloAltDesc" value="Master"}
{/if}
<div align="center">
	<div>
<img src="{$imgSrc}" class="heroUnitImg" alt="Ranking: {$eloAltDesc}" />
</div>
<div>
<strong>{$eloAltDesc}</strong>
</div>
</div>