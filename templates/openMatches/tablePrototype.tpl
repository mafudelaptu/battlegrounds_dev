{if $data|count > 0 && $data|is_array}
<table id="{$TableID}"
	class="table table-striped openMatchesTable" cellpadding="0" cellspacing="0"
	border="0">
	<thead>
		<tr align="center">
			<th></th>
			<th></th>
			<th>Date</th>
			<th>MatchID</th>
			<th>Mode</th>
			<th>Submissions</th>
		</tr>
	</thead>
	<tbody>
		{foreach key=k item=v from=$data name=data_array}
		
		{if $v.Submitted == "1"}
			{assign var="submittedClass" value=""}
			{assign var="submittedIcon" value=""}
			{assign var="submittedTitle" value=""}
		{else}
			{assign var="submittedClass" value="text-error t"}
			{assign var="submittedIcon" value="<i class='icon-exclamation-sign'></i>&nbsp;"}
			{assign var="submittedTitle" value="you have to submit a Match-Result!"}
		{/if}
		
		{if $v.SubmittedCount == ""}
			{assign "submittedCount" "0"}
		{else}
			{assign "submittedCount" $v.SubmittedCount}
		{/if}
		{assign "matchHref" "match.php?matchID={$v.MatchID}"}
		<tr>
			<td><span class="{$submittedClass}" title="{$submittedTitle}">{$submittedIcon}</span></td>
			<td>{include file="prototypes/matchTypeIcon.tpl" MatchTypeID=$v.MatchTypeID}</td>
			<td><span class="timeago" title="{$v.TimestampCreated|date_format:'%Y-%m-%d %H:%M:%S'}" datasort="{$v.TimestampCreated}">{$v.TimestampCreated|date_format:'%Y-%m-%d %H:%M:%S'}</span></td>
			<td><a href="match.php?matchID={$v.MatchID}">{$v.MatchID}</a></td>
			<td><span class="t" title="{$v.MatchMode}">{$v.MatchModeShortcut}</span></td>
			<td><span class="label">{$submittedCount}</span></td>
		</tr>

		{/foreach}
	</tbody>
</table>
<br> <!-- Scrollbalken workaround -->
{else} no Data{/if}
