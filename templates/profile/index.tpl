{if $smarty.const.NEWPROFILE==true}

<div class="row-fluid">
	<div class="span5">
		{include "profile/userInfo/index.tpl" username=$userData.Name credits=$userCredits avatar=$userData.AvatarFull steamID=$userData.SteamID profileURL=$userData.ProfileURL createdTimestamp=$userData.CreatedTimestamp lastTimestamp=$userData.LastActivityTimestamp}
		
		{include "profile/userStats/index.tpl" userStats=$userStats}
	</div>
	<div class="span4">
		{include "profile/awards/index.tpl" data=$awardsData}
		
		{include "profile/gameStats/index.tpl" data=$gameStats}
		
		{include "profile/MMRankings/index.tpl"}
	</div>
	<div class="span3">
		{include "profile/matchStats/index.tpl" data=$matchStats}
		
		{include "profile/matchStats/lastMatches.tpl"}
	</div>
</div>

{if $smarty.const.TEAMSACTIVE==true}
	{assign "teamsClass" ""}
	{assign "teamsToggle" "data-toggle='tab'"}
	{assign "teamsHref" "#teams"}
	
{else}
	{assign "teamsClass" "disabled"}
	{assign "teamsToggle" ""}
	{assign "teamsHref" "#"}
{/if}

{if $smarty.const.GBACTIVE==true}
	{assign "GBClass" ""}
	{assign "GBToggle" "data-toggle='tab'"}
{else}
	{assign "GBClass" "disabled"}
	{assign "GBToggle" ""}
{/if}

{if $smarty.const.REFERAFRIEND==true}
	{assign "RAFClass" ""}
	{assign "RAFToggle" "data-toggle='tab'"}
	{assign "RAFHref" "#RAF"}
{else}
	{assign "RAFClass" "disabled"}
	{assign "RAFToggle" ""}
	{assign "RAFHref" "#"}
{/if}

{if $smarty.const.BACKPACK==true}
	{assign "bpClass" ""}
	{assign "bpToggle" "data-toggle='tab'"}
	{assign "bpHref" "#bp"}
{else}
	{assign "bpClass" "disabled"}
	{assign "bpToggle" ""}
	{assign "bpHref" "#"}
{/if}

<ul class="nav nav-tabs" id="profileTabs">
  <li class="active"><a href="#overview" data-toggle="tab">Overview</a></li>
  <li class=""><a href="#warns" data-toggle="tab">Warns</a></li>
  {if $besucher!=true}
	<li class="{$teamsClass}"><a href="{$teamsHref}" {$teamsToggle}>Teams</a></li>
	<li class="{$bpClass}"><a href="{$bpHref}" {$bpToggle}>Backpack</a></li>
  	<li class="{$RAFClass}"><a href="{$RAFHref}" {$RAFToggle}>Refer-A-Friend</a></li>
{/if}
  
  {* <li class="{$GBClass}"><a href="#guestbook" {$GBToggle}>Guestbook</a></li> *}
</ul>

<div class="tab-content">
  <div class="tab-pane active" id="overview">

<div class="row-fluid" id="graphArea">
	<div class="span4">
		{include "profile/generalWinRateTrend.tpl"}
	</div>
	<div class="span4">
		{include "profile/eloRose.tpl"}
	</div>
	<div class="span4">
		{include "profile/eloHistory.tpl"}
	</div>
</div>
  </div>
  <div class="tab-pane" id="warns">
  	{include "profile/warns/index.tpl"}
  </div>
  {if $smarty.const.TEAMSACTIVE==true}
	  <div class="tab-pane" id="teams">
	  	{include "profile/teams/index.tpl"}
	  </div>
  {/if}
  {if $smarty.const.BACKPACK==true}
	  <div class="tab-pane" id="bp">
	  	{include "profile/backpack/index.tpl" data=$userBackpackItems}
	  </div>
  {/if}
  <div class="tab-pane" id="RAF">
  	{include "profile/referAFriend/index.tpl" referedCount=$referedCount refererLink=$refererLink inviteCount=$inviteCount}
  </div>
  {*<div class="tab-pane" id="guestbook">guestbook</div>*}
</div>
{else}

{include "profile/heroUnit.tpl" index="false"}

{if $smarty.const.TEAMSACTIVE==true}
	{assign "teamsClass" ""}
	{assign "teamsToggle" "data-toggle='tab'"}
	{assign "teamsHref" "#teams"}
	
{else}
	{assign "teamsClass" "disabled"}
	{assign "teamsToggle" ""}
	{assign "teamsHref" "#"}
{/if}

{if $smarty.const.GBACTIVE==true}
	{assign "GBClass" ""}
	{assign "GBToggle" "data-toggle='tab'"}
{else}
	{assign "GBClass" "disabled"}
	{assign "GBToggle" ""}
{/if}

{if $smarty.const.REFERAFRIEND==true}
	{assign "RAFClass" ""}
	{assign "RAFToggle" "data-toggle='tab'"}
	{assign "RAFHref" "#RAF"}
{else}
	{assign "RAFClass" "disabled"}
	{assign "RAFToggle" ""}
	{assign "RAFHref" "#"}
{/if}

<ul class="nav nav-tabs" id="profileTabs">
  <li class="active"><a href="#overview" data-toggle="tab">Overview</a></li>
  <li class=""><a href="#warns" data-toggle="tab">Warns</a></li>
  {if $besucher!=true}
	{* <li class="{$teamsClass}"><a href="{$teamsHref}" {$teamsToggle}>Teams</a></li> *}
  	<li class="{$RAFClass}"><a href="{$RAFHref}" {$RAFToggle}>Refer-A-Friend</a></li>
{/if}
  
  {* <li class="{$GBClass}"><a href="#guestbook" {$GBToggle}>Guestbook</a></li> *}
</ul>

<div class="tab-content">
  <div class="tab-pane active" id="overview">
  	<div class="row-fluid">
	<div class="span4">
		{include "profile/last5Matches.tpl"}
	</div>
	<div class="span8">
		{include "profile/bestRankings.tpl"}
	</div>
</div>

<div class="row-fluid" id="graphArea">
	<div class="span4">
		{include "profile/generalWinRateTrend.tpl"}
	</div>
	<div class="span4">
		{include "profile/eloRose.tpl"}
	</div>
	<div class="span4">
		{include "profile/eloHistory.tpl"}
	</div>
</div>
  </div>
  <div class="tab-pane" id="warns">
  	{include "profile/warns/index.tpl"}
  </div>
  {if $smarty.const.TEAMSACTIVE==true}
	  <div class="tab-pane" id="teams">
	  	{include "profile/teams/index.tpl"}
	  </div>
  {/if}
  <div class="tab-pane" id="RAF">
  	{include "profile/referAFriend/index.tpl" referedCount=$referedCount refererLink=$refererLink}
  </div>
  {*<div class="tab-pane" id="guestbook">guestbook</div>*}
</div>

{/if}

<!-- Modals einbinden -->
{include file="profile/modals/fullGeneralWinRate.tpl"}