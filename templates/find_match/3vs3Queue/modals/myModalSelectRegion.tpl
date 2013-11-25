<!-- Modal -->

<div id="myModalSelectRegion3vs3" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalSelectRegion3vs3" aria-hidden="true">
  <div class="modal-header">
    <h3 id="myModalSelectRegion3vs3Label">Select Region</h3>
  </div>
  <div class="modal-body">
  	<div class="control-group">
      {if $regions|count > 0 && $regions|is_array}
      		<div class="controls span2">
          {for $it=0 to $halfCountRegion}
          	{if $smarty.cookies.3vs3QueueRegion[$regions.$it.RegionID] == true}
              {assign var="checked" value="checked"}
            {else}
              {assign var="checked" value=""}
            {/if}
          	<label class="checkbox">
                <input type="checkbox" value="{$regions.$it.RegionID}" id="region{$regions.$it.RegionID}" shortcut="{$regions.$it.Shortcut}" name="{$regions.$it.Name}" {$checked}> {$regions.$it.Name} ({$regions.$it.Shortcut}) <span class="label t" title="{$regionsCounts[$regions.$it.RegionID]} Player(s) in Queue with this Region">{$regionCounts[$regions.$it.RegionID]}</span>
            </label>
          {/for}
          </div>
          <div class="controls span2">
          {for $it=($halfCountRegion+1) to $regions|count}
          	{if $regions.$it.RegionID != ""}
              {if $smarty.cookies.3vs3QueueRegion[$regions.$it.RegionID] == true}
                {assign var="checked" value="checked"}
              {else}
                {assign var="checked" value=""}
              {/if}
            	<label class="checkbox">
                <input type="checkbox" value="{$regions.$it.RegionID}" id="region{$regions.$it.RegionID}" shortcut="{$regions.$it.Shortcut}" name="{$regions.$it.Name}" {$checked}> {$regions.$it.Name} ({$regions.$it.Shortcut}) <span class="label t" title="{$regionsCounts[$regions.$it.RegionID]} Player(s) in Queue with this Region">{$regionCounts[$regions.$it.RegionID]}</span>
            	</label>
            {/if}
          {/for}
          </div>
         {/if}
</div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" onclick="handleRegion3vs3Selection()">Save selection!</button>
  </div>
</div>
