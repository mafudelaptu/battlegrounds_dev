{if $data|count > 0 && $data|is_array}

 {foreach key=k item=v from=$data name=data_array}
 	<div class="row-fluid">
	<div class="span5" style="line-height: 30px;">
		 <strong>{$v.Name} (#{$v.data.GroupID})</strong>
	</div>
	<div class="span7">
		 {if $v.members|count > 0 && $v.members|is_array}
		  {foreach key=kk item=vv from=$v.members name=data_array}
		  	{if $vv.Accepted == 1}
		  		{assign "icon" "icon-thumbs-up"}
		  		{assign "color" "text-success"}
		  		{assign "title" "accepted"}
		  	{elseif $vv.Accepted == -1}
		  		{assign "icon" "icon-thumbs-down"}
		  		{assign "color" "text-error"}
		  		{assign "title" "declined"}
		  	{else}
		  		{assign "icon" "icon-time"}
		  		{assign "color" "text-warning"}
		  		{assign "title" "waiting for submit"}
		  	{/if}
		  	<div class="{$color} t" title="{$title}" style="display:inline-block;"><i class="{$icon} "></i>&nbsp;<img src="{$vv.Avatar}">&nbsp;{$vv.Name}</div><br>
		  {/foreach}
		 {/if}
		
	</div>
</div>
 {/foreach}
 {else}
 	<div class="alert">no open Teams</div>
{/if}
