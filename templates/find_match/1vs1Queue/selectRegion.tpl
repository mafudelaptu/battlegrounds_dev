 <h4 class="media-heading">Selected Region <a href="#myModalSelectRegion1vs1" class="btn btn-mini btn-inverse" data-toggle="modal" data-backdrop="static" data-keyboard="false"><i class="icon-pencil icon-white t" title="Customize selected regions"></i></a></h4>
 <div id="1vs1QueueRegion">

     {if $regions|count > 0 && $regions|is_array}
     {foreach key=k item=v from=$regions name=region_array}
       {if $smarty.cookies.1vs1QueueRegion[$v.RegionID] == true}
         <span class="badge badge-important t" data-value="{$v.RegionID}" data-original-title="{$v.Name}" title="{$v.Name}">{$v.Shortcut}</span>&nbsp;
       {/if} 

     {/foreach}
     {/if}
   
 </div>
 <div id="1vs1QueueRegionErrors">
        </div>