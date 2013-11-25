<!-- Modal -->

<div id="myModalSelectMatchMode" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalSelectMatchMode" aria-hidden="true">
  <div class="modal-header">
    <h3 id="myModalSelectMatchModeLabel">Select Matchmode</h3>
  </div>
  <div class="modal-body">
  	{if $leagueData.LeagueTypeID < 3}
	  	<div class="alert alert-warning">
	  		<strong>Note:</strong> You are in the <strong>{$leagueData.Name}-League</strong>. Therefore, only these modes below are available. If you reach <strong>Silver-League</strong> you can use all available Dota2-Matchmodes.
	  	</div>
  	{/if}
  	<div class="control-group">
      {if $modi|count > 0 && $modi|is_array}
      		<div class="controls span2">
          {for $it=0 to $halfCount}
          {if $userPermissions.matchModes|count > 0 && $userPermissions.matchModes|is_array}
          	{if $modi.$it.MatchModeID|in_array:$userPermissions.matchModes}
	          	{if $smarty.cookies.singleQueue[$modi.$it.MatchModeID] == true}
	              {assign var="checked" value="checked"}
	            {else}
	              {assign var="checked" value=""}
	            {/if}
	          	<label class="checkbox">
	                <input type="checkbox" value="{$modi.$it.MatchModeID}" id="matchMode{$modi.$it.MatchModeID}" shortcut="{$modi.$it.Shortcut}" name="{$modi.$it.Name}" {$checked}> {$modi.$it.Name} ({$modi.$it.Shortcut}) <span class="label t" title="{$matchModeCounts[$modi.$it.MatchModeID]} Player(s) in Queue with this Matchmode">{$matchModeCounts[$modi.$it.MatchModeID]}</span> 
	            </label>
	         {/if}
		{/if}
          
          {/for}
          </div>
          <div class="controls span2">
          {for $it=($halfCount+1) to $modi|count}
            {if $userPermissions.matchModes|count > 0 && $userPermissions.matchModes|is_array}
            {if $modi.$it.MatchModeID|in_array:$userPermissions.matchModes}
	          	{if $modi.$it.MatchModeID != ""}
	              {if $smarty.cookies.singleQueue[$modi.$it.MatchModeID] == true}
	                {assign var="checked" value="checked"}
	              {else}
	                {assign var="checked" value=""}
	              {/if}
	            	<label class="checkbox">
	                <input type="checkbox" value="{$modi.$it.MatchModeID}" id="matchMode{$modi.$it.MatchModeID}" shortcut="{$modi.$it.Shortcut}" name="{$modi.$it.Name}" {$checked}> {$modi.$it.Name} ({$modi.$it.Shortcut}) <span class="label t" title="{$matchModeCounts[$modi.$it.MatchModeID]} Player(s) in Queue with this Matchmode">{$matchModeCounts[$modi.$it.MatchModeID]}</span>
	            	</label>
	            {/if}
              {/if}
{/if}
          	
          {/for}
          </div>
         {/if}
</div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" onclick="handleMatchModeSelection()">Save selection!</button>
  </div>
</div>
