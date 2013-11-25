<div class="page-header">
<h1>Match <small>MatchID:{$matchID}</small></h1> 
</div>
	
     {if $data.data.team1|is_array && $data.data.team1|count > 0 && $data.data.team2|is_array && $data.data.team2|count > 0}
     {include "match/info_message.tpl"}
<!--       <div class="row-fluid"> -->
<!-- 	        <div class="span5 alert-success" align="center" style="text-transform:uppercase; line-height:30px; margin-bottom:5px; border-bottom: 1px #aaa solid; border-radius: 5px;"><strong>{$data.data.team1.0.Team}</strong></div> -->
<!-- 	        <div class="span2" align="center"></div> -->
<!-- 	        <div class="span5 alert-error" align="center" style="text-transform:uppercase; line-height:30px; margin-bottom:5px; border-bottom: 1px #aaa solid; border-radius: 5px;"><strong>{$data.data.team2.0.Team}</strong></div> -->
<!--         </div> -->
        <div class="row-fluid">
          <div class="span5">
           <div class="alert-success" align="center" style="text-transform:uppercase; line-height:30px; margin-bottom:5px; border-bottom: 1px #aaa solid; border-radius: 5px;"><strong>{$data.data.team1.0.Team}</strong></div>
          	{foreach key=k item=v from=$data.data.team1 name=team1_array}
          		{include "match/matchfound_player.tpl" teamID=1 teamWonID=$teamWonID}
          	{/foreach}
			
          </div>
          <div class="span2" align="center">
          	{include "match/middleArea_new.tpl"}
          </div>
          <div class="span5">
            <div class=" alert-error" align="center" style="text-transform:uppercase; line-height:30px; margin-bottom:5px; border-bottom: 1px #aaa solid; border-radius: 5px;"><strong>{$data.data.team2.0.Team}</strong></div>
      
          	{foreach key=k item=v from=$data.data.team2 name=team2_array} 

          		{include "match/matchfound_player.tpl" teamID=2 teamWonID=$teamWonID} 
			{/foreach}
			</div>
        </div>
        
        {if $smarty.const.DEBUG || $isAdmin}
        	
			<div class="row-fluid">
	          <div class="span5">
	          	{foreach key=k item=v from=$data.data.team1 name=team1_array}
	          	<div><img alt="Avatar" src="{$v.Avatar}" width="25" height="25"> {$v.Name} <div class="pull-right">{$v.SteamID}</div></div>
	          	{/foreach}
				
	          </div>
	          <div class="span2" align="center">
	        		<strong>FOR ADMINS</strong>
	          </div>
	          <div class="span5">
	          	{foreach key=k item=v from=$data.data.team2 name=team2_array} 
	          		<div><img alt="Avatar" src="{$v.Avatar}" width="25" height="25"> {$v.Name} <div class="pull-right">{$v.SteamID}</div></div>
				{/foreach}
				</div>
	        </div>
		{/if}
        
        {if $replayData|is_array && $replayData|count > 0}
        	{include file="match/replay/index.tpl"}
		{/if}
		
        <div class="row-fluid">
			<div class="span6" id="matchChat">
			
				<!-- Chat includen -->
				
				{* include file="match/chat/index.tpl" *}
					{include "prototypes/chat.tpl" chatID="matchChat" matchChat=true}
				
				
			</div>
			<div class="span6">
				{if $matchData.MatchTypeID == 8}
					<!-- Screenshots includen -->
				 {include file="match/screenshots/index.tpl" data=$data.screenshots}
				{else}
				<!-- Chat includen -->
				 {include file="match/ts3/index.tpl"}
				{/if}
				
				
			</div>
		</div>
     {else}
     	access denied!
     {/if}
      
{* include all Modals *}
{if $submitLock == false}
{include "match/modals/submitResult.tpl"}
{include "match/modals/cancelMatch.tpl"}
{include "match/modals/cancelMatchSuccess.tpl"}
{include "match/modals/cancelHardMatchSuccess.tpl"}

{/if}
{include "match/modals/replayUploadModal.tpl"}
