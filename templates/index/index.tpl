{if $smarty.const.CLOSED == false}
	{if $userLoggedIn}
		{if $smarty.now >= $smarty.const.RELAUNCH}
			{include file="index/indexLoggedIn.tpl"}
		{else}
			{include file="index/indexNotLoggedIn.tpl"}
		{/if}
	
	{else}
	{include file="index/indexNotLoggedIn.tpl"}
	{/if}
{else}
	{include file="index/indexClosed.tpl"}
{/if}
{* FirstLogin-Modal *}
{include "index/modals/firstLogin.tpl"}
{include "index/modals/notAllowed.tpl"}
{include "index/modals/showSignedInPlayers.tpl"}
{include "events/modals/readyForEvent.tpl"}