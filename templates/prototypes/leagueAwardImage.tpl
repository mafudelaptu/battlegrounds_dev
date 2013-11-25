{if $smarty.const.NOLEAGUES==true}
	{assign "leagueLvl" ""}
{/if}

{if $league == 1}
{assign var="imgSrc" value="img/league/qualifying`$leagueLvl`.png"}
{assign var="eloClass" value="qualifying"}
{assign var="eloAltDesc" value="Qualifying-League"}

{elseif $league == 2}
{assign var="imgSrc" value="img/league/bronze`$leagueLvl`.png"}
{assign var="eloClass" value="bronze"}
{assign var="eloAltDesc" value="Bronze-League"}

{elseif $league == 3}
{assign var="imgSrc" value="img/league/silver`$leagueLvl`.png"}
{assign var="eloClass" value="silver"}
{assign var="eloAltDesc" value="Silver-League"}

{elseif $league == 4}
{assign var="imgSrc" value="img/league/gold`$leagueLvl`.png"}
{assign var="eloClass" value="gold"}
{assign var="eloAltDesc" value="Gold-League"}

{elseif $league == 5}
{assign var="imgSrc" value="img/league/platinum`$leagueLvl`.png"}
{assign var="eloClass" value="platinum"}
{assign var="eloAltDesc" value="Platinum-League"}

{elseif $league == 6}
{assign var="imgSrc" value="img/league/diamond`$leagueLvl`.png"}
{assign var="eloClass" value="diamond"}
{assign var="eloAltDesc" value="Diamond-League"}
{/if}



<div align="center" style="width:131px">
	<h3 class="heroUnitElo {$eloClass} t" title="Your Single-5vs5 D2L-Points. You are in: {$eloAltDesc}" align="center">{$points}</h3>
	<img src="{$imgSrc}" class="heroUnitImg" alt="Ranking: {$eloAltDesc}" />	
</div>