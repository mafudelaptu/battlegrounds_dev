<style type="text/css">
.dlEventStyle>dt,.dlEventStyle>dd {
	height: 22px;
	text-align: left;
}

.dlEventStyle>dt {
	width: 220px;
}
</style>

<div class="row-fluid">
	<div class="span6">
		<dl class="dl-horizontal dlEventStyle">
			<dt>Events starts on:</dt>
			<dd>{$data.StartTimestamp|date_format:"%d.%m. - %H:%M"}</dd>
			<dt>Event sign-in on:</dt>
			<dd>{$data.StartSubmissionTimestamp|date_format:"%d.%m. -
				%H:%M"}</dd>
			<dt>Tournament-Type:</dt>
			<dd>
				<span class="t badge badge-warning"
					data-original-title="{$data.TTName}">{$data.TTShortcut}</span>
				(Bo1)
			</dd>
			{if $userAlreadyInEvent}
			<dt>Signed-in Player-Count:</dt>
			<dd>
				<span class="label">{$eventSubsCount}</span>
			</dd>
			<dt>Reached min number players:</dt>
			<dd>
				{if {$eventSubsCount} >= $data.MinSubmissions} {assign "reachedText"
				"yes"} {assign "reachedClass" "success"} {else} {assign
				"reachedText" "no"} {assign "reachedClass" "important"} {/if} <span
					class="label label-{$reachedClass}">{$reachedText}</span>
			</dd>
			{/if}
		</dl>

	</div>
	<div class="span6">
		<dl class="dl-horizontal dlEventStyle">
			<dt>Match-Mode:</dt>
			<dd>
				<span class="t badge badge-info"
					data-original-title="{$data.MatchModeName}">{$data.MMShortcut}</span>
			</dd>
			<dt>Region:</dt>
			<dd>
				<span class="t badge badge-important"
					data-original-title="{$data.RegionName}">{$data.RShortcut}</span>
			</dd>
			<dt>Minimum Players:</dt>
			<dd>
				<span class="t label" title="Minimum Players">{$data.MinSubmissions}</span>
			</dd>
			{if $userAlreadyInEvent}
			<dt>current Stack-count:</dt>
			<dd>
				{assign "vielfaches" $eventSubsCount/$data.MinSubmissions} {if
				$vielfaches|floor == 0} {assign "vielfaches" "0"} {else} {assign
				"vielfaches" $vielfaches|floor} {/if} <span class="label t"
					title="{$vielfaches} parallel Events will be created">{$vielfaches}</span>
			</dd>
			{/if}
		</dl>
	</div>
</div>