<div id="SingleQueueCarousel" class="carousel slide span5 eloCarousel"> 
    <!-- Carousel items -->
    <div class="carousel-inner">
      {if $modi|count > 0 && $modi|is_array}
          {foreach key=k item=v from=$modi name=carousel_array}
          		{if $v@iteration == 1}
              	{assign var="active_item" value="active"}
              {else}
              	{assign var="active_item" value=""}
              {/if}
              <div class="item {$active_item}">
												<div style="background-image:url(img/wreaths_bigger.png);">
							                 		<div class="title" align="center">{$v.Name}</div>
							                    <h2 align="center" style="margin-top:49px">{$eloArray.1[$v.MatchModeID]}</h2>	
						            </div>
							</div>
          {/foreach}
      {/if}
    </div>
    <!-- Carousel nav --> 
    <a class="carousel-control left" href="#SingleQueueCarousel" data-slide="prev">&lsaquo;</a> 
    <a class="carousel-control right" href="#SingleQueueCarousel" data-slide="next">&rsaquo;</a> 
</div>
