<div class="hero-unit" style="margin-top:20px">
	<div class="row-fluid">
		<div class="span2">
			{include file="profile/heroUnit.avatarTemplate.tpl" avatarSrc=$userData.AvatarFull ranking=$ranking isPermaBanned=$isPermaBanned}
			<p style="text-align: center; font-size: 14px;">
				<a href="http://dotabuff.com/players/{$steamID}" target="_blank"><i
					class=" icon-tasks"></i>&nbsp;DotaBuff-Profile</a>
					<a
					href="{$userData.ProfileURL}" target="_blank"><i
					class="icon-user"></i>&nbsp;Steam-Profile</a>
			</p>
		</div>
		<div class="span8">
			<h1 class=""><span class="t" title="{$userData.Name}">{$userData.Name|truncate:14:"...":true}</span>{include "prototypes/creditValue.tpl" creditValue=$userCredits}</h1>
			
			<div class="clearfix"></div>
			<ul class="nav nav-tabs">
			  <li class="active" onclick="profileChangeSkillBracketIcon('1vs1');"><a href="#heroUnit1vs1Stats" data-toggle="tab">1vs1</a></li>
			</ul>
			<div class="tab-content">	 
			  <div class="tab-pane active" id="heroUnit1vs1Stats">
			 	 {include "profile/stats.tpl" data=$userStats.GameStats1vs1 activeWarnsCount=$activeWarnsCount warnsCount=$warnsCount}
			  	{if $smarty.const.NOLEAGUES == false}
					{include "profile/neededStatsForLvlUp.tpl" data=$requirementsForNextBracketData1vs1 skillBracketTypeID=$skillBracketTypeID1vs1}
				{/if}
			  </div>
			 
			</div>
		</div>
		<div class="span2" align="center">
		
			<div id="1vs1SkillBracketIcon">
				{include "prototypes/skillBracketImage.tpl"
				points=$userStats.Points1vs1 skillBracket=$skillBracketTypeID1vs1}
				
				{if $smarty.const.NOLEAGUES == false}
					<div class="t" title="Your Skill-Bracket is: {$leagueName1vs1}" style="/*margin-top:50px;*/">{$leagueName1vs1}</div>
				{/if}
			</div>
			
		</div>
	</div>
</div>