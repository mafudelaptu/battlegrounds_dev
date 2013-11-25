 <h4 class="media-heading">Selected Matchmode <a href="#myModalSelectMatchMode1vs1" class="btn btn-mini btn-inverse" data-toggle="modal" data-backdrop="static" data-keyboard="false"><i class="icon-pencil icon-white t" title="Customize selected Matchmodes"></i></a></h4>
        <div id="1vs1QueueMatchModes">

            {if $modi|count > 0 && $modi|is_array}
            {foreach key=k item=v from=$modi name=modi_array}
              {if $smarty.cookies.1vs1Queue[$v.MatchModeID] == true}
                <span class="badge badge-info t" data-value="{$v.MatchModeID}" data-original-title="{$v.Name}" title="{$v.Name}">{$v.Shortcut}</span>&nbsp;
              {/if} 
    
            {/foreach}
            {/if}
        
		</div>
<div id="1vs1QueueMatchModesErrors">
        </div>