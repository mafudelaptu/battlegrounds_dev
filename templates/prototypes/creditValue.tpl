<style type="text/css">
.text-bronze{
color: peru;
}
.text-silver{
color: silver;
}
.text-gold{
color: gold;
}

</style>

{if $creditValue < 0}
	{assign "textClass" "text-error"}
	{assign "iconClass" "icon-thumbs-down"}
{elseif $creditValue >= 0 && $creditValue < $smarty.const.BRONZECREDITBORDER}
	{assign "textClass" ""}
	{assign "iconClass" "icon-thumbs-up"}
{elseif $creditValue >= $smarty.const.BRONZECREDITBORDER && $creditValue < $smarty.const.SILVERCREDITBORDER}
	{assign "textClass" "text-bronze"}
	{assign "iconClass" "icon-thumbs-up"}
{elseif $creditValue >= $smarty.const.SILVERCREDITBORDER && $creditValue < $smarty.const.GOLDCREDITBORDER}
	{assign "textClass" "text-silver"}
	{assign "iconClass" "icon-thumbs-up"}
{elseif $creditValue >= $smarty.const.GOLDCREDITBORDER}
	{assign "textClass" "text-gold"}
	{assign "iconClass" "icon-thumbs-up"}
{/if}
{*if $steamID == $smarty.session.user.steamID *}
<span class="t {$textClass}" title="earned Creditpoints: {$creditValue}"><i class="{$iconClass}"></i>{$creditValue}</span>
<a href="help.php#WhatIsTheCreditSystem" target="_blank" class="t" title="What is the Credit-System and how does this work? "><i class="icon-question-sign"></i>&nbsp;</a>
{* /if *}
