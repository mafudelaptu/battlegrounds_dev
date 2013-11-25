{if $teamID1 == "1" || $teamID1 == "2"}
	{assign "matchesDataNr" "0"}
{elseif $teamID1 == "3" || $teamID1 == "4"}
{assign "matchesDataNr" "1"}
{elseif $teamID1 == "5" || $teamID1 == "6"}
{assign "matchesDataNr" "0"}
{/if}

{if $teamOfPlayer == $teamID1 || $teamOfPlayer == $teamID2}
		{if $eventMatchesData.1.0.TeamWonID != "0"}
			{if $eventMatchesData.1.0.TeamWonID == "-1"}
				Match was canceled by Players 
			{elseif $eventMatchesData.1.0.TeamWonID == -2}
				Match have to be proofed by Admins (strange Submits by Players)
			{else}
				Team {$eventMatchesData.$round.$matchesDataNr.TeamWonID}
			{/if}		
		{else}
			<a href="eventMatch.php?matchID={$playerStatus.round1.matchesData.MatchID}" class="btn btn-success">Go to Match ({$playerStatus.round1.matchesData.MatchID})</a>
		{/if}
{else}
		{if $eventMatchesData.$round.$matchesDataNr.TeamWonID == "0"}
			<div class="btn btn-warning disabled">Players in Match</div>
		{elseif $eventMatchesData.$round.$matchesDataNr.TeamWonID == "-1"}
			Match was canceled by Players 
		{elseif $eventMatchesData.$round.$matchesDataNr.TeamWonID == "-2"}
			Match have to be proofed by Admins (strange Submits by Players)
		{else}
			Team {$eventMatchesData.$round.$matchesDataNr.TeamWonID}
		{/if}
{/if}