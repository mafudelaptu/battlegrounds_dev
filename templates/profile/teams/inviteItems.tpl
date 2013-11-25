{if $data|count > 0 && $data|is_array}
<div style="margin:5px 0 10px 0">
 {foreach key=k item=v from=$data name=data_array}
 	<div class="row-fluid">
		<div class="span5" style="line-height: 30px;">
			 <strong>{$v.Name} (#{$v.GroupID})</strong>
		</div>
		<div class="span4">
			 {foreach key=kk item=vv from=$v.members name=data_array}
			 	<p><img src="{$vv.Avatar}">&nbsp;{$vv.Name}</p>
			 {/foreach}
		</div>
		<div class="span3" style="line-height: 30px;">
			<button class="btn btn-success btn-mini" onclick="acceptTeamInvite({$v.GroupID})">Accept</button>
			&nbsp;
			<button class="btn btn-danger btn-mini" onclick="declineTeamInvite({$v.GroupID})">Decline</button>
		</div>
	</div>
 {/foreach}
 </div>
 {else}
 <div class="alert">no Team invites</div>
{/if}

