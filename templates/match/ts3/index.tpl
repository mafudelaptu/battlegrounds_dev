{if !$matchSubmitted}
{if $smarty.const.TS3SERVER != ""}
<div class="page-header">
	<h2>
		<img src="img/ts3/teamspeak_logo.png" alt="Teamspeak 3 Logo"
			width="32" height="32">&nbsp;Teamspeak
	</h2>
</div>
<div class="row-fluid">
	<div class="span6" align="center"><strong>The Radiant</strong></div>
	<div class="span6" align="center"><strong>The Dire</strong></div>
</div>
<div class="row-fluid" align="center">
	<div class="span6">
		<a href="ts3server://{$smarty.const.TS3SERVER}:{$smarty.const.TS3PORT}?password={$smarty.const.TS3PW}&nickname={$userName}&channel=Event"><img src="img/ts3/ts3_weblink.png" alt="TS3 Weblink" width="100" class="t" title="Join Radiant TS3-Server-Channel"></a>
	</div>
	<div class="span6">
		<a href="ts3server://{$smarty.const.TS3SERVER}:{$smarty.const.TS3PORT}?password={$smarty.const.TS3PW}&nickname={$userName}"><img src="img/ts3/ts3_weblink.png" alt="TS3 Weblink" width="100" class="t" title="Join Dire TS3-Server-Channel"></a>
	</div>
</div>
<div class="muted"><small>* Note: you have to start Teamspeak3 before you click on a "Join TS3-Server-Channel"-Link</small></div>
{/if}
{/if}
<!-- ts3server://ts3.hoster.com?port=9987&nickname=UserNickname&password=serverPassword&channel=MyDefaultChannel&channelpassword=defaultChannelPassword&token=TokenKey&addbookmark=1 -->