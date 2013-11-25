<div class="blackH2">
	<div class="pull-right">{include "prototypes/creditValue.tpl"
		creditValue=$credits}</div>
	{$username|truncate:20:"...":false}
</div>
<div>
	<div class="pull-left" style="width:25%;">
		<img alt="Avatar of {$username}" src="{$avatar}">
	</div>
	<div class="pull-left" style="width:75%;">
		{include "profile/userInfo/userItems.tpl" data=$userItems}
		
		{include "profile/userInfo/neededStatsForLvlUp.tpl" data=$requirementsForNextBracketData skillBracketTypeID=$skillBracketTypeID}
	</div>
</div>
<div>
	<div class="pull-left">
		<div>
		Acc created: {$createdTimestamp|date_format:'%Y-%m-%d %H:%M'}
		</div>
		<div>
		Last activity: {$lastTimestamp|date_format:'%Y-%m-%d %H:%M'}
		</div>
	</div>
	<div class="pull-right" align="right">
		<div>
			<a href="http://dotabuff.com/players/{$steamID}" target="_blank"><i
					class=" icon-tasks"></i>&nbsp;DotaBuff-Profile</a>
		</div>
		<div>
			<a href="{$profileURL}" target="_blank"><i
					class="icon-user"></i>&nbsp;Steam-Profile</a>
		</div>
	</div>
	<div class="clearer"></div>
</div>
