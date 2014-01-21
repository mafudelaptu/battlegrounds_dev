<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<style>
	table form { margin-bottom: 0; }
	form ul { margin-left: 0; list-style: none; }
	.error { color: red; font-style: italic; }
	body { padding-top: 20px; }
	</style>
	<meta name="csrf-token" content="<?= csrf_token() ?>">
	{{ HTML::style("css/bootstrap.min.css")}}
	{{ HTML::style("css/bootstrap-theme.min.css")}}
	{{ HTML::style("css/font-awesome.min.css")}}
	{{ HTML::style("css/main.css")}}
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
				@if (Session::has('message'))
				<div class="flash alert">
					<p>{{ Session::get('message') }}</p>
				</div>
				@endif

				@yield('main')
			</div>

		</div>
		<div class="push"></div>	
	</div>
	@section("footer")
		@include("footer")
	@show
	@include('generalModal')



</body>

</html>