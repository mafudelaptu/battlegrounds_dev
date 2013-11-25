
<div class="container">
<div id="homeStatsBar">{include file="prototypes/statusBar.tpl"
	matchesCount=$matchesCount userCount=$userCount
	inQueueCount=$inQueueCount liveMatches=$liveMatches}</div>

{if $smarty.const.NEWHOMEDESIGN == true}
	<div class="row-fluid">
		<div class="span9">
			<ul class="nav nav-tabs" id="homeTabs">
			  <li class="active"><a href="#home" data-toggle="tab">Home</a></li>
			  <li><a href="#stream" data-toggle="tab">Stream</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="home">{include
					"index/loggedIn/home/homeTab.tpl"}</div>
				<div class="tab-pane" id="stream">
					{include "index/loggedIn/home/streamTab.tpl"}
				</div>
			</div>
		</div>
		<div class="span3">
			{include "index/loggedIn/streams/index.tpl" data=$streamerData}
		</div>
	</div>
{else}
	<div class="row-fluid">
		<div class="span9">
			{include "index/loggedIn/latestNews/index.tpl"}
		</div>
		{*<div class="span3">
			<!-- Events -->
			{include "index/loggedIn/event/upcomingEvents.tpl" data=$nextEvent}
		</div>*}
		<div class="span3">
			{include "index/loggedIn/streams/index.tpl" data=$streamerData}
		</div>
	</div>
{/if}


{**
	<h2>Poll</h2>
	<div class="row-fluid">
		<div class="span4">
			<!-- Web Poll Code -->
			<div id="wpg_517d32f01a5b2">
				Loading <a href="http://www.webpollgenerator.com">web poll</a>...
			</div>
			<script language="JavaScript">
				(function() {
					wpgscript_517d32f01a5b2 = document.createElement('script');
					wpgscript_517d32f01a5b2.type = "text/javascript";
					wpgscript_517d32f01a5b2.src = "http://www.webpollgenerator.com/GetPoll2.php?p=517d32f01a5b2"
							+ "&s=" + escape(window.location);
					setTimeout(
							"document.getElementById('wpg_517d32f01a5b2').appendChild(wpgscript_517d32f01a5b2)",
							1);
				})();
			</script>
			<noscript>
				<div>
					<a
						href="http://www.webpollgenerator.com/Which-feature-is-more-important-or-should-be-implemented-as-soon-as-possible-517d32f01a5b2"
						title="generator html polls">Generate HTML Polls</a>&nbsp;<br />-&nbsp;<a
						href="http://www.webpollgenerator.com" title="html poll creator">HTML
						Poll Creator</a>
				</div>
			</noscript>
			<!-- End Web Poll Generator JavaScript Code -->
		</div>
		<div class="span4">
			<!-- Web Poll Code -->
			<div id="wpg_517d338edefc7">
				Loading <a href="http://www.webpollgenerator.com">web poll</a>...
			</div>
			<script language="JavaScript">
				(function() {
					wpgscript_517d338edefc7 = document.createElement('script');
					wpgscript_517d338edefc7.type = "text/javascript";
					wpgscript_517d338edefc7.src = "http://www.webpollgenerator.com/GetPoll2.php?p=517d338edefc7"
							+ "&s=" + escape(window.location);
					setTimeout(
							"document.getElementById('wpg_517d338edefc7').appendChild(wpgscript_517d338edefc7)",
							1);
				})();
			</script>
			<noscript>
				<div>
					<a
						href="http://www.webpollgenerator.com/Which-Type-of-Event-you-would-prefer-517d338edefc7"
						title="generator html polls">Generate HTML Polls</a>&nbsp;<br />-&nbsp;<a
						href="http://www.webpollgenerator.com" title="html poll creator">HTML
						Poll Creator</a>
				</div>
			</noscript>
			<!-- End Web Poll Generator JavaScript Code -->
		</div>
		<div class="span4">
			<!-- Web Poll Code -->
			<div id="wpg_517d3ca8522d6">
				Loading <a href="http://www.webpollgenerator.com">web poll</a>...
			</div>
			<script language="JavaScript">
				(function() {
					wpgscript_517d3ca8522d6 = document.createElement('script');
					wpgscript_517d3ca8522d6.type = "text/javascript";
					wpgscript_517d3ca8522d6.src = "http://www.webpollgenerator.com/GetPoll2.php?p=517d3ca8522d6"
							+ "&s=" + escape(window.location);
					setTimeout(
							"document.getElementById('wpg_517d3ca8522d6').appendChild(wpgscript_517d3ca8522d6)",
							1);
				})();
			</script>
			<noscript>
				<div>
					<a
						href="http://www.webpollgenerator.com/When-should-Events-start-517d3ca8522d6"
						title="generator html polls">Generate HTML Polls</a>&nbsp;<br />-&nbsp;<a
						href="http://www.webpollgenerator.com" title="html poll creator">HTML
						Poll Creator</a>
				</div>
			</noscript>
			<!-- End Web Poll Generator JavaScript Code -->
		</div>
	</div>

*}
	<br>
	<!-- 	Wall OF Fame -->
	{include file="index/loggedIn/wallOfFame/index.tpl"}
</div>
