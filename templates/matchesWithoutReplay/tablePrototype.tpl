{if $data|count > 0 && $data|is_array}
	<table  id="{$TableID}" class="table table-striped" cellpadding="0" cellspacing="0" border="0">
		<thead>
			<tr align="center">
				<th></th>
				<th>Date</th>
				<th>Result</th>
				<th>MatchID</th>
				<th>Mode</th>
			</tr>
		</thead>
		
		<tbody>
		  {foreach key=k item=v from=$data name=matchesWithoutReplay_array}
			{if $v.TeamID == 1}
				{assign var="team" value='The Radiant'}
			{else}
				{assign var="team" value='The Dire'}
			{/if}
		  	
		  	{if $v.TeamWonID == $v.TeamID}
		  		{assign var="statusClass" value='success'}
		  		{assign var="result" value='won'}
		  	{else}
		  		{if $v.Canceled == 1}
		  			{assign var="statusClass" value='important'}
		  			{assign var="result" value='<i class="icon-remove t" title="Match canceled"></i>'}
		  		{else}
		  			{assign var="statusClass" value='important'}
		  			{assign var="result" value='lost'}
		  		{/if}
		  	{/if}
		  	
		  	<tr>
				<td>{include file="prototypes/matchTypeIcon.tpl" MatchTypeID="{$v.MatchTypeID}"}</td>
				<td><span class="timeago" title="{$v.TimestampCreated|date_format:'%Y-%m-%d %H:%M:%S'}" datasort="{$v.TimestampCreated}">{$v.TimestampCreated|date_format:'%Y-%m-%d %H:%M:%S'}</span></td>
				<td><span class="label label-{$statusClass}">{$result} {$v.EloChange}</span>{include "prototypes/leaverIcon.tpl" width="16" leaver=$v.Leaver}</td>
				<td><a href="match.php?matchID={$v.MatchID}">{$v.MatchID}</a></td>
				<td>{$v.MatchMode}&nbsp;<span class="t" title="{$v.MatchMode}">({$v.MatchModeShortcut})</span></td>
			</tr>
		  {/foreach}
		</tbody>
	</table>
{else}
	<div class="alert">no Matches found</div>
{/if}
