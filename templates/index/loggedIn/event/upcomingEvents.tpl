<style type="text/css">
#upcomingEvent #eventClock, #upcomingEvent div.well{
	color:#000;
}

</style>
<div style="background-color: #292929; color:#fff; padding:0 5px 5px 5px;" id="upcomingEvent">
{if $data|count > 0 && $data|is_array}
	{if $userAlreadyInEvent  && $createdEventID != "-1" && $createdEventID != "-2"}
		{if $isEventActiv}
			{include "index/loggedIn/event/goToEventTeaser.tpl" data=$data}
		{else}
			{include "index/loggedIn/event/signedInTeaser.tpl" data=$eventSubsData}
		{/if}
		
	{else}
		{include "index/loggedIn/event/eventTeaser.tpl" data=$data}
	{/if}
{else}
	<div id="upcomingEvents" align="center" class="">
		<h2>No Upcoming Events</h2>
		<div class="" style="height: 230px">
		</div>
	</div>
{/if}
</div>