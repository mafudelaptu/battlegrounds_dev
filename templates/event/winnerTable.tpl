<style type="text/css">

.winnerTable{
	position:absolute;
	margin-top: 200px;
	margin-left: 100px;
	width:270px;
	border: gold 2px solid;
	border-radius: 5px;
	padding: 0 10px 10px 10px;
}
</style>
{if  $createdEventData.TeamWonID != 0 &&  $createdEventData.EndTimestamp != 0}
<div class="winnerTable">
	<div class="page-header">
	  <h2>Winner <small>Team {$createdEventData.TeamWonID}</small></h2>
	</div>
	{if $createdEventData.TeamWonID == 1}
		{include 'event/playerListOfTeam.tpl' data=$team1Data win=true}
	{elseif  $createdEventData.TeamWonID == 2}
		{include 'event/playerListOfTeam.tpl' data=$team2Data win=true}
	{elseif  $createdEventData.TeamWonID == 3}
		{include 'event/playerListOfTeam.tpl' data=$team3Data win=true}
	{elseif  $createdEventData.TeamWonID == 4}
		{include 'event/playerListOfTeam.tpl' data=$team4Data win=true}
	{elseif $createdEventData.TeamWonID == 5}
		{include 'event/playerListOfTeam.tpl' data=$team5Data win=true}
	{elseif  $createdEventData.TeamWonID == 6}
		{include 'event/playerListOfTeam.tpl' data=$team6Data win=true}
	{/if}
	
</div>

{else}

{/if}
