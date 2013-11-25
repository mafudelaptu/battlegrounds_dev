{if $modi|is_array && $modi|count > 0}
<div id="{$carouselID}" class="carousel slide {$spanClass} eloCarousel"> 
    <!-- Carousel items -->
    <div class="carousel-inner">
      {if $modi|count > 0 && $modi|is_array}
          {foreach key=k item=v from=$modi name=carousel_array}
          	{if $matchTypeID == 8}
          		{if $v.MatchModeID == 5}
              	{assign var="active_item" value="active"}
              {else}
              	{assign var="active_item" value=""}
              {/if}
          	 {else}
          	 {if $v@iteration == 1}
              	{assign var="active_item" value="active"}
              {else}
              	{assign var="active_item" value=""}
              {/if}
          	{/if}
          		
              <div class="item {$active_item}">
												{include file="prototypes/eloAward.tpl" 
												elo=$eloArray.$matchTypeID[$v.MatchModeID].Elo 
												matchModeShortcut=$v.Shortcut 
												matchMode=$v.Name 
												wins=$eloArray.$matchTypeID[$v.MatchModeID].Wins 
												loses=$eloArray.$matchTypeID[$v.MatchModeID].Loses
												winRate=$eloArray.$matchTypeID[$v.MatchModeID].WinRate}
							</div>
          {/foreach}
      {/if}
    </div>
    <!-- Carousel nav --> 
    <a class="carousel-control left" href="#{$carouselID}" data-slide="prev">&lsaquo;</a> 
    <a class="carousel-control right" href="#{$carouselID}" data-slide="next">&rsaquo;</a> 
</div>
{else}
	
{/if}
