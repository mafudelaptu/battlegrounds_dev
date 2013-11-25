<div class="blackH2">FIND<green>MATCH</green></div>
<!-- <img src="img/find_match/findmatch.jpg"> -->
<!-- <div class="page-header"> -->
<!-- <h1><img src="img/find_match/findmatch.jpg"></h1> -->
<!-- </div> -->

{if $userLoggedIn == "true"}
	{include "find_match/content.tpl"}
{else}
	{include "find_match/errorNotLoggedIn.tpl"}
{/if}

{include "find_match/modals/myModalMatchMaking.tpl"}
{include "find_match/modals/myModalWaitingForOtherPlayers.tpl"}
{include "find_match/modals/myModalReadyForMatch.tpl"}
{include "find_match/modals/myModalLeaveQueue.tpl"}
{include "find_match/modals/myModalSelectMatchMode.tpl"}
{include "find_match/modals/myModalSelectRegion.tpl"}
{include "find_match/modals/selectDuoPartnerModal.tpl"}
{include "find_match/modals/waitingForOtherPlayers1vs1.tpl"}

{include "find_match/1vs1QueueNew/modals/myModalSelectMatchMode1vs1.tpl"}
{include "find_match/1vs1QueueNew/modals/myModalSelectRegion1vs1.tpl"}

