@if( count($data) > 0 && is_array($data))
<h3>Your Status</h3>
		<div class="row">
			<?php 
			$i=1;
			?>
				@foreach($data as $k=>$v)
				@if( $v['matchesData'] != "")
				<div class="col-sm-6">
						<h4 class="muted">< Round {{$i}} ></h4>
						<div>
							<strong>MatchID: </strong> {{$v['matchesData']->match_id}}
						</div>
						<div>
							<strong>You are in Team: </strong>  {{$v['playerTeam']}}
						</div>
						<div>
							<strong>You have to Play vs. Team: </strong> {{$v['opponentTeam']}}
						</div>
						<hr>
						<div>
						{{$v['status']}}
						</div>
					</div>
					<?php 
					$i++;
					?>
				@endif
				@endforeach

		</div>
@endif