<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>
		{{$title}}
	</title>
	<meta name="csrf-token" content="<?= csrf_token() ?>">
	{{ HTML::style("css/bootstrap-theme.min.css")}}
	{{ HTML::style("css/bootstrap.css")}}
	{{ HTML::style("css/font-awesome.min.css")}}
	{{ HTML::style("css/main.css")}}
	{{ HTML::style("css/bootstrap_datatable.css")}}
	{{ HTML::style("css/jquery.bracket.min.css")}}
	{{ HTML::style("css/jquery.fileupload.css")}}
	{{ HTML::style("css/6.jquery.countdown.css")}}
	{{ HTML::style("css/findMatch/findMatch.css")}}
	{{ HTML::style("css/findMatch/queueStats.css")}}
	{{ HTML::style("css/findMatch/modal.css")}}
	{{ HTML::style("css/match/match.css")}}
	{{ HTML::style("css/profile/profile.css")}}
	{{ HTML::style("css/news/news.css")}}
	{{ HTML::style("css/events/events.css")}}
	{{ HTML::style("css/home/home.css")}}
	{{ HTML::style("css/home/events.css")}}
	{{ HTML::style("css/chat/chat.css")}}
	
	{{ HTML::style("css/fonts.css")}}
	{{ HTML::style("css/arena.css")}}
	{{ HTML::style("css/sprites.css")}}

	{{ HTML::script('js/10.jquery-1.10.2.min.js') }}

	<script type="text/javascript">
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-18243363-4']);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
	</script>
</head>
<body>
	<?php

		include ('../../forum_connector.php');
		$forumConnector = new IPB_ForumConnector();
	?>
	<header>
		<div class="container">
			@include("navigation.social")
			@include("featured.index")
		</div>
			@section("topnavi")
			@include("navigation.topnav")
	</header>
	<div class="bottom_ad_container">
		@include ("ads.horizontal_ad")
	</div>
	<div class="container">

		@include("navigation.arena")
		
		<div class="main_container">
			@show
			@yield("content")
		</div>
		<!-- <div class="push"></div> -->
	</div>
	
	<div class="bottom_ad_container">
		@include ("ads.horizontal_ad")
	</div>

	<footer>
		@section("footer")
		@include("footer")	
	</footer>
	@show
	@include('generalModal')
	
	<script type="text/javascript">
	ARENA_PATH = "{{url('/')}}";
	</script>
	@if(Auth::check())
		<script type="text/javascript">
		USER = {};
		USER['username'] = "{{Auth::user()->name}}";
		USER['user_id'] ="{{Auth::user()->id}}";
		USER['avatar'] = "{{Auth::user()->avatar}}";
		USER['region'] = "{{Auth::user()->region_id}}";
		SOCKET = "{{GlobalSetting::getSocketLink()}}";
		USER['token'] = "{{base64_encode(hash_hmac('SHA256', Auth::user()->id.Auth::user()->region_id, '5uatH3Q2TqWnHYvJLGZbP8Fm', true))}}";
		</script>
	@else
		<script type="text/javascript">
		USER = {};
		SOCKET = "";
		</script>
	@endif
	<script src="{{GlobalSetting::getSocketLink()}}/socket.io/socket.io.js"></script>

	{{-- HTML::script('js/jquery-1.10.2.min.map') --}}
	{{ HTML::script('js/11.bootstrap.min.js') }}
	{{ HTML::script('js/12.jquery.ui.widget.js') }}
	{{ HTML::script('js/14.bootbox.min.js') }}
	{{ HTML::script('js/20.jquery.cookie.js') }}
	{{ HTML::script('js/20.jquery.validate.min.js') }}
	{{ HTML::script('js/22.jquery.tinysort.min.js') }}
	{{ HTML::script('js/30.jquery.countDown.js') }}
	{{ HTML::script('js/31.jquery.stopwatch.js') }}
	{{ HTML::script('js/32.jquery.timeago.js') }}
	{{ HTML::script('js/33.jquery.titleAlert.min.js') }}
	{{ HTML::script('js/34.jquery.countdown.min.js') }}
	{{ HTML::script('js/35.jquery.blockUI.js') }}
	{{ HTML::script('js/50.jquery.iframe-transport.js') }}
	{{ HTML::script('js/51.jquery.fileupload.js') }}
	{{ HTML::script('js/60.jquery.dataTables.min.js') }}
	{{ HTML::script('js/61.dataTable.paging.js') }}
	{{ HTML::script('js/80.highcharts-all.js') }}
	{{ HTML::script('js/81.jquery.bracket.min.js') }}
	{{ HTML::script('js/90.buzz.js') }}
	<script src='https://cdn.firebase.com/v0/firebase.js'></script>
	{{ HTML::script("js/main.js")}}
	{{ HTML::script("js/notification/notification.js")}}
	{{ HTML::script("js/bootstrap_datatable.js")}}
	{{ HTML::script("js/findMatch/findMatch.js")}}
	{{ HTML::script("js/findMatch/queue.js")}}
	{{ HTML::script("js/findMatch/queueModal.js")}}
	{{ HTML::script("js/audio/audio.js")}}
	{{ HTML::script("js/match/match.js")}}
	{{ HTML::script("js/match/replayUpload.js")}}
	{{ HTML::script("js/profile/profile.js")}}
	{{ HTML::script("js/profile/graphs.js")}}
	{{ HTML::script("js/ladder/ladder.js")}}
	{{ HTML::script("js/home/home.js")}}
	{{ HTML::script("js/firebase/chat.js")}}
	{{ HTML::script("js/event/event.js")}}
	{{ HTML::script("js/event/events.js")}}
	{{ HTML::script("js/testpage/testpage.js")}}
	{{ HTML::script("js/chat/chatNew.js")}}
	{{ HTML::script("js/openMatches/openMatches.js")}}
	{{ HTML::script("js/lastMatches/lastMatches.js")}}

</body>
</html>