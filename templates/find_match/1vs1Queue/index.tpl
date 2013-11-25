<div class="row-fluid">
  <div class="span7">
    <div class="media">
      <a class="pull-left" href="#"> <img src="img/stock_people.png" class="float_left"> </a>
      <div class="media-body">
        <h4 class="media-heading">Queue Details</h4>
        <p><span class="label label-info">{$oneVsOneQueueCount}</span> Players in Queue</p>
        <p><span class="label label-info">{$oneVsOneQueueInMatchCount}</span> Players in Match</p>
        {if $openMatches1vs1|count > 0 && $openMatches1vs1|is_array}
			<p>
				<a href="openMatches.php?guest=true&MTID=8">Players just playing a Match ({$openMatches1vs1|count})</a>
			</p>
		{/if}
        {include "find_match/1vs1Queue/selectMatchmode.tpl"}
        {include "find_match/1vs1Queue/selectRegion.tpl"}
        <br />
      </div>
    </div>
  </div>
   {include "prototypes/eloCarousel.tpl" carouselID=1vs1QueueCarousel spanClass=span5 eloArray=$eloArray modi=$modi matchTypeID=8}
</div>
{if $openSubmitsLock}
<button type="button" class="btn btn-large btn-block btn-success pull-right disabled t" title="You dont submitted a result of a previous Match before. Please check your open Matches!">JOIN</button>
{else}
<button type="button" class="btn btn-large btn-block btn-success pull-right" onclick="join1vs1Queue()">JOIN</button>
{/if}

