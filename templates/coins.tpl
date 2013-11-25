{if $smarty.const.COINSACTIVE==true}
<ul class="nav pull-right">
	<li id="naviUserCoins" data-content="<div style='width:190px'>Your N-Gage-Coins: <b>{$coins}</b> <i class='icon-money'></i></div>" data-toggle="popover" data-original-title="">
		<a href="javascript:void(0);">
			<strong>{$coins}</strong>&nbsp;<i class="icon-money"></i>
		</a>
	</li>
</ul>

{/if}
