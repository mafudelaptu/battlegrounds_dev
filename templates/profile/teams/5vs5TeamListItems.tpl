{if $data|count > 0 && $data|is_array}
{foreach key=k item=v from=$data name=data_array}
	<fieldset style="margin:0 5px 5px 0; padding:5px;">
		<legend class="blackStyle" style="font-size:12px; padding-left:10px; vertical-align: middle">
			{$v.Name} <small>(#{$k})</small>
			<button class="btn btn-mini" type="button">
				<i class="icon-pencil"></i>
			</button>
			<button class="btn btn-mini btn-danger" type="button">
				<i class="icon-remove"></i>
			</button>
		</legend>
		<div class="row-fluid">
			<div class="span6">
			<strong>Statistics: </strong>
			<span class="text-success">{$v.stats.Wins}</span> - 
						<span class="text-error">{$v.stats.Losses}</span> - 
						<span class="text-warning">{$v.stats.WinRate}%</span>
			</div>
			<div class="span6" align="right">
				<strong>Points earned:</strong> {$v.stats.Points}
			</div>
		</div>
		<div class="row-fluid">
			<div class="span3"><strong>Team-Members:</strong></div>
			<div class="span9">
				{if $v.members|count > 0 && $v.members|is_array}
					{foreach key=kk item=vv from=$v.members name=data_array}
						<div class="pull-left span4"><img src="{$vv.Avatar}">&nbsp;{$vv.Name}</div>
					{/foreach}
				{/if}
			</div>
		</div>

		
	</fieldset>
	
{/foreach}
{else}
	<div class="alert">You don't have created any 5vs5-Teams!</div>
{/if}