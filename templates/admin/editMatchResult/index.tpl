<div class="page-header">
	<h2>Match: {$generalMatchData.MatchID}</h2>
</div>
{include "admin/editMatchResult/generalMatchData.tpl" data=$generalMatchData leaverVotes=$leaverVotes cancelLeaverVotes=$cancelLeaverVotes screenshots=$screenshots}
{include "admin/editMatchResult/matchResult.tpl" data=$data generalMatchData=$generalMatchData}

{include "admin/editMatchResult/modals/editUserPointsModal.tpl"}