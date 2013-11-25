{if $smarty.const.REFERAFRIEND == true}

<div class="blackH2">REFER<green>A</green>FRIEND</div>
<p>
	<strong>Invite your friends to the league!</strong>
</p>
<p>When player that has been invited by you plays his first <strong>{$RAFCountBorder} games</strong> on the website, you will receive <strong>{$RAFCoinBonus} N-Gage-Coins</strong>!</p>
<p>Points can be used in our shop on the new website that will be launched soon!</p>
	<p>
		Friends refered: <strong>{$referedCount}</strong>(<span class="t" title="Number of Players, who logged in through your Referrer-Link">{$inviteCount}</span>) <small class="muted">(update every full hour)</small>
	</p>
	<h3>Referral-Link for your friends:</h3>
	 <div class="well well-mini">{$refererLink}</div>
{/if}
