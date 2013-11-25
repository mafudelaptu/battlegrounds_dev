<style type="text/css">
.team {
	background-color: #eee;
	font-size: 22px;
	padding: 10px;
	width: 200px;
	text-align: center;
}
</style>

	<script type="text/javascript">
	{literal}
	$(document).ready(function() {
		var teamID = {/literal}{$playerStatus.round1.playerTeam.EventTeamID}{literal};
			id = "#team"+teamID+"Box";
				l(id);
			$(id).popover('show');
		{/literal}
		{if $playerStatus.round2.playerTeam.EventTeamID != ""}
		{literal}
			var teamID = {/literal}{$playerStatus.round2.playerTeam.EventTeamID}{literal};
				id = "#team"+teamID+"Box";
					l(id);
				$(id).popover('show');
		{/literal}
			{/if}
		{literal}
			});
		{/literal}
		
	</script>

<h2>Tourney Bracket</h2>
<div
	style="background-image: url(img/event/bg_turnierbaum.gif); background-repeat: no-repeat; height: 800px;">
	{include "event/winnerTable.tpl"}
	{include "event/prizeDiv.tpl"}
	<div class="row">
		<div class="span6">
			<div id="team1Box" class="team pointer" data-content="" title="Members of Team 1">Team 1</div>
		</div>

		<div class="span6">
			<div id="team2Box" class="team pull-right pointer" data-toggle="popover" title="" data-content="" data-original-title="Members of Team 2">Team 2</div>
		</div>
	</div> 
	<div align="center" class="team" style="margin:auto; margin-top:37px;">
		{include "event/teamDisplay.tpl" teamID1=1 teamID2=2 round=1}
	</div>
	<div id="team5Box" class="team pointer" data-content="???" title="Members of Team 5" style="margin:auto; margin-top:10px;">
	{if $playerStatus.round2.playerTeam.EventTeamID == 5}
		{if $eventMatchesData.2.0.TeamWonID != "0"}
			{if $eventMatchesData.2.0.TeamWonID == "-1"}
				Match was canceled by Players 
			{elseif $eventMatchesData.2.0.TeamWonID == -2}
				Match have to be proofed by Admins (strange Submits by Players)
			{else}
				Team 5
			{/if}		
		{else}
			<a href="eventMatch.php?matchID={$playerStatus.round2.matchesData.MatchID}" class="btn btn-success">Go to Match ({$playerStatus.round2.matchesData.MatchID})</a>
		{/if}
	{else}
		{if $eventMatchesData.2.0.TeamWonID != "0"}
			{if $eventMatchesData.2.0.TeamWonID == "-1"}
				Match was canceled by Players 
			{elseif $eventMatchesData.2.0.TeamWonID == -2}
				Match have to be proofed by Admins (strange Submits by Players)
			{else}
				Team 5
			{/if}		
		{else}
			Team 5
		{/if}
		
	{/if}
	</div>
	<div
		style="background-image: url(img/event/pokal.png); background-repeat: no-repeat;
		 width: 337px; height: 395px; margin: auto; margin-top: 10px;">
			<div style="margin-left: 114px; margin-top: 321px; height:50px; width:104px; position:absolute; line-height: 50px;" align="center">
	{if $createdEventData.TeamWonID == 0}
		???
	{else}
		<strong>Team {$createdEventData.TeamWonID}</strong>
	{/if}
</div>
	</div>
	
	<div id="team6Box" class="team pointer" data-content="???" title="Members of Team 6" style="margin:auto; margin-top:10px;">
	{if $playerStatus.round2.playerTeam.EventTeamID == 6}
		{if $eventMatchesData.2.0.TeamWonID != "0"}
			{if $eventMatchesData.2.0.TeamWonID == "-1"}
				Match was canceled by Players 
			{elseif $eventMatchesData.2.0.TeamWonID == -2}
				Match have to be proofed by Admins (strange Submits by Players)
			{else}
				Team 6
			{/if}		
		{else}
			<a href="eventMatch.php?matchID={$playerStatus.round2.matchesData.MatchID}" class="btn btn-success">Go to Match ({$playerStatus.round2.matchesData.MatchID})</a>
		{/if}
	{else}
		{if $eventMatchesData.2.0.TeamWonID != "0"}
			{if $eventMatchesData.2.0.TeamWonID == "-1"}
				Match was canceled by Players 
			{elseif $eventMatchesData.2.0.TeamWonID == -2}
				Match have to be proofed by Admins (strange Submits by Players)
			{else}
				Team 6
			{/if}		
		{else}
			Team 6
		{/if}
		
	{/if}
	</div>
	
	<div align="center" class="team" style="margin:auto; margin-top:10px;">
	{include "event/teamDisplay.tpl" teamID1=3 teamID2=4 round=1}
</div>
<div class="row" style="margin-top:42px;">
		<div class="span6">
			<div id="team3Box"class="team pointer" data-toggle="popover" title="" data-content="" data-original-title="Members of Team 3">Team 3</div>
		</div>
		<div class="span6">
			<div id="team4Box" class="team pull-right pointer" data-toggle="popover" title="" data-content="" data-original-title="Members of Team 4">Team 4</div>
		</div>
	</div>
</div>

<div id="team1DataHTML" class="hide">
{include 'event/playerListOfTeam.tpl' data=$team1Data}
</div>
<div id="team2DataHTML" class="hide">
{include 'event/playerListOfTeam.tpl' data=$team2Data}
</div>
<div id="team3DataHTML" class="hide">
{include 'event/playerListOfTeam.tpl' data=$team3Data}
</div>
<div id="team4DataHTML" class="hide">
{include 'event/playerListOfTeam.tpl' data=$team4Data}
</div>
<div id="team5DataHTML" class="hide">
{include 'event/playerListOfTeam.tpl' data=$team5Data}
</div>
<div id="team6DataHTML" class="hide">
{include 'event/playerListOfTeam.tpl' data=$team6Data}
</div>