{if $elo > $smarty.const.DIAMONDELO  }
{assign var="heroUnitImgSrc" value="img/diamond-award.png"}
{assign var="eloClass" value="diamong"}
{assign var="eloAltDesc" value="Diamond"}

{elseif $elo >= $smarty.const.GOLDELO && $elo < $smarty.const.DIAMONDELO  }
{assign var="heroUnitImgSrc" value="img/gold-award.png"}
{assign var="eloClass" value="gold"}
{assign var="eloAltDesc" value="Gold"}

{elseif $elo >= $smarty.const.SILVERELO && $elo < $smarty.const.GOLDELO}
{assign var="heroUnitImgSrc" value="img/silver-award.png"}
{assign var="eloClass" value="silver"}
{assign var="eloAltDesc" value="Silver"}
{else}
{assign var="heroUnitImgSrc" value="img/bronze-award.png"}
{assign var="eloClass" value="bronze"}
{assign var="eloAltDesc" value="Bronze"}
{/if}


<div align="center">
	<h3 class="eloAwardElo {$eloClass} t" title="General Elo-Rating. Your Ranking is: {$eloAltDesc}" align="center" style="width:{$width}">{$elo}</h3>
	<div class="eloAwardMatchMode t" title="{$matchMode}" style="width:{$width}">{$matchModeShortcut}</div>
	<div class="eloAwardWins" style="width:{$width}">Wins: <span class="text-success">{$wins}</span></div>
	<div class="eloAwardLoses" style="width:{$width}">Losses: <span class="text-error">{$loses}</span></div>
		<img src="{$heroUnitImgSrc}" class="" alt="Ranking: {$eloAltDesc}" style="margin:0 0px"/>	
</div>