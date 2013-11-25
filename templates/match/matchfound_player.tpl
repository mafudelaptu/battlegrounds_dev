{if $steamID == $v.SteamID}
	{assign "highlight" "background-color:#D9EDF7;"}
{else}
	{assign "highlight" ""}
{/if}

{* LEAVER ABFRAGE *}
{if $v.Leaver == 1}
	{assign "highlight" "background-color:#fcf8e3;"}
{elseif $steamID != $v.SteamID}
	{assign "highlight" ""}
{/if}

{if $data.matchdetails.host == $v.SteamID}
	{assign "hostBG" "background-color: #dff0d8;"}
	{assign "hostIcon" '<span class="t" title="Host - have to create the Lobby"><i class="icon-home"></i></span>'}
{else}
	{assign "hostBG" ""}
	{assign "hostIcon" ""}
{/if}

{if $v.GroupID > 0}
	{assign "groupIcon" '<i class="icon-group t" title="Group: '|cat:$v.GroupName|cat:' #'|cat:$v.GroupID|cat:'"></i>'}
{else}
	{assign "groupIcon" ''}
{/if}

<div class="row-fluid " style="line-height:26px;{$highlight}{$hostBG}">
	<div class="span1" >
		<img alt="Avatar" src="{$v.Avatar}" width="25" height="25">
	</div>
	<div class="span6">
		<span>{include "prototypes/creditBasedName.tpl" name=$v.Name creditValue=$v.Credits}</span>&nbsp;{$hostIcon}{$groupIcon}{include "prototypes/leaverIcon.tpl" leaver=$v.Leaver width=16}
		<button class="btn btn-mini pull-right t" title="Ping Player!" onclick="sendPingNotification(this)" data-value="{$v.SteamID}" style="padding:4px;margin-top:4px;"><i class="icon-exclamation"></i></button>
		<a style="padding:4px;margin-top:4px;" href="javascript:void(0)" class="btn btn-mini pull-right" data-toggle="popover" title="" data-content="Wins: <span class='text-success'>{$v.Wins}</span> Losses: <span class='text-error'>{$v.Losses}</span> Winrate: <span class='text-warning'>{$v.WinRate}%</span> Leaves: {$v.Leaves}" data-original-title="User-Statistics"><i class="icon-bar-chart"></i></a>
		<div class="btn-group pull-right" style="padding-top:4px;">
				  <a class="btn dropdown-toggle btn-mini" data-toggle="dropdown" href="#">
				    <i class="icon-eye-open"></i>
				    <span class="caret"></span>
				  </a>
				  <ul class="dropdown-menu">
				   <li><a href="profile.php?ID={$v.SteamID}" target="_blank"><i class="icon-user"></i> show Profile</a></li>
				    <li class="divider"></li>
				    <li><a href="https://dotabuff.com/players/{$v.SteamID}" target="_blank">show DotaBuff-Profile</a></li>
				    <li><a href="{$v.ProfileURL}ProfileURL" target="_blank">show Steam-Profile</a></li>
				  </ul>
		</div>
	</div>
	<div class="span2" style="white-space:nowrap; margin-top: 6px; margin-bottom: -6px;">
		{if $matchSubmitted}


			{if $v.PointsChange > 0}
				{assign "textClass" "success"}
				{assign "eloChange" "+`$v.PointsChange`"}
			{else}
				{assign "textClass" "error"}
				{assign "eloChange" $v.PointsChange}
			{/if}

			
			{if $v.Leaver == 1}
				{assign "textClass" "error"}
				{assign "eloChange" $v.PointsChange}
			{/if}
	
			
			
			
		<span class="label">{$v.Points}</span>&nbsp;<span class="text-{$textClass}">{$eloChange}</span>&nbsp;
		<a href="help.php#winLosePoints" target="_blank" class="t" title="FAQ: How is the WinPoints/LosePoints calculated?"><i class="icon-question-sign"></i></a>
		
		{elseif $matchAlreadyCanceled}
			{if $v.PointsChange == ""}
				{assign "eloChange" ""}
			{else}
				{assign "textClass" "error"}
				{assign "eloChange" $v.PointsChange}
			{/if}
			
			
			<span class="label">{$v.Elo}</span><span class="text-{$textClass}">{$eloChange}</span>&nbsp;
			<a href="help.php#winLosePoints" target="_blank" class="t" title="FAQ: How is the WinPoints/LosePoints calculated?"><i class="icon-question-sign"></i></a>
		
		 
		{else}
		<span class="label">{$v.Points}</span>
		{**
			<span class="label">{$v.Points}</span>(<span class="text-success">+{$v.WinPoints}</span><span class="text-warning t" title="Bonus-Ponts for Matchmode">+{$data.matchdetails.PointBonus}</span>/<span class="text-error">-{$v.LosePoints}</span>)&nbsp;
			<a href="help.php#winLosePoints" target="_blank" class="t" title="FAQ: How is the WinPoints/LosePoints calculated?"><i class="icon-question-sign"></i></a>
		*}	
		{/if}
		
	</div>
	<div class="span3">

	{if $userVotesUser|count > 0 && $userVotesUser|is_array}
		{if $v.SteamID|in_array:$userVotesUser || $besucher == true || $userTeam != $teamID}
		Votes: <span class="text-success t" title="positive Votes">{$userVotes[$v.SteamID].posCounts|string_format:"%d"}</span> - <span class="text-error t" title="negative Votes">{$userVotes[$v.SteamID].negCounts|string_format:"%d"}</span>
		{else}
			{if $v.SteamID != $steamID}
				{if $userTeam == $teamID}
				<div class="voteSection" style="display:inline">
					{if $userVotesAllowed.upvotesAllowed}
						{if $voteTypes|count > 0 && $voteTypes|is_array}
						<div class="btn-group voteButtonGroup">
						  <button class="btn btn-mini btn-success" value="{$v.SteamID}" data-type="1" data-value="7" data-label="Upvote"><i class="icon-thumbs-up t"></i>&nbsp;</button>
						  <button class="btn btn dropdown-toggle btn-success" data-toggle="dropdown">
						    <span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu good">
						  		
									{foreach key=k item=vpv from=$voteTypes name=posVotes}
										{if $vpv.Type == "1"}
											<li><a href="javascript:void(0);" data-value="{$vpv.VoteTypeID}" data-name="{$v.SteamID}" data-type="1">{$vpv.Name}</a></li>
										{/if}
								    {/foreach}
								
						  </ul>
						</div>
						{else}
							 <button class="btn btn-mini btn-success normalVote" value="{$v.SteamID}" data-type="1" data-value="7" data-label="Upvote"><i class="icon-thumbs-up t"></i>&nbsp;</button>
						  
						{/if}
					{else}
						 <button class="btn btn-mini btn-success normalVote" value="{$v.SteamID}" data-type="1" data-value="7" data-label="Upvote"><i class="icon-thumbs-up t"></i>&nbsp;</button>
					{/if}
					{if $userVotesAllowed.downvotesAllowed}
						{if $voteTypes|count > 0 && $voteTypes|is_array}
						<div class="btn-group voteButtonGroup">
						  <button class="btn btn-mini btn-danger" value="{$v.SteamID}" data-type="-1" data-value="8" data-label="Downvote"><i class="icon-thumbs-down"></i>&nbsp;</button>
						  <button class="btn btn dropdown-toggle btn-danger" data-toggle="dropdown">
						    <span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu bad">
						     
									{foreach key=k item=vnv from=$voteTypes name=negVotes}
										{if $vnv.Type == "-1"}
											<li><a href="javascript:void(0);" data-value="{$vnv.VoteTypeID}" data-name="{$v.SteamID}" data-type="-1">{$vnv.Name}</a></li>
										{/if}
								    {/foreach}
								
						  </ul>
						</div>
						{else}
							<button class="btn btn-mini btn-danger normalVote" value="{$v.SteamID}" data-type="-1" data-value="8" data-label="Downvote"><i class="icon-thumbs-down"></i>&nbsp;</button>
						  
						{/if}
					{else}
						<button class="btn btn-mini btn-danger normalVote" value="{$v.SteamID}" data-type="-1" data-value="8" data-label="Downvote"><i class="icon-thumbs-down"></i>&nbsp;</button>
					{/if}
				</div>
				{/if}
			{/if}
		
		{/if}
	{else}
		{if $besucher == true}
			Votes: <span class="text-success t" title="positive Votes">{$userVotes[$v.SteamID].posCounts|string_format:"%d"}</span> - <span class="text-error t" title="negative Votes">{$userVotes[$v.SteamID].negCounts|string_format:"%d"}</span>
		{else}
			
			{if $v.SteamID != $steamID}
				{if $userTeam == $teamID}
					<div class="voteSection" style="display:inline" >
					{if $userVotesAllowed.upvotesAllowed}
						{if $voteTypes|count > 0 && $voteTypes|is_array}
						<div class="btn-group voteButtonGroup">
						  <button class="btn btn-mini btn-success" value="{$v.SteamID}" data-type="1" data-value="7" data-label="Upvote"><i class="icon-thumbs-up"></i>&nbsp;</button>
						  <button class="btn btn dropdown-toggle btn-success" data-toggle="dropdown">
						    <span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu good">
						  		
									{foreach key=k item=vpv from=$voteTypes name=posVotes}
										{if $vpv.Type == "1"}
											<li><a href="javascript:void(0);" data-value="{$vpv.VoteTypeID}" data-name="{$v.SteamID}" data-type="1">{$vpv.Name}</a></li>
										{/if}
								    {/foreach}
								
						  </ul>
						</div>
						{else}
							 <button class="btn btn-mini btn-success normalVote" value="{$v.SteamID}" data-type="1" data-value="7" data-label="Upvote"><i class="icon-thumbs-up"></i>&nbsp;</button>
						  {/if}
					{/if}
					{if $userVotesAllowed.downvotesAllowed}
					 {if $voteTypes|count > 0 && $voteTypes|is_array}
						<div class="btn-group voteButtonGroup">
						  <button class="btn btn-mini btn-danger" value="{$v.SteamID}" data-type="-1" data-value="8" data-label="Downvote"><i class="icon-thumbs-down"></i>&nbsp;</button>
						  <button class="btn btn dropdown-toggle btn-danger" data-toggle="dropdown">
						    <span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu bad">
						    
									{foreach key=k item=vnv from=$voteTypes name=negVotes}
										{if $vnv.Type == "-1"}
											<li><a href="javascript:void(0);" data-value="{$vnv.VoteTypeID}" data-name="{$v.SteamID}" data-type="-1">{$vnv.Name}</a></li>
										{/if}
								    {/foreach}
								
						  </ul>
						</div>
						{else}
							 <button class="btn btn-mini btn-danger normalVote" value="{$v.SteamID}" data-type="-1" data-value="8" data-label="Downvote"><i class="icon-thumbs-down"></i>&nbsp;</button>
						{/if}
					{/if}
					
				</div>
				{else}
					Votes: <span class="text-success t" title="positive Votes">{$userVotes[$v.SteamID].posCounts|string_format:"%d"}</span> - <span class="text-error t" title="negative Votes">{$userVotes[$v.SteamID].negCounts|string_format:"%d"}</span>
		
				{/if}
			
			{/if}
		{/if}
		
	{/if}
	</div>
</div>


	