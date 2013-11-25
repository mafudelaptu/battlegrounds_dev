{if $elo > $smarty.const.DIAMONDELO  }
{assign var="heroUnitImgSrc" value="img/diamond-award-heroUnit.png"}
{assign var="eloClass" value="diamond"}
{assign var="eloAltDesc" value="Diamond"}

{elseif $elo >= $smarty.const.GOLDELO && $elo < $smarty.const.DIAMONDELO  }
{assign var="heroUnitImgSrc" value="img/gold-award-heroUnit.png"}
{assign var="eloClass" value="gold"}
{assign var="eloAltDesc" value="Gold"}

{elseif $elo >= $smarty.const.SILVERELO && $elo < $smarty.const.GOLDELO}
{assign var="heroUnitImgSrc" value="img/silver-award-heroUnit.png"}
{assign var="eloClass" value="silver"}
{assign var="eloAltDesc" value="Silver"}
{else}
{assign var="heroUnitImgSrc" value="img/bronze-award-heroUnit.png"}
{assign var="eloClass" value="bronze"}
{assign var="eloAltDesc" value="Bronze"}
{/if}
<div align="center" style="width:131px">
	<h3 class="heroUnitElo {$eloClass} t" title="General Elo-Rating. Your Ranking is: {$eloAltDesc}" align="center">{$elo}</h3>
	<img src="{$heroUnitImgSrc}" class="heroUnitImg" alt="Ranking: {$eloAltDesc}" />	
</div>
