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
<!-- <h5>MatchID: {$matchID}</h5> -->

<!-- <h4>{$matchData.MatchMode} ({$matchData.Shortcut})</h4> -->

<p>
	<span class="label t" title="average Elo of Team Radiant">{$data.matchdetails.ave_rank_team1}</span>
	<span style="font-size: 22px;"><strong>VS</strong></span> <span
		class="label t" title="average Elo of Team Dire">{$data.matchdetails.ave_rank_team2}</span>
</p>
<br>
<!-- <div class="btn-group"> -->
<!-- 	<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> -->
<!-- 		Vote <span class="caret"></span> -->
<!-- 	</a> -->
<!-- 	<ul class="dropdown-menu"> -->
<!-- 		<li><a href="#" target="_blank">Rematch (unbalanced)</a></li> -->
<!-- 		<li><a href="#" target="_blank">Cancel Match</a></li> -->
<!-- 	</ul> -->
<!-- </div> -->

{if $smarty.cookies.matchFoundResultSubmitted == $matchID && !$matchSubmitted && $playerSubmitted} 
	{assign var="disabled" value="disabled"} 
	{assign var="html" value="Result submitted"} 
	{assign var="onclick" value=""}
	{assign var="hideCancelButton" value="hide"} 
{elseif $matchSubmitted && $playerSubmitted}
	{if $teamWonID == 1} 
		{assign var="disabled" value="disabled btn-success"} 
		{assign var="html" value="The Radiant Team won"} 
 	{else}
		{assign var="disabled" value="disabled btn-danger"} 
		{assign var="html" value="The Dire Team won"} 
	{/if} 

	{assign var="hideCancelButton" value="hide"} 
	{assign var="onclick" value=""} 
{elseif $matchSubmitted && !$playerSubmitted} 
	{assign var="disabled" value="btn-primary"} 
	{assign var="html" value="Submit result!"} 
	{assign var="onclick" value="onclick='checkForStrangeSubmissions()'"} 
{elseif $matchSubmitted && $besucher == true} 
	{if $teamWonID == 1} 
			{assign var="disabled" value="disabled btn-success"} 
			{assign var="html" value="The Radiant Team won"} 
	 	{else}
			{assign var="disabled" value="disabled btn-danger"} 
			{assign var="html" value="The Dire Team won"} 
		{/if} 
	
		{assign var="hideCancelButton" value="hide"} 
		{assign var="onclick" value=""} 
{else}
	{assign var="disabled" value="btn-primary"} 
	{assign var="html" value="Submit result!"} 
	{assign var="onclick" value="onclick='checkForStrangeSubmissions()'"} 
{/if}

<p id="middleAreaButtonArea">{if $canceled == true}
<div class="alert-info">you voted for canceling the Match!</div>
{elseif $matchAlreadyCanceled && $playerSubmitted}
<div class="alert-info">Match was canceled!</div>
{else}

<button class="btn btn-large {$disabled}" {$onclick} type="button"
	id="matchSubmitresultButton">{$html}</button>

<a href="#myModalCancelMatch" id="cancelMatchButton"
	class="btn {$hideCancelButton}" role="button" data-toggle="modal"
	style="margin-top: 10px;">Cancel Match</a>

{/if}

</p>