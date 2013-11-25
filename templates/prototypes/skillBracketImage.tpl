{if $skillBracket == 1}
{assign var="imgSrc" value="img/league/qualifying.png"}
{assign var="eloClass" value="qualifying"}
{assign var="eloAltDesc" value="Prison-Bracket"}

{elseif $skillBracket == 2}
{assign var="imgSrc" value="img/league/bronze.png"}
{assign var="eloClass" value="bronze"}
{assign var="eloAltDesc" value="Trainee-Bracket"}

{elseif $skillBracket == 3}
{assign var="imgSrc" value="img/league/silver.png"}
{assign var="eloClass" value="silver"}
{assign var="eloAltDesc" value="Amateur-Bracket"}

{elseif $skillBracket == 4}
{assign var="imgSrc" value="img/league/gold.png"}
{assign var="eloClass" value="gold"}
{assign var="eloAltDesc" value="Skilled-Bracket"}

{elseif $skillBracket == 5}
{assign var="imgSrc" value="img/league/platinum.png"}
{assign var="eloClass" value="platinum"}
{assign var="eloAltDesc" value="Expert-Bracket"}

{elseif $skillBracket == 6}
{assign var="imgSrc" value="img/league/diamond.png"}
{assign var="eloClass" value="diamond"}
{assign var="eloAltDesc" value="Master-Bracket"}
{/if}

<div align="center" style="width:131px">
	<h3 class="heroUnitElo {$eloClass} t" title="Your Single-5vs5 D2L-Points.<br>You are in: {$eloAltDesc}" align="center">{$points}</h3>
	<img src="{$imgSrc}" class="heroUnitImg" alt="Ranking: {$eloAltDesc}" />	
</div>