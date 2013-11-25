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
	<h3 class="eloAwardElo {$eloClass} t" title="<b>General Elo-Rating</b><br>Your Ranking is: {$eloAltDesc}" data-html="true" align="center">{$elo}</h3>
	<div class="eloAwardMatchMode t" title="{$matchMode}">{$matchModeShortcut}</div>
	<div class="eloAwardWins">Wins: <span class="text-success">{$wins}</span></div>
	<div class="eloAwardLoses">Losses: <span class="text-error">{$loses}</span></div>
		<img src="{$heroUnitImgSrc}" class="" alt="Ranking: {$eloAltDesc}" style="margin:0 0px"/>	
</div>