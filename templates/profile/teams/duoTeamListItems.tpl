{if $data|count > 0 && $data|is_array}
	<div id="duoTeamsCarousel" class="carousel slide" data-interval="false" data-pause="hover">
	  <!-- Carousel items -->
	  <div class="carousel-inner">
	  {foreach key=k item=v from=$data name=data_array}
	  		{if $smarty.foreach.data_array.iteration ==1}
				{assign "active" "active"}
			{else}
				{assign "active" ""}
			{/if}
	  	<div class="{$active} item" align="center">
	  		<fieldset style="margin:0 5px 5px 0;">
				<legend class="blackStyle" style="font-size:14px; padding-left:10px;">
					{$v.Name} <small>(#{$k})</small>
					<button class="btn btn-mini" type="button" onclick="editTeamName({$k},1, '{$v.Name}')">
						<i class="icon-pencil"></i>
					</button>
					<button class="btn btn-mini btn-danger" type="button" onclick="deleteTeam({$k}, 1)">
						<i class="icon-remove"></i>
					</button>
				</legend>
				<div>
					<strong>Statistics: </strong>
					<span class="text-success">{$v.stats.Wins}</span> - 
								<span class="text-error">{$v.stats.Losses}</span> - 
								<span class="text-warning">{$v.stats.WinRate}%</span>
					
					<strong>Points earned:</strong> {$v.stats.Points}
</div>

	
						{if $v.members|count > 0 && $v.members|is_array}
						<br>
						<div class="row-fluid">
							{foreach key=kk item=vv from=$v.members name=data_array}
								<div class="span6"><img src="{$vv.Avatar}">&nbsp;
									{if $vv.Name|strlen > 15}
										 <span class="t" title="{$vv.Name}">{$vv.Name|truncate:15:"...":true}</span>
									{else}
										{$vv.Name}
									{/if}
								</div>
							{/foreach}
							</div>
						{/if}
							
			</fieldset>
	  	</div>
	{/foreach}
	  </div>
	  {if $data|count > 1}
		 <!-- Carousel nav -->
	  <a class="carousel-control left" href="#duoTeamsCarousel" data-slide="prev" style="margin-top:0px;">&lsaquo;</a>
	  <a class="carousel-control right" href="#duoTeamsCarousel" data-slide="next" style="margin-top:0px;">&rsaquo;</a>
		{/if}
	 
	</div>

{if $data|count > 1}
<div align="center">
	<div class="control-group">
    <label class="control-label" for="quickJumpTeam">Quickjump to Team:</label>
    <div class="controls">
      <select id="quickJumpTeam" name="quickJumpTeam" onchange="$('#duoTeamsCarousel').carousel(parseInt($('#quickJumpTeam').val()-1));">
      		{foreach key=k item=v from=$data name=data_array}
      			{assign "titleAttr" ""}
      			{foreach key=kk item=vv from=$v.members name=data_array2}
	      		 	{if $smarty.foreach.data_array2.iteration > 1}
	      		 		{assign "sepText" " - "}
	      		 	{else}
	      		 		{assign "sepText" ""}
	      		 	{/if}
						{if $vv.Name|strlen > 15}
							{assign "titleAttr" "`$titleAttr``$sepText`"|cat:$vv.Name|truncate:15:"...":true}
							 
						{else}
							{assign "titleAttr" "`$titleAttr``$sepText`"|cat:$vv.Name}
						{/if}		
				{/foreach}
      		 <option value="{$smarty.foreach.data_array.iteration}" title="{$titleAttr}">
					{$v.Name} (#{$k})
      		 </option>
      		{/foreach}
		</select>
    </div>
  </div>
</div>
{/if}
{else}
	<div class="alert">You don't have created any Duo-Teams!</div>
{/if}