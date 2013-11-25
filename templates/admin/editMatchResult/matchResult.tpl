<form name="mRForm" id="mRForm" method="post" action="{$smarty.server.REQUEST_URI}">
<h3>Matchresult</h3>
<div>
	{if $generalMatchData.TeamWonID == -1 && $generalMatchData.Canceled == 0}
		{assign "radioChecked1" "checked='checked'"}
		{assign "radioChecked2" ""}
		{assign "radioChecked3" ""}
		{assign "radioChecked4" ""}
	{/if}
	{if $generalMatchData.TeamWonID == 1}
		{assign "radioChecked2" "checked='checked'"}
		{assign "radioChecked3" ""}
		{assign "radioChecked1" ""}
		{assign "radioChecked4" ""}
	{/if}
	{if $generalMatchData.TeamWonID == 2}
		{assign "radioChecked3" "checked='checked'"}
		{assign "radioChecked2" ""}
		{assign "radioChecked1" ""}
		{assign "radioChecked4" ""}
	{/if}
	{if $generalMatchData.TeamWonID == -1 && $generalMatchData.Canceled == 1}
		{assign "radioChecked4" "checked='checked'"}
		{assign "radioChecked2" ""}
		{assign "radioChecked1" ""}
		{assign "radioChecked3" ""}
	{/if}
	<label class="radio inline">
	  <input type="radio" name="mRMatchResultRadio" id="mRMatchResultRadio1" value="-1" {$radioChecked1}>
		 no Match-Result
	</label>
	<label class="radio inline text-success">
	  <input type="radio" name="mRMatchResultRadio" id="mRMatchResultRadio2" value="1" {$radioChecked2}>
		 the Radiant won
	</label>
	<label class="radio inline text-error">
	  <input type="radio" name="mRMatchResultRadio" id="mRMatchResultRadio3" value="2" {$radioChecked3}>
		 the Dire won
	</label>
	 | 
	<label class="radio inline text-warning">
	  <input type="radio" name="mRMatchResultRadio" id="mRMatchResultRadio4" value="cancelMatch" {$radioChecked4}>
		 Cancel Match
	</label>
</div>
<br>
<input class="btn btn-primary" type="submit" value="submit Changes" id="mRsubmitChanges" name="submitChanges">
<br><br>
{* Radiant includen *}
{include "admin/editMatchResult/team.tpl" data=$data.team1  teamID=1}

{* Dire includen *}
{include "admin/editMatchResult/team.tpl" data=$data.team2 teamID=2}

</form>