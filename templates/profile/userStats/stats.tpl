<div class="row-fluid" align="center">
	<div class="span2 offset1">
		<div><strong>WINS</strong></div>
		<div><span class="text-success">{$data.Wins}</span></div>
	</div>
	<div class="span2">
		<div><strong>LOSSES</strong></div>
		<div><span class="text-error">{$data.Losses}</span></div>
	</div>
	<div class="span2">
		<div><strong>WINRATE</strong></div>
		<div><span class="text-success">{$data.WinRate}</span></div>
	</div>
	<div class="span2">
		<div><strong>LEAVES</strong></div>
		<div><span class="text-success">{$data.Leaves}</span></div>
	</div>
	<div class="span2">
		<div><strong>WARNS</strong></div>
		<div>{$activeWarnsCount}&nbsp;<span class="t muted" title="total warns">({$warnsCount})</span></div>
	</div>
</div>
<div class="row-fluid">
	<div class="span6">
		{include "prototypes/skillBracketImage2.tpl" skillBracket=$skillBracketTypeID}
	</div>
	<div class="span6 fontMich" align="center">
		<h4>Points</h4>
		<div style="font-size:50px; line-height: 50px;"><strong>{$points}</strong></div>
		<h4 style="margin-top:20px;">Ranking</h4>
		{if $ranking == 0}
{assign "ranking" "-"}
{else}
	
{/if}
		<div style="font-size:40px;"><strong>{$ranking}.</strong></div>
	</div>
</div>
