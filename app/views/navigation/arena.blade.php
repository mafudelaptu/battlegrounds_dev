<!-- Collect the nav links, forms, and other content for toggling -->

	<ul class="inLine secondary_nav">
		<!-- <li>{{ HTML::link('find_match', 'Find Match') }}</li> -->
		<a href="{{URL::to('/')}}"><li>Home</li></a>
		<a href="{{URL::to('profile')}}"><li>Arena Profile</li></a>
		<a href="{{URL::to('ladder')}}"><li>Ladder</li></a>
		<a href="{{URL::to('events')}}"><li>Events</li></a>
		<!-- <li>{{ HTML::link(GlobalSetting::getForumLink(), 'Forum') }}</li> -->
		<!-- 		<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Help <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li>{{ HTML::link('help/faq', 'FAQ') }}</li>
					<li>{{ HTML::link('help/rules', 'Rules') }}</li>
				</ul>
			</li> -->
		
		@include("navigation.region")
		@include("navigation.notification-arena")
		@include("navigation.custom")
	</ul>



<!-- 	<ul class="nav navbar-nav navbar-right">
		
		
		
	</ul> -->