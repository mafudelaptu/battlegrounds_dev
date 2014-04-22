<div class="row">
	@if($matchState != "closed")
	<div class="col-sm-8">
		<div class="alert alert-info">
			
			@if($host->user_id == Auth::user()->id)

			<strong>You are Host of this Match! <br></strong>
			Now go into Dota2 and create a <strong>lobby</strong>. Therefore
			click on <i>"Play"</i> -> <i>"create Lobby"</i>. Set the password to
			the current MatchID: <strong>{{$matchData->id}}</strong>, the Matchmode to <strong>{{$matchData->matchmode}}</strong>
			and Region to <strong>{{$matchData->region}}</strong>

			@else
			@if($host->user_id > 0)
			Host of this Match is <img alt="Avatar" src="{{$host->avatar}}" width="25" height="25">&nbsp;{{$host->name}}. The Lobby password is <strong>{{$match_id}}</strong>
			@endif
			@endif
		
		</div>
	</div>
	@endif

	@if($inMatch)
	<div class="col-sm-4">

		You have <span id="userUpvotesLeft">
		@if($voteCounts->upvotes > 0)
		{{$voteCounts->upvotes}}
		@else
		0
		@endif
	</span>
	Upvotes and <span id="userDownvotesLeft">
	@if($voteCounts->downvotes > 0)
	{{$voteCounts->downvotes}}
	@else
	0
	@endif
</span>
Downvotes left
<a href="{{URL::to('help/faq')}}"
	target="_blank" class="t"
	title="What is the Credit-System and how does this work? "><i class="fa fa-question-circle"></i></a>
</div>
@endif
</div>
@if($matchData->matchtype_id == 2)
<button class="button mb_5" onclick="$('#rules').slideToggle();">Rules</button>
<div class="box_content" id="rules" style="display:none">
	<strong>Rules: </strong>
	<ul class="ulwp">
		<li>Win:
			<ul class="ulwp">
				<li>First to 2 kills or</li>
				<li>2 Towers destroyed</li>
			</ul>
		</li>
		<li>Not Allowed
			<ul class="ulwp">
				<li>Jungling</li>
				<li>Major creep/blocks/reroutes (like Fissure)</li>
				<li>Soul Ring</li>
			</ul>
		</li>
		<li>Allowed
			<ul class="ulwp">
				<li>Runes and Bottle Crowing</li>
				<li>Upgrading Courier</li>
			</ul>
		</li>
	</ul>
</div>
@endif


