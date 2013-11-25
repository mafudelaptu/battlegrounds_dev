<!-- <div class="page-header"> -->
<!-- 	<h1>Events</h1> -->
<!-- </div> -->
<div class="page-header">
<img src="img/events/n-gageevents.jpg">
</div>
<div class="row-fluid">
	

	<div class="span8">
		{* Upcoming Event *}
		{include "events/upcomingEvent.tpl"}
		
		{* Last Events *}
		{include "events/lastEvents.tpl"}
	</div>
	<div class="span4">
		
		{* Calendar *}
		{include "events/info/calendar.tpl"}
		
		
		{* Stats *}
		{include "events/info/stats.tpl"}
		
		{* Last Event Winners *}
		{include "events/info/lastEventWinners.tpl"}
		
		{* Most Event Wins *}
		{include "events/info/mostEventWins.tpl"}
	</div>
</div>

{include "events/modals/readyForEvent.tpl"}