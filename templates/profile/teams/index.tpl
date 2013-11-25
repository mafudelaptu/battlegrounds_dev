<div class="row-fluid">
	<div class="span6">{include "profile/teams/invites.tpl"}</div>
	<div class="span6">{include "profile/teams/openTeams.tpl" data=$openTeams}</div>
</div>


<div class="blackH2">Duo-<green>Teams</green></div>
<div class="row-fluid">
	<div class="span6">{include "profile/teams/createDuoTeam.tpl"}</div>
	<div class="span6">{include "profile/teams/duoTeamList.tpl" data=$duoTeamList}</div>
</div>
<br>
<div class="blackH2">5vs5-<green>Teams</green></div>
<div class="row-fluid">
	<div class="span6">{include "profile/teams/create5vs5Team.tpl"}</div>
	<div class="span6">{include "profile/teams/5vs5TeamList.tpl" data=$duoTeamList}</div>
</div>