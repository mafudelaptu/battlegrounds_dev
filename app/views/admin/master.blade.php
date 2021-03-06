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
	{{ HTML::style("css/admin/main.css")}}
	{{ HTML::style("css/bootstrap_datatable.css")}}
	{{ HTML::style("css/jquery.bracket.min.css")}}
	{{ HTML::style("css/jquery.fileupload.css")}}
	{{ HTML::style("css/findMatch/findMatch.css")}}
	{{ HTML::style("css/findMatch/queueStats.css")}}
	{{ HTML::style("css/findMatch/modal.css")}}
	{{ HTML::style("css/match/match.css")}}
	{{ HTML::style("css/profile/profile.css")}}
	{{ HTML::style("css/news/news.css")}}
	{{ HTML::style("css/events/events.css")}}

	{{ HTML::script('js/10.jquery-1.10.2.min.js') }}
</head>
<body>
	<div class="wrapper">
		<div class="main-container">
			@section("topnavi")
			@include("admin.navigation.topnavi")
			@show
			<div class="container">
				@yield("content")
			</div>
			<div class="push"></div>
		</div>	
	</div>
	@section("footer")
	@include("footer")
	@show
	@include('generalModal')

	<script type="text/javascript">
	var ARENA_PATH = "{{url('/')}}";
	</script>

	{{ HTML::script('js/11.bootstrap.min.js') }}
	{{ HTML::script('js/12.jquery.ui.widget.js') }}
	{{ HTML::script('js/14.bootbox.min.js') }}
	{{ HTML::script('js/20.jquery.cookie.js') }}
	{{ HTML::script('js/20.jquery.validate.min.js') }}
	{{ HTML::script('js/30.jquery.countDown.js') }}
	{{ HTML::script('js/31.jquery.stopwatch.js') }}
	{{ HTML::script('js/32.jquery.timeago.js') }}
	{{ HTML::script('js/33.jquery.titleAlert.min.js') }}
	{{ HTML::script('js/34.jquery.countdown.min.js') }}
	{{ HTML::script('js/50.jquery.iframe-transport.js') }}
	{{ HTML::script('js/51.jquery.fileupload.js') }}
	{{ HTML::script('js/60.jquery.dataTables.min.js') }}
	{{ HTML::script('js/61.dataTable.paging.js') }}
	{{ HTML::script('js/81.jquery.bracket.min.js') }}
	{{ HTML::script('js/90.buzz.js') }}
	{{ HTML::script("js/main.js")}}
	{{ HTML::script("js/findMatch/queue.js")}}
	{{ HTML::script("js/findMatch/queueModal.js")}}
	{{ HTML::script("js/admin/queue.js")}}
	{{ HTML::script("js/admin/admin.js")}}
	{{ HTML::script("js/admin/match.js")}}
	{{ HTML::script("js/admin/bans.js")}}
</body>
</html>