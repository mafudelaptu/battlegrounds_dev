<ul class="nav nav-tabs" id="userStatsTabs">
	<li class="active"><a href="#userStatsSingle5vs5" data-toggle="tab">Single
			5vs5</a>
	</li>
	<li class=""><a href="#userStats1vs1" data-toggle="tab">1vs1</a>
	</li>
	<li class="disabled"><a href="#userStatsTeam5vs5" data-toggle="">Team
			5vs5</a>
	</li>
</ul>
<div class="tab-content" style="height:240px">
	<div class="tab-pane active" id="userStatsSingle5vs5">
		{include "profile/userStats/stats.tpl" data=$userStats.GameStats activeWarnsCount=$activeWarnsCount warnsCount=$warnsCount points=$userStats.Points ranking=$ranking}
	</div>
	<div class="tab-pane" id="userStats1vs1">
		{include "profile/userStats/stats.tpl" data=$userStats.GameStats1vs1 activeWarnsCount=$activeWarnsCount warnsCount=$warnsCount points=$userStats.Points1vs1 ranking=$ranking1vs1}
	</div>
	<div class="tab-pane" id="userStatsTeam5vs5">team 5vs5</div>
</div>

<div>
<div class="blackH2">MATCHMODES<green>PLAYED</green></div>
{include "profile/userStats/matchmodes.tpl" data=$matchModesStatsData}
</div>

<div>
<div class="blackH2">HEROES<green>PLAYED</green> <i class="icon-question-sign t" title="data is comming from all uploaded replays on this site"></i></div>
{include "profile/userStats/heroes.tpl" data=$heroesStatsData}
</div>