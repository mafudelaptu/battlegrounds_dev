 <h4 class="media-heading">Selected Region <a href="#myModalSelectRegion3vs3" class="btn btn-mini btn-inverse" data-toggle="modal" data-backdrop="static" data-keyboard="false"><i class="icon-pencil icon-white t" title="Customize selected regions"></i></a></h4>
 <div id="3vs3QueueRegion">

     {if $regions|count > 0 && $regions|is_array}
     {foreach key=k item=v from=$regions name=region_array}
       {if $smarty.cookies.3vs3QueueRegion[$v.RegionID] == true}
         <span class="badge badge-important t" data-value="{$v.RegionID}" data-original-title="{$v.Name}" title="{$v.Name}">{$v.Shortcut}</span>&nbsp;
       {/if} 

     {/foreach}
     {/if}
   
 </div>
 <div id="3vs3QueueRegionErrors">
        </div>