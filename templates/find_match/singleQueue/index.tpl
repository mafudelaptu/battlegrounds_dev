<div class="row-fluid">
  <div class="span7">
    <div class="media">
      <a class="pull-left" href="javascript:void(0);"> <img src="img/stock_people.png" class="float_left"/></a>
      <div class="media-body">
        <h4 class="media-heading">Queue Details</h4>
        <p><span class="label label-info">{$singleQueueCount}</span> Players in Queue</p>
        <p><span class="label label-info">{$singleQueueInMatchCount}</span> Players in Match</p>
        <p>
        	Best Queue-Time: <span class="label">19:00</span>-<span class="label">22:30</span> CET
        </p>
        {if $openMatches|count > 0 && $openMatches|is_array}
			<p>
				<a href="openMatches.php?guest=true&MTID=1">Players just playing a Match ({$openMatches|count})</a>
			</p>
		{/if}
        
        {include "find_match/singleQueue/selectMatchmode.tpl"}
        {include "find_match/singleQueue/selectRegion.tpl"}
        <br />
      </div>
    </div>
    
  </div>
   {include "prototypes/eloCarousel.tpl" carouselID=SingleQueueCarousel spanClass=span5 eloArray=$eloArray modi=$modi matchTypeID=1}
<div class="row-fluid">
	<div class="span6">
		{if $openSubmitsLock}
			<button type="button" class="btn btn-large btn-block btn-success pull-right disabled t" title="You dont submitted a result of a previous Match before. Please check your open Matches!"><i class="icon-user"></i>&nbsp;Single-Queue-Join</button>
		{else}
			<button type="button" class="btn btn-large btn-block btn-success pull-right" onclick="joinSingleQueue3(false)"><i class="icon-user"></i>&nbsp;Single-Queue-Join</button>
		{/if}
	</div>
	<div class="span6">
		{if $openSubmitsLock}
			<button type="button" class="btn btn-large btn-block btn-success pull-right disabled t" title="You dont submitted a result of a previous Match before. Please check your open Matches!"><i class="icon-user"></i><i class="icon-user"></i>&nbsp;Duo-Queue-Join [Beta]</button>
	
		{else}
			<button type="button" class="btn btn-large btn-block btn-success pull-right" onclick="joinDuoSingleQueue2(false)"><i class="icon-user"></i><i class="icon-user"></i>&nbsp;Duo-Queue-Join [Beta]</button>
			
		{/if}
	</div>
</div>
	
</div>

<hr />

 <div class="media">
      <a class="pull-left" href="javascript:void(0);"> <img src="img/quickJoin.png" class="float_left"/> </a>
      <div class="media-body">
        <h4 class="media-heading">Queue takes too long?</h4>
        <p>Try Quick-Join!</p>
        	<li>Join automaticly into AP, SD, RD and AR Queue</li>
        	<li>Region is set to "Automatic"</li>
        <br />
      </div>
    </div>
	{if $openSubmitsLock}
<button type="button" class="btn btn-large btn-block btn-success pull-right disabled t" title="You dont submitted a result of a previous Match before. Please check your open Matches!">JOIN</button>
{else}
<button type="button" class="btn btn-large btn-block btn-success pull-right" onclick="joinSingleQueue3(true)">QUICK-JOIN</button>
{/if}

<!-- For Rejoin -->
<input name="openSubmitsLock" type="hidden" value="{$openSubmitsLock}" id="hiddenInputOpenSubmitsLock">
<br>
<br>
<hr />

{include "find_match/singleQueue/queueStats.tpl"}