{if $smarty.const.NOLUEAGUES == false}
<div class="row-fluid well well-small">
	{if $data|count > 0 && $data|is_array}
		{assign "size" 12/$data|count}
	  {foreach key=k item=v from=$data name=WOF_array}
	  		<div class="span{$size}" align="center">
	  		<!-- 	  		Single ranking template -->
			{include file="index/loggedIn/wallOfFame/singleRanking.tpl" rankingData=$v rankingName = $k}
	  		</div>
	  {/foreach}
	{/if}

</div>
{/if}
