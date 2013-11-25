<blockquote>
	<p>
<i class="icon-time icon-2x"></i> <strong>Matchmaking</strong> is every minute. 
{* Next Matchmaking will be at: <strong>{$nextHour|date_format:"%H:%M"} CET</strong> *}
</p>
</blockquote>

<!-- NEW DESIGN -->
<ul class="nav nav-tabs">
    {if $smarty.const.ONEVSONEQUEUE == true}
		{assign "oneVsOneToggle" "tab"}
		{assign "oneVsOneActive" ""}
	{else}
		{assign "oneVsOneToggle" ""}
		{assign "oneVsOneActive" "disabled"}
	{/if}
    <li class="active"><a href="#1vs1Queue" data-toggle="tab">1vs1-Queue</a></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane active" id="1vs1Queue">
    	<div id="1vs1QueueArea">
    	
      {include "find_match/1vs1QueueNew/index.tpl"}
      </div>
    </div>
  </div>

  <hr>
  {include "prototypes/chat.tpl" chatID="findMatchGeneralChat" maxHeight="500"}