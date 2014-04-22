@section("featured")
	<a href="/" title="Homepage">
		<div class="header_logo"></div>
	</a>

<div class="user_nav">
	<ul class="inLine">				

		@include("navigation.notification")
		<!-- 
		@include("navigation.region") -->
		@include("navigation.usernavi")
	</ul>
</div>
	
	@include("featured.stats")
@show