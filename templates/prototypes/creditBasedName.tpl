<style type="text/css">
.text-bronze{
color: peru;
text-shadow: 1px 1px 2px #999;
}
.text-silver{
color: silver;
text-shadow: 1px 1px 2px #666;
}
.text-gold{
color: gold;
text-shadow: 1px 1px 2px #666;
}

</style>

{if $creditValue < 0}
	{assign "textClass" ""}
{elseif $creditValue >= 0 && $creditValue < $smarty.const.BRONZECREDITBORDER}
	{assign "textClass" ""}
	{assign "creditValue" ""}
{elseif $creditValue >= $smarty.const.BRONZECREDITBORDER && $creditValue < $smarty.const.SILVERCREDITBORDER}
	{assign "textClass" "text-bronze"}
{elseif $creditValue >= $smarty.const.SILVERCREDITBORDER && $creditValue < $smarty.const.GOLDCREDITBORDER}
	{assign "textClass" "text-silver"}
{elseif $creditValue >= $smarty.const.GOLDCREDITBORDER}
	{assign "textClass" "text-gold"}
{/if}

{if $creditValue >= $smarty.const.BRONZECREDITBORDER}
	{assign "truncateValue" "10"}
	{assign "titleText" "earned Creditpoints: $creditValue"}
	{assign "iconClass" "icon-thumbs-up"}
{else}
	{assign "truncateValue" "13"}
	{assign "iconClass" ""}
{/if}

{if $name|strlen >= $truncateValue}
	{assign "titleName" $name}
{else}
	{assign "titleName" ""}
{/if}

{if $steamID == $smarty.session.user.steamID}
<span class="t" title="{$titleName}">
{if $playerSteamID > 0}
<a href="profile.php?ID={$playerSteamID}" target="_blank">{$name|truncate:$truncateValue:"...":true}</a>
{else}
{$name|truncate:$truncateValue:"...":true}
{/if}
</span>
<span class="t {$textClass}" title="{$titleText}"><i class="{$iconClass}"></i></span>
{/if}