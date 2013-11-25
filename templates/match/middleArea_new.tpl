<div class="row-fluid">
	<div class="span4">
		<div><strong>Mode</strong></div>
		<div><div class="badge badge-info t" title="{$matchData.MatchMode} ">{$matchData.Shortcut}</div></div>
	</div>
	<div class="span4">
		<div><strong>Region</strong></div>
		<div><div class="badge badge-important t" title="{$matchData.Region}">{$matchData.RegionShortcut}</div></div>
	</div>
	<div class="span4">
		<div><strong>MatchID</strong></div>
		<div><div class="badge t" title="MatchID">{$matchID}</div></div>
	</div>
</div>
<br>
<input type="hidden" value="{$eventMatch}" id="eventMatchIndicator" data-EID="{$eventID}" data-CEID="{$createdEventID}">
<p>
	<span class="label t" title="average Elo of Team Radiant">{$data.matchdetails.ave_rank_team1}</span>
	<span style="font-size: 22px;"><strong>VS</strong></span> <span
		class="label t" title="average Elo of Team Dire">{$data.matchdetails.ave_rank_team2}</span>
</p>
<p id="middleAreaButtonArea">
{if $matchStatus.playerOfMatch}

	{if $matchStatus.MatchClosed}
		{if $replaySubmitted.status}
					{assign var="htmlReplay" value="replay-files already submitted!"}
					{assign var="htmlReplayDisabled" value="disabled"}
				{else}
					{assign var="htmlReplay" value="Upload the parsed replay-files and earn Credit-Points!"}
					{assign var="htmlReplayDisabled" value=""}
		{/if}
		
		{if !$matchStatus.playerSubmitted} {* Submit fehlt! *}
		
			{assign var="disabled" value="btn-primary"}
			{assign var="html" value="Submit result!"}
			{assign var="onclick" value="onclick='checkForStrangeSubmissions()'"}
			
			<button class="btn btn-large {$disabled}" {$onclick} type="button"
				id="matchSubmitresultButton">{$html}</button>
			
			
			<a href="#myModalCancelMatch" id="cancelMatchButton"
				class="btn {$hideCancelButton}" role="button" data-toggle="modal"
				style="margin-top: 10px;">Cancel Match</a>
		{else}{* schon Submitted *}
			{if $matchAlreadyCanceled}
				<div class="alert-info">Match was canceled!</div>
			{else}
				{if $teamWonID == 1} 
					{assign var="disabled" value="disabled btn-success"} 
					{assign var="html" value="The Radiant Team won"} 
			 	{else}
					{assign var="disabled" value="disabled btn-danger"} 
					{assign var="html" value="The Dire Team won"} 
				{/if} 
			
				{assign var="hideCancelButton" value="hide"} 
				{assign var="onclick" value=""} 
				
				<button class="btn btn-large {$disabled}" {$onclick} type="button"
					id="matchSubmitresultButton">{$html}</button>
				
				<a href="#myModalCancelMatch" id="cancelMatchButton"
					class="btn {$hideCancelButton}" role="button" data-toggle="modal"
					style="margin-top: 10px;">Cancel Match</a>
			{/if}
			
		{/if}
	
	{else}{* Match Offen *}
		{if !$matchStatus.playerSubmitted} {* Submit fehlt! *}
			{if $replaySubmitted.status}
					{assign var="htmlReplay" value="replay-files already submitted!"}
					{assign var="htmlReplayDisabled" value="disabled"}
				{else}
					{assign var="htmlReplay" value="Upload the parsed replay-files and earn Credit-Points!"}
					{assign var="htmlReplayDisabled" value=""}
			{/if}
			{assign var="disabled" value="btn-primary"}
			{assign var="html" value="Submit result!"}
			{assign var="onclick" value="onclick='checkForStrangeSubmissions()'"}
			
			<button class="btn btn-large {$disabled}" {$onclick} type="button"
				id="matchSubmitresultButton">{$html}</button>
				
			
			<a href="#myModalCancelMatch" id="cancelMatchButton"
				class="btn {$hideCancelButton}" role="button" data-toggle="modal"
				style="margin-top: 10px;">Cancel Match</a>
		{else}{* schon Submitted *}
			{if $canceled == true}
				<div class="alert-info">you voted for canceling the Match!</div>
			{elseif $data.matchdetails.manuallyCheck == 1}
				<div class="alert-info">strange submissions were made by some players. Contact an admin to solve this problem!</div>
			{else}
				{if $replaySubmitted.status}
					{assign var="htmlReplay" value="replay-files already submitted!"}
					{assign var="htmlReplayDisabled" value="disabled"}
				{else}
					{assign var="htmlReplay" value="Upload the parsed replay-files and earn Credit-Points!"}
					{assign var="htmlReplayDisabled" value=""}
				{/if}
					{assign var="disabled" value="disabled"} 
					{assign var="html" value="Result submitted"} 
					
					{assign var="onclick" value=""}
					{assign var="hideCancelButton" value="hide"} 
				
				<button class="btn btn-large {$disabled}" {$onclick} type="button"
					id="matchSubmitresultButton">{$html}</button>
					
				
				<a href="#myModalCancelMatch" id="cancelMatchButton"
					class="btn {$hideCancelButton}" role="button" data-toggle="modal"
					style="margin-top: 10px;">Cancel Match</a>
			{/if}
			
		{/if}
	{/if}
	
{else}{* BESUCHER *}

	{if $matchAlreadyCanceled}
		<div class="alert-info">Match was canceled!</div>
	{else}
		{if $teamWonID == 1} 
			{assign var="disabled" value="disabled btn-success"} 
			{assign var="html" value="The Radiant Team won"} 
	 	{elseif $teamWonID == 2}
			{assign var="disabled" value="disabled btn-danger"} 
			{assign var="html" value="The Dire Team won"} 
		{else}
			{assign var="disabled" value="disabled"} 
			{assign var="html" value="Players in Match"} 
		{/if} 
	
		{assign var="hideCancelButton" value="hide"} 
		{assign var="onclick" value=""} 
		
		<button class="btn btn-large {$disabled}" {$onclick} type="button"
			id="matchSubmitresultButton">{$html}</button>
		
		<a href="#myModalCancelMatch" id="cancelMatchButton"
			class="btn {$hideCancelButton}" role="button" data-toggle="modal"
			style="margin-top: 10px;">Cancel Match</a>
	{/if}

{/if}

</p>

