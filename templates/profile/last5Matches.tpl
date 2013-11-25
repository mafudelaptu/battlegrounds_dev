<!-- <h3>Last 5 Matches</h3> -->
<!-- <img src="img/profile/last5matches.jpg"> -->
<div class="blackH2">Last<green>5</green>Matches</div>
{if $last5Matches|count > 0 && $last5Matches|is_array}
	<table class="table table-striped">
		<thead>
			<tr align="center">
				<th></th>
				<th>Result</th>
				<th>MatchID</th>
				<th>Mode</th>
<!-- 				<th>Team</th> -->
			</tr>
		</thead>
		
		<tbody>
	<!-- TBODY zusammenbauen -->
		  {foreach key=k item=v from=$last5Matches name=last5Matches_array}
		  	
		  	{if $v.TeamWonID == $v.TeamID}
		  		{assign var="statusClass" value='success'}
		  		{assign var="result" value='won'}
		  		{if !$v.Leaver}
		  			{assign "vorzeichen" "+"}
		  		{/if}
		  		
		  	{else}
		  		{if $v.Canceled == 1}
		  			{assign var="statusClass" value='important'}
		  			{assign var="result" value='<i class="icon-remove t" title="Match canceled"></i>'}
		  		{else}
		  			{assign var="statusClass" value='important'}
		  			{assign var="result" value='lost'}
		  		{/if}
		  		{assign "vorzeichen" ""}
		  	{/if}
		  	
		  	{if $v.PointsChange == "" && $v.TeamWonID == $v.TeamID}
		  		{assign var="v.PointsChange" value='+0'}
		  	{elseif $v.PointsChange == "" && $v.TeamWonID != $v.TeamID}
		  		{assign var="v.PointsChange" value='-0'}
		  	{/if}
			
		  	<tr>
				<td style="text-align:center">{include file="prototypes/matchTypeIcon.tpl" MatchTypeID="{$v.MatchTypeID}"}</td>
				<td><span class="label label-{$statusClass}">{$result} {$vorzeichen}{$v.PointsChange}</span>{include "prototypes/leaverIcon.tpl" width="16" leaver=$v.Leaver}</td>
				<td><a href="match.php?matchID={$v.MatchID}">{$v.MatchID}</a></td>
				<td><span class="t" title="{$v.MatchMode}">{$v.MatchModeShortcut}</span></td>
			</tr>
		  {/foreach}
		</tbody>
	</table>
	<p>
	<a href="lastMatches.php?ID={$steamID}">View All Matches <i class="icon-double-angle-right"></i></a>
	</p>
{else}
	<div class="alert fade in">No Matches found!</div>
	
{/if}