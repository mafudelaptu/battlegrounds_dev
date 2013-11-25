<!-- Modal -->

<div id="myModalSelectMatchMode1vs1" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalSelectMatchMode1vs1" aria-hidden="true">
  <div class="modal-header">
    <h3 id="myModalSelectMatchMode1vs1Label">Select Matchmode</h3>
  </div>
  <div class="modal-body">
  	<div class="control-group">
      {if $modi|count > 0 && $modi|is_array}
      		<div class="controls span2">
          {for $it=0 to $halfCount}
          	{if $modi.$it.MatchModeID == 5}
          		{assign "disabled" ""}
          	{else}
          		{assign "disabled" "disabled"}
          	{/if}
          	{if $smarty.cookies.1vs1Queue[$modi.$it.MatchModeID] == true}
              {assign var="checked" value="checked"}
            {else}
              {assign var="checked" value=""}
            {/if}
          	<label class="checkbox">
                <input type="checkbox" value="{$modi.$it.MatchModeID}" id="matchMode{$modi.$it.MatchModeID}" shortcut="{$modi.$it.Shortcut}" name="{$modi.$it.Name}" {$checked}  {$disabled}> {$modi.$it.Name} ({$modi.$it.Shortcut}) <span class="label t" title="{$matchModeCounts[$modi.$it.MatchModeID]} Player(s) in Queue with this Matchmode">{$matchModeCounts[$modi.$it.MatchModeID]}</span> 
            </label>
          {/for}
          </div>
          <div class="controls span2">
          {for $it=($halfCount+1) to $modi|count}
          	{if $modi.$it.MatchModeID == 5}
          		{assign "disabled" ""}
          	{else}
          		{assign "disabled" "disabled"}
          	{/if}
          	{if $modi.$it.MatchModeID != ""}
              {if $smarty.cookies.1vs1Queue[$modi.$it.MatchModeID] == true}
                {assign var="checked" value="checked"}
              {else}
                {assign var="checked" value=""}
              {/if}
            	<label class="checkbox">
                <input type="checkbox" value="{$modi.$it.MatchModeID}" id="matchMode{$modi.$it.MatchModeID}" shortcut="{$modi.$it.Shortcut}" name="{$modi.$it.Name}" {$checked} {$disabled}> {$modi.$it.Name} ({$modi.$it.Shortcut}) <span class="label t" title="{$matchModeCounts[$modi.$it.MatchModeID]} Player(s) in Queue with this Matchmode">{$matchModeCounts[$modi.$it.MatchModeID]}</span>
            	</label>
            {/if}
          {/for}
          </div>
         {/if}
</div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" onclick="handleMatchMode1vs1Selection()">Save selection!</button>
  </div>
</div>
