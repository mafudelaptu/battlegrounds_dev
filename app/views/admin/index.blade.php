@section('content')
<div class="page-header">
	<h1>{{$heading}}</h1>
</div>
<h2>Management</h2>
<div id="adminPanelLinks">
	<div class="row">
		<div class="col-sm-2 adminPanelLink pointer" data-link="queues">
			<div>{{HTML::image("img/admin/adminPanel/queueIcon.png", "Queue-Management", array("height"=>"56"))}}</div>
			<div class="h4">Queue</div>
		</div>
		<div class="col-sm-2 adminPanelLink pointer" data-link="matches">
			<div><i class="fa fa-gamepad fa-4x"></i></div>
			<div class="h4">Match</div>
		</div>
		<div class="col-sm-2 adminPanelLink pointer" data-link="bans">
			<div><i class="fa fa-ban fa-4x"></i></div>
			<div class="h4">Ban</div>
		</div>
		<div class="col-sm-2 adminPanelLink pointer" data-link="helps">
			<div><i class="fa fa-question-circle fa-4x"></i></div>
			<div class="h4">Help-Section</div>
		</div>
		<div class="col-sm-2 adminPanelLink pointer" data-link="news">
			<div><i class="fa fa-file-text fa-4x"></i></div>
			<div class="h4">News</div>
		</div>
		<div class="col-sm-2 adminPanelLink pointer" data-link=""></div>
	</div>
<hr>
<h2>Settings</h2>
	<div class="row">
		<div class="col-sm-2 adminPanelLink pointer" data-link="globalsettings">
			<div><i class="fa fa-cogs fa-4x"></i></div>
			<div class="h4">Global Settings</div>
		</div>
		<div class="col-sm-2 adminPanelLink pointer" data-link="matchmodes">
			<div><span class="badge badge-info">MM</span></div>
			<div class="h4">Matchmodes</div>
		</div>
		<div class="col-sm-2 adminPanelLink pointer" data-link="matchtypes">
			<div><span class="badge badge-inverse">MT</span></div>
			<div class="h4">Matchtypes</div>
		</div>
		<div class="col-sm-2 adminPanelLink pointer" data-link="regions">
			<div><span class="badge badge-danger">R</span></div>
			<div class="h4">Regions</div>
		</div>
		<div class="col-sm-2 adminPanelLink pointer" data-link="banlistreasons">
			<div><i class="fa fa-user fa-4x"></i><i class="fa fa-ban fa-4x"></i></div>
			<div class="h4">Ban - Reasons</div>
		</div>
		<div class="col-sm-2 adminPanelLink pointer" data-link="pointtypes">
			<div><i class="fa fa-dot-circle-o fa-4x"></i></i></div>
			<div class="h4">Pointtypes</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-2 adminPanelLink pointer" data-link="skillbrackettypes">
			<div><i class="fa fa-bar-chart-o fa-4x"></i></i></div>
			<div class="h4">Skillbrackettypes</div>
		</div>
		<div class="col-sm-2 adminPanelLink pointer" data-link="matchmodes">
			
		</div>
		<div class="col-sm-2 adminPanelLink pointer" data-link="matchtypes">
			
		</div>
		<div class="col-sm-2 adminPanelLink pointer" data-link="regions">
			
		</div>
		<div class="col-sm-2 adminPanelLink pointer" data-link=""></div>
		<div class="col-sm-2 adminPanelLink pointer" data-link=""></div>
	</div>
</div>

@stop